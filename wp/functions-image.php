<?php
/**
 * Image Functions
 *
 * This file contains helpers for images, thumbnails, etc.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

use \blobfolio\common\constants;
use \blobfolio\common\image;
use \blobfolio\common\ref\cast as r_cast;
use \blobfolio\common\ref\sanitize as r_sanitize;
use \blobfolio\common\sanitize as v_sanitize;

// JIT images is split off into its own file.
if (defined('WP_JIT_IMAGES') && WP_JIT_IMAGES) {
	@require_once(BLOBCOMMON_ROOT . '/functions-jit.php');
}

// WebP is likewise in its own file.
if (defined('WP_WEBP_IMAGES') && WP_WEBP_IMAGES) {
	@require_once(BLOBCOMMON_ROOT . '/functions-webp.php');
}



// ---------------------------------------------------------------------
// SVG
// ---------------------------------------------------------------------

if (!function_exists('common_get_clean_svg')) {
	/**
	 * Clean SVG for Inline Embedding
	 *
	 * @param string $path Source path.
	 * @param mixed $args Arguments.
	 *
	 * @arg bool $clean_styles Fix <style> formatting, combine tags.
	 * @arg bool $fix_dimensions Fix missing width, height, viewBox.
	 * @arg bool $namespace Generate an xmlns:svg namespace.
	 * @arg bool $random_id Randomize IDs.
	 * @arg bool $rewrite_styles Redo class assignments to group like rules.
	 * @arg bool $sanitize Sanitize content.
	 * @arg bool $save Save cleaned file for faster repeat processing.
	 * @arg bool $strip_data Remove data-x attributes.
	 * @arg bool $strip_id Remove IDs.
	 * @arg bool $strip_style Remove styles.
	 * @arg bool $strip_title Remove titles.
	 * @arg array $whitelist_attr Additional attributes to allow.
	 * @arg array $whitelist_tags Additional tags to allow.
	 * @arg array $whitelist_protocols Additional protocols to allow.
	 * @arg array $whitelist_domains Additional domains to allow.
	 *
	 * @return string|bool SVG code or false.
	 */
	function common_get_clean_svg($path, $args=null) {
		// For historical reasons, $args used to just be one arg: random_id.
		// If a bool, we'll assume that's what was meant.
		if (is_bool($args)) {
			$args = array('random_id'=>$args);
		}
		elseif (
			(is_null($args) || (is_array($args) && !count($args))) &&
			defined('WP_CLEAN_SVG')
		) {
			$args = WP_CLEAN_SVG;
		}

		r_cast::to_array($args);

		// Make sure the site URL is whitelisted.
		if (!isset($args['whitelist_domains'])) {
			$args['whitelist_domains'] = array();
		}
		else {
			r_cast::to_array($args['whitelist_domains']);
		}
		$args['whitelist_domains'][] = common_get_site_hostname();

		return image::clean_svg($path, $args);
	}
}

if (!function_exists('common_shortcode_clean_svg')) {
	/**
	 * Shortcode for clean SVG insertion. It accepts
	 * all the same arguments, but with the addition
	 * of classes, which are applied to the figure.
	 * The SVG is passed as "attachment_id".
	 *
	 * @param mixed $args Arguments.
	 * @param string $content Content.
	 *
	 * @return string HTML.
	 */
	function common_shortcode_clean_svg($args=null, $content='') {
		r_cast::to_array($args);

		// We need to convert array fields to proper arrays.
		foreach (array('classes','whitelist_attr','whitelist_tags','whitelist_protocols','whitelist_domains') as $field) {
			if (isset($args[$field])) {
				$args[$field] = explode(',', $args[$field]);
				r_sanitize::whitespace($args[$field]);
				$args[$field] = array_unique($args[$field]);
				$args[$field] = array_filter($args[$field], 'strlen');
			}
		}

		// Get the SVG first, in case it fails.
		if (
			!isset($args['attachment_id']) ||
			false === ($svg = get_attached_file($args['attachment_id']))
		) {
			return false;
		}

		// Separate SVG arguments.
		$svgargs = $args;
		unset($svgargs['attachment_id']);
		if (isset($svgargs['classes'])) {
			unset($svgargs['classes']);
		}

		$svg = common_get_clean_svg($svg, $svgargs);
		if (!is_string($svg) || !strlen($svg)) {
			return false;
		}

		$classes = isset($args['classes']) ? $args['classes'] : array();

		ob_start();
		?>
		<figure class="wp-caption <?=esc_attr(implode(' ', $classes))?>">
			<?=$svg?>
			<?php if (strlen($content)) { ?>
				<figcaption class="wp-caption-text"><?=$content?></figcaption>
			<?php } ?>
		</figure>
		<?php
		return ob_get_clean();
	}
	add_shortcode('common-clean-svg', 'common_shortcode_clean_svg');
}

