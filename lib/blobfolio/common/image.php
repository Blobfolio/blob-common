<?php
//---------------------------------------------------------------------
// IMAGE HELPERS
//---------------------------------------------------------------------
// various functions for managing images



namespace blobfolio\common;

class image {

	protected static $svg_ids = array();

	//-------------------------------------------------
	// Get DomDocument SVG
	//
	// this is an internal function for parsing an SVG
	// string into a DomDocument object.
	//
	// @param code
	// @return dom
	protected static function get_domdocument_svg(string $svg='') {
		try {
			//first thing first, lowercase all tags
			$svg = preg_replace('/<svg/i', '<svg', $svg);
			$svg = preg_replace('/<\/svg>/i', '</svg>', $svg);

			//find the start and end tags so we can cut out miscellaneous garbage
			if (
				false === ($start = mb::strpos($svg, '<svg')) ||
				false === ($end = mb::strrpos($svg, '</svg>'))
			) {
				return false;
			}
			$svg = mb::substr($svg, $start, ($end - $start + 6));

			//parse and resave it
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->formatOutput = false;
			$dom->preserveWhiteSpace = false;
			$dom->loadXML(constants::SVG_HEADER . "\n{$svg}");

			return $dom;
		} catch (\Throwable $e) {
			return $dom = new \DOMDocument('1.0', 'UTF-8');
		}
	}

	//-------------------------------------------------
	// Get Nodes by Class
	//
	// this is an internal function to retrieve all
	// DomElement nodes containing a certain CSS class
	//
	// @param starter
	// @return nodes
	protected static function get_domdocument_nodes_by_class($parent, string $class='') {
		$nodes = array();
		try {
			if ($parent->childNodes && $parent->childNodes->length) {
				foreach ($parent->childNodes as $child) {
					if ($child->hasAttribute('class')) {
						$classes = $child->getAttribute('class');
						ref\sanitize::whitespace($classes);
						$classes = explode(' ', $classes);
						if (in_array($class, $classes)) {
							$nodes[] = $child;
						}
					}
					if ($child->childNodes && $child->childNodes->length) {
						$deep = static::get_domdocument_nodes_by_class($child, $class);
						foreach ($deep as $d) {
							$nodes[] = $d;
						}
					}
				}
			}
		} catch (\Throwable $e) {
			return $nodes;
		}

		return $nodes;
	}

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

			ref\mb::strtoupper($output);

			$svg = sanitize::whitespace(@file_get_contents($path));

			//bugs from old versions of Illustrator
			$svg = str_replace(
				array_keys(constants::SVG_ATTR_CORRECTIONS),
				array_values(constants::SVG_ATTR_CORRECTIONS),
				$svg
			);

			//options
			$options = data::parse_args($args, constants::SVG_CLEAN_OPTIONS);
			//some options imply or override others
			if ($options['strip_style']) {
				$options['clean_styles'] = false;
				$options['namespace'] = false;
				$options['rewrite_styles'] = false;
				$options['random_class'] = false;
			}
			if ($options['strip_id']) {
				$options['random_id'] = false;
			}
			if ($options['rewrite_styles']) {
				$options['clean_styles'] = true;
			}

			//do a quick pass through DomDoc to standardize formatting
			$dom = static::get_domdocument_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			if ($tmp->length) {
				$svg = $tmp->item(0)->ownerDocument->saveXML($tmp->item(0));
			}
			else {
				return false;
			}

			//if this SVG is marked "passthrough", don't process it
			$passthrough_key = hash('crc32', json_encode($options));
			$dom = static::get_domdocument_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			if ($tmp->item(0)->hasAttribute('data-cleaned') && $tmp->item(0)->getAttribute('data-cleaned') === $passthrough_key) {
				if ($output === 'DATA_URI') {
					return 'data:image/svg+xml;base64,' . base64_encode($svg);
				}
				elseif ($output === 'HTML') {
					return $svg;
				}
				else {
					return false;
				}
			}

			//make sure SVGs have the current standard
			$dom = static::get_domdocument_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			foreach ($tmp as $t) {
				$t->setAttribute('version', '1.1');
				$t->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
			}
			$svg = $dom->saveXML();

