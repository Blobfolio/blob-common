<?php
/**
 * Behavioral Overrides
 *
 * This file contains certain behavioral overrides that fix common
 * annoyances with WordPress. Some of them are automatic, some are
 * controlled via constants or direct calls.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}



// ---------------------------------------------------------------------
// MIME Fix
// ---------------------------------------------------------------------

if (!function_exists('common_upload_real_mimes')) {
	/**
	 * Fix/Improve Upload MIME Detection
	 *
	 * WordPress 4.7.1 and 4.7.2 contain a bug
	 * preventing non-normal images from being
	 * uploaded.
	 *
	 * This also allows for Magic MIME-based
	 * renaming in cases where a file's name
	 * does not match its content.
	 *
	 * @param array $checked Checked data.
	 * @param str $file File path.
	 * @param str $filename File name.
	 * @param mixed $mimes Allowed MIMEs.
	 * @return array Checked data.
	 */
	function common_upload_real_mimes($checked, $file, $filename, $mimes) {

		// Only worry if the first check failed.
		if (!$checked['type'] || !$checked['ext']) {
			$finfo = \blobfolio\common\mime::finfo($file, $filename);

			// The time for checking certain image types has passed already.
			$mime_to_ext = apply_filters('getimagesize_mimes_to_exts', array(
				'image/jpeg'=>'jpg',
				'image/png'=>'png',
				'image/gif'=>'gif',
				'image/bmp'=>'bmp',
				'image/tiff'=>'tif',
			));
			if (
				0 !== \blobfolio\common\mb::strpos($finfo['mime'], 'image/') ||
				!in_array($finfo['extension'], $mime_to_ext, true)
			) {
				// Was the extension wrong?
				if (count($finfo['suggested_filename'])) {
					$filename = \blobfolio\common\data::array_pop_top($finfo['suggested_filename']);
				}

				// What does WP think?
				$wp_filetype = wp_check_filetype($filename, $mimes);
				if ($wp_filetype['ext'] && $wp_filetype['type']) {
					$checked['ext'] = $wp_filetype['ext'];
					$checked['type'] = $wp_filetype['type'];
					$checked['proper_filename'] = $filename;
				}
			}
		}

		// Sanitize SVGs.
		if ('image/svg+xml' === $checked['type']) {
			$contents = @file_get_contents($file);
			\blobfolio\common\ref\sanitize::svg($contents);
			if (strlen($contents)) {
				@file_put_contents($file, $contents);
			}
			else {
				$checked['type'] = false;
				$checked['ext'] = false;
			}
		}

		return $checked;
	}
	add_filter('wp_check_filetype_and_ext', 'common_upload_real_mimes', 10, 4);
}

// --------------------------------------------------------------------- end MIME fix



// ---------------------------------------------------------------------
// Automatic Overrides
// ---------------------------------------------------------------------

// Do not include back/next links in meta.
add_filter('previous_post_rel_link', '__return_false');
add_filter('next_post_rel_link', '__return_false');

if (!function_exists('common_disable_wp_embed')) {
	/**
	 * Disable WP-Embed Scripts
	 *
	 * @return void Nothing.
	 */
	function common_disable_wp_embed() {
		wp_deregister_script('wp-embed');
	}
	add_action('wp', 'common_disable_wp_embed');
}

if (!function_exists('common_disable_checked_to_top')) {
	/**
	 * Don't Re-Order Post Taxonomy
	 *
	 * Keep checkbox-style terms ordered
	 * alphabetically, otherwise the
	 * hierarchy breaks when a child is
	 * selected.
	 *
	 * @param array $args Arguments.
	 * @return array Arguments.
	 */
	function common_disable_checked_to_top($args) {
		$args['checked_ontop'] = false;
		return $args;
	}
	add_filter('wp_terms_checklist_args', 'common_disable_checked_to_top');
}

if (!function_exists('common_cron_schedules')) {
	/**
	 * More WP CRON Schedules
	 *
	 * Using the shorter invertvals will probably
	 * require moving from pseudo-cron to real-cron.
	 *
	 * In wp-config.php:
	 * define('DISABLE_WP_CRON', true);
	 *
	 * Crontab entry:
	 * * * * * * wget -O- https://domain.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
	 *
	 * @param array $schedules Schedules.
	 * @return array Schedules.
	 */
	function common_cron_schedules($schedules) {

		// Every minute.
		$schedules['oneminute'] = array(
			'interval'=>60,
			'display'=>'Every 1 minute'
		);

		// Every other minute.
		$schedules['twominutes'] = array(
			'interval'=>120,
			'display'=>'Every 2 minutes'
		);

		// Every five minutes.
		$schedules['fiveminutes'] = array(
			'interval'=>300,
			'display'=>'Every 5 minutes'
		);

		// Every ten minutes.
		$schedules['tenminutes'] = array(
			'interval'=>600,
			'display'=>'Every 10 minutes'
		);

		// Ever half hour.
		$schedules['halfhour'] = array(
			'interval'=>1800,
			'display'=>'Every 30 minutes'
		);

		return $schedules;
	}
	add_filter('cron_schedules', 'common_cron_schedules');
}