if (!function_exists('common_get_svg_dimensions')) {
	/**
	 * Determine SVG Dimensions
	 *
	 * @param string $svg SVG content or file path.
	 * @return array Dimensions.
	 */
	function common_get_svg_dimensions($svg) {
		if (false === ($out = image::svg_dimensions($svg))) {
			$out = array('width'=>0, 'height'=>0);
		}
		return $out;
	}
}

// --------------------------------------------------------------------- end svg



// ---------------------------------------------------------------------
// Misc
// ---------------------------------------------------------------------

if (!function_exists('common_get_featured_image_src')) {
	/**
	 * Get Featured Image SRC
	 *
	 * A shorthand function for returning a post's featured
	 * image at a particular size.
	 *
	 * @param int $id Post ID.
	 * @param string $size Size.
	 * @param bool $attributes Return attributes or just URL.
	 * @param int $fallback Attachment ID to use as fallback image.
	 * @return mixed Image URL. If $attributes, an array containing the URL, width, and height. False on failure.
	 */
	function common_get_featured_image_src($id=0, $size=null, $attributes=false, $fallback=0) {
		$id = (int) $id;
		if (!$id) {
			$id = (int) get_the_ID();
		}
		$tmp = (int) get_post_thumbnail_id($id);

		// Using a fallback?
		if (!$tmp && $fallback > 0) {
			$tmp = $fallback;
		}

		if ($tmp) {
			$tmp2 = wp_get_attachment_image_src($tmp, $size);
			if (is_array($tmp2) && filter_var($tmp2[0], FILTER_VALIDATE_URL)) {
				return true === $attributes ? $tmp2 : $tmp2[0];
			}
		}

		return false;
	}
}

if (!function_exists('_common_sort_srcset')) {
	/**
	 * Sort SRCSET by Size
	 *
	 * This is a usort() callback for sorting an
	 * array of SRCSET sources by size.
	 *
	 * @param string $a Source.
	 * @param string $b Source.
	 * @return int Order: -1, 0, 1.
	 */
	function _common_sort_srcset($a, $b) {
		$a1 = explode(' ', v_sanitize::whitespace($a));
		$b1 = explode(' ', v_sanitize::whitespace($b));

		// Can't compute, leave it alone.
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

if (!function_exists('common_get_image_srcset')) {
	/**
	 * SRCSET Wrapper
	 *
	 * Generate a list of SRCSET sources using specific
	 * sizes, or have WordPress come up with the list
	 * using a single size.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @param array|string $size Size(s).
	 * @return string|bool SRCSET string or false.
	 */
	function common_get_image_srcset($attachment_id=0, $size) {
		r_cast::to_int($attachment_id, true);

		if ($attachment_id < 1) {
			return false;
		}

		r_cast::to_array($size);
		r_sanitize::whitespace($size);
		$size = array_unique($size);
		$size = array_filter($size, 'strlen');
		$size = array_values($size);

		// No size or bad attachment.
		if (!count($size)) {
			$size[] = 'full';
		}

		// If there's just one, try to let WP do it.
		$srcset = false;
		if (count($size) === 1) {
			$srcset = wp_get_attachment_image_srcset($attachment_id, $size[0]);
		}

		// No srcset yet?
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
		// Convert WP's answer to an array.
		else {
			$srcset = explode(',', $srcset);
			r_sanitize::whitespace($srcset);
		}

		// Sort.
		usort($srcset, '_common_sort_srcset');

		// Return.
		return implode(', ', $srcset);
	}
}

if (!function_exists('common_get_featured_image_path')) {
	/**
	 * Get Featured Image Path
	 *
	 * This works like `common_get_featured_image_src()`
	 * but returns a file path instead of a URL.
	 *
	 * @param int $id Post ID.
	 * @param string $size Size.
	 * @return string|bool File path or false.
	 */
	function common_get_featured_image_path($id=0, $size=null) {
		// Surprisingly, there isn't a built-in function for this, so
		// let's just convert the URL back into the path.
		if (false === ($url = common_get_featured_image_src($id, $size))) {
			return false;
		}

		return common_get_path_by_url($url);
	}
}

if (!function_exists('common_get_blank_image')) {
	/**
	 * Blank Image
	 *
	 * Get a Data-URI representing a simple 1x1
	 * transparent GIF.
	 *
	 * @return string Data-URI.
	 */
	function common_get_blank_image() {
		return constants::BLANK_IMAGE;
	}
}

// --------------------------------------------------------------------- end misc