			//let's get some early stripping done
			if ($options['strip_data']) {
				$svg = preg_replace('/(\sdata\-[a-z\d_\-]+\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_id']) {
				$svg = preg_replace('/(\sid\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_js']) {
				$svg = preg_replace('/(\son[a-z\d_\-]+\s*=\s*"[^"]*")/i', '', $svg);
				$dom = static::get_domdocument_svg($svg);
				$tmp = $dom->getElementsByTagName('script');
				while ($tmp->length) {
					$tmp->item(0)->parentNode->removeChild($tmp->item(0));
				}
				$svg = $dom->saveXML();
			}
			if ($options['strip_style']) {
				$svg = preg_replace('/(\s(style|class)\s*=\s*"[^"]*")/i', '', $svg);
				$dom = static::get_domdocument_svg($svg);
				$tmp = $dom->getElementsByTagName('style');
				while ($tmp->length) {
					$tmp->item(0)->parentNode->removeChild($tmp->item(0));
				}
				$svg = $dom->saveXML();
			}
			if ($options['strip_title']) {
				$dom = static::get_domdocument_svg($svg);
				$tmp = $dom->getElementsByTagName('title');
				while ($tmp->length) {
					$tmp->item(0)->parentNode->removeChild($tmp->item(0));
				}
				$svg = $dom->saveXML();
			}

			//randomize IDs?
			if ($options['random_id']) {
				preg_match_all('/\sid\s*=\s*"([^"]*)"/i', $svg, $matches);
				if (count($matches[0])) {
					foreach ($matches[0] as $k=>$v) {
						$id_string = $v;
						$id_value = $matches[1][$k];
						$id_new = 's' . mb::strtolower(data::random_string(4));
						while (in_array($id_new, static::$svg_ids)) {
							$id_new = 's' . mb::strtolower(data::random_string(4));
						}
						static::$svg_ids[] = $id_new;

						//replace just the first occurrence
						$svg = preg_replace('/' . preg_quote($id_string, '/') . '/', ' id="' . $id_new . '"', $svg, 1);
					}
				}
			}

			//fix dimensions?
			if ($options['fix_dimensions']) {
				//before we dive in, fix existing viewbox values
				preg_match_all('/\sviewbox\s*=\s*"([^"]*)"/i', $svg, $matches);
				if (count($matches[0])) {
					foreach ($matches[0] as $k=>$v) {
						$vb_string = $v;
						$vb_value = $matches[1][$k];
						$vb_new = '';
						if (false !== strpos($vb_value, ',')) {
							$vb_new = explode(',', $vb_value);
						}
						else {
							$vb_new = explode(' ', $vb_value);
						}
						$vb_new = array_map('trim', $vb_new);
						$vb_new = array_filter($vb_new, 'strlen');
						$vb_new = array_filter($vb_new, 'is_numeric');

						//remove invalid entries entirely
						if (count($vb_new) !== 4) {
							$svg = preg_replace('/' . preg_quote($vb_string, '/') . '/', '', $svg, 1);
						}
						else {
							$vb_new = implode(' ', $vb_new);
							//update it
							if ($vb_new !== $vb_value) {
								$svg = preg_replace('/' . preg_quote($vb_string, '/') . '/', ' viewBox="' . $vb_new . '"', $svg, 1);
							}
						}
					}
				}

				//tags supporting viewBox, width, and height
				$dom = static::get_domdocument_svg($svg);
				foreach (array('svg','pattern') as $tag) {
					$tmp = $dom->getElementsByTagName($tag);
					if ($tmp->length) {
						foreach ($tmp as $t) {
							$width = $t->hasAttribute('width') ? $t->getAttribute('width') : null;
							$height = $t->hasAttribute('height') ? $t->getAttribute('height') : null;
							$vb = $t->hasAttribute('viewBox') ? $t->getAttribute('viewBox') : null;

							//make sure width and height are numbers
							if (!is_numeric($width) && !preg_match('/^[\d\.]+%$/', $width)) {
								ref\cast::float($width);
								if ($width <= 0) {
									$width = null;
								}
							}
							if (!is_numeric($height) && !preg_match('/^[\d\.]+%$/', $height)) {
								ref\cast::float($height);
								if ($height <= 0) {
									$height = null;
								}
							}

							//all there or none there? can't help it
							if (
								(!is_null($width) && !is_null($height) && !is_null($vb)) ||
								(is_null($width) && is_null($height) && is_null($vb))
							) {
								continue;
							}

							//width and height from viewbox
							if (!is_null($vb)) {
								$d = explode(' ', $vb);
								$t->setAttribute('width', $d[2]);
								$t->setAttribute('height', $d[3]);
							}
							//viewbox from width and height
							elseif (is_numeric($width) && is_numeric($height)) {
								$t->setAttribute('viewBox', "0 0 $width $height");
							}
						}
					}
				}
				$svg = $dom->saveXML();
			}

