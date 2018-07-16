//<?php
/**
 * Blobfolio: Numbers
 *
 * Number manipulation.
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

final class Dom {
	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------



	// -----------------------------------------------------------------
	// Sanitizing
	// -----------------------------------------------------------------

	/**
	 * Decode JS Entities
	 *
	 * Decode escape and unicode chars.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeJsEntities(string str) -> string {
		let str = self::decodeUnicodeEntities(str);
		return self::decodeEscapeEntities(str);
	}

	/**
	 * Decode Escape Entities
	 *
	 * Decode \b, \f, \n, \r, \t.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeEscapeEntities(string str) -> string {
		let str = Strings::utf8(str);

		array from = [
			"\\b",
			"\\f",
			"\\n",
			"\\r",
			"\\t"
		];
		array to = [
			chr(0x08),
			chr(0x0C),
			chr(0x0A),
			chr(0x0D),
			chr(0x09)
		];

		return str_replace(from, to, str);
	}

	/**
	 * Decode Unicode Entities
	 *
	 * Decode \u1234 into chars.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeUnicodeEntities(string str) -> string {
		let str = Strings::utf8(str);

		string last = "";
		while (str !== last) {
			let last = str;

			let str = preg_replace_callback(
				"/\\\u([0-9A-Fa-f]{4})/u",
				[__CLASS__, "decodeHexEntities"],
				str
			);

			let str = Strings::utf8(str);
		}

		return str;
	}

	/**
	 * Decode HTML Entities
	 *
	 * Decode all HTML entities back into their char counterparts,
	 * recursively until every last one is captured.
	 *
	 * @param string $str String.
	 * @return void Nothing.
	 */
	public static function decodeEntities(string str) -> string {
		let str = Strings::utf8(str);

		string last = "";
		while (str !== last) {
			let last = str;

			let str = html_entity_decode(str, ENT_QUOTES, "UTF-8");
			let str = preg_replace_callback(
				"/&#([0-9]+);/",
				[__CLASS__, "decodeChrEntities"],
				str
			);
			let str = preg_replace_callback(
				"/&#[Xx]([0-9A-Fa-f]+);/",
				[__CLASS__, "decodeHexEntities"],
				str
			);

			let str = Strings::utf8(str);
		}

		return str;
	}

	/**
	 * Decode HTML Entities Callback - Chr
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	private static function decodeChrEntities(array matches) -> string {
		return chr(matches[1]);
	}

	/**
	 * Decode HTML Entities Callback - Hex
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	private static function decodeHexEntities(array matches) -> string {
		return chr(hexdec(matches[1]));
	}

	/**
	 * HTML
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function html(string str) -> string {
		let str = Strings::utf8(str);
		return htmlspecialchars(str, ENT_QUOTES | ENT_HTML5, "UTF-8");
	}

	/**
	 * JS
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @return string JS.
	 */
	public static function js(string str, string quote="'") -> string {
		let str = Strings::whitespace(str);
		let str = Strings::quotes(str);

		// Escape slashes.
		let str = str_replace("/", "\\/", str);

		if ("'" === quote) {
			let str = str_replace("'", "\\'", str);
		}
		elseif ("\"" === quote) {
			let str = str_replace("\"", "\\\"", str);
		}

		return str;
	}
}
