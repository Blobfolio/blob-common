<?php
//---------------------------------------------------------------------
// IMAGE HELPERS
//---------------------------------------------------------------------
// various functions for managing images



namespace blobfolio\common;

class image {

	protected static $svg_ids = array();
	protected static $svg_classes = array();

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
			ref\cast::array($args);

			//strip_js is a deprecated alias for sanitize
			//pass its value on if sanitize isn't set
			if (isset($args['strip_js']) && !isset($args['sanitize'])) {
				$args['sanitize'] = $args['strip_js'];
			}

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
			$dom = dom::load_svg($svg);
			$svg = dom::save_svg($dom);
			if (!strlen($svg)) {
				return false;
			}

			//if this SVG is marked "passthrough", don't process it
			$passthrough_key = hash('crc32', json_encode($options));
			$dom = dom::load_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			if ($tmp->item(0)->hasAttribute('data-cleaned') && $tmp->item(0)->getAttribute('data-cleaned') === $passthrough_key) {
				if ('DATA_URI' === $output) {
					return 'data:image/svg+xml;base64,' . base64_encode($svg);
				}
				elseif ('HTML' === $output) {
					return $svg;
				}
				else {
					return false;
				}
			}

			//make sure SVGs have the current standard
			$dom = dom::load_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			foreach ($tmp as $t) {
				$t->setAttribute('version', '1.1');
				$t->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
			}
			$svg = dom::save_svg($dom);

			//let's get some early stripping done
			if ($options['strip_data']) {
				$svg = preg_replace('/(\sdata\-[a-z\d_\-]+\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_id']) {
				$svg = preg_replace('/(\sid\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_style']) {
				$svg = preg_replace('/(\s(style|class)\s*=\s*"[^"]*")/i', '', $svg);
			}

			//let's do the dom tasks in one swoop
			if ($options['strip_title'] || $options['strip_style']) {
				$dom = dom::load_svg($svg);

				if ($options['strip_style']) {
					dom::remove_nodes($dom->getElementsByTagName('style'));
				}
				if ($options['strip_title']) {
					dom::remove_nodes($dom->getElementsByTagName('title'));
				}

				$svg = dom::save_svg($dom);
			}

