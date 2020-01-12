<?php
/**
 * Blobfolio: JSON
 *
 * Improved JSON support for PHP.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Json {
	/**
	 * JSON Decode
	 *
	 * A more robust version of JSON decode that can somewhat handle
	 * general Javascript objects. This always returns objecty things as
	 * associative arrays.
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function decode($str, bool $recursed=false) {
		// Copy str over to our typed variable.
		if (!$recursed) {
			$encoded = (string) \Blobfolio\Cast::toString($str, globals_get("flag_flatten"));
		}
		else {
			$encoded = (string) $str;
		}

		// Remove comments.
		$encoded = preg_replace("#(^\s*//(.+)$|^\s*/\*(.+)\*/|/\*(.+)\*/\s*$)#m", "", $encoded);

		// Trim it.
		$encoded = preg_replace("/(^\s+|\s+$)/u", "", $encoded);

		// Is it empty?
		if (empty($encoded) || ("''" === $encoded) || ("\"\"" === $encoded)) {
			return "";
		}

		// Maybe it just works?
		$tmp = json_decode($encoded, true);
		if (null !== $tmp) {
			return $tmp;
		}

		// A lot of the following tests are case-insensitive.
		$lower = (string) \Blobfolio\Strings::toLower($encoded, globals_get("flag_trusted"));

		// Bool.
		if (("true" === $lower) || ("false" === $lower)) {
			return \Blobfolio\Cast::toBool($encoded, globals_get("flag_flatten"));
		}
		// Null.
		elseif ("null" === $lower) {
			return null;
		}
		// Number.
		elseif (is_numeric($lower)) {
			if (false !== strpos($lower, ".")) {
				return \Blobfolio\Cast::toFloat($lower);
			}

			return \Blobfolio\Cast::toInt($lower);
		}
		// String.
		elseif (
			preg_match(
				"/^(\"|')(.+)(\1)$/s",
				$encoded,
				$match
			) &&
			count($match) >= 3 &&
			($match[1] === $match[3])
		) {
			$encoded = (string) $match[2];
			return \Blobfolio\Dom::decodeJsEntities($encoded);
		}
		// Bail if we don't have an object at this point.
		elseif (
			!preg_match("/^(\[.*\]|\{.*\})$/s", $encoded)
		) {
			return null;
		}

		// We have to parse it all manually. Ug.
		$out = [];
		$sliceLast = null;
		$slices = [];
		$apostrophe = 39;
		$asterisk = 42;
		$backslash = 92;
		$braceClose = 125;
		$braceOpen = 123;
		$bracketClose = 93;
		$bracketOpen = 91;
		$comma = 44;
		$quote = 22;
		$slash = 47;
		$x = 0;
		$y = 0;

		// Start building an array.
		$slices[] = ["type"=>"slice", "from"=>0, "delimiter"=>false];

		// Figure out what kind of wrapper we have.
		if (0 === strpos($encoded, "[")) {
			$sliceType = "array";
		}
		else {
			$sliceType = "object";
		}

		$encoded = (string) mb_substr($encoded, 1, -1, "UTF-8");

		// The length of our pie. Note: we are looping through ASCII
		// chars, so this is the non-MB size.
		$sliceLength = (int) strlen($encoded);

		while ($x <= $sliceLength) {
			// Fill out the current and future chars.
			if ($x < $sliceLength) {
				$slice = (string) ord($encoded[$x]);
				if ($x + 1 < $sliceLength) {
					$slice1 = (string) ord($encoded[$x + 1]);
				}
				else {
					$slice1 = 32;
				}
			}
			else {
				$slice = 32;
				$slice1 = 32;
			}

			// Fill out the previous chars.
			if ($x > 0) {
				$slice_1 = (string) ord($encoded[$x - 1]);
				if ($x > 1) {
					$slice_2 = (string) ord($encoded[$x - 2]);
				}
				else {
					$slice_2 = 32;
				}
			}
			else {
				$slice_1 = 32;
				$slice_2 = 32;
			}

			// What were we last up to?
			if (count($slices)) {
				$sliceLast = (array) end($slices);
				$hasSlice = true;
			}
			else {
				$sliceLast = ["delimiter"=>false];
				$hasSlice = false;
			}

			// Are we done?
			if (
				($x >= $sliceLength) ||
				(($comma === $slice) && $hasSlice && ("slice" === $sliceLast["type"]))
			) {
				$sliceCurrent = (string) trim(substr(
					$encoded,
					$sliceLast["from"],
					($x - $sliceLast["from"])
				));

				// Arrays are straight forward.
				if ("array" === $sliceType) {
					$out[] = self::decode($sliceCurrent, true);
				}
				// Objects can be much more annoying.
				else {
					// Tease apart the keys and values.
					if (
						(":" !== $sliceCurrent) &&
						(false !== strpos($sliceCurrent, ":"))
					) {
						$sliceKeyEnd = -1;
						$sliceValStart = -1;
						$sliceFirstChar = (string) ord($sliceCurrent[0]);
						$sliceIndex = 1;
						$stubQuoted = false;
						$stubK = "";
						$stubV = "";

						// The first key is quoted.
						if (($quote === $sliceFirstChar) || ($apostrophe === $sliceFirstChar)) {
							$stubQuoted = true;
							// A preg match would make more sense, but
							// there's something wrong with Zephir's
							// implementation.
							while ($sliceIndex > 0 && $sliceKeyEnd < 0) {
								$sliceIndex = (int) mb_strpos(
									$sliceCurrent,
									$sliceCurrent[0],
									$sliceIndex,
									"UTF-8"
								);

								if (
									$sliceIndex &&
									("\\" !== mb_substr($sliceCurrent, $sliceIndex - 1, 1, "UTF-8"))
								) {
									$sliceKeyEnd = $sliceIndex;
								}
							}

							if ($sliceKeyEnd) {
								$sliceIndex = (int) mb_strpos(
									$sliceCurrent,
									":",
									$sliceKeyEnd,
									"UTF-8"
								);
								if ($sliceIndex) {
									$sliceValStart = $sliceIndex + 1;
								}
							}
						}
						// Unquoted key.
						else {
							// This we can just split on the :.
							$sliceIndex = (int) mb_strpos(
								$sliceCurrent,
								":",
								0,
								"UTF-8"
							);
							$sliceKeyEnd = $sliceIndex - 1;
							$sliceValStart = $sliceIndex + 1;
						}

						// We found them!
						if ($sliceKeyEnd > 0 && $sliceValStart > 0) {
							$stubK = (string) trim(mb_substr(
								$sliceCurrent,
								0,
								$sliceKeyEnd + 1,
								"UTF-8"
							));

							$stubV = (string) trim(mb_substr(
								$sliceCurrent,
								$sliceValStart,
								null,
								"UTF-8"
							));

							$stubK = $stubQuoted ? self::decode($stubK, true) : \Blobfolio\Dom::decodeJsEntities($stubK);

							$out[$stubK] = self::decode($stubV, true);
						}
					}
				}

				array_pop($slices);
				$slices[] = ["type"=>"slice", "from"=>$x + 1, "delimiter"=>false];

				// Reboot.
				$x++;
				continue;
			}

			// Open: quote.
			if (
				(($quote === $slice) || ($apostrophe === $slice)) &&
				("string" !== $sliceLast["type"]) &&
				("comment" !== $sliceLast["type"])
			) {
				$slices[] = ["type"=>"string", "from"=>$x, "delimiter"=>(($quote === $slice) ? "\"" : "'")];
				$x++;
				continue;
			}

			// Close: quote.
			if (
				($slice === (string) ord($sliceLast["delimiter"])) &&
				("string" === $sliceLast["type"]) &&
				(($backslash !== $slice_1) && ($backslash !== $slice_2))
			) {
				array_pop($slices);
				$x++;
				continue;
			}

			// Open: bracket.
			if (
				($bracketOpen === $slice) &&
				(
					("slice" === $sliceLast["type"]) ||
					("array" === $sliceLast["type"]) ||
					("object" === $sliceLast["type"])
				)
			) {
				$slices[] = ["type"=>"array", "from"=>$x, "delimiter"=>false];
				$x++;
				continue;
			}

			// Close: bracket.
			if (($bracketClose === $slice) && ("array" === $sliceLast["type"])) {
				array_pop($slices);
				$x++;
				continue;
			}

			// Open: brace.
			if (
				($braceOpen === $slice) &&
				(
					("slice" === $sliceLast["type"]) ||
					("array" === $sliceLast["type"]) ||
					("object" === $sliceLast["type"])
				)
			) {
				$slices[] = ["type"=>"object", "from"=>$x, "delimiter"=>false];
				$x++;
				continue;
			}

			// Close: brace.
			if (($braceClose === $slice) && ("object" === $sliceLast["type"])) {
				array_pop($slices);
				$x++;
				continue;
			}

			// Open: comment.
			if (
				($slash === $slice) &&
				($asterisk === $slice1) &&
				(
					("slice" === $sliceLast["type"]) ||
					("array" === $sliceLast["type"]) ||
					("object" === $sliceLast["type"])
				)
			) {
				$slices[] = ["type"=>"comment", "from"=>$x, "delimiter"=>false];
				$x++;
				continue;
			}

			// Close: comment.
			if (
				($asterisk === $slice) &&
				($slash === $slice1) &&
				("comment" === $sliceLast["type"])
			) {
				array_pop($slices);
				$x++;

				if ($y <= $x) {
					$encoded = substr_replace(
						$encoded,
						str_repeat(" ", ($x + 1 - $sliceLast["from"])),
						$sliceLast["from"],
						($x + 1 - $sliceLast["from"])
					);
				}

				// One extra tick because we're matching 2 chars.
				$x++;
				continue;
			}

			// Not everything is open and shut.
			$x++;
		}

		return $out;
	}

	/**
	 * JSON Decode Array
	 *
	 * This ensures a JSON string is always returned as an array with
	 * optional argument parsing.
	 *
	 * @param string $json JSON.
	 * @param array $defaults Defaults.
	 * @param int $flags Flags.
	 * @return array Data.
	 */
	public static function decodeArray($json, $defaults=null, int $flags = 3) : array {
		$json = self::decode($json);

		if ((null === $json) || (("string" === gettype($json) && empty($json)))) {
			$json = [];
		}
		elseif ("array" !== gettype($json)) {
			$json = \Blobfolio\Cast::toArray($json);
		}

		// Parse args?
		if ("array" === gettype($defaults)) {
			return \Blobfolio\Cast::parseArgs($json, $defaults, $flags);
		}

		return $json;
	}

	/**
	 * JSON Encode
	 *
	 * This is a wrapper for json_encode, but will try to fix common
	 * issues.
	 *
	 * @param mixed $value Value.
	 * @param int $options Options.
	 * @param int $depth Depth.
	 * @return void Nothing.
	 */
	public static function encode($value, int $options=0, int $depth=512) : ?string {
		// Simple values don't require a lot of thought.
		if (empty($value) || is_numeric($value) || is_bool($value)) {
			return json_encode($value, $options, $depth);
		}

		// Make a copy, try PHP's version, and revert if necessary.
		$original = $value;
		$value = json_encode($value, $options, $depth);

		// Try again with UTF-8 sanitizing if this failed.
		if (empty($value)) {
			$original = \Blobfolio\Strings::utf8Recursive($original);
			$value = json_encode($original, $options, $depth);
		}

		return $value;
	}

	/**
	 * JSON
	 *
	 * Fix JSON formatting.
	 *
	 * @param string $str String.
	 * @param bool $pretty Pretty.
	 * @return bool True/false.
	 */
	public static function fix($str, bool $pretty=true) : ?string {
		if ("string" !== gettype($str)) {
			if ($pretty) {
				return self::encode($str, $pretty);
			}

			return self::encode($str);
		}

		$decoded = self::decode($str);
		if ((false === $decoded) || (null === $decoded)) {
			return null;
		}

		// Regular PHP can handle the rest.
		if ($pretty) {
			return json_encode($decoded, JSON_PRETTY_PRINT);
		}

		return json_encode($decoded);
	}

	/**
	 * Is JSON
	 *
	 * @param mixed $str String.
	 * @param bool $loose Allow empty.
	 * @return bool True/false.
	 */
	public static function isJson($str, bool $loose=false) : bool {
		if (("string" !== gettype($str)) || (!$loose && empty($str))) {
			return false;
		}

		if ($loose && empty($str)) {
			return true;
		}

		$json = json_decode($str);
		return (null !== $json);
	}
}
