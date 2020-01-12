<?php
/**
 * Blobfolio: Images
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use DOMXPath;



final class Images {
	// Random IDs and classes we've generated for SVGs.
	private static $_svg_ids = [];
	private static $_svg_classes = [];

	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Blank Image
	 *
	 * A simple transparent GIF that can be shoved into an SRC without
	 * causing troubles.
	 *
	 * @return string URI.
	 */
	public static function getBlankImage() {
		return "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=";
	}

	/**
	 * Nice SVG
	 *
	 * Sanitize an SVG's attributes, protocols, etc.
	 *
	 * @param string $svg SVG.
	 * @param int $flags Flags.
	 * @return string SVG.
	 */
	public static function niceSvg(string $svg, int $flags=0) : string {
		$trusted = !! ($flags & globals_get("flag_trusted"));
		if (!$trusted) {
			$svg = \Blobfolio\Strings::utf8($svg);
		}

		// This should validate.
		$dom = \Blobfolio\Dom::svgToDom($svg, globals_get("flag_trusted"));
		if (false === $dom) {
			return "";
		}

		$wAttr = (array) \Blobfolio\Dom::whitelistAttributes();
		$wIri = (array) \Blobfolio\Dom::iriAttributes();
		$wTags = (array) \Blobfolio\Dom::whitelistTags();
		$found = false;
		$x = 0;
		$y = 0;

		// Initialize XPath.
		$xpath = new DOMXPath($dom);

		// All tags.
		$tags = $dom->getElementsByTagName("*");
		$x = $tags->length - 1;
		while ($x >= 0) {
			$tag_name = strtolower($tags->item($x)->tagName);

			// The tag might be namespaced. We'll allow it if the tag
			// itself is allowed.
			if (
				(false !== strpos($tag_name, ":")) &&
				!isset($wTags[$tag_name])
			) {
				$tag_name = substr(strstr($tag_name, ":"), 1);
			}

			// Bad tag.
			if (!isset($wTags[$tag_name])) {
				\Blobfolio\Dom::removeNode($tags->item($x));
				$x--;
				continue;
			}

			// If this is a style tag, we have to decode its children
			// because XML wants us to fail. Haha.
			if ("style" === $tag_name) {
				$tmp = $tags->item($x);

				$tmp->textContent = \Blobfolio\Dom::attributeValue($tmp->textContent, globals_get("flag_trusted"));
				$tmp->textContent = \Blobfolio\Dom::decodeJsEntities($tmp->textContent);
				$tmp->textContent = strip_tags($tmp->textContent);
			}

			// Use XPath for attributes because DOMDocument will skip
			// anything with a namespace.
			$attr = $xpath->query(".//@*", $tags->item($x));
			$y = $attr->length - 1;
			while ($y >= 0) {
				$attr_name = strtolower($attr->item($y)->nodeName);

				// Could also be namespaced.
				if ((false !== strpos($attr_name, ":")) && !isset($wAttr[$attr_name])) {
					$attr_name = substr(strstr($attr_name, ":"), 1);
				}

				// Bad attribute.
				if ((0 !== strpos($attr_name, "data-")) && !isset($wAttr[$attr_name])) {
					$tags->item($x)->removeAttribute($attr->item($y)->nodeName);
					$y--;
					continue;
				}

				$attr_value = (string) \Blobfolio\Dom::attributeValue($attr->item($y)->value, globals_get("flag_trusted"));

				// Validate protocols.
				$found = false;
				if (isset($wIri[$attr_name])) {
					$found = true;
					$attr_value = \Blobfolio\Dom::iriValue($attr_value, globals_get("flag_trusted"));
				}
				// For the rest, we're specifically interested in scripty
				// things.
				elseif (preg_match("/(?:\w+script):/xi", $attr_value)) {
					$attr_value = "";
				}

				// Update it.
				if ($attr_value !== $attr->item($y)->value) {
					// Kill bad IRI values.
					if ($found) {
						$tags->item($x)->removeAttribute($attr->item($y)->nodeName);
					}
					else {
						$tags->item($x)->setAttribute(
							$attr->item($y)->nodeName,
							$attr_value
						);
					}
				}

				$y--;
			}

			$x--;
		}

		// Once more through tags to find namespaces.
		$tags = $dom->getElementsByTagName("*");
		$x = 0;
		while ($x < $tags->length) {
			$nodes = $xpath->query("namespace::*", $tags->item($x));
			$y = 0;
			while ($y < $nodes->length) {
				$node_name = (string) strtolower($nodes->item($y)->nodeName);

				// Not xmlns?
				if (0 !== strpos($node_name, "xmlns:")) {
					\Blobfolio\Dom::removeNamespace($dom, $nodes->item($y)->localName);
					$y++;
					continue;
				}

				// Validate values as the first pass would have missed
				// them.
				$node_value = (string) \Blobfolio\Dom::iriValue($nodes->item($y)->nodeValue, globals_get("flag_trusted"));

				// Remove empties.
				if (empty($node_value)) {
					\Blobfolio\Dom::removeNamespace($dom, $nodes->item($y)->localName);
				}
				// Update on change.
				elseif ($node_value !== $nodes->item($y)->nodeValue) {
					$tmp = $nodes->item($y);
					$tmp->nodeValue = $node_value;
				}

				$y++;
			}

			$x++;
		}

		// Let's get back to a string.
		$svg = (string) \Blobfolio\Dom::domToSvg($dom);
		if (empty($svg)) {
			return "";
		}

		// One more task: catch URLs in the CSS.
		$svg = preg_replace_callback(
			"/url\s*\((.*)\s*\)/Ui",
			[static::class, "niceSvgCallback"],
			$svg
		);

		return $svg;
	}

	/**
	 * Nice SVG Callback (CSS URLs)
	 *
	 * @param array $match Matches.
	 * @return string Replacement.
	 */
	private static function niceSvgCallback(array $match) : string {
		$str = (string) \Blobfolio\Strings::quotes($match[1], globals_get("flag_trusted"));
		$str = str_replace(["'", "\""], "", $str);
		$str = (string) \Blobfolio\Dom::iriValue($str, globals_get("flag_trusted"));

		if (empty($str)) {
			return "none";
		}

		return "url('" . $str . "')";
	}

	/**
	 * RGB to Brightness
	 *
	 * @param int $r Red.
	 * @param int $g Green.
	 * @param int $b Blue.
	 * @return float Brightness.
	 */
	public static function rgbToBrightness(int $r, int $g, int $b) : int {
		// Make sure everything is in range.
		if ($r > 255) {
			$r = 255;
		}
		if ($g > 255) {
			$g = 255;
		}
		if ($b > 255) {
			$b = 255;
		}

		return (int) ceil(sqrt(
			0.241 * $r * $r +
			0.691 * $g * $g +
			0.068 * $b * $b
		));
	}


	// -----------------------------------------------------------------
	// Dimensions
	// -----------------------------------------------------------------

	/**
	 * Wrapper for getimagesize()
	 *
	 * @param string $file File.
	 * @return array|bool Info or false.
	 */
	public static function size(string $file) {
		if (!is_file($file)) {
			return false;
		}

		// Make sure it is an image-like thing.
		$mime = (string) \Blobfolio\Files::getMimeType($file);
		if (0 !== strpos($mime, "image/")) {
			return false;
		}

		// If this is an SVG, steal results from our SVG size method.
		if ("image/svg+xml" === $mime) {
			$tmp = self::svgSize($file, globals_get("flag_trusted"));
			if (false === $tmp) {
				return false;
			}

			return [
				$tmp["width"],
				$tmp["height"],
				-1,
				sprintf(
					"width=\"%d\" height=\"%d\"",
					$tmp["width"],
					$tmp["height"]
				),
				"mime"=>"image/svg+xml"
			];
		}

		$info = getimagesize($file);
		if (false !== $info) {
			return $info;
		}

		// This shouldn't be needed, but just in case a local PHP is
		// wonky, we can calculate WebP dimensions manually.
		if ("image/webp" === $mime) {
			$tmp = self::webpSize($file, globals_get("flag_trusted"));
			if (false === $tmp) {
				return false;
			}

			return [
				$tmp["width"],
				$tmp["height"],
				18,
				sprintf(
					"width=\"%d\" height=\"%d\"",
					$tmp["width"],
					$tmp["height"]
				),
				"mime"=>"image/webp"
			];
		}

		return false;
	}

	/**
	 * Determine SVG Dimensions
	 *
	 * @param string $svg SVG content or file path.
	 * @param int $flags Flags.
	 * @return array|bool Dimensions or false.
	 */
	public static function svgSize(string $svg, int $flags=0) {
		$trusted = !! ($flags & globals_get("flag_trusted"));
		if (!$trusted) {
			$svg = \Blobfolio\Strings::utf8($svg);
		}

		// Make sure this is SVG-looking.
		$start = stripos($svg, "<svg");
		if (false === $start) {
			if (is_file($svg)) {
				$svg = file_get_contents($svg);
				$start = stripos($svg, "<svg");
				if (false === $start) {
					return false;
				}
			}
			else {
				return false;
			}
		}

		// Chop the code to the opening <svg> tag.
		if (0 !== $start) {
			$svg = substr($svg, $start);
		}
		$end = strpos($svg, '>');
		if (false === $end) {
			return false;
		}
		$svg = strtolower(substr($svg, 0, $end + 1));

		// Hold our values.
		$out = [
			"width"=>null,
			"height"=>null
		];
		$viewbox = null;

		// Search for width, height, and viewbox.
		$svg = \Blobfolio\Strings::whitespace($svg, 0, globals_get("flag_trusted"));
		preg_match_all(
			"/(height|width|viewbox)\s*=\s*([\"'])((?:(?!\2).)*)\2/",
			$svg,
			$match,
			PREG_SET_ORDER
		);

		if (("array" === gettype($match)) && count($match)) {
			foreach ($match as $v) {
				switch ($v[1]) {
					case "width":
					case "height":
						$v[3] = \Blobfolio\Cast::toFloat($v[3], globals_get("flag_flatten"));
						if ($v[3] > 0.0) {
							$out[$v[1]] = $v[3];
						}

						break;
					case "viewbox":
						// Defer processing for later.
						$viewbox = $v[3];
						break;
				}
			}
		}

		// If we have a width and height, we're done!
		if (!empty($out["width"]) && !empty($out["height"])) {
			return $out;
		}

		// Maybe pull from viewbox?
		if (!empty($viewbox)) {
			// Sometimes these are comma-separated.
			$viewbox = trim(str_replace(",", " ", $viewbox));
			$viewbox = explode(" ", $viewbox);

			foreach ($viewbox as $k=>$v) {
				$viewbox[$k] = \Blobfolio\Cast::toFloat($v, globals_get("flag_flatten"));
				if ($viewbox[$k] < 0.0) {
					$viewbox[$k] = 0.0;
				}
			}
			if ((count($viewbox) === 4) && $viewbox[2] > 0.0 && $viewbox[3] > 0.0) {
				$out["width"] = $viewbox[2];
				$out["height"] = $viewbox[3];
				return $out;
			}
		}

		return false;
	}

	/**
	 * WebP Size
	 *
	 * @param string $webp WebP file path.
	 * @param int $flags Flags.
	 * @return array|bool Dimensions or false.
	 */
	public static function webpSize(string $webp, int $flags=0) {
		if (!is_file($webp)) {
			return false;
		}

		$trusted = !! ($flags & globals_get("flag_trusted"));
		if (!$trusted) {
			$mime = (string) \Blobfolio\Files::getMimeType($webp);
			if ("image/webp" !== $mime) {
				return false;
			}
		}

		$handle = fopen($webp, "rb");
		if (false !== $handle) {
			$magic = fread($handle, 40);
			fclose($handle);

			// We should have 40 bytes of goods.
			if (strlen($magic) < 40) {
				return false;
			}

			$width = 0;
			$height = 0;
			$parts = [];

			switch(substr($magic, 12, 4)) {
				// Lossy WebP.
				case "VP8 ":
					$parts = (array) unpack("v2", substr($magic, 26, 4));
					$width = (intval($parts[1]) & 0x3FFF);
					$height = (intval($parts[2]) & 0x3FFF);
					break;
				// Lossless WebP.
				case "VP8L":
					$parts = (array) unpack("C4", substr($magic, 21, 4));
					$width = (intval($parts[1]) | ((intval($parts[2]) & 0x3F) << 8)) + 1;
					$height = (((intval($parts[2]) & 0xC0) >> 6) | (intval($parts[3]) << 2) | ((intval($parts[4]) & 0x03) << 10)) + 1;
					break;
				// Animated/Alpha WebP.
				case "VP8X":
					// Pad 24-bit int.
					$parts = (array) unpack("V", substr($magic, 24, 3) . "\x00");
					$width = (intval($parts[1]) & 0xFFFFFF) + 1;

					// Pad 24-bit int.
					$parts = (array) unpack("V", substr($magic, 27, 3) . "\x00");
					$height = (intval($parts[1]) & 0xFFFFFF) + 1;
					break;
			}

			if ($width && $height) {
				return [
					"width"=>$width,
					"height"=>$height
				];
			}
		}

		return false;
	}

	// -----------------------------------------------------------------
	// End dimensions.



	// -----------------------------------------------------------------
	// Misc Helpers
	// -----------------------------------------------------------------

	/**
	 * Apparent Brightness
	 *
	 * This attempts to identify the apparent brightness of an image.
	 *
	 * @param string $str Image path.
	 * @param float $coverage Coverage.
	 * @return float Luminosity.
	 */
	public static function niceBrightness(string $file, float $coverage = 0.05) : int {
		// First things first, open a resource.
		$mime = (string) \Blobfolio\Files::getMimeType($file);
		switch ($mime) {
			case "image/jpeg":
				$source = imagecreatefromjpeg($file);
				break;
			case "image/png":
				$source = imagecreatefrompng($file);
				break;
			case "image/gif":
				$source = imagecreatefromgif($file);
				break;
			case "image/webp":
				$source = imagecreatefromwebp($file);
				break;
			default:
				return 0;
		}

		// Abort if we don't have a valid image resource.
		if (false === $source) {
			return 0;
		}

		// Make sure coverage is adequate.
		if ($coverage <= 0) {
			$coverage = 0.01;
		}
		elseif ($coverage >= 1.0) {
			$coverage = 1.0;
		}

		// The image dimensions.
		$width = (int) imagesx($source);
		$height = (int) imagesy($source);

		// Calculate
		$step = floor(1 / $coverage);

		// Make sure the source is True Color.
		if (!imageistruecolor($source)) {
			imagepalettetotruecolor($source);
		}

		$indexes = [];
		$x = 0;
		$y = 0;
		$tick = -1;

		// Pixel by pixel, row by row.
		while ($y < $height) {
			$x = 0;
			while ($x < $width) {
				$tick++;

				// If we're at a coverage tick, grab the color index.
				if ($tick === $step) {
					$tick = -1;

					$tmp = imagecolorat($source, $x, $y);
					if (!empty($tmp)) {
						$index = (int) $tmp;

						if (!isset($indexes[$index])) {
							$indexes[$index] = 1;
						}
						else {
							$indexes[$index] = $indexes[$index] + 1;
						}
					}
				}

				$x++;
			}

			$y++;
		}

		// Find the RGB and brightness for each index.
		$brightness = 0;
		foreach ($indexes as $k=>$v) {
			$line = (int) self::rgbToBrightness(
				(($k >> 16) & 0xFF),
				(($k >> 8) & 0xFF),
				($k & 0xFF)
			);
			$brightness += ($line * $v);
		}

		// Clear the working image.
		imagedestroy($source);

		// Return the average.
		return (int) ceil($brightness / array_sum($indexes));
	}

	/**
	 * Is Dark?
	 *
	 * @param int $luminosity Luminosity.
	 * @param int $threshold Threshold.
	 * @return bool True/false.
	 */
	public static function isDark(int $luminosity, int $threshold=140) {
		return ($luminosity < $threshold);
	}

	/**
	 * Is Light?
	 *
	 * @param int $luminosity Luminosity.
	 * @param int $threshold Threshold.
	 * @return bool True/false.
	 */
	public static function isLight(int $luminosity, int $threshold=140) {
		return ($luminosity >= $threshold);
	}



	// -----------------------------------------------------------------
	// Conversion
	// -----------------------------------------------------------------

	/**
	 * Clean SVG for Inline Embedding
	 *
	 * @param string $path SVG path.
	 * @param int $flags Flags.
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
	 *
	 * @return string|bool Clean SVG code. False on failure.
	 */
	public static function cleanSvg(string $path, int $flags=64) : string {
		if (!is_file($path)) {
			return "";
		}

		$svg = file_get_contents($path);
		$start = stripos($svg, "<svg");
		if (false === $start) {
			return "";
		}
		elseif ($start > 0) {
			$svg = substr($svg, $start);
		}

		// Parse flags.
		$flagCleanStyles = !! ($flags & globals_get("flag_svg_clean_styles"));
		$flagFixDimensions = !! ($flags & globals_get("flag_svg_fix_dimensions"));
		$flagNamespace = !! ($flags & globals_get("flag_svg_namespace"));
		$flagRandomId = !! ($flags & globals_get("flag_svg_random_id"));
		$flagRewriteStyles = !! ($flags & globals_get("flag_svg_rewrite_styles"));
		$flagSanitize = !! ($flags & globals_get("flag_svg_sanitize"));
		$flagSave = !! ($flags & globals_get("flag_svg_save"));
		$flagStripData = !! ($flags & globals_get("flag_svg_strip_data"));
		$flagStripId = !! ($flags & globals_get("flag_svg_strip_id"));
		$flagStripStyle = !! ($flags & globals_get("flag_svg_strip_style"));
		$flagStripTitle = !! ($flags & globals_get("flag_svg_strip_title"));

		// Some options imply or override others.
		if ($flagStripStyle) {
			$flagCleanStyles = false;
			$flagNamespace = false;
			$flagRewriteStyles = false;
		}
		if ($flagStripId) {
			$flagRandomId = false;
		}
		if ($flagRewriteStyles) {
			$flagCleanStyles = true;
		}

		// Skip the hard stuff maybe.
		if (false !== strpos($svg, "data-cleaned=\"" . strval($flags) . "\"")) {
			return $svg;
		}

		// Convert this to a DOMDocument.
		$dom = \Blobfolio\Dom::svgToDom($svg);
		if (false === $dom) {
			return "";
		}

		$tags = $dom->getElementsByTagName("svg");
		if (!$tags->length) {
			return "";
		}

		// Make sure all SVGs are tagged with the right version.
		$x = 0;
		while ($x < $tags->length) {
			$tags->item($x)->setAttribute("version", "1.1");
			$x++;
		}

		// Strip title or style?
		if ($flagStripStyle) {
			\Blobfolio\Dom::removeNodes($dom->getElementsByTagName("style"));
		}
		if ($flagStripTitle) {
			\Blobfolio\Dom::removeNodes($dom->getElementsByTagName("title"));
		}

		// Temporarily convert the SVG back to a string.
		$svg = (string) \Blobfolio\Dom::domToSvg($dom);
		if (empty($svg)) {
			return "";
		}

		// Strip some more things.
		if ($flagStripData) {
			$svg = preg_replace(
				"/(\sdata\-[a-z\d_\-]+\s*=\s*\"[^\"]*\")/i",
				"",
				$svg
			);
		}
		if ($flagStripId) {
			$svg = preg_replace(
			"/(\sid\s*=\s*\"[^\"]*\")/i",
				"",
				$svg
			);
		}
		if ($flagStripStyle) {
			$svg = preg_replace(
				"/(\s(style|class)\s*=\s*\"[^\"]*\")/i",
				"",
				$svg
			);
		}

		if ($flagSanitize) {
			$svg = self::niceSvg($svg, globals_get("flag_trusted"));
		}

		// Randomize IDs?
		if ($flagRandomId) {
			preg_match_all("/\sid\s*=\s*\"([^\"]*)\"/i", $svg, $matches);
			if (count($matches[0])) {
				foreach ($matches[0] as $k=>$v) {
					$id_string = (string) $v;
					$id_value = (string) $matches[1][$k];
					$id_new = "s" . strtolower(\Blobfolio\Strings::random(4));
					while (isset(self::$_svg_ids[$id_new])) {
						$id_new = "s" . strtolower(\Blobfolio\Strings::random(4));
					}
					self::$_svg_ids[$id_new] = true;

					// Replace just the first occurrence.
					$svg = preg_replace(
						"/" . preg_quote($id_string, "/") . "/",
						" id=\"" . $id_new . "\"",
						$svg,
						1
					);
				}
			}
		}

		// Fix dimensions?
		if ($flagFixDimensions) {
			preg_match_all("/\sviewbox\s*=\s*\"([^\"]*)\"/i", $svg, $matches);
			if (("array" === gettype($matches)) && count($matches[0])) {
				foreach ($matches[0] as $k=>$v) {
					$vbPattern = "/" . preg_quote($v, "/") . "/";
					$vbValue = (string) $matches[1][$k];

					if (false !== strpos($vbValue, ",")) {
						$parts = (array) explode(",", $vbValue);
					}
					else {
						$parts = (array) explode(" ", $vbValue);
					}

					$parts = array_map("trim", $parts);
					$parts = array_filter($parts, "is_numeric");

					// Remove invalid entries.
					if (count($parts) !== 4) {
						$svg = preg_replace(
							$vbPattern,
							"",
							$svg,
							1
						);
					}
					else {
						$vbValue2 = (string) implode(" ", $parts);
						if ($vbValue !== $vbValue2) {
							$svg = preg_replace(
								$vbPattern,
								"viewBox=\"" . $vbValue2 . "\"",
								$svg,
								1
							);
						}
					}
				}
			}

			// Back to a DOM.
			$dom = \Blobfolio\Dom::svgToDom($svg, globals_get("flag_trusted"));
			foreach (["svg", "pattern"] as $v) {
				$tags = $dom->getElementsByTagName($v);
				if (!$tags->length) {
					continue;
				}

				$x = 0;
				while ($x < $tags->length) {
					$width = 0;
					$height = 0;
					$vb = "";

					if ($tags->item($x)->hasAttribute("width")) {
						$width = (int) preg_replace(
							"/[^\d.]/",
							"",
							$tags->item($x)->getAttribute("width")
						);
					}
					if ($tags->item($x)->hasAttribute("height")) {
						$height = (int) preg_replace(
							"/[^\d.]/",
							"",
							$tags->item($x)->getAttribute("height")
						);
					}
					if ($tags->item($x)->hasAttribute("viewBox")) {
						$vb = (string) $tags->item($x)->getAttribute("viewBox");
					}

					// Everything or nothing, we're done.
					if (
						($width && $height && !empty($vb)) ||
						(!$width && !$height && empty($vb))
					) {
						$x++;
						continue;
					}

					// Set width and height from VB.
					if (!empty($vb)) {
						$parts = (array) explode(" ", $vb);
						if (count($vb) === 4) {
							$tags->item($x)->setAttribute("width", $parts[2]);
							$tags->item($x)->setAttribute("height", $parts[3]);
						}
					}
					// Viewbox from width and height.
					elseif ($width && $height) {
						$tags->item($x)->setAttribute("viewBox", "0 0 " . strval($width) . " " . strval($height));
					}

					$x++;
				}
			}

			// Back to string.
			$svg = (string) \Blobfolio\Dom::domToSvg($dom);
			if (empty($svg)) {
				return "";
			}
		}

		// This is so big it is pushed off to another function. Haha.
		if ($flagCleanStyles) {
			$svg = (string) self::restyleSvg($svg, $flagRewriteStyles);
		}

		// Namespacing is a bitch.
		if ($flagNamespace) {
			$dom = \Blobfolio\Dom::svgToDom($svg, globals_get("flag_trusted"));
			$tags = $dom->getElementsByTagName("svg");
			if (!$tags->length) {
				return "";
			}

			$x = 0;
			while ($x < $tags->length) {
				// Add the namespace.
				$tags->item($x)->setAttribute("xmlns:svg", \Blobfolio\Dom::SVG_NAMESPACE);

				$y = 0;
				$z = 0;

				// Copy style tags to the new namespace.
				$styles = $tags->item($x)->getElementsByTagName("style");
				$y = 0;
				while (y < $styles->length) {
					$style = $dom->createElement("svg:style");
					$z = 0;
					while ($z < $styles->item($y)->childNodes->length) {
						$style->appendChild($styles->item($y)->childNodes->item($z)->cloneNode(true));
						$z++;
					}

					$styles->item($y)->parentNode->appendChild($style);
					$y++;
				}

				$x++;
			}

			$svg = (string) \Blobfolio\Dom::domToSvg($dom);
			if (empty($svg)) {
				return "";
			}
		}

		// Save it?
		if ($flagSave) {
			// Add/update our passthrough key.
			$dom = \Blobfolio\Dom::svgToDom($svg, globals_get("flag_trusted"));
			$tags = $dom->getElementsByTagName("svg");
			$tags->item(0)->setAttribute("data-cleaned", $flags);
			$svg = (string) \Blobfolio\Dom::domToSvg($dom);

			$path_old = $path . ".dirty." . microtime(true);
			$x = 0;
			while (file_exists($path_old)) {
				$x++;
				$path_old = $path . ".dirty." . microtime(true) . "-" . $x;
			}

			rename($path, $path_old);
			file_put_contents($path, \Blobfolio\Dom::SVG_HEADER . "\n" . $svg);
		}

		// Finally done!
		return $svg;
	}

	/**
	 * Restyle SVGs
	 *
	 * This code has been abstracted from cleanSvg due to its
	 * complexity.
	 *
	 * @param string $str SVG.
	 * @param bool $rewrite Rewrite.
	 * @return string SVG.
	 */
	private static function restyleSvg(string $svg, bool $rewrite=false) : string {
		// Start by merging styles. This is much easier to achieve with
		// string matching than DOM match.
		preg_match_all(
			"#<style[^>]*>(.*)</style>#iU",
			$svg,
			$styleMatch
		);

		// Early abort.
		if (!is_array($styleMatch) || !count($styleMatch[1])) {
			return $svg;
		}

		$styleMerged = "";
		foreach ($styleMatch[1] as $v) {
			$styleMerged .= " " . $v;
		}

		// We'll need our DOM at some point.
		$dom = \Blobfolio\Dom::svgToDom($svg, globals_get("flag_trusted"));
		if (false === $dom) {
			return "";
		}

		// Let's go ahead and nuke style tags. We don't need them any
		// more.
		$tags = $dom->getElementsByTagName("style");
		if ($tags->length) {
			\Blobfolio\Dom::removeNodes($tags);
		}

		// If no styles were parsed, let's quickly remove styles and get
		// on with life.
		$parsed = (array) \Blobfolio\Dom::parseCss($styleMerged);
		if (!count($parsed)) {
			return (string) \Blobfolio\Dom::domToSvg($dom);
		}

		$styleCleaned = [];
		$classesOld = [];

		// Life is so much easier if we aren't rewriting anything.
		if (!$rewrite) {
			foreach ($parsed as $v) {
				$styleCleaned[] = $v["raw"];
			}
		}
		// Otherwise we have to parse a ton of data. Haha.
		else {
			$rules = [];
			foreach ($parsed as $v) {
				// Copy @ rules wholesale.
				if (false !== $v["@"]) {
					$rules[$v["raw"]] = [];
				}
				else {
					foreach ($v["rules"] as $rk=>$rv) {
						$r = $rk . ":" . $rv;

						if (!isset($rules[$r])) {
							$rules[$r] = [];
						}

						$rules[$r] = array_merge(
							$rules[$r],
							array_values($v["selectors"])
						);
					}
				}
			} // End parsed.

			// Clean up and build output.
			foreach ($rules as $rule=>$selectors) {
				// Selectorless rules get added as is.
				if (!count($selectors)) {
					$styleCleaned[] = rule;
					continue;
				}

				// Clean up selectors a touch.
				$selectors = array_unique($selectors);
				sort($selectors);

				// Look for selectors.
				$classes = [];
				foreach ($selectors as $k=>$selector) {
					// A valid class.
					if (preg_match("/^\.[a-z\d_\-]+$/i", $selector)) {
						$classes[] = $selector;
						unset($selectors[$k]);
					}
					// A broken Adobe class,
					// e.g. .\38 ab9678e-54ee-493d-b19f-2215c5549034.
					else {
						$selector = str_replace(".\\3", ".", $selector);
						// Fix weird adobe rules.
						preg_match_all("/^\.([\d]) ([a-z\d\-]+)$/", $selector, $matches);
						if (count($matches[0])) {
							$classes[] = preg_replace("/\s/", "", $matches[0][0]);
							unset($selectors[$k]);
						}
					}
				} // Each selector.

				// We have classes!
				if (count($classes)) {
					$classNew = strtolower("c" . \Blobfolio\Strings::random(4));
					while (isset(self::$_svg_classes[$classNew])) {
						$classNew = strtolower("c" . \Blobfolio\Strings::random(4));
					}
					$selectors[] = "." . $classNew;

					// Add the class to all affected nodes.
					$nodes = \Blobfolio\Dom::getNodesByClass($dom, $classes);
					foreach ($nodes as $node) {
						$classValue = (string) $node->getAttribute("class");
						$classValue .= " " . $classNew;
						$node->setAttribute("class", $classValue);
					}

					// And note our old classes.
					foreach ($classes as $v) {
						$classesOld[] = ltrim($v, ".");
					}
				}

				$styleCleaned[] = implode(",", $selectors) . "{" . $rule . "}";
			} // Each rule.
		}

		// We should finally have styles!
		$styleMerged = (string) implode("", $styleCleaned);

		// We need to inject our new style tag.
		$defs = $dom->createElement("defs");
		$style = $dom->createElement("style");
		$style->nodeValue = $styleMerged;
		$defs->appendChild($style);

		// Add it.
		$dom->getElementsByTagName("svg")->item(0)->insertBefore(
			$defs,
			$dom->getElementsByTagName("svg")->item(0)->childNodes->item(0)
		);

		// We have to go through and remove old classes.
		if (count($classesOld)) {
			$classesOld = array_unique($classesOld);
			sort($classesOld);

			$nodes = \Blobfolio\Dom::getNodesByClass($dom, $classesOld);
			foreach ($nodes as $node) {
				$tmp = (string) $node->getAttribute("class");
				$tmp = (string) \Blobfolio\Strings::whitespace($tmp, 0, globals_get("flag_trusted"));
				$tmp = (array) explode(" ", $tmp);
				$tmp = array_unique($tmp);
				$tmp = array_diff($tmp, $classesOld);
				$node->setAttribute("class", implode(" ", $tmp));
			}
		}

		$svg = (string) \Blobfolio\Dom::domToSvg($dom);
		return $svg;
	}

	/**
	 * To WebP
	 *
	 * @param string $from From.
	 * @param string $to To.
	 * @param int $flags Flags.
	 * @return bool True/false.
	 */
	public static function toWebp(string $from, string $to="", int $flags=0) : bool {
		$from = \Blobfolio\Files::path($from, globals_get("flag_path_validate"));
		if (empty($from)) {
			return false;
		}

		// We can only convert JPEG, PNG, and GIF sources.
		$finfo = (array) \Blobfolio\Files::finfo($from);
		if (
			("image/jpeg" !== $finfo["mime"]) &&
			("image/gif" !== $finfo["mime"]) &&
			("image/png" !== $finfo["mime"])
		) {
			return false;
		}

		// Just make a sister file.
		if (empty($to)) {
			$to = $finfo["dirname"] . "/" . $finfo["filename"] . ".webp";
		}
		else {
			$to = \Blobfolio\Files::path($to);

			// If only a file name was passed, throw it in the dir.
			if (false === strpos($to, "/")) {
				$to = $finfo["dirname"] . "/" . $to;
			}
			if (".webp" !== substr(strtolower($to), -5)) {
				return false;
			}
		}

		// Early abort.
		$refresh = !! ($flags & globals_get("flag_refresh"));
		if (!$refresh && file_exists($to)) {
			return true;
		}

		// We need somewhere to shove errors.
		$tmp_dir = (string) sys_get_temp_dir();
		if (empty($tmp_dir) || !is_dir($tmp_dir)) {
			return false;
		}

		$error_log = (string) tempnam($tmp_dir, "webp");
		if (empty($error_log)) {
			return false;
		}

		// The WebP binaries are here.
		$bin_dir = \Blobfolio\Blobfolio::getDataDir() . "webp/bin/";

		// Gifs get their own binary.
		if ("image/gif" === $finfo["mime"]) {
			$cmd = (string) sprintf(
				"%s -m 6 -quiet %s -o %s",
				escapeshellcmd($bin_dir . "gif2webp"),
				escapeshellarg($from),
				escapeshellarg($to)
			);
		}
		else {
			$cmd = (string) sprintf(
				"%s -mt -quiet -jpeg_like %s -o %s",
				escapeshellcmd($bin_dir . "cwebp"),
				escapeshellarg($from),
				escapeshellarg($to)
			);
		}

		pclose(popen($cmd, "r"));

		// If the file didn't end up a thing, we're done.
		if (!file_exists($to)) {
			return false;
		}

		// Try to give it the same permissions as the original.
		$tmp = fileperms($from);
		if (false !== $tmp) {
			chmod($to, $tmp);
		}
		$tmp = fileowner($from);
		if (false !== $tmp) {
			chown($to, $tmp);
		}
		$tmp = filegroup($from);
		if (false !== $tmp) {
			chgrp($to, $tmp);
		}

		return true;
	}
}
