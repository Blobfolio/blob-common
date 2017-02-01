<?php
//---------------------------------------------------------------------
// IMAGE HELPERS
//---------------------------------------------------------------------
// various functions for managing images



namespace blobfolio\common;

class image {

	protected static $svg_ids = array();

	//-------------------------------------------------
	// Clean SVG
	//
	// @param path
	// @param args
	// @param output
	// @return svg or false
	public static function clean_svg(string $path, $args=null, string $output='HTML') {
		try {
			if (!is_file($path)) {
				return false;
			}

			$svg = sanitize::whitespace(@file_get_contents($path));

			//bugs from old versions of Illustrator
			$svg = str_replace(
				array_keys(constants::SVG_ATTR_CORRECTIONS),
				array_values(constants::SVG_ATTR_CORRECTIONS),
				$svg
			);

			//lowercase the tags
			$svg = preg_replace('/<svg/i', '<svg', $svg);
			$svg = preg_replace('/<\/svg>/i', '</svg>', $svg);

			//find the start and end of the tag
			if (
				false === ($start = mb::strpos($svg, '<svg')) ||
				false === ($end = mb::strpos($svg, '</svg>'))
			) {
				return false;
			}

			$svg = sanitize::whitespace(mb::substr($svg, $start, ($end - $start + 6)));

			//parse it
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$headers = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
			$dom->formatOutput = false;
			$dom->preserveWhiteSpace = false;
			$dom->loadXML("{$headers}\n{$svg}");

			//create svg: prefixed namespace, which can help isolate
			//properties from greedy scripts like Vue.js
			$svgs = $dom->getElementsByTagName('svg');
			if ($svgs->length) {
				foreach ($svgs as $s) {
					$s->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
					$s->setAttribute('svg:xmlns', 'http://www.w3.org/2000/svg');

					//look for styles. we'll merge them along the way
					//in case the file ended up with several
					$styles = $s->getElementsByTagName('style');
					if ($styles->length) {
						$parent = $styles->item(0)->parentNode;
						$svgstyle = $dom->createElement('svg:style');
						$style = $dom->createElement('style');
						while ($styles->length) {
							$tmp = $styles->item(0);
							//copy children
							if ($tmp->childNodes->length) {
								foreach ($tmp->childNodes as $t) {
									$t->nodeValue .= ' ';
									$clone = $t->cloneNode(true);
									$style->appendChild($clone);
									$svgstyle->appendChild($t);
								}
							}
							$tmp->parentNode->removeChild($tmp);
						}
						$parent->appendChild($style);
						$parent->appendChild($svgstyle);
					}
				}
			}

			$options = data::parse_args($args, constants::SVG_CLEAN_OPTIONS);

			//are we randomizing the id?
			if ($options['random_id']) {
				$svgs = $dom->getElementsByTagName('svg');
				if ($svgs->length) {
					foreach ($svgs as $v) {
						$id = 'svg-' . strtolower(data::random_string(5));
						while (in_array($id, static::$svg_ids)) {
							$id = 'svg-' . strtolower(data::random_string(5));
						}
						static::$svg_ids[] = $id;
						$v->setAttribute('id', $id);
					}
				}
			}

			//are we stripping the title?
			if ($options['strip_title']) {
				$titles = $dom->getElementsByTagName('title');
				while ($titles->length) {
					$tmp = $titles->item(0);
					$tmp->parentNode->removeChild($tmp);
				}
			}

			$svg = $dom->saveXML();

			//get back to just the object
			if (
				false === ($start = mb::strpos($svg, '<svg')) ||
				false === ($end = mb::strpos($svg, '</svg>'))
			) {
				return false;
			}

			$svg = sanitize::whitespace(mb::substr($svg, $start, ($end - $start + 6)));

			ref\mb::strtoupper($output);
			if ($output === 'DATA_URI') {
				return 'data:image/svg+xml;base64,' . base64_encode($svg);
			}
			elseif ($output === 'HTML') {
				return $svg;
			}
			else {
				return false;
			}
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// Supports WebP?
	//
	// @param n/a
	// @return true/false
	public static function has_webp(string $cwebp=null, string $gif2webp=null) {
		try {
			if (is_null($cwebp)) {
				$cwebp = constants::CWEBP;
			}
			if (is_null($gif2webp)) {
				$gif2webp = constants::GIF2WEBP;
			}

			return (
				@file_exists($cwebp) &&
				@file_exists($gif2webp) &&
				@is_readable($cwebp) &&
				@is_readable($gif2webp)
			);
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// SVG Dimensions
	//
	// @param svg
	// @return dimensions or false
	public static function svg_dimensions($svg) {
		ref\cast::string($svg);

		try {
			//is this a file path?
			if (false === mb::strpos(mb::strtolower($svg), '<svg')) {
				if (false === $svg = static::clean_svg($svg)) {
					return false;
				}
			}

			$out = array('width'=>0.0, 'height'=>0.0);
			$xml = simplexml_load_string($svg);
			$attr = $xml->attributes();
			$out['width'] = cast::number($attr->width);
			$out['height'] = cast::number($attr->height);

			if ($out['width'] <= 0 || $out['height'] <= 0) {
				return false;
			}

			return $out;
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// Generate WebP
	//
	// @param source
	// @param destination
	// @param refresh
	// @param cwebp
	// @param gif2webp
	// @param refresh
	// @return true/false
	public static function to_webp(string $source, string $out=null, string $cwebp=null, string $gif2webp=null, bool $refresh=false) {
		if (false === $source = file::path($source, true)) {
			return false;
		}

		$info = mime::finfo($source);
		if (!preg_match('/^(jpe?g|png|gif)$/', $info['extension'])) {
			return false;
		}

		//do we need to build an out file?
		if (is_null($out)) {
			$out = "{$info['dirname']}/{$info['filename']}.webp";
		}
		//needs to have the right extension
		elseif (!preg_match('/\.webp$/i', $out)) {
			return false;
		}
		else {
			$out = file::path($out, false);
		}

		//already exists?
		if (!$refresh && file_exists($out)) {
			return true;
		}

		//can't do it?
		if (is_null($cwebp)) {
			$cwebp = constants::CWEBP;
		}
		if (is_null($gif2webp)) {
			$gif2webp = constants::GIF2WEBP;
		}
		if (!static::has_webp($cwebp, $gif2webp)) {
			return false;
		}

		//try to open the process
		try {
			$tmp_dir = sys_get_temp_dir();
			$error_log = file::trailingslash($tmp_dir) . 'cwebp-error_' . microtime(true) . '.txt';

			//proc setup
			$descriptors = array(
				0=>array('pipe', 'w'), //stdout
				1=>array('file', $error_log, 'a') //stderr
			);
			$cwd = $tmp_dir;
			$pipes = array();

			$type = $info['mime'];
			if ($type === 'image/gif') {
				$cmd = escapeshellarg($gif2webp) . ' -m 6 -quiet ' . escapeshellarg($source) . ' -o ' . escapeshellarg($out);
			}
			else {
				$cmd = escapeshellarg($cwebp) . ' -mt -quiet -jpeg_like ' . escapeshellarg($source) . ' -o ' . escapeshellarg($out);
			}

			$process = proc_open(
				escapeshellcmd($cmd),
				$descriptors,
				$pipes,
				$cwd
			);

			if (is_resource($process)) {
				$life = stream_get_contents($pipes[0]);
				fclose($pipes[0]);
				$return_value = proc_close($process);

				if (file_exists($error_log)) {
					@unlink($error_log);
				}

				return file_exists($out);
			}
		} catch (\Throwable $e) {
			return false;
		}

		return false;
	}

}

?>