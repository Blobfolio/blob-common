//<?php
/**
 * Blobfolio: DOM
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

final class Dom {
	const SVG_HEADER = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">";
	const SVG_NAMESPACE = "http://www.w3.org/2000/svg";

	// User settable.
	public static $whitelistAttributes;
	public static $whitelistDomains;
	public static $whitelistProtocols;
	public static $whitelistTags;



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Nice Attribute Value
	 *
	 * Note: this is for decoding entities, stripping control
	 * characters, etc., NOT for pushing an arbitrary string between
	 * quotes in HTML. For that, use ::html()
	 *
	 * @param string $str String.
	 * @param bool $trusted Trusted.
	 * @return string String.
	 */
	public static function attributeValue(string str, const bool trusted=false) -> string {
		if (!trusted) {
			let str = \Blobfolio\Strings::utf8(str);
		}
		let str = \Blobfolio\Strings::controlChars(str, true);
		let str = self::decodeEntities(str);
		return \Blobfolio\Strings::trim(str, true);
	}

	/**
	 * Nice IRI Value
	 *
	 * @param string $str String.
	 * @param bool $trusted Trusted.
	 * @return string String.
	 */
	public static function iriValue(string str, const bool trusted=false) -> string {
		// Remove vertical whitespace.
		let str = self::attributeValue(str, trusted);
		let str = \Blobfolio\Strings::whitespace(str, true);

		// Early abort.
		if (empty str) {
			return "";
		}

		// Assign a protocol if missing.
		if (0 === strpos(str, "//")) {
			let str = "https:" . str;
		}

		// Check protocols.
		string test = (string) \Blobfolio\Strings::toLower(str, true);
		let test = preg_replace("/\s/u", "", test);
		if (strpos(test, ":")) {
			let test = strstr(test, ":", true);
			array protocols = (array) self::whitelistProtocols();
			if (!isset(protocols[test])) {
				return "";
			}
		}

		// Abort if not URLish.
		if (filter_var(str, FILTER_SANITIZE_URL) !== str) {
			return "";
		}

		// Check the domain if applicable.
		if (preg_match("/^[\w\d]+:\/\//ui", str)) {
			let test = (string) \Blobfolio\Domains::niceDomain(str);
			if (empty test) {
				return "";
			}

			array domains = (array) self::whitelistDomains();
			if (!isset(domains[test])) {
				return "";
			}
		}

		return str;
	}

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
		let args = (array) \Blobfolio\Cast::parseArgs(
			args,
			["class": [], "rel": "", "target": ""]
		);

		// Make classes easier to deal with.
		if (count(args["class"])) {
			let args["class"] = (array) \Blobfolio\Arrays::fromList(args["class"], " ");
			let args["class"] = (string) implode(" ", args["class"]);
		}
		else {
			let args["class"] = "";
		}

		// Correct any weird UTF8 issues on the first pass.
		if (1 === pass) {
			let str = \Blobfolio\Strings::utf8(str);
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
		let link = \Blobfolio\Domains::parseUrl(raw);
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

		let link = (string) \Blobfolio\Domains::unparseUrl(link);
		if (filter_var(link, FILTER_SANITIZE_URL) !== link) {
			return matches[1];
		}

		// Finally, make a link!
		let link = self::html(link, true);
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

		let email = (string) \Blobfolio\Domains::niceEmail(raw);
		if (empty email) {
			return matches[1];
		}

		// Finally, make a link!
		let email = self::html(email, true);
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

		let phone = (string) \Blobfolio\Phones::nicePhone(raw);
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
		let str = \Blobfolio\Strings::utf8(str);

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
		let str = \Blobfolio\Strings::utf8(str);

		string last = "";
		while (str !== last) {
			let last = str;

			let str = preg_replace_callback(
				"/\\\u([0-9A-Fa-f]{4})/u",
				[__CLASS__, "decodeHexEntities"],
				str
			);

			let str = \Blobfolio\Strings::utf8(str);
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
		let str = \Blobfolio\Strings::utf8(str);

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

			let str = \Blobfolio\Strings::utf8(str);
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
	 * @param bool $trusted Trusted.
	 * @return string String.
	 */
	public static function html(string str, const bool trusted=false) -> string {
		if (!trusted) {
			let str = \Blobfolio\Strings::utf8(str);
		}

		return htmlspecialchars(str, ENT_QUOTES | ENT_HTML5, "UTF-8");
	}

	/**
	 * JS
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @param bool $trusted Trusted.
	 * @return string JS.
	 */
	public static function js(string str, string quote="'", const bool trusted=false) -> string {
		let str = \Blobfolio\Strings::whitespace(str, 0, trusted);
		let str = \Blobfolio\Strings::quotes(str, true);

		if ("'" === quote) {
			let str = str_replace(
				["/", "'"],
				["\\/", "\\'"],
				str
			);
		}
		elseif ("\"" === quote) {
			let str = str_replace(
				["/", "\""],
				["\\/", "\\\""],
				str
			);
		}

		return str;
	}



	// -----------------------------------------------------------------
	// Conversion and Helpers
	// -----------------------------------------------------------------

	/**
	 * SVG to DOM
	 *
	 * @param string $svg SVG code.
	 * @param bool $trusted Trusted.
	 * @return bool|DOMDocument DOM object or false.
	 */
	public static function svgToDom(string svg, const bool trusted=false) -> bool | <\DOMDocument> {
		if (!trusted) {
			let svg = \Blobfolio\Strings::utf8(svg);
		}

		// At the very least we expect tags.
		var start = mb_stripos(svg, "<svg", 0, "UTF-8");
		var end = mb_strripos(svg, "</svg>", 0, "UTF-8");
		if (
			(false === start) ||
			(false === end) ||
			(end < start)
		) {
			return false;
		}

		// Chop it if needed.
		let svg = mb_substr(svg, start, (end - start + 6), "UTF-8");

		// Get rid of some stupid Illustrator problems.
		array replace_keys = [
			"xmlns=\"&ns_svg;\"",
			"xmlns:xlink=\"&ns_xlink;\"",
			"id=\"Layer_1\""
		];
		array replace_values = [
			"xmlns=\"http://www.w3.org/2000/svg\"",
			"xmlns:xlink=\"http://www.w3.org/1999/xlink\"",
			""
		];
		let svg = str_replace(replace_keys, replace_values, svg);

		// Remove XML, PHP, ASP, comments, etc.
		if (false !== strpos(svg, "<?")) {
			let svg = preg_replace("/<\?(.*)\?>/Us", "", svg);
		}
		if (false !== strpos(svg, "<%")) {
			let svg = preg_replace("/<\%(.*)\%>/Us", "", svg);
		}
		if (false !== strpos(svg, "<!--")) {
			let svg = preg_replace("/<!--(.*)-->/Us", "", svg);
		}
		if (false !== strpos(svg, "/*")) {
			let svg = preg_replace("/\/\*(.*)\*\//Us", "", svg);
		}

		// If there are any opening comments still around, we're done.
		if ((false !== strpos(svg, "<!--")) || (false !== strpos(svg, "/*"))) {
			return false;
		}

		// Add the SVG header back to help DOMDocument correctly read
		// the file.
		let svg = self::SVG_HEADER . "\n" . svg;

		// Open it.
		libxml_use_internal_errors(true);
		libxml_disable_entity_loader(true);
		var dom;
		let dom = new \DOMDocument("1.0", "UTF-8");
		let dom->formatOutput = false;
		let dom->preserveWhiteSpace = false;
		dom->loadXML(svg);

		// Make sure there's a tag.
		if (dom->getElementsByTagName("svg")->length === 0) {
			return false;
		}

		return dom;
	}

	/**
	 * DOM to SVG
	 *
	 * @param DOMDocument $dom Dom.
	 * @return string SVG.
	 */
	public static function domToSvg(<\DOMDocument> dom) -> string {
		var tags;
		let tags = <\DOMNodeList> dom->getElementsByTagName("svg");
		if (tags->length === 0) {
			return "";
		}

		string svg = (string) tags->item(0)->ownerDocument->saveXML(
			tags->item(0),
			LIBXML_NOBLANKS
		);

		// Make sure an XMLNS exists and is correct. We can't alter
		// that in DOMDocument, unfortunately.
		let svg = preg_replace(
			"/xmlns\s*=\s*\"[^\"]*\"/",
			"xmlns=\"" . self::SVG_NAMESPACE . "\"",
			svg
		);

		// One more pass to remove scripts and shit.
		if (false !== strpos(svg, "<?")) {
			let svg = preg_replace("/<\?(.*)\?>/Us", "", svg);
		}
		if (false !== strpos(svg, "<%")) {
			let svg = preg_replace("/<\%(.*)\%>/Us", "", svg);
		}
		if (false !== strpos(svg, "<!--")) {
			let svg = preg_replace("/<!--(.*)-->/Us", "", svg);
		}
		if (false !== strpos(svg, "/*")) {
			let svg = preg_replace("/\/\*(.*)\*\//Us", "", svg);
		}

		// If there are any opening comments still around, we're done.
		if ((false !== strpos(svg, "<!--")) || (false !== strpos(svg, "/*"))) {
			return "";
		}

		// Find the start and end tags so we can send what matters.
		var start = mb_stripos(svg, "<svg", 0, "UTF-8");
		var end = mb_strripos(svg, "</svg>", 0, "UTF-8");
		if (
			(false === start) ||
			(false === end) ||
			(end < start)
		) {
			return "";
		}

		// Chop it if needed.
		return mb_substr(svg, start, (end - start + 6), "UTF-8");
	}

	/**
	 * Get Nodes By Class
	 *
	 * This will return an array of DOMNode objects containing the
	 * specified class(es). This does not use DOMXPath.
	 *
	 * @param mixed $parent Parent.
	 * @param mixed $class Classes.
	 * @param bool $all Must match all rather than any.
	 * @return array Nodes.
	 */
	public static function getNodesByClass(var parent, array classes, const bool all=false) -> array {
		if (!method_exists(parent, "getElementsByTagName")) {
			return [];
		}

		let classes = \Blobfolio\Arrays::flatten(classes);
		var k;
		var v;
		for k, v in classes {
			let classes[k] = ltrim(v, ".");
			if (empty classes[k]) {
				unset(classes[k]);
			}
		}

		int classesLength = (int) count(classes);
		if (!classesLength) {
			return [];
		}

		let classes = array_unique(classes);
		sort(classes);

		array nodes = [];
		var tags;

		let tags = parent->getElementsByTagName("*");
		if (tags->length) {
			int x = 0;
			while x < tags->length {
				if (tags->item(x)->hasAttribute("class")) {
					// Parse this tag's classes.
					var class_value;
					let class_value = tags->item(x)->getAttribute("class");
					let class_value = \Blobfolio\Strings::whitespace(class_value, 0);
					let class_value = (array) explode(" ", class_value);

					// Find the intersect.
					array intersect = (array) array_intersect(classes, class_value);
					int intersectLength = (int) count(intersect);

					if (
						intersectLength &&
						(!all || (intersectLength === classesLength))
					) {
						let nodes[] = tags->item(x);
					}
				}

				let x++;
			}
		}

		return nodes;
	}

	/**
	 * InnerHTML
	 *
	 * Return the "innerHTML" of a DOMNode or DOMElement.
	 *
	 * @param mixed $node Node.
	 * @param bool $xml Use saveXML instead of saveHTML.
	 * @param int $flags Additional flags (XML only).
	 * @return string Content.
	 */
	public static function innerHtml(var node, const bool xml=false, var flags=null) -> string {
		if (
			!is_a(node, "\\DOMElement") &&
			!is_a(node, "\\DOMNode")
		) {
			return "";
		}

		string out = "";
		if (node->childNodes->length) {
			int x = 0;
			if (xml) {
				while x < node->childNodes->length {
					if (is_int(flags)) {
						let out .= node->ownerDocument->saveXML(
							node->childNodes->item(x),
							flags
						);
					}
					else {
						let out .= node->ownerDocument->saveXml(node->childNodes->item(x));
					}

					let x++;
				}
			}
			else {
				while x < node->childNodes->length {
					let out .= node->ownerDocument->saveHTML(node->childNodes->item(x));
					let x++;
				}
			}
		}

		return out;
	}

	/**
	 * Merge Classes
	 *
	 * The HTML "class" attribute frequently requires sanitization and
	 * merging. This function takes any number of arguments, each either
	 * a string or array, and returns a single array containing each
	 * unique class.
	 *
	 * @param mixed $classes Classes.
	 * @return array Classes.
	 */
	public static function mergeClasses() -> array {
		array args = (array) func_get_args();
		array out = [];
		var v;
		var v2;

		// Run through each and add as needed.
		for v in args {
			let v = \Blobfolio\Arrays::fromList(v, " ");
			for v2 in v {
				// Strip obviously bad characters.
				let v2 = preg_replace(
					"/[^a-z\d_:\{\}-]/",
					"",
					strtolower(v2)
				);

				if (!empty v2) {
					let out[v2] = true;
				}
			}
		}

		return array_keys(out);
	}

	/**
	 * Parse Styles
	 *
	 * This will convert CSS text (from e.g. a <style> tag) into an
	 * array broken down by rules and selectors.
	 *
	 * Note: This can deal with "proper" CSS, but has trouble with some
	 * of the new kid shit like using {} in class names.
	 *
	 * @param string $styles Styles.
	 * @param bool $trusted Trusted.
	 * @return array Parsed styles.
	 */
	public static function parseCss(string css, const bool trusted=false) -> array {
		if (!trusted) {
			let css = \Blobfolio\Strings::utf8(css);
		}

		var start;

		// Check for comments.
		if (false !== strpos(css, "/*")) {
			let css = preg_replace("/\/\*(.*)\*\//Us", "", css);
		}
		let start = mb_strpos(css, "/*", 0, "UTF-8");
		if (false !== start) {
			let css = mb_substr(css, 0, start, "UTF-8");
		}

		// Get rid of non-style sister comments and markers.
		let css = str_replace(
			["<!--", "//-->", "//<![CDATA[", "//]]>", "<![CDATA[", "]]>"],
			"",
			css
		);

		// Clean up characters a bit.
		let css = \Blobfolio\Strings::niceText(css, true);

		// Early bail.
		if (empty css) {
			return [];
		}

		// Substitute braces for unlikely characters to make parsing
		// easier hopefully nobody's using braille in their
		// stylesheets...
		let css = preg_replace(
			"/\{(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
			"⠁",
			css
		);
		let css = preg_replace(
			"/\}(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
			"⠈",
			css
		);

		// Make sure there rae spaces before and after parentheses.
		let css = preg_replace(
			"/\s*(\()\s*(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
			" (",
			css
		);
		let css = preg_replace(
			"/\s*(\))\s*(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
			") ",
			css
		);

		// Make sure {} have no whitespace on either end.
		let css = preg_replace("/\s*(⠁|⠈|@)\s*/u", "$1", css);

		// Push @ rules to their own lines.
		let css = str_replace("@", "\n@", css);

		array styles = (array) explode("\n", css);
		array tmp;
		int x;
		var k;
		var v;

		for k, v in styles {
			let styles[k] = trim(v);
			if (empty styles[k]) {
				unset(styles[k]);
				continue;
			}

			// An @ rule.
			if (0 === strpos(styles[k], "@")) {
				// Nested, like @media.
				if (false !== strpos(styles[k], "⠈⠈")) {
					let styles[k] = preg_replace(
						"/(⠈{2,})/u",
						"$1\n",
						styles[k]
					);
				}
				// Not nested, but has properties like @font-face.
				elseif (false !== strpos(styles[k], "⠈")) {
					let styles[k] = str_replace("⠈", "⠈\n", styles[k]);
				}
				// A one-liner, like @import.
				elseif (preg_match(
					"/;(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/",
					styles[k]
				)) {
					let styles[k] = preg_replace(
						"/;(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
						";\n",
						styles[k],
						1
					);
				}

				// Clean up what we have.
				let tmp = (array) explode("\n", styles[k]);
				let x = 1;
				while x < count(tmp) {
					let tmp[x] = str_replace("⠈", "⠈\n", tmp[x]);
					let x++;
				}
				let styles[k] = implode("\n", tmp);
			}
			// Just regular stuff.
			else {
				let styles[k] = str_replace("⠈", "⠈\n", styles[k]);
			}
		}

		// Back to a string.
		let css = (string) implode("\n", styles);

		// One more quick formatting thing, we can get rid of spaces
		// between closing) and punctuation.
		let css = preg_replace(
			"/\)\s(,|;)(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
			")$1",
			css
		);

		// And between RGB/URL stuff.
		let css = preg_replace("/(url|rgba?)\s+\(/", "$1(", css);

		// One more time around.
		array matches;
		array out = [];
		array rules;
		array tmp2;
		string chunk;
		string key;
		string value;
		var k2;
		var v2;

		let styles = (array) explode("\n", css);
		for k, v in styles {
			let styles[k] = trim(v);
			if (empty styles[k]) {
				continue;
			}

			// Nested rule.
			if (
				(0 === strpos(styles[k], "@")) &&
				(false !== strpos(styles[k], "⠈⠈"))
			) {
				let tmp = [
					"@": false,
					"nested": true,
					"selector": "",
					"nest": [],
					"raw": ""
				];

				// What kind of @ is this?
				preg_match_all("/^@([a-z\-]+)/ui", styles[k], matches);
				let tmp["@"] = \Blobfolio\Strings::toLower(matches[1][0], true);

				let start = mb_strpos(styles[k], "⠁", 0, "UTF-8");
				if (false === start) {
					continue;
				}

				let tmp["selector"] = \Blobfolio\Strings::toLower(
					trim(mb_substr(styles[k], 0, start, "UTF-8")),
					true
				);

				let chunk = (string) mb_substr(styles[k], start + 1, -1, "UTF-8");
				let chunk = str_replace(
					["⠁", "⠈"],
					["{", "}"],
					chunk
				);
				let tmp["nest"] = self::parseCss(chunk, true);

				// And build the raw.
				let tmp["raw"] = tmp["selector"] . "{";
				for v2 in tmp["nest"] {
					let tmp["raw"] .= v2["raw"];
				}
				let tmp["raw"] .= "}";
			}
			else {
				let tmp = [
					"@": false,
					"nested": false,
					"selectors": [],
					"rules": [],
					"raw": ""
				];

				if (0 === strpos(styles[k], "@")) {
					// What kind of @ is this?
					preg_match_all("/^@([a-z\-]+)/ui", styles[k], matches);
					let tmp["@"] = \Blobfolio\Strings::toLower(matches[1][0], true);
				}

				// A normal {k:v, k:v}
				preg_match_all("/^([^⠁]+)⠁([^⠈]*)⠈/u", styles[k], matches);
				if (count(matches[0])) {
					// Sorting selectors is easy.
					let tmp["selectors"] = (array) explode(",", matches[1][0]);
					let tmp["selectors"] = array_map("trim", tmp["selectors"]);

					// Rules are trickier.
					let rules = (array) explode(";", matches[2][0]);
					for k2, v2 in rules {
						let rules[k2] = trim(v2);
						if (empty rules[k2]) {
							continue;
						}

						let rules[k2] = rtrim(rules[k2], ";") . ";";
						if (preg_match(
							"/:(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/",
							rules[k2]
						)) {
							let rules[k2] = preg_replace(
								"/:(?![^\"]*\"(?:(?:[^\"]*\"){2})*[^\"]*$)/u",
								"\n",
								rules[k2],
								1
							);

							let tmp2 = (array) explode("\n", rules[k2]);
							let key = (string) \Blobfolio\Strings::toLower(trim(tmp2[0]), true);
							let value = trim(tmp2[1]);
							let tmp["rules"][key] = value;
						}
						else {
							let tmp["rules"]["__NONE__"] = (string) v2;
						}
					}

					// Build the raw.
					string raw = (string) implode(",", tmp["selectors"]) . "{";
					for k2, v2 in tmp["rules"] {
						if ("__NONE__" === k2) {
							let raw .= v2;
						}
						else {
							let raw .= k2 . ":" . v2;
						}
					}
					let raw .= "}";
					let tmp["raw"] = raw;
				}
				// This is something strange.
				else {
					let styles[k] = str_replace(
						["⠁", "⠈"],
						["{", "}"],
						styles[k]
					);
					let styles[k] = trim(rtrim(styles[k], ";"));
					if ("}" !== substr(styles[k], -1)) {
						let styles[k] .= ";";
					}
					let tmp["rules"][] = styles[k];
					let tmp["raw"] = styles[k];
				}
			}

			let out[] = tmp;
		}

		return out;
	}

	/**
	 * Remove namespace (and attached nodes) from a DOMDocument
	 *
	 * @param \DOMDocument $dom Object.
	 * @param string $namespace Namespace.
	 * @return bool True/False.
	 */
	public static function removeNamespace(<\DOMDocument> dom, const string ns) -> bool {
		if (empty ns) {
			return false;
		}

		var xpath;
		let xpath = new \DOMXPath(dom);

		var nodes;
		let nodes = xpath->query("//*[namespace::" . ns . " and not(../namespace::" . ns . ")]");

		int x = 0;
		while x < nodes->length {
			nodes->item(x)->removeAttributeNS(
				nodes->item(x)->lookupNamespaceURI(ns),
				ns
			);
			let x++;
		}

		return true;
	}

	/**
	 * Remove Nodes
	 *
	 * @param \DOMNodeList $nodes Nodes.
	 * @return bool True/false.
	 */
	public static function removeNodes(<\DOMNodeList> nodes) -> bool {
		while (nodes->length) {
			self::removeNode(nodes->item(0));
		}

		return true;
	}

	/**
	 * Remove Node
	 *
	 * @param mixed $node Node.
	 * @return bool True/false.
	 */
	public static function removeNode(var node) -> bool {
		if (
			!is_a(node, "\\DOMNode") &&
			!is_a(node, "\\DOMElement")
		) {
			return false;
		}

		node->parentNode->removeChild(node);
		return true;
	}



	// -----------------------------------------------------------------
	// Whitelist
	// -----------------------------------------------------------------

	/**
	 * IRI Attributes
	 *
	 * Not a whitelist, per se, but IRI is handled along with domains,
	 * protocols, etc.
	 *
	 * @return array Attributes.
	 */
	public static function iriAttributes() -> array {
		return [
			"href": true,
			"src": true,
			"xlink:arcrole": true,
			"xlink:href": true,
			"xlink:role": true,
			"xml:base": true,
			"xmlns": true,
			"xmlns:xlink": true
		];
	}

	/**
	 * Whitelisted Domains
	 *
	 * For SVGs and other IRI-type fields, these domains are A-OK.
	 *
	 * @return array Domains.
	 */
	public static function whitelistDomains() -> array {
		// Our defaults.
		array out = [
			"creativecommons.org": true,
			"inkscape.org": true,
			"sodipodi.sourceforge.net": true,
			"w3.org": true
		];

		// Add user domains.
		if (
			("array" === typeof self::$whitelistDomains) &&
			count(self::$whitelistDomains)
		) {
			var v;
			for v in self::$whitelistDomains {
				if (("string" === typeof v) && !empty v) {
					string host = (string) \Blobfolio\Domains::niceDomain(v);
					if (!empty host) {
						let out[host] = true;
					}
				}
			}

			ksort(out);
		}

		return out;
	}

	/**
	 * Whitelisted Protocols
	 *
	 * For SVGs and other IRI-type fields, these protocols are A-OK.
	 *
	 * @return array Protocols.
	 */
	public static function whitelistProtocols() -> array {
		// Our defaults.
		array out = [
			"http": true,
			"https": true
		];

		// Add user protocols.
		if (
			("array" === typeof self::$whitelistProtocols) &&
			count(self::$whitelistProtocols)
		) {
			var v;
			string protocol;
			for v in self::$whitelistProtocols {
				if (("string" === typeof v) && !empty v) {
					let protocol = (string) rtrim(strtolower(v), ":");
					let protocol = preg_replace("/[^a-z\d_-]/", "", protocol);
					if (!empty protocol) {
						let out[protocol] = true;
					}
				}
			}

			ksort(out);
		}

		return out;
	}

	/**
	 * Whitelisted Attributes
	 *
	 * These are the attributes allowed by SVGs.
	 *
	 * @return array Attributes.
	 */
	public static function whitelistAttributes() -> array {
		// Our defaults.
		array out = [
			"accent-height": true,
			"accumulate": true,
			"additive": true,
			"alignment-baseline": true,
			"allowreorder": true,
			"alphabetic": true,
			"amplitude": true,
			"arabic-form": true,
			"ascent": true,
			"attributename": true,
			"attributetype": true,
			"autoreverse": true,
			"azimuth": true,
			"basefrequency": true,
			"baseline-shift": true,
			"baseprofile": true,
			"bbox": true,
			"begin": true,
			"bias": true,
			"by": true,
			"calcmode": true,
			"cap-height": true,
			"class": true,
			"clip": true,
			"clip-path": true,
			"clip-rule": true,
			"clippathunits": true,
			"color": true,
			"color-interpolation": true,
			"color-interpolation-filters": true,
			"color-profile": true,
			"color-rendering": true,
			"contentstyletype": true,
			"cursor": true,
			"cx": true,
			"cy": true,
			"d": true,
			"decelerate": true,
			"descent": true,
			"diffuseconstant": true,
			"direction": true,
			"display": true,
			"divisor": true,
			"dominant-baseline": true,
			"dur": true,
			"dx": true,
			"dy": true,
			"edgemode": true,
			"elevation": true,
			"enable-background": true,
			"end": true,
			"exponent": true,
			"externalresourcesrequired": true,
			"fill": true,
			"fill-opacity": true,
			"fill-rule": true,
			"filter": true,
			"filterres": true,
			"filterunits": true,
			"flood-color": true,
			"flood-opacity": true,
			"font-family": true,
			"font-size": true,
			"font-size-adjust": true,
			"font-stretch": true,
			"font-style": true,
			"font-variant": true,
			"font-weight": true,
			"format": true,
			"from": true,
			"fx": true,
			"fy": true,
			"g1": true,
			"g2": true,
			"glyph-name": true,
			"glyph-orientation-horizontal": true,
			"glyph-orientation-vertical": true,
			"glyphref": true,
			"gradienttransform": true,
			"gradientunits": true,
			"hanging": true,
			"height": true,
			"horiz-adv-x": true,
			"horiz-origin-x": true,
			"href": true,
			"id": true,
			"ideographic": true,
			"image-rendering": true,
			"in": true,
			"in2": true,
			"intercept": true,
			"k": true,
			"k1": true,
			"k2": true,
			"k3": true,
			"k4": true,
			"kernelmatrix": true,
			"kernelunitlength": true,
			"kerning": true,
			"keypoints": true,
			"keysplines": true,
			"keytimes": true,
			"lang": true,
			"lengthadjust": true,
			"letter-spacing": true,
			"lighting-color": true,
			"limitingconeangle": true,
			"local": true,
			"marker-end": true,
			"marker-mid": true,
			"marker-start": true,
			"markerheight": true,
			"markerunits": true,
			"markerwidth": true,
			"mask": true,
			"maskcontentunits": true,
			"maskunits": true,
			"mathematical": true,
			"max": true,
			"media": true,
			"method": true,
			"min": true,
			"mode": true,
			"name": true,
			"numoctaves": true,
			"offset": true,
			"opacity": true,
			"operator": true,
			"order": true,
			"orient": true,
			"orientation": true,
			"origin": true,
			"overflow": true,
			"overline-position": true,
			"overline-thickness": true,
			"paint-order": true,
			"panose-1": true,
			"pathlength": true,
			"patterncontentunits": true,
			"patterntransform": true,
			"patternunits": true,
			"pointer-events": true,
			"points": true,
			"pointsatx": true,
			"pointsaty": true,
			"pointsatz": true,
			"preservealpha": true,
			"preserveaspectratio": true,
			"primitiveunits": true,
			"r": true,
			"radius": true,
			"refx": true,
			"refy": true,
			"rendering-intent": true,
			"repeatcount": true,
			"repeatdur": true,
			"requiredextensions": true,
			"requiredfeatures": true,
			"restart": true,
			"result": true,
			"rotate": true,
			"rx": true,
			"ry": true,
			"scale": true,
			"seed": true,
			"shape-rendering": true,
			"slope": true,
			"spacing": true,
			"specularconstant": true,
			"specularexponent": true,
			"speed": true,
			"spreadmethod": true,
			"startoffset": true,
			"stddeviation": true,
			"stemh": true,
			"stemv": true,
			"stitchtiles": true,
			"stop-color": true,
			"stop-opacity": true,
			"strikethrough-position": true,
			"strikethrough-thickness": true,
			"string": true,
			"stroke": true,
			"stroke-dasharray": true,
			"stroke-dashoffset": true,
			"stroke-linecap": true,
			"stroke-linejoin": true,
			"stroke-miterlimit": true,
			"stroke-opacity": true,
			"stroke-width": true,
			"style": true,
			"surfacescale": true,
			"systemlanguage": true,
			"tabindex": true,
			"tablevalues": true,
			"target": true,
			"targetx": true,
			"targety": true,
			"text-anchor": true,
			"text-decoration": true,
			"text-rendering": true,
			"textlength": true,
			"to": true,
			"transform": true,
			"type": true,
			"u1": true,
			"u2": true,
			"underline-position": true,
			"underline-thickness": true,
			"unicode": true,
			"unicode-bidi": true,
			"unicode-range": true,
			"units-per-em": true,
			"v-alphabetic": true,
			"v-hanging": true,
			"v-ideographic": true,
			"v-mathematical": true,
			"values": true,
			"version": true,
			"vert-adv-y": true,
			"vert-origin-x": true,
			"vert-origin-y": true,
			"viewbox": true,
			"viewtarget": true,
			"visibility": true,
			"width": true,
			"widths": true,
			"word-spacing": true,
			"writing-mode": true,
			"x": true,
			"x-height": true,
			"x1": true,
			"x2": true,
			"xchannelselector": true,
			"xlink:actuate": true,
			"xlink:arcrole": true,
			"xlink:href": true,
			"xlink:role": true,
			"xlink:show": true,
			"xlink:title": true,
			"xlink:type": true,
			"xml:base": true,
			"xml:lang": true,
			"xml:space": true,
			"xmlns": true,
			"xmlns:xlink": true,
			"xmlns:xml": true,
			"y": true,
			"y1": true,
			"y2": true,
			"ychannelselector": true,
			"z": true,
			"zoomandpan": true
		];

		// Add user attributes.
		if (
			("array" === typeof self::$whitelistAttributes) &&
			count(self::$whitelistAttributes)
		) {
			var v;
			string attribute;
			for v in self::$whitelistAttributes {
				if (("string" === typeof v) && !empty v) {
					let attribute = (string) trim(strtolower(v));
					if (!empty attribute) {
						let out[attribute] = true;
					}
				}
			}

			ksort(out);
		}

		return out;
	}

	/**
	 * Whitelisted Tags
	 *
	 * These tags are allowed by SVGs.
	 *
	 * @return array Tags.
	 */
	public static function whitelistTags() -> array {
		// Our defaults.
		array out = [
			"a": true,
			"altglyph": true,
			"altglyphdef": true,
			"altglyphitem": true,
			"animate": true,
			"animatecolor": true,
			"animatemotion": true,
			"animatetransform": true,
			"audio": true,
			"canvas": true,
			"circle": true,
			"clippath": true,
			"color-profile": true,
			"cursor": true,
			"defs": true,
			"desc": true,
			"discard": true,
			"ellipse": true,
			"feblend": true,
			"fecolormatrix": true,
			"fecomponenttransfer": true,
			"fecomposite": true,
			"feconvolvematrix": true,
			"fediffuselighting": true,
			"fedisplacementmap": true,
			"fedistantlight": true,
			"fedropshadow": true,
			"feflood": true,
			"fefunca": true,
			"fefuncb": true,
			"fefuncg": true,
			"fefuncr": true,
			"fegaussianblur": true,
			"feimage": true,
			"femerge": true,
			"femergenode": true,
			"femorphology": true,
			"feoffset": true,
			"fepointlight": true,
			"fespecularlighting": true,
			"fespotlight": true,
			"fetile": true,
			"feturbulence": true,
			"filter": true,
			"font": true,
			"font-face": true,
			"font-face-format": true,
			"font-face-name": true,
			"font-face-src": true,
			"font-face-uri": true,
			"g": true,
			"glyph": true,
			"glyphref": true,
			"hatch": true,
			"hatchpath": true,
			"hkern": true,
			"image": true,
			"line": true,
			"lineargradient": true,
			"marker": true,
			"mask": true,
			"mesh": true,
			"meshgradient": true,
			"meshpatch": true,
			"meshrow": true,
			"metadata": true,
			"missing-glyph": true,
			"mpath": true,
			"path": true,
			"pattern": true,
			"polygon": true,
			"polyline": true,
			"radialgradient": true,
			"rect": true,
			"set": true,
			"solidcolor": true,
			"stop": true,
			"style": true,
			"svg": true,
			"switch": true,
			"symbol": true,
			"text": true,
			"textpath": true,
			"title": true,
			"tref": true,
			"tspan": true,
			"unknown": true,
			"use": true,
			"video": true,
			"view": true,
			"vkern": true
		];

		// Add user tags.
		if (
			("array" === typeof self::$whitelistTags) &&
			count(self::$whitelistTags)
		) {
			var v;
			string tag;
			for v in self::$whitelistTags {
				if (("string" === typeof v) && !empty v) {
					let tag = (string) trim(strtolower(v));
					if (!empty tag) {
						let out[tag] = true;
					}
				}
			}

			ksort(out);
		}

		return out;
	}
}
