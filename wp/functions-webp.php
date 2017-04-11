<?php
/**
 * WebP Functions
 *
 * WebP compression is a lot better than anything WordPress
 * uses on its own. Browser support isn't quite there, but
 * this plugin provides a number of functions for wrapping
 * WebP and JPG/GIF/PNG sources in <picture> elements,
 * which are supported. The browser will then pick the
 * first source it can handle for display.
 *
 * To enable WebP features, add the following to wp-config:
 * define('WP_WEBP_IMAGES', true);
 *
 * Note: WebP binaries must be installed and accessible to
 * WordPress or else the generation functions will fail
 * silently.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

// Binary paths.
if (!defined('WP_WEBP_CWEBP')) {
	define('WP_WEBP_CWEBP', \blobfolio\common\constants::CWEBP);
}
if (!defined('WP_WEBP_GIF2WEBP')) {
	define('WP_WEBP_GIF2WEBP', \blobfolio\common\constants::GIF2WEBP);
}

if (!function_exists('common_supports_webp')) {
	/**
	 * Is WebP Support Probable
	 *
	 * @return bool True/False.
	 */
	function common_supports_webp() {
		static $support;

		if (is_null($support)) {
			$support = \blobfolio\common\image::has_webp(WP_WEBP_CWEBP, WP_WEBP_GIF2WEBP);
		}

		return $support;
	}
}

if (!function_exists('common_webp_cleanup')) {
	/**
	 * WebP Cleanup
	 *
	 * Delete any WebP thumbnails when the source
	 * image is removed from the Media Library.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return bool True/False.
	 */
	function common_webp_cleanup($attachment_id) {
		if (false === $image = get_attached_file($attachment_id)) {
			return false;
		}
		$stub = pathinfo($image);
		$stub['filename'];
		$path = $stub['dirname'];

		// Find any matching WebPs.
		if (false !== ($dir = opendir($path))) {
			while (false !== ($thumb = readdir($dir))) {
				// Wrong base.
				if (common_substr($thumb, 0, common_strlen($stub['filename'])) !== $stub['filename']) {
					continue;
				}

				$suffix = common_substr($thumb, common_strlen($stub['filename']));
				if (preg_match('/^(\-\d+x\d+)?\.webp$/', $suffix)) {
					$file = trailingslashit($path) . $thumb;
					@unlink($file);
				}
			}
			closedir($dir);
		}

		return true;
	}
	if (common_supports_webp()) {
		add_action('delete_attachment', 'common_webp_cleanup', 10, 1);
	}
}

