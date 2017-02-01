<?php
//---------------------------------------------------------------------
// FUNCTIONS: WEBP
//---------------------------------------------------------------------
// WebP compression is a lot better than anything WordPress comes up
// with on its own. Unfortunately browser support for this format is
// incomplete. These functions cause WordPress to generate WebP copies
// of any requested image. It also provides alternative SRC/SRCSET
// functions that can return both WebP and original sources (for
// e.g. <picture> elements) so your theme can serve lighter copies
// to people who can view them.
//
// To enable WebP features, add the following to wp-config:
// define('WP_WEBP_IMAGES', true);
//
// Note: WebP binaries must be installed on the server and accessible
// to WordPress or else these functions will essentially do nothing.
//
// See README for more information, gotchas, etc.



//binary paths
if (!defined('WP_WEBP_CWEBP')) {
	define('WP_WEBP_CWEBP', \blobfolio\common\constants::CWEBP);
}
if (!defined('WP_WEBP_GIF2WEBP')) {
	define('WP_WEBP_GIF2WEBP', \blobfolio\common\constants::GIF2WEBP);
}



//-------------------------------------------------
// Can the server do the WebP stuff?
//
// @param n/a
// @return true/false
if (!function_exists('common_supports_webp')) {
	function common_supports_webp() {
		static $support;

		if (is_null($support)) {
			$support = \blobfolio\common\image::has_webp(WP_WEBP_CWEBP, WP_WEBP_GIF2WEBP);
		}

		return $support;
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
// WebP Cleanup
//
// when an attachment image is deleted, delete the
// corresponding webp
//
// @param attachment_id
// @return true
if (!function_exists('common_webp_cleanup')) {
	function common_webp_cleanup($attachment_id) {
		if (false === $image = get_attached_file($attachment_id)) {
			return false;
		}
		$stub = pathinfo($image);
		$stub['filename'];
		$path = $stub['dirname'];

		//find any matching webps
		if (false !== ($dir = opendir($path))) {
			while (false !== ($thumb = readdir($dir))) {
				//wrong base
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

//-------------------------------------------------
// WebP src
//
// @param args
// @return picture or false
if (!function_exists('common_get_webp_src')) {
	function common_get_webp_src($args) {

		$defaults = array(
			'attachment_id'=>0,
			'size'=>'full',
			'alt'=>get_bloginfo('name'),
			'classes'=>array()
		);
		$data = common_parse_args($args, $defaults, true);

		//sanitize
		if ($data['attachment_id'] < 1) {
			return false;
		}

		//sanitize classes
		foreach ($data['classes'] as $k=>$v) {
			\blobfolio\common\ref\cast::string($data['classes'][$k]);
			\blobfolio\common\ref\sanitize::whitespace($data['classes'][$k]);
			if (false !== common_strpos($data['classes'][$k], ' ')) {
				$tmp = explode(' ', $data['classes'][$k]);
				foreach ($tmp as $t) {
					$data['classes'][] = $tmp;
				}
				unset($data['classes'][$k]);
			}
		}
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		//all right, let's get started!
		$sources = array();
		if (false === ($image = wp_get_attachment_image_src($data['attachment_id'], $data['size']))) {
			return false;
		}

		//add in webp
		if (common_supports_webp() && false !== ($w = common_get_webp_sister($image[0]))) {
			$sources[] = '<source type="image/webp" srcset="' . esc_attr($w) . '" />';
		}

		//add in regular image
		$sources[] = '<img src="' . esc_attr($image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $image[1] . '" height="' . $image[2] . '" />';

		//wrap in a picture and we're done
		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . implode('', $sources) . '</picture>';
	}
}

//-------------------------------------------------
// WebP srcset
//
// @param args
// @return picture or false
if (!function_exists('common_get_webp_srcset')) {
	function common_get_webp_srcset($args) {
		//preparse sizes
		if (isset($args['sizes']) && is_string($args['sizes'])) {
			$args['sizes'] = explode(',', $args['sizes']);
		}

		$defaults = array(
			'attachment_id'=>0,
			'size'=>'full',
			'sizes'=>array(),
			'alt'=>get_bloginfo('name'),
			'classes'=>array(),
			'default_size'=>null
		);
		$data = common_parse_args($args, $defaults, true);

		//sanitize
		if ($data['attachment_id'] < 1) {
			return false;
		}

		//sanitize classes
		foreach ($data['classes'] as $k=>$v) {
			\blobfolio\common\ref\cast::string($data['classes'][$k]);
			\blobfolio\common\ref\sanitize::whitespace($data['classes'][$k]);
			if (false !== common_strpos($data['classes'][$k], ' ')) {
				$tmp = explode(' ', $data['classes'][$k]);
				foreach ($tmp as $t) {
					$data['classes'][] = $tmp;
				}
				unset($data['classes'][$k]);
			}
		}
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		if (!count($data['sizes'])) {
			$data['sizes'] = array('100vw');
		}

		//all right, let's get started!
		$sources = array();
		$source = '<source type="%s" srcset="%s" sizes="%s" />';

		//make sure the main size is good
		if (false === ($image = wp_get_attachment_image_src($data['attachment_id'], $data['size']))) {
			return false;
		}
		//but is a different default preferred?
		if (is_null($data['default_size']) || false === ($default_image = wp_get_attachment_image_src($data['attachment_id'], $data['size']))) {
			$default_image = $image;
		}

		//no srcset for GIFs
		$type = common_get_mime_type($image[0]);
		if ($type === 'image/gif') {
			if (common_supports_webp() && false !== ($w = common_get_webp_sister($image[0]))) {
				$sources[] = sprintf($source, 'image/webp', esc_attr("$w {$image[1]}w"), '');
			}
		}
		//try srcset
		else {
			//if there is no srcset, let the src function handle it
			//not sure why WP's function just explodes when it could return *something*
			if (false === $srcset = wp_get_attachment_image_srcset($data['attachment_id'], $data['size'])) {
				return common_get_webp_src($data);
			}

			if (is_string($srcset) && common_strlen($srcset)) {
				$srcset = explode(',', $srcset);
				$srcset = array_map('common_sanitize_whitespace', $srcset);
				usort($srcset, '_common_sort_srcset');

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
		}

		//add in regular image
		$sources[] = '<img src="' . esc_attr($default_image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $default_image[1] . '" height="' . $default_image[2] . '" />';

		//wrap in a picture and we're done
		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . implode('', $sources) . '</picture>';
	}
}

//-------------------------------------------------
// Generate <picture>
//
// this allows for specifying different attachment
// sources at different breakpoints, versus the
// more automatic srcset version
//
// @param args(s)
// @return picture or false
if (!function_exists('common_get_webp_picture')) {
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
			'size'=>'full',
			'sizes'=>array(),
			'media'=>''
		);

		//before we get too into it, let's make sure the default exists
		if (false === $default_image = wp_get_attachment_image_src($data['attachment_id'], $data['default_size'])) {
			return false;
		}

		//sanitize classes
		foreach ($data['classes'] as $k=>$v) {
			\blobfolio\common\ref\cast::string($data['classes'][$k]);
			\blobfolio\common\ref\sanitize::whitespace($data['classes'][$k]);
			if (false !== common_strpos($data['classes'][$k], ' ')) {
				$tmp = explode(' ', $data['classes'][$k]);
				foreach ($tmp as $t) {
					$data['classes'][] = $tmp;
				}
				unset($data['classes'][$k]);
			}
		}
		$data['classes'] = array_unique($data['classes']);
		$data['classes'] = array_filter($data['classes'], 'strlen');

		$sources = array();
		$source = '<source type="%s" srcset="%s" sizes="%s" media="%s" />';

		//build and sanitize sources
		foreach ($data['sources'] as $k=>$v) {
			$data['sources'][$k] = common_parse_args($v, $source_defaults, true);

			if ($data['sources'][$k]['attachment_id'] < 1) {
				$data['sources'][$k]['attachment_id'] = $data['attachment_id'];
			}

			if (!count($data['sources'][$k]['sizes'])) {
				$data['sources'][$k]['sizes'] = array('100vw');
			}

			//multiple sources
			if (false !== $tmp = wp_get_attachment_image_srcset($data['sources'][$k]['attachment_id'], $data['sources'][$k]['size'])) {
				$srcset = explode(',', $tmp);
				$srcset = array_map('common_sanitize_whitespace', $srcset);
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
			//single source
			elseif (false !== $tmp = wp_get_attachment_image_src($data['sources'][$k]['attachment_id'], $data['sources'][$k]['size'])) {
				$url = common_array_pop_top($tmp);

				$path = common_get_path_by_url($url);
				if (file_exists($path)) {
					if (common_supports_webp() && false !== ($w = common_get_webp_sister($url))) {
						$sources[] = sprintf(
							$source,
							'image/webp',
							esc_attr($w),
							'',
							esc_attr($data['sources'][$k]['media'])
						);
					}
					$sources[] = sprintf(
						$source,
						$type_normal,
						esc_attr($url),
						'',
						esc_attr($data['sources'][$k]['media'])
					);
				}
			}
			else {
				continue;
			}
		}

		//add a fallback img to the end
		$sources[] = '<img src="' . esc_attr($default_image[0]) . '" alt="' . esc_attr($data['alt']) . '" width="' . $default_image[1] . '" height="' . $default_image[2] . '" />';

		$sources = implode('', $sources);
		$sources = str_replace(array('media=""', 'sizes=""'), '', $sources);

		return '<picture class="' . esc_attr(implode(' ', $data['classes'])) . '">' . $sources . '</picture>';
	}
}

//-------------------------------------------------
// Webp Generation
//
// @param path in
// @param path out
// @return true/false
if (!function_exists('common_generate_webp')) {
	function common_generate_webp($in, $out) {
		return \blobfolio\common\image::to_webp($in, $out, WP_WEBP_CWEBP, WP_WEBP_GIF2WEBP);
	}
}

//-------------------------------------------------
// Webp sister
//
// @param path or url
// @return sister or false
if (!function_exists('common_get_webp_sister')) {
	function common_get_webp_sister($path) {
		if (!common_supports_webp()) {
			return false;
		}

		$mode = 'url';
		if (common_substr($path, 0, common_strlen(ABSPATH)) === ABSPATH) {
			$mode = 'path';
		}

		if ($mode === 'url') {
			$path = common_get_path_by_url($path);
		}

		if (!file_exists($path)) {
			return false;
		}

		$bits = pathinfo($path);
		$webp = trailingslashit($bits['dirname']) . "{$bits['filename']}.webp";

		if (file_exists($webp) || common_generate_webp($path, $webp)) {
			return $mode === 'path' ? $webp : common_get_url_by_path($webp);
		}
		else {
			return false;
		}
	}
}

?>