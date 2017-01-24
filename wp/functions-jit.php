<?php
//---------------------------------------------------------------------
// JIT Images
//---------------------------------------------------------------------
// By default, WordPress generates all possible thumbnail sizes for an
// image immediately after upload. These functions alter this behavior
// so that instead thumbnails are only generated when a size is
// actually requested.
//
// To enable "just in time" thumbnails, add the following to wp-config:
// define('WP_JIT_IMAGES', true);
//
// See README for more information, gotchas, etc.

//-------------------------------------------------
// Disable all but stock sizes
//
// the stock sizes tie into WordPress more directly
// than anything extra defined in a theme, so we
// need to keep these around.
//
// @param sizes
// @return sizes
function _common_intermediate_image_sizes_advanced($sizes) {
	return array(
		'thumbnail'=>$sizes['thumbnail'],
		'medium'=>$sizes['medium'],
		'large'=>$sizes['large']
	);
}
add_filter('intermediate_image_sizes_advanced', '_common_intermediate_image_sizes_advanced');

//-------------------------------------------------
// Generate an image if missing
//
// @param downsize
// @param attachment id
// @param size
// @return size meta or false
function _common_image_downsize($downsize, $attachment_id, $size) {

	//if the size is already set, exit
	$image_meta = wp_get_attachment_metadata($attachment_id);

	//let WP handle fake sizes (e.g. favicons)
	if (is_array($size)) {
		return false;
	}

	//already exists
	if (is_array($image_meta) && isset($size) && isset($image_meta['sizes'][$size])) {
		return false;
	}

	//if the size exists, exit
	global $_wp_additional_image_sizes;
	if (!isset($_wp_additional_image_sizes[$size])) {
		return false;
	}

	//let's pull together a likely file name and see if it already exists,
	//even though the meta does not. this can happen if a plugin corrupts
	//image meta
	$made = false;

	$src_path = get_attached_file($attachment_id);

	//ignore svgs
	if (common_get_mime_type($src_path) === 'image/svg+xml') {
		return false;
	}

	$src_info = pathinfo($src_path);
	if (!isset($src_info['dirname']) || !isset($src_info['extension'])) {
		return false;
	}

	$src_root = trailingslashit($src_info['dirname']);
	$src_ext = $src_info['extension'];
	$src_mime = common_get_mime_type($src_path);
	$src_base = wp_basename($src_path, ".$src_ext");
	$new_size = image_resize_dimensions(
		$image_meta['width'],
		$image_meta['height'],
		$_wp_additional_image_sizes[$size]['width'],
		$_wp_additional_image_sizes[$size]['height'],
		$_wp_additional_image_sizes[$size]['crop']
	);
	if ($new_size) {
		$new_w = (int) $new_size[4];
		$new_h = (int) $new_size[5];
		$new_f = wp_basename("{$src_root}{$src_base}-{$new_w}x{$new_h}." . common_strtolower($src_ext));
		if (file_exists("{$src_root}{$new_f}")) {
			$made = true;
		}
	}

	//make it
	if (!$made) {
		if (!$resized = image_make_intermediate_size(
			$src_path,
			$_wp_additional_image_sizes[$size]['width'],
			$_wp_additional_image_sizes[$size]['height'],
			$_wp_additional_image_sizes[$size]['crop']
		)) {
			return false;
		}
	}
	else {
		$resized = array(
			'file'=>$new_f,
			'width'=>$new_w,
			'height'=>$new_h,
			'mime-type'=>$src_mime
		);
	}

	//update the metadata accordingly
	$image_meta['sizes'][$size] = $resized;
	wp_update_attachment_metadata($attachment_id, $image_meta);

	//return the info
	$src_url = wp_get_attachment_url($attachment_id);
	return array(dirname($src_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true);
}
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
// @param image meta
// @param sizes
// @param image source
// @param attachment id
// @return image meta or false
function _common_wp_calculate_image_srcset_meta($image_meta, $size_array, $image_src, $attachment_id) {
	//all registered sizes
	global $_wp_additional_image_sizes;

	//ignore non-image things
	if (!isset($image_meta['width']) || !isset($image_meta['height'])) {
		return false;
	}

	//some source file specs we'll use a lot
	$src_path = get_attached_file($attachment_id);

	//ignore svgs
	if (common_get_mime_type($src_path) === 'image/svg+xml') {
		return false;
	}

	$src_info = pathinfo($src_path);
	if (!isset($src_info['dirname']) || !isset($src_info['extension'])) {
		return false;
	}

	$src_root = trailingslashit($src_info['dirname']);
	$src_ext = $src_info['extension'];
	$src_mime = common_get_mime_type($src_path);
	$src_base = wp_basename($src_path, ".$src_ext");

	//find what's missing
	foreach ($_wp_additional_image_sizes as $k=>$v) {
		if (!isset($image_meta['sizes'][$k])) {

			//first, let's find out how things would play out dimensionally
			$new_size = image_resize_dimensions(
				$image_meta['width'],
				$image_meta['height'],
				$v['width'],
				$v['height'],
				$v['crop']
			);
			if (!$new_size) {
				continue;
			}
			$new_w = (int) $new_size[4];
			$new_h = (int) $new_size[5];

			//bad values
			if (!$new_h || !$new_w) {
				continue;
			}

			//generate a filename the same way WP_Image_Editor would
			$new_f = wp_basename("{$src_root}{$src_base}-{$new_w}x{$new_h}." . common_strtolower($src_ext));

			//finally, add it!
			$image_meta['sizes'][$k] = array(
				'file'=>$new_f,
				'width'=>$new_w,
				'height'=>$new_h,
				'mime-type'=>$src_mime
			);
		}
	}

	return $image_meta;
}
add_filter('wp_calculate_image_srcset_meta', '_common_wp_calculate_image_srcset_meta', 10, 4);

//-------------------------------------------------
// Generate Matched srcset sources
//
// @param sources
// @param size_array
// @param image_src
// @param image_meta
// @param attachment id
// @return sources or false
function _common_wp_calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {

	global $_wp_additional_image_sizes;

	//get some source info
	$src_path = get_attached_file($attachment_id);

	//ignore svgs
	if (common_get_mime_type($src_path) === 'image/svg+xml') {
		return false;
	}

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
	foreach ($sources as $k=>$v) {
		$name = wp_basename($v['url']);
		if (!file_exists("{$src_root}{$name}")) {
			//find the corresponding size
			foreach ($sizes as $k2=>$v2) {
				//we have a match!
				if ($v2['file'] === $name) {
					//make it
					if ($resized = image_make_intermediate_size(
						$src_path,
						$v2['width'],
						$v2['height'],
						$_wp_additional_image_sizes[$k2]['crop']
					)) {
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
	if ($new) {
		wp_update_attachment_metadata($attachment_id, $src_meta);
	}

	return $sources;
}
add_filter('wp_calculate_image_srcset', '_common_wp_calculate_image_srcset', 10, 5);
?>