if (!function_exists('common_get_webp_src')) {
	/**
	 * Generate <picture> Element Using Single
	 * Source (and WebP Sister)
	 *
	 * @param mixed $args Arguments.
	 *
	 * @arg int $attachment_id Attachment ID.
	 * @arg string $size Size.
	 * @arg string $alt Alternative text.
	 * @arg array $classes Class(es).
	 *
	 * @return string|bool HTML or false.
	 */
	function common_get_webp_src($args) {

		$defaults = array(
			'attachment_id'=>0,
			'size'=>'full',
			'alt'=>get_bloginfo('name'),
			'classes'=>array()
		);
		$data = common_parse_args($args, $defaults, true);

		// Sanitize.
		if ($data['attachment_id'] < 1) {
			return false;
		}

		// Sanitize classes.
		\blobfolio\common\ref\sanitize::whitespace($data['classes']);
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		// All right, let's get started!
		$sources = array();
		if (false === ($image = wp_get_attachment_image_src($data['attachment_id'], $data['size']))) {
			return false;
		}

		// Add in WebP.
		if (common_supports_webp() && false !== ($w = common_get_webp_sister($image[0]))) {
			$sources[] = '<source type="image/webp" srcset="' . esc_attr($w) . '" />';
		}

		// Add in regular image.
		$sources[] = '<img src="' . esc_attr($image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $image[1] . '" height="' . $image[2] . '" />';

		// Wrap in a picture and we're done.
		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . implode('', $sources) . '</picture>';
	}

	/**
	 * Shortcode for above. Same arguments.
	 *
	 * @param mixed $args Arguments.
	 * @param string $content Content.
	 * @return string HTML.
	 */
	function common_shortcode_webp_src($args=null, $content='') {
		\blobfolio\common\ref\cast::to_array($args);

		// Classes is going somewhere else.
		$classes = '';
		if (isset($args['classes'])) {
			$classes = $args['classes'];
			unset($args['classes']);
		}

		$shortcode = '[common-webp-caption classes="' . esc_attr($classes) . '" caption="' . esc_attr($content) . '"]' . common_get_webp_src($args) . '[/common-webp-caption]';
		return do_shortcode($shortcode);
	}
	add_shortcode('common-webp-src', 'common_shortcode_webp_src');
}

if (!function_exists('common_get_webp_srcset')) {
	/**
	 * Generate <picture> Element Using SRCSET
	 * Sources (and WebP Sisters)
	 *
	 * @param mixed $args Arguments.
	 *
	 * @arg int $attachment_id Attachment ID.
	 * @arg string|array $size Size(s).
	 * @arg array $sizes Sizes (i.e. HTML sizes attribute).
	 * @arg string $alt Alternative text.
	 * @arg array $classes Class(es).
	 * @arg string $default_size Src to use as fallback.
	 *
	 * @return string|bool HTML or false.
	 */
	function common_get_webp_srcset($args) {
		// Preparse sizes.
		if (isset($args['sizes']) && is_string($args['sizes'])) {
			$args['sizes'] = explode(',', $args['sizes']);
		}

		$defaults = array(
			'attachment_id'=>0,
			'size'=>array('full'),
			'sizes'=>array(),
			'alt'=>get_bloginfo('name'),
			'classes'=>array(),
			'default_size'=>null,
		);
		$data = common_parse_args($args, $defaults, true);

		// Sanitize.
		if ($data['attachment_id'] < 1) {
			return false;
		}

		// Sanitize classes.
		\blobfolio\common\ref\sanitize::whitespace($data['classes']);
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		// And sizes.
		\blobfolio\common\ref\sanitize::whitespace($data['sizes']);
		$data['sizes'] = array_filter($data['sizes'], 'strlen');
		if (!count($data['sizes'])) {
			$data['sizes'] = array('100vw');
		}

		// All right, let's get started!
		$sources = array();
		$source = '<source type="%s" srcset="%s" sizes="%s" />';

		// Sort out our srcset size(s).
		\blobfolio\common\ref\sanitize::whitespace($data['size']);
		$data['size'] = array_unique($data['size']);
		$data['size'] = array_filter($data['size'], 'strlen');
		$data['size'] = array_values($data['size']);
		if (!count($data['size'])) {
			$data['size'][] = 'full';
		}

		// Can't do it.
		if (false === $srcset = common_get_image_srcset($data['attachment_id'], $data['size'])) {
			return false;
		}

		// Convert srcset to an array.
		$srcset = explode(',', $srcset);
		\blobfolio\common\ref\sanitize::whitespace($srcset);

		// Our default default image.
		if (false === $image = wp_get_attachment_image_src($data['attachment_id'], $data['size'][0])) {
			// Have to wing it.
			$image = explode(' ', $srcset[0]);
			if (count($image) > 1) {
				$image[1] = preg_replace('/[^\d]/', '', $image[1]);
			}
			else {
				$image[1] = '';
			}
			$image[2] = '';
		}

		// Is a specific default image preferred?
		if (is_null($data['default_size']) || false === ($default_image = wp_get_attachment_image_src($data['attachment_id'], $data['default_size']))) {
			$default_image = $image;
		}

		// No srcset for GIFs.
		$type = common_get_mime_type($image[0]);
		if ('image/gif' === $type) {
			if (common_supports_webp() && false !== ($w = common_get_webp_sister($image[0]))) {
				$sources[] = sprintf($source, 'image/webp', esc_attr("$w {$image[1]}w"), '');
			}
		}
		// Try srcset.
		else {
			$source_normal = array();
			$source_webp = array();
			foreach ($srcset as $src) {
				$src = explode(' ', $src);
				$url = $src[0];
				$size = count($src) > 1 ? $src[1] : '';

				$path = common_get_path_by_url($url);
				if (file_exists($path)) {
					$source_normal[] = trim("$url $size");

					if (common_supports_webp() && false !== ($w = common_get_webp_sister($url))) {
						$source_webp[] = trim("$w $size");
					}
				}
			}

			if (count($source_webp)) {
				$sources[] = sprintf(
					$source,
					'image/webp',
					esc_attr(implode(', ', $source_webp)),
					esc_attr(implode(', ', $data['sizes']))
				);
			}
			if (count($source_normal)) {
				$sources[] = sprintf(
					$source,
					$type,
					esc_attr(implode(', ', $source_normal)),
					esc_attr(implode(', ', $data['sizes']))
				);
			}
		}

		// Add in regular image.
		$sources[] = '<img src="' . esc_attr($default_image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $default_image[1] . '" height="' . $default_image[2] . '" />';

		// Wrap in a picture and we're done.
		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . implode('', $sources) . '</picture>';
	}

	/**
	 * Shortcode for above. Same arguments.
	 *
	 * @param mixed $args Arguments.
	 * @param string $content Content.
	 * @return string HTML.
	 */
	function common_shortcode_webp_srcset($args=null, $content='') {
		// Explode arrayable fields by comma.
		foreach (array('size','sizes') as $field) {
			if (isset($args[$field])) {
				$args[$field] = explode(',', $args[$field]);
				$args[$field] = array_map('trim', $args[$field]);
			}
		}

		// Classes is going somewhere else.
		$classes = '';
		if (isset($args['classes'])) {
			$classes = $args['classes'];
			unset($args['classes']);
		}

		$shortcode = '[common-webp-caption classes="' . esc_attr($classes) . '" caption="' . esc_attr($content) . '"]' . common_get_webp_srcset($args) . '[/common-webp-caption]';
		return do_shortcode($shortcode);
	}
	add_shortcode('common-webp-srcset', 'common_shortcode_webp_srcset');
}

if (!function_exists('common_shortcode_webp_caption')) {
	/**
	 * Shortcode for WebP caption. Called automatically
	 * when the WebP shortcodes contain a caption.
	 *
	 * @param mixed $args Arguments.
	 * @param string $content Content.
	 *
	 * @arg string $classes Classes.
	 * @arg string $caption Caption.
	 *
	 * @return string HTML.
	 */
	function common_shortcode_webp_caption($args=null, $content='') {
		$defaults = array(
			'classes'=>'',
			'caption'=>''
		);
		$data = \blobfolio\common\data::parse_args($args, $defaults);
		$data['classes'] = explode(',', $data['classes']);
		\blobfolio\common\ref\sanitize::whitespace($data['classes']);
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		ob_start();
		?>
		<figure class="wp-caption <?=esc_attr(implode(' ', $data['classes']))?>">
			<?=$content?>
			<?php if (strlen($data['caption'])) { ?>
				<figcaption class="wp-caption-text"><?=$data['caption']?></figcaption>
			<?php } ?>
		</figure>
		<?php
		return ob_get_clean();
	}
	add_shortcode('common-webp-caption', 'common_shortcode_webp_caption');
}

if (!function_exists('common_get_webp_picture')) {
	/**
	 * Generate <picture> Element Using arbitrary
	 * Sources (and WebP Sisters)
	 *
	 * @param mixed $args Arguments.
	 *
	 * @arg int $attachment_id Attachment ID.
	 * @arg array $sources Sources.
	 * @arg string $alt Alternative text.
	 * @arg array $classes Class(es).
	 * @arg string $default_size Src to use as fallback.
	 *
	 * @return string|bool HTML or false.
	 */
	function common_get_webp_picture($args) {
		$defaults = array(
			'attachment_id'=>0,
			'sources'=>array(),
			'alt'=>get_bloginfo('name'),
			'classes'=>array(),
			'default_size'=>'full'
		);
		$data = common_parse_args($args, $defaults, true);

		$source_defaults = array(
			'attachment_id'=>0,
			'size'=>array('full'),
			'sizes'=>array(),
			'media'=>''
		);

		// Before we get too into it, let's make sure the default exists.
		if (false === $default_image = wp_get_attachment_image_src($data['attachment_id'], $data['default_size'])) {
			return false;
		}

		// Sanitize classes.
		\blobfolio\common\ref\sanitize::whitespace($data['classes']);
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		$sources = array();
		$source = '<source type="%s" srcset="%s" sizes="%s" media="%s" />';

		// Build and sanitize sources.
		foreach ($data['sources'] as $k=>$v) {
			$data['sources'][$k] = common_parse_args($v, $source_defaults, true);

			if ($data['sources'][$k]['attachment_id'] < 1) {
				$data['sources'][$k]['attachment_id'] = $data['attachment_id'];
			}

			// Sanitize sizes.
			\blobfolio\common\ref\sanitize::whitespace($data['sources'][$k]['sizes']);
			$data['sources'][$k]['sizes'] = array_filter($data['sources'][$k]['sizes'], 'strlen');
			if (!count($data['sources'][$k]['sizes'])) {
				$data['sources'][$k]['sizes'] = array('100vw');
			}

			// Sort out our srcset size(s).
			\blobfolio\common\ref\sanitize::whitespace($data['sources'][$k]['size']);
			$data['sources'][$k]['size'] = array_unique($data['sources'][$k]['size']);
			$data['sources'][$k]['size'] = array_filter($data['sources'][$k]['size'], 'strlen');
			$data['sources'][$k]['size'] = array_values($data['sources'][$k]['size']);
			if (!count($data['sources'][$k]['size'])) {
				$data['sources'][$k]['size'][] = 'full';
			}

			// Bad size(s).
			if (false === $srcset = common_get_image_srcset($data['sources'][$k]['attachment_id'], $data['sources'][$k]['size'])) {
				continue;
			}

			// Multiple sources.
			$srcset = explode(',', $srcset);
			\blobfolio\common\ref\sanitize::whitespace($srcset);
			usort($srcset, '_common_sort_srcset');

			$tmp_webp = array();
			$tmp_normal = array();
			$type_normal = null;
			foreach ($srcset as $src) {
				$src = explode(' ', $src);
				$url = $src[0];
				$size = count($src) > 1 ? $src[1] : '';

				$path = common_get_path_by_url($url);
				if (file_exists($path)) {
					$tmp_normal[] = trim("$url $size");
					if (is_null($type_normal)) {
						$type_normal = common_get_mime_type($url);
					}

					if (common_supports_webp() && false !== ($w = common_get_webp_sister($url))) {
						$tmp_webp[] = trim("$w $size");
					}
				}
			}

			if (count($tmp_webp)) {
				$sources[] = sprintf(
					$source,
					'image/webp',
					esc_attr(implode(', ', $tmp_webp)),
					esc_attr(implode(', ', $data['sources'][$k]['sizes'])),
					esc_attr($data['sources'][$k]['media'])
				);
			}

			if (count($tmp_normal)) {
				$sources[] = sprintf(
					$source,
					$type_normal,
					esc_attr(implode(', ', $tmp_normal)),
					esc_attr(implode(', ', $data['sources'][$k]['sizes'])),
					esc_attr($data['sources'][$k]['media'])
				);
			}
		}

		// Add a fallback img to the end.
		$sources[] = '<img src="' . esc_attr($default_image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $default_image[1] . '" height="' . $default_image[2] . '" />';

		$sources = implode('', $sources);
		$sources = str_replace(array('media=""', 'sizes=""'), '', $sources);

		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . $sources . '</picture>';
	}
}

if (!function_exists('common_generate_webp')) {
	/**
	 * Generate WebP From Source
	 *
	 * @param string $in Source file.
	 * @param string $out Output file.
	 * @return bool True/false.
	 */
	function common_generate_webp($in, $out) {
		return \blobfolio\common\image::to_webp($in, $out, WP_WEBP_CWEBP, WP_WEBP_GIF2WEBP);
	}
}

if (!function_exists('common_get_webp_sister')) {
	/**
	 * Does WebP Sister Exist
	 *
	 * @param string $path Source path.
	 * @return bool True/false.
	 */
	function common_get_webp_sister($path) {
		if (!common_supports_webp()) {
			return false;
		}

		$mode = 'url';
		if (common_substr($path, 0, common_strlen(ABSPATH)) === ABSPATH) {
			$mode = 'path';
		}

		if ('url' === $mode) {
			$path = common_get_path_by_url($path);
		}

		if (!file_exists($path)) {
			return false;
		}

		$bits = pathinfo($path);
		$webp = trailingslashit($bits['dirname']) . "{$bits['filename']}.webp";

		if (file_exists($webp) || common_generate_webp($path, $webp)) {
			return 'path' === $mode ? $webp : common_get_url_by_path($webp);
		}
		else {
			return false;
		}
	}
}


