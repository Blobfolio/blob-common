<?php
//---------------------------------------------------------------------
// FUNCTIONS: IMAGES
//---------------------------------------------------------------------
// This file contains functions relating to images, thumbnails, etc.

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;

//JIT images is split off into its own file
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	@require_once(BLOB_COMMON_ROOT . '/functions-jit-images.php');

//WebP is likewise in its own file
if(defined('WP_WEBP_IMAGES') && WP_WEBP_IMAGES)
	@require_once(BLOB_COMMON_ROOT . '/functions-webp.php');



//---------------------------------------------------------------------
// SVG
//---------------------------------------------------------------------

//-------------------------------------------------
// Allow SVG Uploads
//
// @param image types
// @return image types
if(!function_exists('common_upload_mimes')){
	function common_upload_mimes ($existing_mimes=array()){
		$existing_mimes['svg'] = 'image/svg+xml';
		return $existing_mimes;
	}
	add_filter('upload_mimes', 'common_upload_mimes');
}

//-------------------------------------------------
// SVG Previews in Media Library
//
// @param response
// @param attachment
// @param meta
// @return response
if(!function_exists('common_svg_media_thumbnail')){
	function common_svg_media_thumbnails($response, $attachment, $meta){

		if(!is_array($response) || !isset($response['type'], $response['subtype']))
			return $response;

		if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')){
			try {
				$path = get_attached_file($attachment->ID);
				if(@file_exists($path)){
					$svg = new SimpleXMLElement(@file_get_contents($path));
					$src = $response['url'];
					$width = (int) $svg['width'];
					$height = (int) $svg['height'];

					//media gallery
					$response['image'] = compact('src', 'width', 'height');
					$response['thumb'] = compact('src', 'width', 'height');

					//media single
					$response['sizes']['full'] = array(
						'height'		=> $height,
						'width'			=> $width,
						'url'			=> $src,
						'orientation'	=> $height > $width ? 'portrait' : 'landscape',
					);
				}
			}
			catch(Exception $e){}
		}

		return $response;
	}
	add_filter('wp_prepare_attachment_for_js', 'common_svg_media_thumbnails', 10, 3);
}

//-------------------------------------------------
// Clean SVG
//
// strip out XML headers and garbage that might be
// collected at the top of the file to make for
// better inline inclusion
//
// @param file path
// @return svg data or false
if(!function_exists('common_get_clean_svg')){
	function common_get_clean_svg($path){
		if(!@file_exists($path))
			return false;

		//start by cleaning up whitespace
		$svg = common_sanitize_whitespace(@file_get_contents($path));

		//fix a couple common Illustrator bugs
		$svg = str_replace(array('xmlns="&ns_svg;"','xmlns:xlink="&ns_xlink;"','id="Layer_1"'), array('xmlns="http://www.w3.org/2000/svg"','xmlns:xlink="http://www.w3.org/1999/xlink"',''), $svg);

		//drop spaces between tags
		$svg = str_replace("> <", "><", $svg);

		//find out where our SVG starts and ends
		if(false === ($start = strpos($svg, '<svg')) || false === ($end = strpos($svg, '</svg>')))
			return false;

		//and done!
		return common_sanitize_whitespace(substr($svg, $start, ($end - $start + 6)));
	}
}

//--------------------------------------------------------------------- end svg



//---------------------------------------------------------------------
// Misc
//---------------------------------------------------------------------

//-------------------------------------------------
// Get Featured Image SRC
//
// A shorthand function for returning a post's
// default image at a particular size.
//
// @param post id
// @param size
// @param return attributes (keys correspond to wp_get_attachment_image_src)
//		0 URL
//		1 width
//		2 height
//		3 is resized
// @param fallback to use if no featured thumb is set
// @return array, src or false
if(!function_exists('common_get_featured_image_src')){
	function common_get_featured_image_src($id=0, $size=null, $attributes=false, $fallback=0){
		$id = (int) $id;
		$tmp = (int) get_post_thumbnail_id($id);

		//using a fallback?
		if(!$tmp && $fallback > 0)
			$tmp = $fallback;

		if($tmp){
			$tmp2 = wp_get_attachment_image_src($tmp, $size);
			if(is_array($tmp2) && filter_var($tmp2[0], FILTER_VALIDATE_URL))
				return $attributes === true ? $tmp2 : $tmp2[0];
		}

		return false;
	}
}

//-------------------------------------------------
// Get Featured Image Path
//
// @param post id
// @param size
// @return path or false
if(!function_exists('common_get_featured_image_path')){
	function common_get_featured_image_path($id=0, $size=null){
		//surprisingly, there isn't a built-in function for this, so
		//let's just convert the URL back into the path
		if(false === ($url = common_get_featured_image_src($id, $size)))
			return false;

		return common_get_path_by_url($url);
	}
}

//-------------------------------------------------
// Blank Image
//
// return a simple 1x1 transparent GIF
//
// @param n/a
// @return src
if(!function_exists('common_get_blank_image')){
	function common_get_blank_image(){
		return 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABA
AACAkQBADs=';
	}
}

//--------------------------------------------------------------------- end misc
?>