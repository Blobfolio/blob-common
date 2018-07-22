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
			// Zephir doesn't support (array) hinting in this one place.
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
	public static function toBool(var value, const bool flatten=false) -> boolean | array {
		// Recurse.
		if (unlikely !flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = (bool) self::toBool(v);
			}
			return value;
		}
		else {
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
			}

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
	public static function toFloat(var value, const bool flatten=false) -> float | array {
		// Recurse.
		if (unlikely !flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toFloat(v);
			}
			return value;
		}
		// Short circuit.
		elseif ("double" === typeof value) {
			return value;
		}
		else {
			let value = self::toNumber(value, true);
		}

		return value;
	}

	/**
	 * To Integer
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return int Integer.
	 */
	public static function toInt(var value, const bool flatten=false) -> int | array {
		// Recurse.
		if (unlikely !flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toInt(v);
			}
			return value;
		}
		else {
			switch (typeof value) {
				case "array":
					if (1 === count(value)) {
						reset(value);
						return self::toInt(value[key(value)], true);
					}

					return 0;
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
		}

		let value = (int) self::toNumber(value, true);
		return value;
	}

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
	public static function toNumber(var value, const bool flatten=false) -> float | array {
		// Recurse.
		if (unlikely !flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toNumber(v);
			}
			return value;
		}
		else {
			switch (typeof value) {
				case "array":
					if (1 === count(value)) {
						reset(value);
						return self::toNumber(value[key(value)], true);
					}

					return 0.0;
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
					if (preg_match("/^\-?[\d,]*\.?\d+(¢|%)$/", value)) {
						return self::toNumber(
							preg_replace("/[^\-\d\.]/", "", value)
						) / 100;
					}
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
	 * To String
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @return string String.
	 */
	public static function toString(var value, const bool flatten=false) -> string | array {
		// Recurse.
		if (unlikely !flatten && ("array" === typeof value)) {
			var k, v;
			for k, v in value {
				let value[k] = self::toString(v);
			}
			return value;
		}
		else {
			// If a single-entry array is passed, use that value.
			if (("array" === typeof value)) {
				if (1 === count(value)) {
					reset(value);
					return self::toString(value[key(value)], true);
				}

				return "";
			}

			try {
				let value = (string) value;
			} catch Throwable {
				return "";
			}

			// Fix up UTF-8 maybe.
			if (value && !mb_check_encoding(value, "ASCII")) {
				let value = Strings::utf8(value);
			}
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
	public static function toType(var value, string type, const bool flatten=false) {
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
	 * Parse Arguments
	 *
	 * Make sure user arguments follow a default
	 * format. Unlike `wp_parse_args()`-type functions,
	 * only keys from the template are allowed.
	 *
	 * @param mixed $args User arguments.
	 * @param mixed $defaults Default values/format.
	 * @param bool $strict Strict type enforcement.
	 * @param bool $recursive Recursively apply formatting if inner values are also arrays.
	 * @return array Parsed arguments.
	 */
	public static function parseArgs(var args, var defaults, const bool strict=true, const bool recursive=true) -> array {
		// Nothing to crunch if the template isn't set.
		if ("array" !== typeof defaults || !count(defaults)) {
			return [];
		}

		// If there are no arguments to crunch, return the template.
		let args = self::toArray(args);
		if (!count(args)) {
			return defaults;
		}

		// Rebuild with user args!
		var k, v;
		for k, v in defaults {
			if (array_key_exists(k, args)) {
				// Recurse if the default is a populated associative
				// array.
				if (
					recursive &&
					("array" === typeof defaults[k]) &&
					("associative" === Arrays::getType(defaults[k]))
				) {
					let defaults[k] = self::parseArgs(
						args[k],
						defaults[k],
						strict,
						recursive
					);
				}
				// Otherwise just replace.
				else {
					let defaults[k] = args[k];
					if (strict && (null !== v)) {
						string d_type = typeof v;
						string a_type = typeof defaults[k];

						if (a_type !== d_type) {
							let defaults[k] = self::toType(
								defaults[k],
								d_type,
								true
							);
						}
					}
				}
			}
		}

		return defaults;
	}
}
