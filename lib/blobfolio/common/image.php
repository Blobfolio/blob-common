<?php
/**
 * Image Helpers
 *
 * Functions for managing images.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class image {

	protected static $svg_ids = array();
	protected static $svg_classes = array();

	/**
	 * Clean SVG for Inline Embedding
	 *
	 * @param string $path SVG path.
	 * @param mixed $args Arguments.
	 * @param string $output Output format.
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
	 * @return string|bool Clean SVG code. False on failure.
	 */
	public static function clean_svg($path, $args=null, $output='HTML') {
		ref\cast::to_string($path, true);

		try {
			if (!is_file($path)) {
				return false;
			}

			ref\mb::strtoupper($output);

			$svg = file_get_contents($path);

			// Options.
			ref\cast::to_array($args);

			// The strip_js option is a deprecated alias of sanitize.
			if (isset($args['strip_js']) && !isset($args['sanitize'])) {
				$args['sanitize'] = $args['strip_js'];
			}

			$options = data::parse_args($args, constants::SVG_CLEAN_OPTIONS);
			// Some options imply or override others.
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

			// If this SVG is marked "passthrough", don't process it.
			$passthrough_key = hash('crc32', json_encode($options));
			if (mb::substr_count($svg, 'data-cleaned="' . $passthrough_key . '"')) {
				$svg = substr($svg, strpos($svg, '<svg'));
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

			// Do a quick pass through DomDoc to standardize formatting.
			$dom = dom::load_svg($svg);
			$svg = dom::save_svg($dom);
			if (!$svg) {
				return false;
			}

			// Make sure SVGs have the current standard.
			$dom = dom::load_svg($svg);
			$tmp = $dom->getElementsByTagName('svg');
			foreach ($tmp as $t) {
				$t->setAttribute('version', '1.1');
			}
			$svg = dom::save_svg($dom);

			// Let's get some early stripping done.
			if ($options['strip_data']) {
				$svg = preg_replace('/(\sdata\-[a-z\d_\-]+\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_id']) {
				$svg = preg_replace('/(\sid\s*=\s*"[^"]*")/i', '', $svg);
			}
			if ($options['strip_style']) {
				$svg = preg_replace('/(\s(style|class)\s*=\s*"[^"]*")/i', '', $svg);
			}

			// Let's do the dom tasks in one swoop.
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
				ref\sanitize::svg(
					$svg,
					$options['whitelist_tags'],
					$options['whitelist_attr'],
					$options['whitelist_protocols'],
					$options['whitelist_domains']
				);
			}

			// Randomize IDs?
			if ($options['random_id']) {
				preg_match_all('/\sid\s*=\s*"([^"]*)"/i', $svg, $matches);
				if (count($matches[0])) {
					foreach ($matches[0] as $k=>$v) {
						$id_string = $v;
						$id_value = $matches[1][$k];
						$id_new = 's' . mb::strtolower(data::random_string(4));
						while (in_array($id_new, static::$svg_ids, true)) {
							$id_new = 's' . mb::strtolower(data::random_string(4));
						}
						static::$svg_ids[] = $id_new;

						// Replace just the first occurrence.
						$svg = preg_replace('/' . preg_quote($id_string, '/') . '/', ' id="' . $id_new . '"', $svg, 1);
					}
				}
			}

			// Fix dimensions?
			if ($options['fix_dimensions']) {
				// Before we dive in, fix existing viewbox values.
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

						// Remove invalid entries entirely.
						if (count($vb_new) !== 4) {
							$svg = preg_replace('/' . preg_quote($vb_string, '/') . '/', '', $svg, 1);
						}
						else {
							$vb_new = implode(' ', $vb_new);
							// Update it.
							if ($vb_new !== $vb_value) {
								$svg = preg_replace('/' . preg_quote($vb_string, '/') . '/', ' viewBox="' . $vb_new . '"', $svg, 1);
							}
						}
					}
				}

				// Tags supporting viewBox, width, and height.
				$dom = dom::load_svg($svg);
				foreach (array('svg', 'pattern') as $tag) {
					$tmp = $dom->getElementsByTagName($tag);
					if ($tmp->length) {
						foreach ($tmp as $t) {
							$width = $t->hasAttribute('width') ? $t->getAttribute('width') : null;
							$height = $t->hasAttribute('height') ? $t->getAttribute('height') : null;
							$vb = $t->hasAttribute('viewBox') ? $t->getAttribute('viewBox') : null;

							// Make sure width and height are numbers.
							if (is_numeric($width) || preg_match('/^[\d\.]+px$/', $width)) {
								ref\cast::to_float($width);
								if ($width <= 0) {
									$width = null;
								}
							}
							else {
								$width = null;
							}

							if (is_numeric($width) || preg_match('/^[\d\.]+px$/', $width)) {
								ref\cast::to_float($height);
								if ($height <= 0) {
									$height = null;
								}
							}
							else {
								$height = null;
							}

							// All there or none there? Can't help it.
							if (
								(!is_null($width) && !is_null($height) && !is_null($vb)) ||
								(is_null($width) && is_null($height) && is_null($vb))
							) {
								continue;
							}

							// Width and height from viewbox.
							if (!is_null($vb)) {
								$d = explode(' ', $vb);
								$t->setAttribute('width', $d[2]);
								$t->setAttribute('height', $d[3]);
							}
							// Viewbox from width and height.
							elseif (is_numeric($width) && is_numeric($height)) {
								$t->setAttribute('viewBox', "0 0 $width $height");
							}
						}
					}
				}
				$svg = dom::save_svg($dom);
			}

			// Now styles.
			if ($options['clean_styles'] || $options['namespace']) {
				$dom = dom::load_svg($svg);
				$svgs = $dom->getElementsByTagName('svg');
				if ($svgs->length) {
					foreach ($svgs as $s) {
						// Add namespace.
						if ($options['namespace']) {
							$s->setAttribute('xmlns:svg', 'http://www.w3.org/2000/svg');
						}

						// Store a list of classes to rewrite, if any.
						$classes_old = array();

						// Cleaning styles?
						if ($options['clean_styles']) {

							// First, combine them.
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

								// Now fix formatting.
								$style = $s->getElementsByTagName('style')->item(0);

								// Parse the styles.
								$parsed = dom::parse_css($style->nodeValue);
								if (is_array($parsed) && count($parsed)) {
									$style_new = array();

									if ($options['rewrite_styles']) {
										// Let's try to join identical rules.
										$rules = array();

										foreach ($parsed as $p) {
											// If it is an @ rule, just throw it in wholesale.
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

										// Clean up the rules a touch.
										foreach ($rules as $rule=>$selectors) {
											$rules[$rule] = array_unique($rules[$rule]);
											sort($rules[$rule]);
										}

										// Great, now build the output.
										foreach ($rules as $rule=>$selectors) {
											// Something like an @media, out of scope here.
											if (!count($selectors)) {
												$style_new[] = $rule;
												continue;
											}

											// Look for class selectors, ignoring complex chains.
											$classes = array();
											foreach ($selectors as $k=>$selector) {
												// A valid class.
												if (preg_match('/^\.[a-z\d_\-]+$/i', $selector)) {
													$classes[] = $selector;
													unset($selectors[$k]);
												}
												// A broken Adobe class,
												// e.g. .\38 ab9678e-54ee-493d-b19f-2215c5549034.
												else {
													$selector = str_replace('.\\3', '.', $selector);
													// Fix weird adobe rules.
													preg_match_all('/^\.([\d]) ([a-z\d\-]+)$/', $selector, $matches);
													if (count($matches[0])) {
														$classes[] = preg_replace('/\s/', '', $matches[0][0]);
														unset($selectors[$k]);
													}
												}
											}

											if (count($classes)) {
												$class_new = mb::strtolower('c' . data::random_string(4));
												while (in_array($class_new, static::$svg_classes, true)) {
													$class_new = mb::strtolower('c' . data::random_string(4));
												}
												$selectors[] = '.' . $class_new;

												// Add this class to all affected nodes.
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
									}// End cleanup/rewrite.
									else {
										// Just add the rules.
										foreach ($parsed as $p) {
											$style_new[] = $p['raw'];
										}
									}

									// And save it!
									$style->nodeValue = implode('', $style_new);
								}// Parseable styles.
							}// If styles.
						}// Clean styles.

						// Add namespaced style.
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

						// Remove old classes, if applicable.
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
						}// Rewriting.
					}// Each SVG.
				}// If SVGs.
				$svg = dom::save_svg($dom);
			}

			// Get back to just the string.
			$dom = dom::load_svg($svg);
			$svg = dom::save_svg($dom);
			if (!$svg) {
				return false;
			}

			// Should we save the clean version?
			if ($options['save']) {
				// Add our passthrough header.
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
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Check Probable WebP Support
	 *
	 * This attempts to check whether the required
	 * WebP binaries exist and are accessible to PHP.
	 *
	 * @param string $cwebp Path to cwebp.
	 * @param string $gif2webp Path to gif2webp.
	 * @return bool True/false.
	 */
	public static function has_webp($cwebp=null, $gif2webp=null) {
		try {
			if (is_null($cwebp)) {
				$cwebp = constants::CWEBP;
			}
			else {
				ref\cast::to_string($cwebp, true);
			}
			if (is_null($gif2webp)) {
				$gif2webp = constants::GIF2WEBP;
			}
			else {
				ref\cast::to_string($gif2webp, true);
			}

			return (
				@file_exists($cwebp) &&
				@file_exists($gif2webp) &&
				@is_readable($cwebp) &&
				@is_readable($gif2webp)
			);
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Determine SVG Dimensions
	 *
	 * @param string $svg SVG content or file path.
	 * @return array|bool Dimensions or false.
	 */
	public static function svg_dimensions($svg) {
		ref\cast::to_string($svg, true);

		try {
			// $svg might be a string.
			if (false === strpos(strtolower($svg), '<svg')) {
				if (file_exists($svg)) {
					$svg = file_get_contents($svg);
				}
				else {
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

			// Make sure width and height are numbers.
			if (!is_numeric($width) && !preg_match('/^[\d\.]+%$/', $width)) {
				ref\cast::to_float($width);
				if ($width <= 0) {
					$width = null;
				}
			}
			if (!is_numeric($height) && !preg_match('/^[\d\.]+%$/', $height)) {
				ref\cast::to_float($height);
				if ($height <= 0) {
					$height = null;
				}
			}

			// Pull width and height from viewbox.
			if ((is_null($width) || is_null($height)) && !is_null($vb)) {
				$vb = str_replace(',', ' ', $vb);
				$vb = explode(' ', $vb);
				$vb = array_map('trim', $vb);
				$vb = array_filter($vb, 'strlen');
				$vb = array_filter($vb, 'is_numeric');
				if (count($vb) === 4) {
					$width = cast::to_float($vb[2]);
					$height = cast::to_float($vb[3]);
				}
			}

			if (!is_null($width) && !is_null($height)) {
				return array('width'=>$width, 'height'=>$height);
			}

			return false;
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Generate WebP From Source
	 *
	 * This uses system WebP binaries to generate
	 * a copy of a source file. It uses `proc()`
	 * instead of `exec()`.
	 *
	 * @param string $source Source file.
	 * @param string $out Output file.
	 * @param string $cwebp Path to cwebp.
	 * @param string $gif2webp Path to gif2webp.
	 * @param bool $refresh Recreate it.
	 * @return bool True/false.
	 */
	public static function to_webp($source, $out=null, $cwebp=null, $gif2webp=null, $refresh=false) {
		ref\cast::to_string($source, true);
		if (!is_null($out)) {
			ref\cast::to_string($out, true);
		}
		if (!is_null($cwebp)) {
			ref\cast::to_string($cwebp, true);
		}
		if (!is_null($gif2webp)) {
			ref\cast::to_string($gif2webp, true);
		}
		ref\cast::to_bool($refresh, true);

		if (false === $source = file::path($source, true)) {
			return false;
		}

		$info = mime::finfo($source);
		if (!preg_match('/^(jpe?g|png|gif)$/', $info['extension'])) {
			return false;
		}

		// Do we need to build an out file?
		if (is_null($out)) {
			$out = "{$info['dirname']}/{$info['filename']}.webp";
		}
		// Needs to have the right extension.
		elseif (!preg_match('/\.webp$/i', $out)) {
			return false;
		}
		else {
			$out = file::path($out, false);
		}

		// Already exists?
		if (!$refresh && file_exists($out)) {
			return true;
		}

		// Can't do it?
		if (is_null($cwebp)) {
			$cwebp = constants::CWEBP;
		}
		if (is_null($gif2webp)) {
			$gif2webp = constants::GIF2WEBP;
		}
		if (!static::has_webp($cwebp, $gif2webp)) {
			return false;
		}

		// Try to open the process.
		try {
			$tmp_dir = sys_get_temp_dir();
			$error_log = file::trailingslash($tmp_dir) . 'cwebp-error_' . microtime(true) . '.txt';

			// Proc setup.
			$descriptors = array(
				0=>array('pipe', 'w'), // STDOUT.
				1=>array('file', $error_log, 'a'), // STDERR.
			);
			$cwd = $tmp_dir;
			$pipes = array();

			$type = $info['mime'];
			if ('image/gif' === $type) {
				$cmd = escapeshellcmd($gif2webp) . ' -m 6 -quiet ' . escapeshellarg($source) . ' -o ' . escapeshellarg($out);
			}
			else {
				$cmd = escapeshellcmd($cwebp) . ' -mt -quiet -jpeg_like ' . escapeshellarg($source) . ' -o ' . escapeshellarg($out);
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
		} catch (\Exception $e) {
			return false;
		}

		return false;
	}

}


