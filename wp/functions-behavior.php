<?php
//---------------------------------------------------------------------
// FUNCTIONS: BEHAVIORAL OVERRIDES
//---------------------------------------------------------------------
// This file contains certain behavioral overrides that fix common
// annoyances with WordPress. Some of them are automatic, some of them
// are controlled via CONSTANTS or direct calls.

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



//---------------------------------------------------------------------
// MIME Fix
//---------------------------------------------------------------------

//-------------------------------------------------
// Fix Upload MIME detection
//
// this is an out-and-out bug in 4.7.1 - ..2, but
// in general could use some extra love
//
// @param checked [ext, type, proper_filename]
// @param file
// @param filename
// @param mimes
if (!function_exists('common_upload_real_mimes')) {
	function common_upload_real_mimes($checked, $file, $filename, $mimes) {

		//only worry if the first check failed
		if (!$checked['type'] || !$checked['ext']) {
			$finfo = \blobfolio\common\mime::finfo($file, $filename);

			//the time for checking certain image types has passed already
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
				//was the extension wrong?
				if (count($finfo['suggested_filename'])) {
					$filename = \blobfolio\common\data::array_pop_top($finfo['suggested_filename']);
				}

				//what does WP think?
				$wp_filetype = wp_check_filetype($filename, $mimes);
				if ($wp_filetype['ext'] && $wp_filetype['type']) {
					$checked['ext'] = $wp_filetype['ext'];
					$checked['type'] = $wp_filetype['type'];
					$checked['proper_filename'] = $filename;
				}
			}
		}

		//sanitize SVGs
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

//--------------------------------------------------------------------- end MIME fix



//---------------------------------------------------------------------
// Automatic Overrides
//---------------------------------------------------------------------

//do not include back/next links in meta
add_filter('previous_post_rel_link', '__return_false');
add_filter('next_post_rel_link', '__return_false');

//-------------------------------------------------
// Disable WP-Embed
//
// nobody uses this, I swear!
//
// @param n/a
// @return n/a
if (!function_exists('common_disable_wp_embed')) {
	function common_disable_wp_embed() {
		wp_deregister_script('wp-embed');
	}
	add_action('wp', 'common_disable_wp_embed');
}

//-------------------------------------------------
// Don't bubble selected terms to top on edit
// post pages (nobody likes this, haha)
//
// @param args
// @return args
if (!function_exists('common_disable_checked_to_top')) {
	function common_disable_checked_to_top($args) {
		$args['checked_ontop'] = false;
		return $args;
	}
	add_filter('wp_terms_checklist_args', 'common_disable_checked_to_top');
}

//-------------------------------------------------
// Extend WordPress CRON Scheduling
//
// you might need to manually trigger WP cron
// through the operating system's scheduler to
// faithfully hit these higher frequencies.
//
// wp-config.php:
// define('DISABLE_WP_CRON', true);
//
// crontab entry:
// * * * * * wget https://domain.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
//
// @param schedules
// @return schedules
if (!function_exists('common_cron_schedules')) {
	function common_cron_schedules($schedules) {

		//every minute
		$schedules['oneminute'] = array(
			'interval'=>60,
			'display'=>'Every 1 minute'
		);

		//every other minute
		$schedules['twominutes'] = array(
			'interval'=>120,
			'display'=>'Every 2 minutes'
		);

		//every five minutes
		$schedules['fiveminutes'] = array(
			'interval'=>300,
			'display'=>'Every 5 minutes'
		);

		//every ten minutes
		$schedules['tenminutes'] = array(
			'interval'=>600,
			'display'=>'Every 10 minutes'
		);

		//ever half hour
		$schedules['halfhour'] = array(
			'interval'=>1800,
			'display'=>'Every 30 minutes'
		);

		return $schedules;
	}
	add_filter('cron_schedules', 'common_cron_schedules');
}

//--------------------------------------------------------------------- end automatic



//---------------------------------------------------------------------
// Optional Overrides
//---------------------------------------------------------------------

//-------------------------------------------------
// Allow SVG and WebP Uploads
//
// @param image types
// @return image types
if (!function_exists('common_upload_mimes')) {
	function common_upload_mimes ($existing_mimes=array()) {
		$existing_mimes['svg'] = 'image/svg+xml';
		$existing_mimes['webp'] = 'image/webp';
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
if (!function_exists('common_svg_media_thumbnail')) {
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

					//media gallery
					$response['image'] = compact('src', 'width', 'height');
					$response['thumb'] = compact('src', 'width', 'height');

					//media single
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

//-------------------------------------------------
// Disable jQuery Migrate
//
// define('WP_DISABLE_JQUERY_MIGRATE', true);
//
// @param scripts
// @return n/a
if (!function_exists('common_disable_jquery_migrate')) {
	function common_disable_jquery_migrate(&$scripts) {
		//keep migrate for admin and admin-adjacent pages
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

//-------------------------------------------------
// Disable Emoji
//
// define('WP_DISABLE_EMOJI', true);
//
// @param n/a
// @return n/a
if (!function_exists('common_disable_wp_emojicons')) {
	function common_disable_wp_emojicons() {
		//all actions related to emojis
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'print_emoji_detection_script', 7 );
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');

		//filter to remove TinyMCE emojis
		add_filter('tiny_mce_plugins', 'common_disable_emojicons_tinymce');
	}
	if (defined('WP_DISABLE_EMOJI') && WP_DISABLE_EMOJI) {
		add_action('init', 'common_disable_wp_emojicons');
	}

	//and remove from TinyMCE
	function common_disable_emojicons_tinymce($plugins) {
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		}

		return array();
	}
}

//--------------------------------------------------------------------- end optional

?>