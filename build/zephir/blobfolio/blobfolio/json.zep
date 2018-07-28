//<?php
/**
 * Blobfolio: JSON
 *
 * Improved JSON support for PHP.
 *
 * @see {blobfolio\common\data}
 * @see {blobfolio\common\format}
 * @see {blobfolio\common\ref\format}
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
	public static function decode(var str, const bool recursed=false) {
		array match;
		string encoded;
		string lower;
		var tmp;

		// Copy str over to our typed variable.
		if (!recursed) {
			let encoded = (string) \Blobfolio\Cast::toString(str, true);
		}
		else {
			let encoded = (string) str;
		}

		// Remove comments.
		let encoded = preg_replace("#(^\s*//(.+)$|^\s*/\*(.+)\*/|/\*(.+)\*/\s*$)#m", "", encoded);

		// Trim it.
		let encoded = preg_replace("/(^\s+|\s+$)/u", "", encoded);

		// Is it empty?
		if (empty encoded || ("''" === encoded) || ("\"\"" === encoded)) {
			return "";
		}

		// Maybe it just works?
		let tmp = json_decode(encoded, true);
		if (null !== tmp) {
			return tmp;
		}

		// A lot of the following tests are case-insensitive.
		let lower = (string) \Blobfolio\Strings::toLower(encoded, true);

		// Bool.
		if (("true" === lower) || ("false" === lower)) {
			return \Blobfolio\Cast::toBool(encoded, true);
		}
		// Null.
		elseif ("null" === lower) {
			return null;
		}
		// Number.
		elseif (is_numeric(lower)) {
			if (false !== strpos(lower, ".")) {
				return \Blobfolio\Cast::toFloat(lower);
			}

			return \Blobfolio\Cast::toInt(lower);
		}
		// String.
		elseif (
			preg_match(
				"/^(\"|')(.+)(\1)$/s",
				encoded,
				match
			) &&
			count(match) >= 3 &&
			(match[1] === match[3])
		) {
			let encoded = (string) match[2];
			return \Blobfolio\Dom::decodeJsEntities(encoded);
		}
		// Bail if we don't have an object at this point.
		elseif (
			!preg_match("/^(\[.*\]|\{.*\})$/s", encoded)
		) {
			return null;
		}

		// We have to parse it all manually. Ug.
		array out = [];
		array sliceLast;
		array slices = [];
		boolean hasSlice;
		boolean stubQuoted;
		char apostrophe = 39;
		char asterisk = 42;
		char backslash = 92;
		char braceClose = 125;
		char braceOpen = 123;
		char bracketClose = 93;
		char bracketOpen = 91;
		char comma = 44;
		char quote = 22;
		char slash = 47;
		char slice1;
		char slice;
		char slice_1;
		char slice_2;
		char sliceFirstChar;
		int sliceIndex;
		int sliceKeyEnd;
		int sliceLength;
		int sliceValStart;
		int x = 0;
		int y = 0;
		string sliceCurrent;
		string sliceType;
		var stubK;
		var stubV;

		// Start building an array.
		let slices[] = ["type": "slice", "from": 0, "delimiter": false];

		// Figure out what kind of wrapper we have.
		if (0 === strpos(encoded, "[")) {
			let sliceType = "array";
		}
		else {
			let sliceType = "object";
		}

		let encoded = (string) mb_substr(encoded, 1, -1, "UTF-8");

		// The length of our pie. Note: we are looping through ASCII
		// chars, so this is the non-MB size.
		let sliceLength = (int) strlen(encoded);

		while x <= sliceLength {
			// Fill out the current and future chars.
			if (x < sliceLength) {
				let slice = (char) ord(encoded[x]);
				if (x + 1 < sliceLength) {
					let slice1 = (char) ord(encoded[x + 1]);
				}
				else {
					let slice1 = 32;
				}
			}
			else {
				let slice = 32;
				let slice1 = 32;
			}

			// Fill out the previous chars.
			if (x > 0) {
				let slice_1 = (char) ord(encoded[x - 1]);
				if (x > 1) {
					let slice_2 = (char) ord(encoded[x - 2]);
				}
				else {
					let slice_2 = 32;
				}
			}
			else {
				let slice_1 = 32;
				let slice_2 = 32;
			}

			// What were we last up to?
			if (count(slices)) {
				let sliceLast = (array) end(slices);
				let hasSlice = true;
			}
			else {
				let sliceLast = ["delimiter": false];
				let hasSlice = false;
			}

			// Are we done?
			if (
				(x >= sliceLength) ||
				((comma === slice) && hasSlice && ("slice" === sliceLast["type"]))
			) {
				let sliceCurrent = (string) trim(substr(
					encoded,
					sliceLast["from"],
					(x - sliceLast["from"])
				));

				// Arrays are straight forward.
				if ("array" === sliceType) {
					let out[] = self::decode(sliceCurrent, true);
				}
				// Objects can be much more annoying.
				else {
					// Tease apart the keys and values.
					if (
						(":" !== sliceCurrent) &&
						(false !== strpos(sliceCurrent, ":"))
					) {
						let sliceKeyEnd = -1;
						let sliceValStart = -1;
						let sliceFirstChar = (char) ord(sliceCurrent[0]);
						let sliceIndex = 1;
						let stubQuoted = false;
						let stubK = "";
						let stubV = "";

						// The first key is quoted.
						if ((quote === sliceFirstChar) || (apostrophe === sliceFirstChar)) {
							let stubQuoted = true;
							// A preg match would make more sense, but
							// there's something wrong with Zephir's
							// implementation.
							while sliceIndex > 0 && sliceKeyEnd < 0 {
								let sliceIndex = (int) mb_strpos(
									sliceCurrent,
									sliceCurrent[0],
									sliceIndex,
									"UTF-8"
								);

								if (
									sliceIndex &&
									("\\" !== mb_substr(sliceCurrent, sliceIndex - 1, 1, "UTF-8"))
								) {
									let sliceKeyEnd = sliceIndex;
								}
							}

							if (sliceKeyEnd) {
								let sliceIndex = (int) mb_strpos(
									sliceCurrent,
									":",
									sliceKeyEnd,
									"UTF-8"
								);
								if (sliceIndex) {
									let sliceValStart = sliceIndex + 1;
								}
							}
						}
						// Unquoted key.
						else {
							// This we can just split on the :.
							let sliceIndex = (int) mb_strpos(
								sliceCurrent,
								":",
								0,
								"UTF-8"
							);
							let sliceKeyEnd = sliceIndex - 1;
							let sliceValStart = sliceIndex + 1;
						}

						// We found them!
						if (sliceKeyEnd > 0 && sliceValStart > 0) {
							let stubK = (string) trim(mb_substr(
								sliceCurrent,
								0,
								sliceKeyEnd + 1,
								"UTF-8"
							));

							let stubV = (string) trim(mb_substr(
								sliceCurrent,
								sliceValStart,
								null,
								"UTF-8"
							));

							let stubK = stubQuoted ? self::decode(stubK, true) : \Blobfolio\Dom::decodeJsEntities(stubK);

							let out[stubK] = self::decode(stubV, true);
						}
					}
				}

				array_pop(slices);
				let slices[] = ["type": "slice", "from": x + 1, "delimiter": false];

				// Reboot.
				let x++;
				continue;
			}

			// Open: quote.
			if (
				((quote === slice) || (apostrophe === slice)) &&
				("string" !== sliceLast["type"]) &&
				("comment" !== sliceLast["type"])
			) {
				let slices[] = ["type": "string", "from": x, "delimiter": ((quote === slice) ? "\"" : "'")];
				let x++;
				continue;
			}

			// Close: quote.
			if (
				(slice === (char) ord(sliceLast["delimiter"])) &&
				("string" === sliceLast["type"]) &&
				((backslash !== slice_1) && (backslash !== slice_2))
			) {
				array_pop(slices);
				let x++;
				continue;
			}

			// Open: bracket.
			if (
				(bracketOpen === slice) &&
				(
					("slice" === sliceLast["type"]) ||
					("array" === sliceLast["type"]) ||
					("object" === sliceLast["type"])
				)
			) {
				let slices[] = ["type": "array", "from": x, "delimiter": false];
				let x++;
				continue;
			}

			// Close: bracket.
			if ((bracketClose === slice) && ("array" === sliceLast["type"])) {
				array_pop(slices);
				let x++;
				continue;
			}

			// Open: brace.
			if (
				(braceOpen === slice) &&
				(
					("slice" === sliceLast["type"]) ||
					("array" === sliceLast["type"]) ||
					("object" === sliceLast["type"])
				)
			) {
				let slices[] = ["type": "object", "from": x, "delimiter": false];
				let x++;
				continue;
			}

			// Close: brace.
			if ((braceClose === slice) && ("object" === sliceLast["type"])) {
				array_pop(slices);
				let x++;
				continue;
			}

			// Open: comment.
			if (
				(slash === slice) &&
				(asterisk === slice1) &&
				(
					("slice" === sliceLast["type"]) ||
					("array" === sliceLast["type"]) ||
					("object" === sliceLast["type"])
				)
			) {
				let slices[] = ["type": "comment", "from": x, "delimiter": false];
				let x++;
				continue;
			}

			// Close: comment.
			if (
				(asterisk === slice) &&
				(slash === slice1) &&
				("comment" === sliceLast["type"])
			) {
				array_pop(slices);
				let x++;

				if (y <= x) {
					let encoded = substr_replace(
						encoded,
						str_repeat(" ", (x + 1 - sliceLast["from"])),
						sliceLast["from"],
						(x + 1 - sliceLast["from"])
					);
				}

				// One extra tick because we're matching 2 chars.
				let x++;
				continue;
			}

			// Not everything is open and shut.
			let x++;
		}

		return out;
	}

	/**
	 * JSON Decode Array
	 *
	 * This ensures a JSON string is always returned as an array with
	 * optional argument parsing.
	 *
	 * @param string $json JSON.
	 * @param array $defaults Defaults.
	 * @param bool $strict Strict.
	 * @param bool $recursive Recursive.
	 * @return array Data.
	 */
	public static function decodeArray(var json, const var defaults=null, const bool strict=true, const bool recursive=true) -> array {
		let json = self::decode(json);

		if ((null === json) || (("string" === typeof json && empty json))) {
			let json = [];
		}
		elseif ("array" !== typeof json) {
			let json = \Blobfolio\Cast::toArray(json);
		}

		// Parse args?
		if ("array" === typeof defaults) {
			return \Blobfolio\Cast::parseArgs(json, defaults, strict, recursive);
		}

		return json;
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
	public static function encode(var value, const int options=0, const int depth=512) -> string | null {
		// Simple values don't require a lot of thought.
		if (empty value || is_numeric(value) || is_bool(value)) {
			return json_encode(value, options, depth);
		}

		// Make a copy, try PHP's version, and revert if necessary.
		var original = value;
		let value = json_encode(value, options, depth);

		// Try again with UTF-8 sanitizing if this failed.
		if (null === value) {
			let original = \Blobfolio\Strings::utf8Recursive(original);
			let value = json_encode(original, options, depth);
		}

		return value;
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
	public static function fix(var str, const bool pretty=true) -> string | null {
		if ("string" !== typeof str) {
			if (pretty) {
				return self::encode(str, pretty);
			}

			return self::encode(str);
		}

		var decoded;
		let decoded = self::decode(str);
		if ((false === decoded) || (null === decoded)) {
			return null;
		}

		// Regular PHP can handle the rest.
		if (pretty) {
			return json_encode(decoded, JSON_PRETTY_PRINT);
		}

		return json_encode(decoded);
	}

	/**
	 * Is JSON
	 *
	 * @param mixed $str String.
	 * @param bool $loose Allow empty.
	 * @return bool True/false.
	 */
	public static function isJson(var str, const bool loose=false) -> bool {
		if (("string" !== typeof str) || (!loose && empty str)) {
			return false;
		}

		if (loose && empty str) {
			return true;
		}

		var json;
		let json = json_decode(str);
		return (null !== json);
	}
}
