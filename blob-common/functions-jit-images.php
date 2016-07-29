<?php
//---------------------------------------------------------------------
// JIT Images
//---------------------------------------------------------------------
//toggle JIT images by setting WP_JIT_IMAGES constant

//-------------------------------------------------
// Disable all but stock sizes
//
// @param sizes
// @return sizes
function _common_intermediate_image_sizes_advanced($sizes){
	// Removing these defaults might cause problems, so we don't
	return array(
		'thumbnail' => $sizes['thumbnail'],
		'medium' => $sizes['medium'],
		'large' => $sizes['large']
	);
}
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	add_filter('intermediate_image_sizes_advanced', '_common_intermediate_image_sizes_advanced');

//-------------------------------------------------
// Generate an image if missing
//
// @param downsize
// @param attachment id
// @param size
// @return size meta or false
function _common_image_downsize($downsize, $attachment_id, $size){
	//if the size is already set, exit
	$image_meta = wp_get_attachment_metadata($attachment_id);
	if(is_array($image_meta) && isset($image_meta['sizes'][$size]))
		return false;

	//if the size exists, exit
	global $_wp_additional_image_sizes;
	if(!isset($_wp_additional_image_sizes[$size]))
		return false;

	//make it
	if(!$resized = image_make_intermediate_size(
		get_attached_file($attachment_id),
		$_wp_additional_image_sizes[$size]['width'],
		$_wp_additional_image_sizes[$size]['height'],
		$_wp_additional_image_sizes[$size]['crop']
	))
		return false;

	//update the metadata accordingly
	$image_meta['sizes'][$size] = $resized;
	wp_update_attachment_metadata($attachment_id, $image_meta);

	//return the info
	$src_url = wp_get_attachment_url($attachment_id);
	return array(dirname($src_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true);
}
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	add_filter('image_downsize', '_common_image_downsize', 10, 3);

//-------------------------------------------------
// Add Missing Sizes to SRCSET pool
//
// the earlier filters exclude sizes from image
// metadata, which is fine for functions hooking
// into downsize, but the new srcsets take the
// data for granted. let's temporarily add them
// back.
//
// @param
function _common_wp_calculate_image_srcset_meta($image_meta, $size_array, $image_src, $attachment_id){
	//all registered sizes
	global $_wp_additional_image_sizes;

	//some source file specs we'll use a lot
	$src_path = get_attached_file($attachment_id);
	$src_info = pathinfo($src_path);
	$src_root = trailingslashit($src_info['dirname']);
	$src_ext = $src_info['extension'];
	$src_mime = wp_check_filetype($src_path);
	$src_mime = $src_mime['type'];
	$src_base = wp_basename($src_path, ".$src_ext");

	//find what's missing
	foreach($_wp_additional_image_sizes AS $k=>$v){
		if(!isset($image_meta['sizes'][$k])){
			//first, let's find out how things would play out dimensionally
			$new_size = image_resize_dimensions($image_meta['width'], $image_meta['height'], $v['width'], $v['height'], $v['crop']);
			if(!$new_size)
				continue;
			$new_w = (int) $new_size[4];
			$new_h = (int) $new_size[5];

			//bad values
			if(!$new_h || !$new_w)
				continue;

			//generate a filename the same way WP_Image_Editor would
			$new_f = wp_basename("{$src_root}{$src_base}-{$new_w}x{$new_h}." . strtolower($src_ext));

			//finally, add it!
			$image_meta['sizes'][$k] = array(
				'file'		=> $new_f,
				'width'		=> $new_w,
				'height'	=> $new_h,
				'mime-type'	=> $src_mime
			);
		}
	}

	return $image_meta;
}
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	add_filter('wp_calculate_image_srcset_meta', '_common_wp_calculate_image_srcset_meta', 10, 4);

//-------------------------------------------------
// Generate Matched srcset sources
//
// @param sources
// @param size_array
// @param image_src
// @param image_meta
// @param attachment id
function _common_wp_calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id){
	static $called;

	//get some source info
	$src_path = get_attached_file($attachment_id);
	$src_root = trailingslashit(pathinfo($src_path, PATHINFO_DIRNAME));

	//the actual image metadata (which might be altered here)
	$src_meta = wp_get_attachment_metadata($attachment_id);

	//an array of possible sizes to search through
	$sizes = $image_meta['sizes'];
	unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['large']);

	$new = false;

	//loop through sources
	foreach($sources AS $k=>$v){
		$name = wp_basename($v['url']);
		if(!file_exists("{$src_root}{$name}")){
			//find the corresponding size
			foreach($sizes AS $k2=>$v2){
				//we have a match!
				if($v2['file'] === $name){
					//make it
					if(!$resized = image_make_intermediate_size(
						$src_path,
						$v2['width'],
						$v2['height'],
						isset($v2['crop']) ? $v2['crop'] : null
					)){
						//remove from sources on failure
						//unset($sources[$k]);
					}
					else {
						//add the new thumb to the true meta
						$new = true;
						$src_meta['sizes'][$k2] = $resized;
					}

					//remove from the sizes array so we have
					//less to search next time
					unset($sizes[$k2]);
					break;
				}//match
			}//each size
		}//each 404
	}//each source

	//if we generated something, update the attachment meta
	if($new)
		wp_update_attachment_metadata($attachment_id, $src_meta);

	return $sources;
}
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	add_filter('wp_calculate_image_srcset', '_common_wp_calculate_image_srcset', 10, 5);
?>