			//now styles
			if ($options['clean_styles'] || $options['namespace']) {
				$dom = static::get_domdocument_svg($svg);
				$svgs = $dom->getElementsByTagName('svg');
				if ($svgs->length) {
					foreach ($svgs as $s) {
						//add namespace
						if ($options['namespace']) {
							$s->setAttribute('xmlns:svg', 'http://www.w3.org/2000/svg');
						}

						//store a list of classes to rewrite, if any
						$rewrites = array();

						//cleaning styles?
						if ($options['clean_styles']) {

							//first, combine them
							$tmp = $s->getElementsByTagName('style');
							if ($tmp->length) {
								$parent = $tmp->item(0)->parentNode;
								$style = $dom->createElement('style');
								while ($tmp->length) {
									foreach ($tmp->item(0)->childNodes as $t) {
										$t->nodeValue .= ' ';
										$style->appendChild($t);
									}
									$tmp->item(0)->parentNode->removeChild($tmp->item(0));
								}
								$parent->appendChild($style);

								//now fix formatting
								$style = $s->getElementsByTagName('style')->item(0);

								$css = new \Sabberworm\CSS\Parser($style->nodeValue);
								$css_parsed = $css->parse();
								$css_format = \Sabberworm\CSS\OutputFormat::create()->setSpaceAfterRuleName('')->setSpaceBeforeOpeningBrace('')->setSpaceAfterSelectorSeparator('')->setSpaceBetweenRules("\n");
								$css = $css_parsed->render($css_format);

								//join identical rules
								if (false !== mb::strpos($css, "\n")) {
									$lines = explode("\n", $css);
									$rules = array();
									foreach ($lines as $k=>$v) {
										//ignore lines with @ rules
										if (false !== strpos($v, '@')) {
											continue;
										}

										//look for xxx{yyy} patterns
										preg_match_all('/^([^\{]+)\{([^\}]+)\}$/', $v, $matches);
										if (!count($matches[0])) {
											continue;
										}

										$rule = $matches[2][0];
										if (!isset($rules[$rule])) {
											$rules[$rule] = array();
										}

										$selectors = explode(',', $matches[1][0]);
										foreach ($selectors as $selector) {
											$selector = preg_replace('/\s/', '', $selector);
											$rules[$rule][] = $selector;
										}

										unset($lines[$k]);
									}

									//add our merged rules back to the output
									foreach ($rules as $rule=>$selectors) {
										$selectors = array_unique($selectors);

										//rewriting classes? this is a bit of a pain
										if ($options['rewrite_styles']) {
											$classes = array();
											foreach ($selectors as $k=>$selector) {
												if (preg_match('/^\.[a-z0-9_\-]+$/i', $selector)) {
													unset($selectors[$k]);
													$classes[] = mb::substr($selector, 1);
												}
											}

											if (count($classes)) {
												$class_new = 'c' . mb::strtolower(data::random_string(4));
												while (isset($rewrites[$class_new])) {
													$class_new = 'c' . mb::strtolower(data::random_string(4));
												}

												//only replace straight classes
												$rewrites[$class_new] = $classes;
												$selectors[] = ".$class_new";
											}
										}

										$lines[] = implode(',', $selectors) . '{' . $rule . '}';
									}
									$css = implode(' ', $lines);
								}
								$style->nodeValue = $css;
							}
						}

						//add namespaced style
						if ($options['namespace']) {
							$styles = $s->getElementsByTagName('style');
							if ($styles->length) {
								foreach ($styles as $style) {
									$parent = $style->parentNode;
									$svgstyle = $dom->createElement('svg:style');
									if ($style->childNodes->length) {
										foreach ($style->childNodes as $t) {
											$clone = $t->cloneNode(true);
											$svgstyle->appendChild($clone);
										}
									}
									$parent->appendChild($svgstyle);
								}
							}
						}

						//now fix our classes, if applicable
						if (count($rewrites)) {
							//first pass, add new classes
							foreach ($rewrites as $k=>$v) {
								foreach ($v as $v2) {
									$children = static::get_domdocument_nodes_by_class($s, $v2);
									if (count($children)) {
										foreach ($children as $child) {
											$classes = $child->getAttribute('class');
											$classes .= " $k";
											$child->setAttribute('class', $classes);
										}
									}
								}
							}

							//second pass, remove old classes
							foreach ($rewrites as $k=>$v) {
								foreach ($v as $v2) {
									$children = static::get_domdocument_nodes_by_class($s, $v2);
									if (count($children)) {
										foreach ($children as $child) {
											$classes = $child->getAttribute('class');
											ref\sanitize::whitespace($classes);
											$classes = explode(' ', $classes);
											unset($classes[array_search($v2, $classes)]);
											$child->setAttribute('class', implode(' ', $classes));
										}
									}
								}
							}
						}
					}
				}
				$svg = $dom->saveXML();
			}

