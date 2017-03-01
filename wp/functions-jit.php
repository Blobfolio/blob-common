<?php
/**
 * JIT Images
 *
 * By default WordPress generates all possible thumbnail sizes for
 * every image immediately after upload. These functions alter this
 * behavior so that instead thumbnails are only generated when a
 * size is actually requested.
 *
 * To enable "just in time" thumbnails, add the following to wp-config:
 * define('WP_JIT_IMAGES', true);
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Disable All But Stock Sizes
 *
 * @param array $sizes Sizes.
 * @return array Sizes.
 */
function _common_intermediate_image_sizes_advanced($sizes) {
	return array(
		'thumbnail'=>$sizes['thumbnail'],
		'medium'=>$sizes['medium'],
		'large'=>$sizes['large']
	);
}
add_filter('intermediate_image_sizes_advanced', '_common_intermediate_image_sizes_advanced');

/**
 * Generate Missing Size
 *
 * @param array $downsize Downsize.
 * @param int $attachment_id Attachment ID.
 * @param string|array $size Size.
 * @return array|bool Meta data or false.
 */
function _common_image_downsize($downsize, $attachment_id, $size) {

	// If the size is already set, exit.
	$image_meta = wp_get_attachment_metadata($attachment_id);

	// Let WP handle fake sizes (e.g. favicons).
	if (is_array($size)) {
		return false;
	}

	// Already exists.
	if (is_array($image_meta) && isset($size) && isset($image_meta['sizes'][$size])) {
		return false;
	}

	// If the size exists, exit.
	global $_wp_additional_image_sizes;
	if (!isset($_wp_additional_image_sizes[$size])) {
		return false;
	}

	// Let's pull together a likely file name and see if it already exists,
	// even though the meta does not. this can happen if a plugin corrupts
	// image meta.
	$made = false;

	$src_path = get_attached_file($attachment_id);

	// Ignore SVGs.
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

	// Make it!
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

	// Update the metadata accordingly.
	$image_meta['sizes'][$size] = $resized;
	wp_update_attachment_metadata($attachment_id, $image_meta);

	// Return the info.
	$src_url = wp_get_attachment_url($attachment_id);
	return array(dirname($src_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true);
}
add_filter('image_downsize', '_common_image_downsize', 10, 3);

/**
 * Add Missing Sizes to SRCSET Pool
 *
 * The earlier filters exclude sizes from image
 * metadata until needed, which is fine for
 * functions hooking into downsize, but the new
 * srcset functions take the data for granted.
 * Let's temporarily add them back so they work
 * correctly.
 *
 * @param array $image_meta Existing meta.
 * @param array $size_array Size array.
 * @param string $image_src Image source.
 * @param int $attachment_id Attachment ID.
 * @return array|bool Image meta or false.
 */
function _common_wp_calculate_image_srcset_meta($image_meta, $size_array, $image_src, $attachment_id) {
	// All registered sizes.
	global $_wp_additional_image_sizes;

	// Ignore non-image things.
	if (!isset($image_meta['width']) || !isset($image_meta['height'])) {
		return false;
	}

	// Some source file specs we'll use a lot.
	$src_path = get_attached_file($attachment_id);

	// Ignore SVGs.
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

	// Find what's missing.
	foreach ($_wp_additional_image_sizes as $k=>$v) {
		if (!isset($image_meta['sizes'][$k])) {

			// First, let's find out how things would play out dimensionally.
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

			// Bad values.
			if (!$new_h || !$new_w) {
				continue;
			}

			// Generate a filename the same way WP_Image_Editor would.
			$new_f = wp_basename("{$src_root}{$src_base}-{$new_w}x{$new_h}." . common_strtolower($src_ext));

			// Finally, add it!
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

/**
 * Generate Matched SRCSET sources
 *
 * @param array $sources Sources.
 * @param array $size_array Sizes.
 * @param string $image_src Image source.
 * @param array $image_meta Image meta.
 * @param int $attachment_id Attachment ID.
 * @return array|bool Sources or false.
 */
function _common_wp_calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {

	global $_wp_additional_image_sizes;

	// Get some source info.
	$src_path = get_attached_file($attachment_id);

	// Ignore SVGs.
	if (common_get_mime_type($src_path) === 'image/svg+xml') {
		return false;
	}

	$src_root = trailingslashit(pathinfo($src_path, PATHINFO_DIRNAME));

	// The actual image metadata (which might be altered here).
	$src_meta = wp_get_attachment_metadata($attachment_id);

	// An array of possible sizes to search through.
	$sizes = $image_meta['sizes'];
	unset($sizes['thumbnail']);
	unset($sizes['medium']);
	unset($sizes['large']);

	$new = false;

	// Loop through sources.
	foreach ($sources as $k=>$v) {
		$name = wp_basename($v['url']);
		if (!file_exists("{$src_root}{$name}")) {
			// Find the corresponding size.
			foreach ($sizes as $k2=>$v2) {
				// We have a match!
				if ($v2['file'] === $name) {
					// Make it.
					if ($resized = image_make_intermediate_size(
						$src_path,
						$v2['width'],
						$v2['height'],
						$_wp_additional_image_sizes[$k2]['crop']
					)) {
						// Add the new thumb to the true meta.
						$new = true;
						$src_meta['sizes'][$k2] = $resized;
					}

					// Remove from the sizes array so we have
					// less to search next time.
					unset($sizes[$k2]);
					break;
				}//match
			}//each size
		}//each 404
	}//each source

	// If we generated something, update the attachment meta.
	if ($new) {
		wp_update_attachment_metadata($attachment_id, $src_meta);
	}

	return $sources;
}
add_filter('wp_calculate_image_srcset', '_common_wp_calculate_image_srcset', 10, 5);