			if ($options['sanitize']) {
				ref\sanitize::svg($svg);
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
				$dom = dom::load_svg($svg);
				foreach (array('svg','pattern') as $tag) {
					$tmp = $dom->getElementsByTagName($tag);
					if ($tmp->length) {
						foreach ($tmp as $t) {
							$width = $t->hasAttribute('width') ? $t->getAttribute('width') : null;
							$height = $t->hasAttribute('height') ? $t->getAttribute('height') : null;
							$vb = $t->hasAttribute('viewBox') ? $t->getAttribute('viewBox') : null;

							//make sure width and height are numbers
							if (is_numeric($width) || preg_match('/^[\d\.]+px$/', $width)) {
								ref\cast::float($width);
								if ($width <= 0) {
									$width = null;
								}
							}
							else {
								$width = null;
							}

							if (is_numeric($width) || preg_match('/^[\d\.]+px$/', $width)) {
								ref\cast::float($height);
								if ($height <= 0) {
									$height = null;
								}
							}
							else {
								$height = null;
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
				$svg = dom::save_svg($dom);
			}

			//now styles
			if ($options['clean_styles'] || $options['namespace']) {
				$dom = dom::load_svg($svg);
				$svgs = $dom->getElementsByTagName('svg');
				if ($svgs->length) {
					foreach ($svgs as $s) {
						//add namespace
						if ($options['namespace']) {
							$s->setAttribute('xmlns:svg', 'http://www.w3.org/2000/svg');
						}

						//store a list of classes to rewrite, if any
						$classes_old = array();

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
									dom::remove_node($tmp->item(0));
								}
								$parent->appendChild($style);

								//now fix formatting
								$style = $s->getElementsByTagName('style')->item(0);

								//parse the styles
								$parsed = dom::parse_css($style->nodeValue);
								if (is_array($parsed) && count($parsed)) {
									$style_new = array();

									if ($options['rewrite_styles']) {
										//let's try to join identical rules
										$rules = array();

										foreach ($parsed as $p) {
											//if it is an @ rule, just throw it in wholesale
											if (false !== $p['@']) {
												$rules[$p['raw']] = array();
											}
											else {
												foreach ($p['rules'] as $rk=>$rv) {
													$r = "$rk:$rv";

													if (!isset($rules[$r])) {
														$rules[$r] = array();
													}

													$rules[$r] = array_merge($rules[$r], array_values($p['selectors']));
												}
											}
										}

										//clean up the rules a touch
										foreach ($rules as $rule=>$selectors) {
											$rules[$rule] = array_unique($rules[$rule]);
											sort($rules[$rule]);
										}

										//great, now build the output
										foreach ($rules as $rule=>$selectors) {
											//something like an @media, out of scope here
											if (!count($selectors)) {
												$style_new[] = $rule;
												continue;
											}

											//look for class selectors, ignoring complex chains
											$classes = array();
											foreach ($selectors as $k=>$selector) {
												//a valid class
												if (preg_match('/^\.[a-z\d_\-]+$/i', $selector)) {
													$classes[] = $selector;
													unset($selectors[$k]);
												}
												//a broken Adobe class
												//e.g. .\38 ab9678e-54ee-493d-b19f-2215c5549034
												else {
													$selector = str_replace('.\\3', '.', $selector);
													//fix weird adobe rules
													preg_match_all('/^\.([\d]) ([a-z\d\-]+)$/', $selector, $matches);
													if (count($matches[0])) {
														$classes[] = preg_replace('/\s/', '', $matches[0][0]);
														unset($selectors[$k]);
													}
												}
											}

											if (count($classes)) {
												$class_new = mb::strtolower('c' . data::random_string(4));
												while (in_array($class_new, static::$svg_classes)) {
													$class_new = mb::strtolower('c' . data::random_string(4));
												}
												$selectors[] = '.' . $class_new;

												//add this class to all affected nodes
												$nodes = dom::get_nodes_by_class($s, $classes);
												foreach ($nodes as $node) {
													$class = $node->getAttribute('class');
													$class .= " $class_new";
													$node->setAttribute('class', $class);
												}

												foreach ($classes as $class) {
													$classes_old[] = ltrim($class, '.');
												}
											}

											$style_new[] = implode(',', $selectors) . '{' . $rule . '}';
										}
									}//end cleanup/rewrite
									else {
										//just add the rules
										foreach ($parsed as $p) {
											$style_new[] = $p['raw'];
										}
									}

									//and save it
									$style->nodeValue = implode('', $style_new);
								}//parseable styles
							}//if styles
						}//clean styles

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

						//remove old classes, if applicable
						if (count($classes_old)) {
							sort($classes_old);
							$classes_old = array_unique($classes_old);

							$nodes = dom::get_nodes_by_class($s, $classes_old);
							foreach ($nodes as $node) {
								$classes = $node->getAttribute('class');
								ref\sanitize::whitespace($classes);
								$classes = explode(' ', $classes);
								$classes = array_unique($classes);
								$classes = array_diff($classes, $classes_old);
								$node->setAttribute('class', implode(' ', $classes));
							}
						}//rewriting
					}//each svg
				}//if svgs
				$svg = dom::save_svg($dom);
			}

			//get back to just the object
			$dom = dom::load_svg($svg);
			$svg = dom::save_svg($dom);
			if (!strlen($svg)) {
				return false;
			}

			//should we save the clean version?
			if ($options['save']) {
				//add our passthrough header
				$dom = dom::load_svg($svg);
				$tmp = $dom->getElementsByTagName('svg');
				$tmp->item(0)->setAttribute('data-cleaned', $passthrough_key);
				$svg = dom::save_svg($dom);

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

			if ('DATA_URI' === $output) {
				return 'data:image/svg+xml;base64,' . base64_encode($svg);
			}
			elseif ('HTML' === $output) {
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

			$dom = dom::load_svg($svg);
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
			if ('image/gif' === $type) {
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