			//get back to just the object
			$dom = static::get_domdocument_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			if ($tmp->length) {
				$tmp->item(0)->setAttribute('data-cleaned', $passthrough_key);
				$svg = $tmp->item(0)->ownerDocument->saveXML($tmp->item(0));
			}
			else {
				return false;
			}

			//should we save the clean version?
			if ($options['save']) {
				$path_old = $path . '.dirty.' . microtime(true);
				$num = 0;
				while (file_exists($path_old)) {
					$num++;
					$tmp = $path_old . "-$num";
					if (!file_exists($tmp)) {
						$path_old = $tmp;
					}
				}
				@rename($path, $path_old);
				@file_put_contents($path, constants::SVG_HEADER . "\n{$svg}");
			}

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
		ref\cast::string($svg, true);

		try {
			//$svg might be a string
			if (false === mb::strpos(mb::strtolower($svg), '<svg')) {
				//or a file path
				if (false === $svg = static::clean_svg($svg)) {
					return false;
				}
			}

			$dom = static::get_domdocument_svg($svg);
			$svgs = $dom->getElementsByTagName('svg');
			if (!$svgs->length) {
				return false;
			}

			$svg = $svgs->item(0);

			$width = $svg->hasAttribute('width') ? $svg->getAttribute('width') : null;
			$height = $svg->hasAttribute('height') ? $svg->getAttribute('height') : null;
			$vb = $svg->hasAttribute('viewBox') ? $svg->getAttribute('viewBox') : null;

			//make sure width and height are numbers
			if (!is_numeric($width) && !preg_match('/^[\d\.]+%$/', $width)) {
				ref\cast::float($width);
				if ($width <= 0) {
					$width = null;
				}
			}
			if (!is_numeric($height) && !preg_match('/^[\d\.]+%$/', $height)) {
				ref\cast::float($height);
				if ($height <= 0) {
					$height = null;
				}
			}

			//pull width and height from viewbox
			if ((is_null($width) || is_null($height)) && !is_null($vb)) {
				$vb = str_replace(',', ' ', $vb);
				$vb = explode(' ', $vb);
				$vb = array_map('trim', $vb);
				$vb = array_filter($vb, 'strlen');
				$vb = array_filter($vb, 'is_numeric');
				if (count($vb) === 4) {
					$width = cast::float($vb[2]);
					$height = cast::float($vb[3]);
				}
			}

			if (!is_null($width) && !is_null($height)) {
				return array('width'=>$width, 'height'=>$height);
			}

			return false;
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