// --------------------------------------------------------------------- end automatic



// ---------------------------------------------------------------------
// Optional Overrides
// ---------------------------------------------------------------------

if (!function_exists('common_upload_mimes')) {
	/**
	 * Allow SVG/WebP Uploads
	 *
	 * @param array $existing_mimes MIMEs.
	 * @return array MIMEs.
	 */
	function common_upload_mimes ($existing_mimes=array()) {
		$existing_mimes['svg'] = 'image/svg+xml';
		$existing_mimes['webp'] = 'image/webp';
		return $existing_mimes;
	}
	add_filter('upload_mimes', 'common_upload_mimes');
}

if (!function_exists('common_svg_media_thumbnail')) {
	/**
	 * SVG Thumbnail Support in Media Library Grid
	 *
	 * @param array $response Response.
	 * @param object $attachment Attachment.
	 * @param array $meta Meta data.
	 * @return array Response.
	 */
	function common_svg_media_thumbnails($response, $attachment, $meta) {

		if (!is_array($response) || !isset($response['type'], $response['subtype'])) {
			return $response;
		}

		if (
			'image' === $response['type'] &&
			'svg+xml' === $response['subtype'] &&
			class_exists('SimpleXMLElement')
		) {
			try {
				$path = get_attached_file($attachment->ID);
				if (@file_exists($path)) {
					$svg = new SimpleXMLElement(@file_get_contents($path));
					$src = $response['url'];
					$width = (int) $svg['width'];
					$height = (int) $svg['height'];

					// Media gallery.
					$response['image'] = compact('src', 'width', 'height');
					$response['thumb'] = compact('src', 'width', 'height');

					// Media single.
					$response['sizes']['full'] = array(
						'height'=>$height,
						'width'=>$width,
						'url'=>$src,
						'orientation'=>$height > $width ? 'portrait' : 'landscape',
					);
				}
			} catch (Exception $e) {
				return $response;
			}
		}

		return $response;
	}
	add_filter('wp_prepare_attachment_for_js', 'common_svg_media_thumbnails', 10, 3);
}

if (!function_exists('common_disable_jquery_migrate')) {
	/**
	 * Disable jQuery Migrate
	 *
	 * Enable via wp-config:
	 * define('WP_DISABLE_JQUERY_MIGRATE', true);
	 *
	 * @param array $scripts Enqueued scripts.
	 * @return void Nothing.
	 */
	function common_disable_jquery_migrate(&$scripts) {
		// Keep migrate for admin and admin-adjacent pages.
		if (is_admin() || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'), true)) {
			return;
		}

		if (!isset($scripts->registered['jquery'])) {
			return;
		}

		if (false !== $index = array_search('jquery-migrate', $scripts->registered['jquery']->deps, true)) {
			unset($scripts->registered['jquery']->deps[$index]);
			$scripts->registered['jquery']->deps = array_values($scripts->registered['jquery']->deps);
		}
	}
	if (defined('WP_DISABLE_JQUERY_MIGRATE') && WP_DISABLE_JQUERY_MIGRATE) {
		add_action('wp_default_scripts', 'common_disable_jquery_migrate');
	}
}

if (!function_exists('common_disable_wp_emojicons')) {
	/**
	 * Disable WP-Emoji Scripts
	 *
	 * Enable via wp-config:
	 * define('WP_DISABLE_EMOJI', true);
	 *
	 * @return void Nothing.
	 */
	function common_disable_wp_emojicons() {
		// All actions related to emojis.
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'print_emoji_detection_script', 7 );
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');

		// Filter to remove TinyMCE emojis.
		add_filter('tiny_mce_plugins', 'common_disable_emojicons_tinymce');
	}
	if (defined('WP_DISABLE_EMOJI') && WP_DISABLE_EMOJI) {
		add_action('init', 'common_disable_wp_emojicons');
	}

	/**
	 * Disable WP-Emoji (TinyMCE)
	 *
	 * @param array $plugins Plugins.
	 * @return array Plugins.
	 */
	function common_disable_emojicons_tinymce($plugins) {
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		}

		return array();
	}
}

// --------------------------------------------------------------------- end optional


