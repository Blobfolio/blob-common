//<?php
/**
 * Blobfolio: Cast
 *
 * Type juggling is the best juggling.
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Cast {

	// -----------------------------------------------------------------
	// Properties
	// -----------------------------------------------------------------

	/**
	 * @var array $boolish Boolish values.
	 */
	private static boolish = [
		"0": false,
		"1": true,
		"false": false,
		"no": false,
		"off": false,
		"on": true,
		"true": true,
		"yes": true
	];

	/**
	 * @var array $win1252_chars Win-1252: UTF-8.
	 */
	private static win1252_chars = [
		128: "\xe2\x82\xac", 130: "\xe2\x80\x9a", 131: "\xc6\x92",
		132: "\xe2\x80\x9e", 133: "\xe2\x80\xa6", 134: "\xe2\x80\xa0",
		135: "\xe2\x80\xa1", 136: "\xcb\x86", 137: "\xe2\x80\xb0",
		138: "\xc5\xa0", 139: "\xe2\x80\xb9", 140: "\xc5\x92",
		142: "\xc5\xbd", 145: "\xe2\x80\x98", 146: "\xe2\x80\x99",
		147: "\xe2\x80\x9c", 148: "\xe2\x80\x9d", 149: "\xe2\x80\xa2",
		150: "\xe2\x80\x93", 151: "\xe2\x80\x94", 152: "\xcb\x9c",
		153: "\xe2\x84\xa2", 154: "\xc5\xa1", 155: "\xe2\x80\xba",
		156: "\xc5\x93", 158: "\xc5\xbe", 159: "\xc5\xb8"
	];



	// -----------------------------------------------------------------
	// Conversion
	// -----------------------------------------------------------------

	/**
	 * To Array
	 *
	 * @param mixed $value Value.
	 * @return array Value.
	 */
	public static function toArray(var value) -> array {
		// Short circuit.
		if ("array" === typeof value) {
			return value;
		}

		try {
			// Zephir doesn't support (array) hinting.
			settype(value, "array");
		} catch Throwable {
			let value = [];
		}

		return value;
	}

	/**
	 * To Bool
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return bool Bool.
	 */
	public static function toBool(var value, const bool! flatten=false) -> boolean | array {
		// Recurse.
		if (!flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toBool(v);
			}
			return value;
		}

		switch (typeof value) {
			// Short circuit.
			case "boolean":
				return value;
			case "string":
				let value = strtolower(value);

				// Special cases.
				if (isset(self::boolish[value])) {
					return self::boolish[value];
				}

				return !!value;
			case "array":
				return !!count(value);
			default:
				try {
					let value = (bool) value;
				} catch Throwable {
					let value = false;
				}
		}

		return value;
	}

	/**
	 * To Float
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return float Float.
	 */
	public static function toFloat(var value, const bool! flatten=false) -> float | array {
		// Recurse.
		if (!flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toFloat(v);
			}
			return value;
		}

		// Short circuit.
		if ("double" === typeof value) {
			return value;
		}

		let value = self::toNumber(value, true);
		return value;
	}

	/**
	 * To Integer
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return int Integer.
	 */
	public static function toInt(var value, const bool! flatten=false) -> int | array {
		// Recurse.
		if (!flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toInt(v);
			}
			return value;
		}

		switch (typeof value) {
			case "array":
				if (1 === count(value)) {
					reset(value);
					let value = value[key(value)];
					return self::toInt(value);
				}
				break;
			case "int":
			case "integer":
			case "long":
				return value;
			case "string":
				let value = strtolower(value);

				// Special cases.
				if (isset(self::boolish[value])) {
					return self::boolish[value] ? 1 : 0;
				}
		}

		let value = (int) self::toNumber(value, true);
		return value;
	}

	/**
	 * To String
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return string String.
	 */
	public static function toString(var value, const bool! flatten=false) -> string | array {
		// Recurse.
		if (!flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toString(v);
			}
			return value;
		}

		// If a single-entry array is passed, use that value.
		if (("array" === typeof value) && (1 === count(value))) {
			reset(value);
			let value = value[key(value)];
		}

		try {
			let value = (string) value;
		} catch Throwable {
			let value = "";
		}

		// Fix up UTF-8 maybe.
		if (
			value &&
			(
				!function_exists("mb_check_encoding") ||
				!mb_check_encoding(value, "ASCII")
			)
		) {
			let value = self::_utf8(value);
		}

		return value;
	}

	/**
	 * To X Type
	 *
	 * @param mixed $value Variable.
	 * @param string $type Type.
	 * @param bool $flatten Do not recurse.
	 * @return void Nothing.
	 */
	public static function toType(var value, string! type, const bool! flatten=false) {
		switch (strtolower(type)) {
			case "string":
				return self::toString(value, flatten);
			case "int":
			case "integer":
			case "long":
				return self::toInt(value, flatten);
			case "double":
			case "float":
			case "number":
				return self::toFloat(value, flatten);
			case "bool":
			case "boolean":
				return self::toBool(value, flatten);
			case "array":
				return self::toArray(value);
		}

		return value;
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Sanitize Number
	 *
	 * This ultimately returns a float, but does a lot of string
	 * manipulation along the way to try to get the sanest result.
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Do not recurse.
	 * @return float Number.
	 */
	public static function toNumber(var value, const bool! flatten=false) -> float | array {
		// Recurse.
		if (!flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toNumber(v);
			}
			return value;
		}

		switch (typeof value) {
			case "array":
				if (1 === count(value)) {
					reset(value);
					let value = value[key(value)];
					return self::toNumber(value);
				}
				break;
			case "double":
			case "float":
			case "number":
				return value;
			case "int":
			case "integer":
			case "long":
				return (float) value;
			case "string":
				// Weird Unicode numbers.
				array number_char_keys = [
					"\xef\xbc\x90", "\xef\xbc\x91", "\xef\xbc\x92",
					"\xef\xbc\x93", "\xef\xbc\x94", "\xef\xbc\x95",
					"\xef\xbc\x96", "\xef\xbc\x97", "\xef\xbc\x98",
					"\xef\xbc\x99", "\xd9\xa0", "\xd9\xa1", "\xd9\xa2",
					"\xd9\xa3", "\xd9\xa4", "\xd9\xa5", "\xd9\xa6",
					"\xd9\xa7", "\xd9\xa8", "\xd9\xa9", "\xdb\xb0",
					"\xdb\xb1", "\xdb\xb2", "\xdb\xb3", "\xdb\xb4",
					"\xdb\xb5", "\xdb\xb6", "\xdb\xb7", "\xdb\xb8",
					"\xdb\xb9", "\xe1\xa0\x90", "\xe1\xa0\x91",
					"\xe1\xa0\x92", "\xe1\xa0\x93", "\xe1\xa0\x94",
					"\xe1\xa0\x95", "\xe1\xa0\x96", "\xe1\xa0\x97",
					"\xe1\xa0\x98", "\xe1\xa0\x99"
				];

				// The equivalent as actual numbers.
				array number_char_values = [
					0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6,
					7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3,
					4, 5, 6, 7, 8, 9
				];

				// Fix weird Unicode numbers.
				let value = str_replace(
					number_char_keys,
					number_char_values,
					value
				);

				// Convert from cents.
				if (preg_match("/^\-?[\d,]*\.?\d+¢$/", value)) {
					return self::toNumber(
						preg_replace("/[^\-\d\.]/", "", value)
					) / 100;
				}
				// Convert from percent.
				elseif (preg_match("/^\-?[\d,]*\.?\d+%$/", value)) {
					return self::toNumber(
						preg_replace("/[^\-\d\.]/", "", value)
					) / 100;
				}
		}

		try {
			let value = (float) filter_var(
				value,
				FILTER_SANITIZE_NUMBER_FLOAT,
				FILTER_FLAG_ALLOW_FRACTION
			);
		} catch Throwable {
			let value = 0.0;
		}

		return value;
	}

	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @see {https://github.com/neitanod/forceutf8}
	 *
	 * @param string $str String.
	 * @return void Nothing.
	 */
	private static function _utf8(string str) -> string {
		// Easy bypass.
		if (("" === str) || is_numeric(str)) {
			return str;
		}

		// Let"s run our library checks just once.
		bool has_mb = (
			function_exists("mb_check_encoding") &&
			function_exists("mb_strlen")
		);

		// Fix it up if we need to.
		if (!has_mb || !mb_check_encoding(str, "ASCII")) {
			string out = "";
			int length = 0;

			// The length of the string.
			if (has_mb) {
				let length = (int) mb_strlen(str, "8bit");
			}
			else {
				let length = (int) strlen(str);
			}

			// We need to keep our chars variant for bitwise operations.
			var c1, c2, c3, c4, cc1, cc2;
			var x00 = "\x00";
			var x3f = "\x3f";
			var x80 = "\x80";
			var xbf = "\xbf";
			var xc0 = "\xc0";
			var xdf = "\xdf";
			var xe0 = "\xe0";
			var xef = "\xef";
			var xf0 = "\xf0";
			var xf7 = "\xf7";

			int x = 0;
			while x < length {
				let c1 = substr(str, x, 1);

				// Should be converted to UTF-8 if not already.
				if (c1 >= xc0) {
					let c2 = (x + 1) >= length ? strval(x00) : strval(str[x + 1]);
					let c3 = (x + 2) >= length ? strval(x00) : strval(str[x + 2]);
					let c4 = (x + 3) >= length ? strval(x00) : strval(str[x + 3]);

					// Probably 2-byte UTF-8.
					if ((c1 >= xc0) & (c1 <= xdf)) {
						// Looks good.
						if (c2 >= x80 && c2 <= xbf) {
							let out .= c1 . c2;
							let x += 1;
						}
						// Invalid; convert it.
						else {
							let cc1 = (chr(ord(c1) / 64) | xc0);
							let cc2 = (c1 & x3f) | x80;
							let out .= cc1 . cc2;
						}
					}
					// Probably 3-byte UTF-8.
					elseif ((c1 >= xe0) & (c1 <= xef)) {
						// Looks good.
						if (
							c2 >= x80 &&
							c2 <= xbf &&
							c3 >= x80 &&
							c3 <= xbf
						) {
							let out .= c1 . c2 . c3;
							let x += 2;
						}
						// Invalid; convert it.
						else {
							let cc1 = strval((chr(ord(c1) / 64) | xc0));
							let cc2 = strval((c1 & x3f) | x80);
							let out .= cc1 . cc2;
						}
					}
					// Probably 4-byte UTF-8.
					elseif ((c1 >= xf0) & (c1 <= xf7)) {
						// Looks good.
						if (
							c2 >= x80 &&
							c2 <= xbf &&
							c3 >= x80 &&
							c3 <= xbf &&
							c4 >= x80 &&
							c4 <= xbf
						) {
							let out .= c1 . c2 . c3 . c4;
							let x += 3;
						}
						// Invalid; convert it.
						else {
							let cc1 = strval((chr(ord(c1) / 64) | xc0));
							let cc2 = strval((c1 & x3f) | x80);
							let out .= cc1 . cc2;
						}
					}
					// Doesn"t appear to be UTF-8; convert it.
					else {
						let cc1 = strval((chr(ord(c1) / 64) | xc0));
						let cc2 = strval(((c1 & x3f) | x80));
						let out .= cc1 . cc2;
					}
				}
				// Convert it.
				elseif ((c1 & xc0) === x80) {
					int o1 = (int) ord(c1);

					// Convert from Windows-1252.
					if (isset(self::win1252_chars[o1])) {
						let out .= self::win1252_chars[o1];
					}
					else {
						let cc1 = strval((chr(o1 / 64) | xc0));
						let cc2 = strval(((c1 & x3f) | x80));
						let out .= cc1 . cc2;
					}
				}
				// No change.
				else {
					let out .= c1;
				}

				// Increment.
				let x += 1;
			}

			// If it seems valid, return it, otherwise empty it out.
			return (1 === preg_match("/^./us", out)) ? out : "";
		}

		return str;
	}
}
