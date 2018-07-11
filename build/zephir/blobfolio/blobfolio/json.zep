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

use \Throwable;

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
	public static function decode(var str) {
		let str = Cast::toString(str, true);

		// Remove comments.
		let str = preg_replace("#^\s*//(.+)$#m", "", str);
		let str = preg_replace("#^\s*/\*(.+)\*/#Us", "", str);
		let str = preg_replace("#/\*(.+)\*/\s*$#Us", "", str);

		// Trim it.
		let str = Strings::trim(str);

		// Is it empty?
		if (empty str || ("''" === str) || ("\"\"" === str)) {
			return "";
		}

		// Maybe it just works?
		var tmp = json_decode(str, true);
		if (null !== tmp) {
			return tmp;
		}

		// A lot of the following tests are case-insensitive.
		string lower = (string) Strings::strtolower(str, false);
		var match;

		// Bool.
		if (("true" === lower) || ("false" === lower)) {
			return Cast::toBool(str, true);
		}
		// Null.
		elseif ("null" === lower) {
			return null;
		}
		// Number.
		elseif (is_numeric(lower)) {
			if (false !== strpos(lower, ".")) {
				return Cast::toFloat(lower);
			}

			return Cast::toInt(lower);
		}
		// String.
		elseif (
			preg_match(
				"/^(\"|')(.+)(\1)$/s",
				str,
				match
			) &&
			(match[1] === match[3])
		) {
			let str = match[2];
			return Strings::decodeJsEntities(str);
		}
		// Bail if we don't have an object at this point.
		elseif (
			!preg_match("/^\[.*\]$/s", str) &&
			!preg_match("/^\{.*\}$/s", str)
		) {
			return null;
		}

		// Start building an array.
		array slices = [["type": "slice", "from": 0, "delimiter": false, "open": true]];
		array out = [];

		string sliceType = "object";
		if (0 === strpos(str, "[")) {
			let sliceType = "array";
		}

		// Zephir doesn't allow access to string chars by index, so we
		// have to make an array of it.
		array chunks = (array) Strings::str_split(
			mb_substr(str, 1, -1, "UTF-8")
		);
		long length = (long) count(chunks);
		if (length <= 0) {
			return null;
		}

		long x = 0;
		long y = 0;
		long sliceKey = 0;
		var last;
		string slice = "";
		string chunk = "";
		string chunk2 = "";
		var k, v;

		while x <= length {
			// Find the current slice.
			let sliceKey = (long) self::decodeSliceKey(slices);
			if (sliceKey < 0) {
				let last = false;
			}
			else {
				let last = (array) slices[sliceKey];
			}

			// Set up some chunks.
			if (x < length) {
				let chunk = (string) chunks[x];
				if (x + 1 < length) {
					let chunk2 = (string) chunks[x] . chunks[x + 1];
				}
				else {
					let chunk2 = "";
				}
			}
			else {
				let chunk = "";
				let chunk2 = "";
			}

			// Are we done?
			if (
				(x === length) ||
				(("," === chunk) && (false !== last) && ("slice" === last["type"]))
			) {
				// Things get weird if there is no current slice.
				let slice = (string) trim(implode("", array_slice(chunks, last["from"], (x - last["from"]))));

				// Arrays are straight-forward.
				if ("array" === sliceType) {
					let out[] = self::decode(slice);
				}
				// Objects are more annoying.
				else {
					// We have to tease apart the keys and values, extra
					// annoying since Zephir's regular expressions
					// behaviors are fucked.
					if (":" !== slice && false !== strpos(slice, ":")) {
						var key_end = -1;
						var val_start = -1;
						string first = (string) mb_substr(slice, 0, 1, "UTF-8");
						int last_index = 1;

						// The key is quoted.
						if (("\"" === first) || ("'" === first)) {
							while last_index > 0 && key_end < 0 {
								let last_index = (int) mb_strpos(
									slice,
									first,
									last_index,
									"UTF-8"
								);
								if (
									last_index &&
									("\\" !== mb_substr(slice, last_index - 1, 1, "UTF-8"))
								) {
									let key_end = last_index;
								}
							}

							// We have a key, now find the damn colon.
							if (key_end > 0) {
								let last_index = (int) mb_strpos(
									slice,
									":",
									key_end,
									"UTF-8"
								);
								if (last_index) {
									let val_start = last_index + 1;
								}
							}
						}
						// Unquoted keys just get split on the first :.
						else {
							let last_index = (int) mb_strpos(
								slice,
								":",
								0,
								"UTF-8"
							);
							let key_end = last_index - 1;
							let val_start = last_index + 1;
						}

						// We found them!
						if (key_end > 0 && val_start > 0) {
							let k = (string) trim(mb_substr(
								slice,
								0,
								key_end + 1,
								"UTF-8"
							));

							let v = (string) trim(mb_substr(
								slice,
								val_start,
								null,
								"UTF-8"
							));

							// Recurse.
							if (("\"" === first) || ("'" === first)) {
								let k = self::decode(k);
							}
							else {
								let k = Strings::decodeJsEntities(k);
							}

							let out[k] = self::decode(v);
						}
					}
				}

				// Start a new slice.
				let slices[] = ["type": "slice", "from": x + 1, "delimiter": false, "open": true];

				// Reboot.
				let x++;
				continue;
			} // End the end, or a comma.

			// A new quote.
			if (
				(("\"" === chunk) || ("'" === chunk)) &&
				("string" !== last["type"])
			) {
				let slices[] = ["type": "string", "from": x, "delimiter": chunk, "open": true];
				let x++;
				continue;
			}

			// A closing quote.
			if (
				(chunk === last["delimiter"]) &&
				("string" === last["type"]) &&
				(
					(x > 0 && ("\\" !== chunks[x - 1])) &&
					(x < 2 || ("\\" !== chunks[x - 2]))
				)
			) {
				let slices[sliceKey]["open"] = false;
				let x++;
				continue;
			}

			// Opening bracket (and we're in a slice/objectish thing.
			if (
				("[" === chunk) &&
				in_array(last["type"], ["slice", "array", "object"], true)
			) {
				let slices[] = ["type": "array", "from": x, "delimiter": false, "open": true];
				let x++;
				continue;
			}

			// Closing bracket.
			if (
				("]" === chunk) &&
				("array" === last["type"])
			) {
				let slices[sliceKey]["open"] = false;
				let x++;
				continue;
			}

			// Opening brace (and we're in a slice/objectish thing.
			if (
				("{" === chunk) &&
				in_array(last["type"], ["slice", "array", "object"], true)
			) {
				let slices[] = ["type": "object", "from": x, "delimiter": false, "open": true];
				let x++;
				continue;
			}

			// Closing brace.
			if (
				("}" === chunk) &&
				("object" === last["type"])
			) {
				let slices[sliceKey]["open"] = false;
				let x++;
				continue;
			}

			// Opening comment.
			if (
				("/*" === chunk2) &&
				in_array(last["type"], ["slice", "array", "object"], true)
			) {
				let slices[] = ["type": "comment", "from": x, "delimiter": false, "open": true];
				let x++;
				continue;
			}

			// Closing comment.
			if (
				("*/" === chunk2) &&
				("comment" === last["type"])
			) {
				let slices[sliceKey]["open"] = false;
				let x++;

				let y = (long) last["from"];
				while y <= x && y < length {
					let chunks[y] = " ";
					let y++;
				}

				let x++;
				continue;
			}

			// We shouldn't be hitting this.
			let x++;
		}

		return out;
	}

	/**
	 * JSON Decode Helper
	 *
	 * Zephir has memory leak problems with arrays, preventing us from
	 * sanely unsetting slices aces they're closed. As a workaround,
	 * we'll try to set the closed values to "null".
	 *
	 * @param array Slices.
	 * @return mixed Key.
	 */
	protected static function decodeSliceKey(array slices) -> long {
		let slices = (array) array_reverse(slices, true);
		var k, v;
		for k, v in slices {
			if (v["open"]) {
				return (long) k;
			}
		}

		long wrong = -1;
		return wrong;
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
			let original = Strings::utf8Recursive(original);
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
}
