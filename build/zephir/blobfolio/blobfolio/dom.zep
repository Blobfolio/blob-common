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

	/**
	 * Linkify Text
	 *
	 * Make link-like text things clickable HTML links.
	 *
	 * @param string $str String.
	 * @param array $args Arguments.
	 * @param int $pass Pass (1=URL, 2=EMAIL).
	 *
	 * @arg array $class Class(es).
	 * @arg string $rel Rel.
	 * @arg string $target Target.
	 *
	 * @return void Nothing.
	 */
	public static function linkify(string str, var args=null, const int pass=1) -> string {
		// Ignore bad values.
		if (pass < 1 || pass > 3) {
			return str;
		}

		// Build link attributes.
		let args = (array) Cast::parseArgs(
			args,
			["class": [], "rel": "", "target": ""]
		);

		// Make classes easier to deal with.
		if (count(args["class"])) {
			let args["class"] = (array) Arrays::fromList(args["class"], " ");
			let args["class"] = (string) implode(" ", args["class"]);
		}
		else {
			let args["class"] = "";
		}

		var atts;
		array chunks = (array) preg_split(
			"/(<.+?>)/is",
			str,
			0,
			PREG_SPLIT_DELIM_CAPTURE
		);
		string ignoring = "";
		var k;
		var v;

		// Generate attributes for insertion.
		let atts = [];
		for k, v in args {
			if (!empty v) {
				let atts[] = k . "=\"" . v . "\"";
			}
		}
		let atts = (string) implode(" ", atts);
		if (!empty atts) {
			let atts = " " . atts;
		}

		// Loop the chunks!
		for k, v in chunks {
			// Even keys exist between tags.
			if (0 === k % 2) {
				// Skip if we are waiting for a closing tag.
				if (!empty ignoring) {
					continue;
				}

				switch (pass) {
					// URL bits.
					case 1:
						let chunks[k] = (string) preg_replace_callback(
							"/((ht|f)tps?:\/\/[^\s'\"\[\]\(\){}]+|[^\s'\"\[\]\(\){}]*xn--[^\s'\"\[\]\(\){}]+|[@]?[\w.]+\.[\w\.]{2,}[^\s]*)/ui",
							[__CLASS__, "linkifyCallback1"],
							v
						);

						break;
					// Email bits.
					case 2:
						let chunks[k] = (string) preg_replace_callback(
							"/([\w\.\!#\$%&\*\+\=\?_~]+@[^\s'\"\[\]\(\)\{\}@]{2,})/ui",
							[__CLASS__, "linkifyCallback2"],
							v
						);

						break;
					// Phone bits.
					case 3:
						let chunks[k] = (string) preg_replace_callback(
							"/(\s)?(\+\d[\d\-\s]{5,}+|\(\d{3}\)\s[\d]{3}[\-\.\s]\d{4}|\d{3}[\-\.\s]\d{3}[\-\.\s]\d{4}|\+\d{7,})/ui",
							[__CLASS__, "linkifyCallback3"],
							v
						);

						break;
				}

				let chunks[k] = (string) str_replace(
					"%BLOBCOMMON_ATTS%",
					atts,
					chunks[k]
				);
			}
			// Odd keys indicate a tag, opening or closing.
			else {
				// We are looking for an opening tag.
				if (empty ignoring) {
					var matches;
					preg_match(
						"/<(a|audio|button|code|embed|frame|head|link|object|picture|pre|script|select|style|svg|textarea|video).*(?<!\/)>$/is",
						v,
						matches
					);
					if (("array" === typeof matches) && count(matches) >= 2) {
						let ignoring = (string) preg_quote(matches[1], "/");
					}
				}
				// Wait for a closing tag.
				elseif (preg_match("/<\/\s*" . ignoring . "/i", v)) {
					let ignoring = "";
				}
			}
		}

		let str = (string) implode("", chunks);

		// Linkification is run in stages to prevent overlap issues.
		// Pass #1 is for URL-like bits, #2 for email addresses, and #3
		// for phone numbers.
		if (pass < 3) {
			return self::linkify(str, args, pass + 1);
		}

		// We're done!
		return str;
	}

	/**
	 * Linkify Stage One Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback1(array matches) -> string {
		string raw = (string) matches[1];
		string suffix = "";
		var domain;
		var link;
		var match;

		// Don't do email bits.
		if (0 === strpos(raw, "@")) {
			return matches[1];
		}

		// We don't want trailing punctuation added to the link.
		preg_match("/([^\w\/]+)$/ui", raw, match);
		if (("array" === typeof match) && count(match) >= 2) {
			let suffix = (string) match[1];
			let raw = preg_replace("/([^\w\/]+)$/ui", "", raw);
		}

		// Make sure we have something URL-esque.
		let link = Domains::parseUrl(raw);
		if (("array" !== typeof link) || !isset(link["host"])) {
			return matches[1];
		}

		// Only linkify FQDNs.
		let domain = new Domains(link["host"]);
		if (!domain->isValid() || !domain->isFqdn()) {
			return matches[1];
		}

		// Supply a scheme if missing.
		if (!isset(link["scheme"])) {
			let link["scheme"] = "http";
		}

		let link = (string) Domains::unparseUrl(link);
		if (filter_var(link, FILTER_SANITIZE_URL) !== link) {
			return matches[1];
		}

		// Finally, make a link!
		let link = self::html(link);
		return "<a href=\"" . link . "\"%BLOBCOMMON_ATTS%>" . raw . "</a>" . suffix;
	}

	/**
	 * Linkify Stage Two Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback2(array matches) -> string {
		string email;
		string raw = (string) matches[1];
		string suffix = "";
		var match;

		// We don't want trailing punctuation added to the link.
		preg_match("#([^\w]+)$#ui", raw, match);
		if (("array" === typeof match) && count(match) >= 2) {
			let suffix = (string) match[1];
			let raw = preg_replace("#([^\w]+)$#ui", "", raw);
		}

		let email = (string) Domains::niceEmail(raw);
		if (empty email) {
			return matches[1];
		}

		// Finally, make a link!
		let email = self::html(email);
		return "<a href=\"mailto:" . email . "\"%BLOBCOMMON_ATTS%>" . raw . "</a>" . suffix;
	}

	/**
	 * Linkify Stage Three Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback3(array matches) -> string {
		string phone;
		string prefix = (string) matches[1];
		string raw = (string) matches[2];
		string suffix = "";
		var match;

		preg_match("/([^\d]+)$/ui", raw, match);
		if (("array" === typeof match) && count(match) >= 2) {
			let suffix = (string) match[1];
			let raw = preg_replace("/([^\d]+)$/ui", "", raw);
		}

		let phone = (string) Phones::nicePhone(raw);
		let phone = preg_replace("/[^\d]/", "", phone);
		if (empty phone) {
			return matches[1] . matches[2];
		}

		return prefix . "<a href=\"tel:+" . phone . "\"%BLOBCOMMON_ATTS%>" . raw . "</a>" . suffix;
	}



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
