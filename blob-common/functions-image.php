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
	@require_once(BLOB_COMMON_ROOT . '/functions-jit.php');

//WebP is likewise in its own file
if(defined('WP_WEBP_IMAGES') && WP_WEBP_IMAGES)
	@require_once(BLOB_COMMON_ROOT . '/functions-webp.php');



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
if(!function_exists('common_get_clean_svg')){
	function common_get_clean_svg($path, $random_id=false){
		if(!@file_exists($path))
			return false;

		static $ids;
		if(is_null($ids))
			$ids = array();

		//start by cleaning up whitespace
		$svg = common_sanitize_whitespace(@file_get_contents($path));

		//fix a couple common Illustrator bugs
		$svg = str_replace(array('xmlns="&ns_svg;"','xmlns:xlink="&ns_xlink;"','id="Layer_1"'), array('xmlns="http://www.w3.org/2000/svg"','xmlns:xlink="http://www.w3.org/1999/xlink"',''), $svg);

		//find out where our SVG starts and ends
		if(false === ($start = strpos($svg, '<svg')) || false === ($end = strpos($svg, '</svg>')))
			return false;

		$svg = common_sanitize_whitespace(substr($svg, $start, ($end - $start + 6)));

		try {
			$dom = new DOMDocument('1.0', get_bloginfo('charset'));
			$headers = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
			$dom->formatOutput = false;
			$dom->preserveWhiteSpace = false;
			$dom->loadXML("{$headers}\n{$svg}");

			//are we randomizing the id?
			if($random_id){
				$svgs = $dom->getElementsByTagName('svg');
				if($svgs->length){
					foreach($svgs AS $v){
						$id = 'svg-' . strtolower(common_generate_random_string(5));
						while(in_array($id, $ids))
							$id = 'svg-' . strtolower(common_generate_random_string(5));
						$ids[] = $id;
						$v->setAttribute('id', $id);
					}
				}
			}

			$svg = $dom->saveXML();
			if(false === ($start = strpos($svg, '<svg')) || false === ($end = strpos($svg, '</svg>')))
				return false;
			$svg = common_sanitize_whitespace(substr($svg, $start, ($end - $start + 6)));
		}
		catch(Exception $e){ return false; }

		return $svg;
	}
}

//-------------------------------------------------
// SVG Dimensions
//
// @param svg path
// @return return width/height
if(!function_exists('common_get_svg_dimensions')){
	function common_get_svg_dimensions($path){
		$out = array('width'=>0, 'height'=>0);
		if(false === $svg = common_get_clean_svg($path))
			return $out;

		try {
			$xml = simplexml_load_string($svg);
			$attr = $xml->attributes();
			$out['width'] = common_sanitie_number($attr->width);
			$out['height'] = common_sanitize_number($attr->height);
		}
		catch(Exception $e){}

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
if(!function_exists('common_get_featured_image_src')){
	function common_get_featured_image_src($id=0, $size=null, $attributes=false, $fallback=0){
		$id = (int) $id;
		if(!$id)
			$id = (int) get_the_ID();
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