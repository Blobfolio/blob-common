<?php
//---------------------------------------------------------------------
// FUNCTIONS: IMAGES
//---------------------------------------------------------------------
// This file contains functions relating to images, thumbnails, etc.

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}

//JIT images is split off into its own file
if (defined('WP_JIT_IMAGES') && WP_JIT_IMAGES) {
	@require_once(BLOB_COMMON_ROOT . '/functions-jit.php');
}

//WebP is likewise in its own file
if (defined('WP_WEBP_IMAGES') && WP_WEBP_IMAGES) {
	@require_once(BLOB_COMMON_ROOT . '/functions-webp.php');
}



//---------------------------------------------------------------------
// SVG
//---------------------------------------------------------------------

//-------------------------------------------------
// Clean SVG
//
// strip out XML headers and garbage that might be
// collected at the top of the file to make for
// better inline inclusion
//
// @param file path
// prevent randomize id
// @return svg data or false
if (!function_exists('common_get_clean_svg')) {
	function common_get_clean_svg($path, $random_id=false) {
		return \blobfolio\common\image::clean_svg($path, array('random_id'=>$random_id));
	}
}

//-------------------------------------------------
// SVG Dimensions
//
// @param svg path
// @return return width/height
if (!function_exists('common_get_svg_dimensions')) {
	function common_get_svg_dimensions($path) {
		if (false === ($out = \blobfolio\common\image::svg_dimensions($path))) {
			$out = array('width'=>0, 'height'=>0);
		}
		return $out;
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
if (!function_exists('common_get_featured_image_src')) {
	function common_get_featured_image_src($id=0, $size=null, $attributes=false, $fallback=0) {
		$id = (int) $id;
		if (!$id) {
			$id = (int) get_the_ID();
		}
		$tmp = (int) get_post_thumbnail_id($id);

		//using a fallback?
		if (!$tmp && $fallback > 0) {
			$tmp = $fallback;
		}

		if ($tmp) {
			$tmp2 = wp_get_attachment_image_src($tmp, $size);
			if (is_array($tmp2) && filter_var($tmp2[0], FILTER_VALIDATE_URL)) {
				return $attributes === true ? $tmp2 : $tmp2[0];
			}
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
if (!function_exists('common_get_featured_image_path')) {
	function common_get_featured_image_path($id=0, $size=null) {
		//surprisingly, there isn't a built-in function for this, so
		//let's just convert the URL back into the path
		if (false === ($url = common_get_featured_image_src($id, $size))) {
			return false;
		}

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
if (!function_exists('common_get_blank_image')) {
	function common_get_blank_image() {
		return \blobfolio\common\constants::BLANK_IMAGE;
	}
}

//--------------------------------------------------------------------- end misc
?>