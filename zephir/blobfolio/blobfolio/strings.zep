//<?php
/**
 * Blobfolio: Strings
 *
 * String manipulation.
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Strings {
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

		let str = \Blobfolio\Cast::toString(str, true);
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

		let str = \Blobfolio\Cast::toString(str, true);

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

		let str = \Blobfolio\Cast::toString(str, true);

		let str = preg_replace("/^\s+/u", "", str);
		let str = preg_replace("/\s+$/u", "", str);

		return str;
	}

	/**
	 * Whitespace
	 *
	 * @param array|string $str String.
	 * @param int $newlines Newlines.
	 * @return array|string String.
	 */
	public static function whitespace(var str, int newlines=0) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::whitespace(v, newlines);
			}
			return str;
		}

		let str = \Blobfolio\Cast::toString(str, true);
		if (newlines < 0) {
			let newlines = 0;
		}

		// If we aren't allowing new lines at all, we can do this
		// quickly.
		if (!newlines) {
			let str = preg_replace("/\s+/u", " ", str);
			return self::trim(str);
		}

		// Convert different types of whitespace.
		let str = str_replace("\r\n", "\n", str);
		let str = preg_replace("/\v/u", "\n", str);

		// Go through line by line.
		let str = explode("\n", str);
		var k, v;
		for k, v in str {
			let str[k] = self::whitespace(v, 0);
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
