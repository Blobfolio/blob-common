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
// prevent args
// @return svg data or false
if (!function_exists('common_get_clean_svg')) {
	function common_get_clean_svg($path, $args=null) {
		//for historical reasons, $args used to just be one arg: random_id.
		//if a bool, we'll assume that's what was meant.
		if (is_bool($args)) {
			$args = array('random_id'=>$args);
		}
		elseif (is_null($args) && defined('WP_CLEAN_SVG')) {
			$args = WP_CLEAN_SVG;
		}

		\blobfolio\common\ref\cast::array($args);

		return \blobfolio\common\image::clean_svg($path, $args);
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
// Sort SRCSET by size
//
// @param a
// @param b
if (!function_exists('_common_sort_srcset')) {
	function _common_sort_srcset($a, $b) {
		$a1 = explode(' ', common_sanitize_whitespace($a));
		$b1 = explode(' ', common_sanitize_whitespace($b));

		//can't compute, leave it alone
		if (count($a1) !== 2 || count($b1) !== 2) {
			return 0;
		}

		$a2 = round(preg_replace('/[^\d]/', '', $a1[1]));
		$b2 = round(preg_replace('/[^\d]/', '', $b1[1]));

		if ($a2 === $b2) {
			return 0;
		}

		return $a2 < $b2 ? -1 : 1;
	}
}

//-------------------------------------------------
// Srcset Wrapper
//
// @param attachment_id
// @param sizes
// @return srcset string or false
if (!function_exists('common_get_image_srcset')) {
	function common_get_image_srcset(int $attachment_id=0, $size) {
		if ($attachment_id < 1) {
			return false;
		}

		\blobfolio\common\ref\cast::array($size);
		\blobfolio\common\ref\sanitize::whitespace($size);
		$size = array_unique($size);
		$size = array_filter($size, 'strlen');
		$size = array_values($size);

		//no size or bad attachment
		if (!count($size)) {
			$size[] = 'full';
		}

		//if there's just one, try to let WP do it
		$srcset = false;
		if (count($size) === 1) {
			$srcset = wp_get_attachment_image_srcset($attachment_id, $size[0]);
		}

		//no srcset yet?
		if (false === $srcset) {
			$srcset = array();
			foreach ($size as $s) {
				if (false !== $tmp = wp_get_attachment_image_src($attachment_id, $s)) {
					$srcset[] = "{$tmp[0]} {$tmp[1]}w";
				}
			}

			if (!count($srcset)) {
				return false;
			}
		}
		//convert WP's answer to an array
		else {
			$srcset = explode(',', $srcset);
			\blobfolio\common\ref\sanitize::whitespace($srcset);
		}

		//sort
		usort($srcset, '_common_sort_srcset');

		//return
		return implode(', ', $srcset);
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