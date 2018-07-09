//<?php
/**
 * Blobfolio: Strings
 *
 * String manipulation.
 *
 * @see {blobfolio\common\mb}
 * @see {blobfolio\common\ref\mb}
 * @see {blobfolio\common\ref\sanitize}
 * @see {blobfolio\common\sanitize}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Strings {
	/**
	 * @var array $case_char_upper Uppercase Unicode.
	 */
	private static case_char_upper = [
		"\xC7\x85", "\xC7\x88", "\xC7\x8B", "\xC7\xB2", "\xCF\xB7", "\xCF\xB9",
		"\xCF\xBA", "\xE1\xBE\x88", "\xE1\xBE\x89", "\xE1\xBE\x8A",
		"\xE1\xBE\x8B", "\xE1\xBE\x8C", "\xE1\xBE\x8D", "\xE1\xBE\x8E",
		"\xE1\xBE\x8F", "\xE1\xBE\x98", "\xE1\xBE\x99", "\xE1\xBE\x9A",
		"\xE1\xBE\x9B", "\xE1\xBE\x9C", "\xE1\xBE\x9D", "\xE1\xBE\x9E",
		"\xE1\xBE\x9F", "\xE1\xBE\xA8", "\xE1\xBE\xA9", "\xE1\xBE\xAA",
		"\xE1\xBE\xAB", "\xE1\xBE\xAC", "\xE1\xBE\xAD", "\xE1\xBE\xAE",
		"\xE1\xBE\xAF", "\xE1\xBE\xBC", "\xE1\xBF\x8C", "\xE1\xBF\xBC",
		"\xE2\x85\xA0", "\xE2\x85\xA1", "\xE2\x85\xA2", "\xE2\x85\xA3",
		"\xE2\x85\xA4", "\xE2\x85\xA5", "\xE2\x85\xA6", "\xE2\x85\xA7",
		"\xE2\x85\xA8", "\xE2\x85\xA9", "\xE2\x85\xAA", "\xE2\x85\xAB",
		"\xE2\x85\xAC", "\xE2\x85\xAD", "\xE2\x85\xAE", "\xE2\x85\xAF",
		"\xE2\x92\xB6", "\xE2\x92\xB7", "\xE2\x92\xB8", "\xE2\x92\xB9",
		"\xE2\x92\xBA", "\xE2\x92\xBB", "\xE2\x92\xBC", "\xE2\x92\xBD",
		"\xE2\x92\xBE", "\xE2\x92\xBF", "\xE2\x93\x80", "\xE2\x93\x81",
		"\xE2\x93\x82", "\xE2\x93\x83", "\xE2\x93\x84", "\xE2\x93\x85",
		"\xE2\x93\x86", "\xE2\x93\x87", "\xE2\x93\x88", "\xE2\x93\x89",
		"\xE2\x93\x8A", "\xE2\x93\x8B", "\xE2\x93\x8C", "\xE2\x93\x8D",
		"\xE2\x93\x8E", "\xE2\x93\x8F", "\xF0\x90\xA6", "\xF0\x90\xA7"
	];

	/**
	 * @var array $case_char_lower Lowercase Unicode.
	 */
	private static case_char_lower = [
		"\xC7\x86", "\xC7\x89", "\xC7\x8C", "\xC7\xB3", "\xCF\xB8", "\xCF\xB2",
		"\xCF\xBB", "\xE1\xBE\x80", "\xE1\xBE\x81", "\xE1\xBE\x82",
		"\xE1\xBE\x83", "\xE1\xBE\x84", "\xE1\xBE\x85", "\xE1\xBE\x86",
		"\xE1\xBE\x87", "\xE1\xBE\x90", "\xE1\xBE\x91", "\xE1\xBE\x92",
		"\xE1\xBE\x93", "\xE1\xBE\x94", "\xE1\xBE\x95", "\xE1\xBE\x96",
		"\xE1\xBE\x97", "\xE1\xBE\xA0", "\xE1\xBE\xA1", "\xE1\xBE\xA2",
		"\xE1\xBE\xA3", "\xE1\xBE\xA4", "\xE1\xBE\xA5", "\xE1\xBE\xA6",
		"\xE1\xBE\xA7", "\xE1\xBE\xB3", "\xE1\xBF\x83", "\xE1\xBF\xB3",
		"\xE2\x85\xB0", "\xE2\x85\xB1", "\xE2\x85\xB2", "\xE2\x85\xB3",
		"\xE2\x85\xB4",  "\xE2\x85\xB5", "\xE2\x85\xB6", "\xE2\x85\xB7",
		"\xE2\x85\xB8", "\xE2\x85\xB9", "\xE2\x85\xBA", "\xE2\x85\xBB",
		"\xE2\x85\xBC", "\xE2\x85\xBD", "\xE2\x85\xBE", "\xE2\x85\xBF",
		"\xE2\x93\x90", "\xE2\x93\x91", "\xE2\x93\x92", "\xE2\x93\x93",
		"\xE2\x93\x94", "\xE2\x93\x95", "\xE2\x93\x96", "\xE2\x93\x97",
		"\xE2\x93\x98", "\xE2\x93\x99", "\xE2\x93\x9A", "\xE2\x93\x9B",
		"\xE2\x93\x9C", "\xE2\x93\x9D", "\xE2\x93\x9E", "\xE2\x93\x9F",
		"\xE2\x93\xA0", "\xE2\x93\xA1", "\xE2\x93\xA2", "\xE2\x93\xA3",
		"\xE2\x93\xA4", "\xE2\x93\xA5", "\xE2\x93\xA6", "\xE2\x93\xA7",
		"\xE2\x93\xA8", "\xE2\x93\xA9", "\xF0\x91\x8E", "\xF0\x91\x8F"
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

	/**
	 * Accents
	 *
	 * Convert accented to non-accented characters.
	 *
	 * @param array|string String.
	 * @return array|string String.
	 */
	public static function accents(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::accents(v);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);
		if (preg_match("/[\x80-\xff]/", str)) {
			array accent_chars = [
				"ª":"a", "º":"o", "À":"A", "Á":"A", "Â":"A", "Ã":"A",
				"Ä":"A", "Å":"A", "Æ":"AE", "Ç":"C", "È":"E", "É":"E",
				"Ê":"E", "Ë":"E", "Ì":"I", "Í":"I", "Î":"I", "Ï":"I",
				"Ð":"D", "Ñ":"N", "Ò":"O", "Ó":"O", "Ô":"O", "Õ":"O",
				"Ö":"O", "Ù":"U", "Ú":"U", "Û":"U", "Ü":"U", "Ý":"Y",
				"Þ":"TH", "ß":"s", "à":"a", "á":"a", "â":"a", "ã":"a",
				"ä":"a", "å":"a", "æ":"ae", "ç":"c", "è":"e", "é":"e",
				"ê":"e", "ë":"e", "ì":"i", "í":"i", "î":"i", "ï":"i",
				"ð":"d", "ñ":"n", "ò":"o", "ó":"o", "ô":"o", "õ":"o",
				"ö":"o", "ø":"o", "ù":"u", "ú":"u", "û":"u", "ü":"u",
				"ý":"y", "þ":"th", "ÿ":"y", "Ø":"O", "Ā":"A", "ā":"a",
				"Ă":"A", "ă":"a", "Ą":"A", "ą":"a", "Ć":"C", "ć":"c",
				"Ĉ":"C", "ĉ":"c", "Ċ":"C", "ċ":"c", "Č":"C", "č":"c",
				"Ď":"D", "ď":"d", "Đ":"D", "đ":"d", "Ē":"E", "ē":"e",
				"Ĕ":"E", "ĕ":"e", "Ė":"E", "ė":"e", "Ę":"E", "ę":"e",
				"Ě":"E", "ě":"e", "Ĝ":"G", "ĝ":"g", "Ğ":"G", "ğ":"g",
				"Ġ":"G", "ġ":"g", "Ģ":"G", "ģ":"g", "Ĥ":"H", "ĥ":"h",
				"Ħ":"H", "ħ":"h", "Ĩ":"I", "ĩ":"i", "Ī":"I", "ī":"i",
				"Ĭ":"I", "ĭ":"i", "Į":"I", "į":"i", "İ":"I", "ı":"i",
				"Ĳ":"IJ", "ĳ":"ij", "Ĵ":"J", "ĵ":"j", "Ķ":"K", "ķ":"k",
				"ĸ":"k", "Ĺ":"L", "ĺ":"l", "Ļ":"L", "ļ":"l", "Ľ":"L",
				"ľ":"l", "Ŀ":"L", "ŀ":"l", "Ł":"L", "ł":"l", "Ń":"N",
				"ń":"n", "Ņ":"N", "ņ":"n", "Ň":"N", "ň":"n", "ŉ":"N",
				"Ŋ":"n", "ŋ":"N", "Ō":"O", "ō":"o", "Ŏ":"O", "ŏ":"o",
				"Ő":"O", "ő":"o", "Œ":"OE", "œ":"oe", "Ŕ":"R", "ŕ":"r",
				"Ŗ":"R", "ŗ":"r", "Ř":"R", "ř":"r", "Ś":"S", "ś":"s",
				"Ŝ":"S", "ŝ":"s", "Ş":"S", "ş":"s", "Š":"S", "š":"s",
				"Ţ":"T", "ţ":"t", "Ť":"T", "ť":"t", "Ŧ":"T", "ŧ":"t",
				"Ũ":"U", "ũ":"u", "Ū":"U", "ū":"u", "Ŭ":"U", "ŭ":"u",
				"Ů":"U", "ů":"u", "Ű":"U", "ű":"u", "Ų":"U", "ų":"u",
				"Ŵ":"W", "ŵ":"w", "Ŷ":"Y", "ŷ":"y", "Ÿ":"Y", "Ź":"Z",
				"ź":"z", "Ż":"Z", "ż":"z", "Ž":"Z", "ž":"z", "ſ":"s",
				"Ș":"S", "ș":"s", "Ț":"T", "ț":"t", "€":"E", "£":"",
				"Ơ":"O", "ơ":"o", "Ư":"U", "ư":"u", "Ầ":"A", "ầ":"a",
				"Ằ":"A", "ằ":"a", "Ề":"E", "ề":"e", "Ồ":"O", "ồ":"o",
				"Ờ":"O", "ờ":"o", "Ừ":"U", "ừ":"u", "Ỳ":"Y", "ỳ":"y",
				"Ả":"A", "ả":"a", "Ẩ":"A", "ẩ":"a", "Ẳ":"A", "ẳ":"a",
				"Ẻ":"E", "ẻ":"e", "Ể":"E", "ể":"e", "Ỉ":"I", "ỉ":"i",
				"Ỏ":"O", "ỏ":"o", "Ổ":"O", "ổ":"o", "Ở":"O", "ở":"o",
				"Ủ":"U", "ủ":"u", "Ử":"U", "ử":"u", "Ỷ":"Y", "ỷ":"y",
				"Ẫ":"A", "ẫ":"a", "Ẵ":"A", "ẵ":"a", "Ẽ":"E", "ẽ":"e",
				"Ễ":"E", "ễ":"e", "Ỗ":"O", "ỗ":"o", "Ỡ":"O", "ỡ":"o",
				"Ữ":"U", "ữ":"u", "Ỹ":"Y", "ỹ":"y", "Ấ":"A", "ấ":"a",
				"Ắ":"A", "ắ":"a", "Ế":"E", "ế":"e", "Ố":"O", "ố":"o",
				"Ớ":"O", "ớ":"o", "Ứ":"U", "ứ":"u", "Ạ":"A", "ạ":"a",
				"Ậ":"A", "ậ":"a", "Ặ":"A", "ặ":"a", "Ẹ":"E", "ẹ":"e",
				"Ệ":"E", "ệ":"e", "Ị":"I", "ị":"i", "Ọ":"O", "ọ":"o",
				"Ộ":"O", "ộ":"o", "Ợ":"O", "ợ":"o", "Ụ":"U", "ụ":"u",
				"Ự":"U", "ự":"u", "Ỵ":"Y", "ỵ":"y", "ɑ":"a", "Ǖ":"U",
				"ǖ":"u", "Ǘ":"U", "ǘ":"u", "Ǎ":"A", "ǎ":"a", "Ǐ":"I",
				"ǐ":"i", "Ǒ":"O", "ǒ":"o", "Ǔ":"U", "ǔ":"u", "Ǚ":"U",
				"ǚ":"u", "Ǜ":"U", "ǜ":"u"
			];

			let str = strtr(str, accent_chars);
		}

		return str;
	}

	/**
	 * Quotes
	 *
	 * Straighten out various forms of curly quotes and apostrophes.
	 *
	 * @param array|string $str String.
	 * @return array|string String.
	 */
	public static function quotes(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::quotes(v);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);

		// Curly quotes.
		array quote_char_keys = [
			"\xC2\x82", "\xC2\x84", "\xC2\x8B", "\xC2\x91", "\xC2\x92",
			"\xC2\x93", "\xC2\x94", "\xC2\x9B", "\xC2\xAB", "\xC2\xBB",
			"\xE2\x80\x98", "\xE2\x80\x99", "\xE2\x80\x9A", "\xE2\x80\x9B",
			"\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x9E", "\xE2\x80\x9F",
			"\xE2\x80\xB9", "\xE2\x80\xBA"
		];

		array quote_char_values = [
			"'", "\"", "'", "'", "'", "\"", "\"", "'", "\"", "\"", "'",
			"'", "'", "'", "\"", "\"", "\"", "\"", "'", "'"
		];

		let str = str_replace(quote_char_keys, quote_char_values, str);
		return str;
	}

	/**
	 * Wrapper For strlen()
	 *
	 * @param string $str String.
	 * @return int String length.
	 */
	public static function strlen(const string str) -> int {
		if (function_exists("mb_strlen")) {
			return (int) mb_strlen(str, "UTF-8");
		}

		return (int) strlen(str);
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool First occurrence or false.
	 */
	public static function strpos(const string haystack, const string needle, const int offset=0) -> int | bool {
		if (function_exists("mb_strpos")) {
			return mb_strpos(haystack, needle, offset, "UTF-8");
		}

		return strpos(haystack, needle, offset);
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool Last occurrence or false.
	 */
	public static function strrpos(const string haystack, const string needle, const int offset=0) -> int | bool {
		if (function_exists("mb_strrpos")) {
			return mb_strrpos(haystack, needle, offset, "UTF-8");
		}

		return strrpos(haystack, needle, offset);
	}

	/**
	 * Strtolower
	 *
	 * @param array|string $str String.
	 * @param bool $strict Strict.
	 */
	public static function strtolower(var str, const bool strict=false) {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::strtolower(v, strict);
			}
			return str;
		}

		// Proceed if we have a string, or don't care about type
		// conversion.
		if (!strict || ("string" === typeof str)) {
			let str = (string) \Blobfolio\Cast::toString(str, true);

			if (!empty str) {
				if (
					function_exists("mb_strtolower") &&
					(
						!function_exists("mb_check_encoding") ||
						!mb_check_encoding($str, "ASCII")
					)
				) {
					// Hit the bulk of the conversion.
					let str = mb_strtolower(str, "UTF-8");

					// Replace some more.
					let str = str_replace(
						self::case_char_upper,
						self::case_char_lower,
						str
					);
				}
				else {
					let str = strtolower(str);
				}
			}
		}

		return str;
	}

	/**
	 * Strtoupper
	 *
	 * @param array|string $str String.
	 * @param bool $strict Strict.
	 */
	public static function strtoupper(var str, const bool strict=false) {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::strtoupper(v, strict);
			}
			return str;
		}

		// Proceed if we have a string, or don't care about type
		// conversion.
		if (!strict || ("string" === typeof str)) {
			let str = (string) \Blobfolio\Cast::toString(str, true);

			if (!empty str) {
				if (
					function_exists("mb_strtoupper") &&
					(
						!function_exists("mb_check_encoding") ||
						!mb_check_encoding($str, "ASCII")
					)
				) {
					// Hit the bulk of the conversion.
					let str = mb_strtoupper(str, "UTF-8");

					// Replace some more.
					let str = str_replace(
						self::case_char_lower,
						self::case_char_upper,
						str
					);
				}
				else {
					let str = strtoupper(str);
				}
			}
		}

		return str;
	}

	/**
	 * Wrapper For substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @return string String.
	 */
	public static function substr(const string str, const int start=0, const var length=null) -> string | bool {
		if (function_exists("mb_substr")) {
			return mb_substr(str, start, length, "UTF-8");
		}

		return substr(str, start, length);
	}

	/**
	 * Trim
	 *
	 * @param array|string $str String.
	 * @return array|string String.
	 */
	public static function trim(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::trim(v);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);
		return preg_replace("/(^\s+|\s+$)/u", "", str);
	}

	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond the native PHP
	 * functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function ucfirst(var str, const bool strict=false) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::ucfirst(v, strict);
			}
			return str;
		}

		// Proceed if we have a string, or don't care about type
		// conversion.
		if (!strict || ("string" === typeof str)) {
			let str = (string) \Blobfolio\Cast::toString(str, true);

			if (str) {
				if (
					function_exists("mb_substr") &&
					(
						!function_exists("mb_check_encoding") ||
						!mb_check_encoding(str, "ASCII")
					)
				) {
					string first = (string) self::substr(str, 0, 1);
					let first = self::strtoupper($first, false);
					let str = first . self::substr(str, 1, null);
				}
				else {
					let str = ucfirst(str);
				}
			}
		}

		return str;
	}

	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond the native PHP
	 * functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function ucwords(var str, const bool strict=false) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::ucwords(v, strict);
			}
			return str;
		}

		// Proceed if we have a string, or don't care about type
		// conversion.
		if (!strict || ("string" === typeof str)) {
			let str = (string) \Blobfolio\Cast::toString(str, true);

			if (str) {
				// The first letter.
				let str = preg_replace_callback(
					"/^(\p{L})/u",
					[__CLASS__, "ucwordsCallback1"],
					str
				);

				// Any letter following a dash, space, or forward slash.
				let str = preg_replace_callback(
					"/(\s|\p{Pd}|\/)(.)/u",
					[__CLASS__, "ucwordsCallback2"],
					str
				);
			}
		}

		return str;
	}

	/**
	 * UCWords First Letter Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	public static function ucwordsCallback1(array matches) -> string {
		return self::strtoupper(matches[0]);
	}

	/**
	 * UCWords First Letter (after dash) Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	public static function ucwordsCallback2(array matches) -> string {
		return matches[1] . self::strtoupper(matches[2]);
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
	public static function utf8(string str) -> string {
		// Easy bypass.
		if (empty str || is_numeric(str)) {
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

	/**
	 * Whitespace
	 *
	 * @param array|string $str String.
	 * @param int $newlines Newlines.
	 * @return array|string String.
	 */
	public static function whitespace(var str, const int newlines=0) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::whitespace(v, newlines);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);

		// If we aren't allowing new lines at all, we can do this
		// quickly.
		if (newlines <= 0) {
			let str = preg_replace("/\s+/u", " ", str);
			return trim(str);
		}

		// Convert different types of whitespace.
		let str = str_replace("\r\n", "\n", str);
		let str = preg_replace("/\v/u", "\n", str);

		// Go through line by line.
		let str = explode("\n", str);
		var k, v;
		for k, v in str {
			let str[k] = trim(preg_replace("/\s+/u", " ", v));
		}
		let str = implode("\n", str);
		let str = self::trim(str);

		// Cap newlines.
		let str = preg_replace(
			"/\n{" . (newlines + 1) . ",}/",
			str_repeat("\n", newlines),
			str
		);

		return str;
	}
}
