//<?php
/**
 * Blobfolio: Images
 *
 * Image helpers.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Images {

	const SVG_CLEAN_STYLES = 1;
	const SVG_FIX_DIMENSIONS = 2;
	const SVG_NAMESPACE = 4;
	const SVG_RANDOM_ID = 8;
	const SVG_REWRITE_STYLES = 16;
	const SVG_SANITIZE = 32;
	const SVG_SAVE = 64;
	const SVG_STRIP_DATA = 128;
	const SVG_STRIP_ID = 256;
	const SVG_STRIP_STYLE = 512;
	const SVG_STRIP_TITLE = 1024;

	// Random IDs and classes we've generated for SVGs.
	private static $_svg_ids = [];
	private static $_svg_classes = [];

	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Blank Image
	 *
	 * A simple transparent GIF that can be shoved into an SRC without
	 * causing troubles.
	 *
	 * @return string URI.
	 */
	public static function getBlankImage() {
		return "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=";
	}

	/**
	 * Nice SVG
	 *
	 * Sanitize an SVG's attributes, protocols, etc.
	 *
	 * @param string $svg SVG.
	 * @param bool $trusted Trusted.
	 * @return string SVG.
	 */
	public static function niceSvg(string svg, const bool trusted=false) -> string {
		if (!trusted) {
			let svg = \Blobfolio\Strings::utf8(svg);
		}

		// This should validate.
		var dom;
		let dom = \Blobfolio\Dom::svgToDom(svg, true);
		if (false === dom) {
			return "";
		}

		array wAttr = (array) \Blobfolio\Dom::whitelistAttributes();
		array wIri = (array) \Blobfolio\Dom::iriAttributes();
		array wTags = (array) \Blobfolio\Dom::whitelistTags();
		bool found = false;
		int x = 0;
		int y = 0;
		var attr;
		var nodes;
		var tags;
		var tmp;
		var xpath;

		// Initialize XPath.
		let xpath = new \DOMXPath(dom);

		// All tags.
		let tags = dom->getElementsByTagName("*");
		let x = tags->length - 1;
		while x >= 0 {
			string tag_name = strtolower(tags->item(x)->tagName);

			// The tag might be namespaced. We'll allow it if the tag
			// itself is allowed.
			if (
				(false !== strpos(tag_name, ":")) &&
				!isset(wTags[tag_name])
			) {
				let tag_name = substr(strstr(tag_name, ":"), 1);
			}

			// Bad tag.
			if (!isset(wTags[tag_name])) {
				\Blobfolio\Dom::removeNode(tags->item(x));
				let x--;
				continue;
			}

			// If this is a style tag, we have to decode its children
			// because XML wants us to fail. Haha.
			if ("style" === tag_name) {
				let tmp = tags->item(x);

				let tmp->textContent = \Blobfolio\Dom::attributeValue(tmp->textContent, true);
				let tmp->textContent = \Blobfolio\Dom::decodeJsEntities(tmp->textContent);
				let tmp->textContent = strip_tags(tmp->textContent);
			}

			// Use XPath for attributes because DOMDocument will skip
			// anything with a namespace.
			let attr = xpath->query(".//@*", tags->item(x));
			let y = attr->length - 1;
			while y >= 0 {
				string attr_name = strtolower(attr->item(y)->nodeName);

				// Could also be namespaced.
				if ((false !== strpos(attr_name, ":")) && !isset(wAttr[attr_name])) {
					let attr_name = substr(strstr(attr_name, ":"), 1);
				}

				// Bad attribute.
				if ((0 !== strpos(attr_name, "data-")) && !isset(wAttr[attr_name])) {
					tags->item(x)->removeAttribute(attr->item(y)->nodeName);
					let y--;
					continue;
				}

				string attr_value = (string) \Blobfolio\Dom::attributeValue(attr->item(y)->value, true);

				// Validate protocols.
				let found = false;
				if (isset(wIri[attr_name])) {
					let found = true;
					let attr_value = \Blobfolio\Dom::iriValue(attr_value, true);
				}
				// For the rest, we're specifically interested in scripty
				// things.
				elseif (preg_match("/(?:\w+script):/xi", attr_value)) {
					let attr_value = "";
				}

				// Update it.
				if (attr_value !== attr->item(y)->value) {
					// Kill bad IRI values.
					if (found) {
						tags->item(x)->removeAttribute(attr->item(y)->nodeName);
					}
					else {
						tags->item(x)->setAttribute(
							attr->item(y)->nodeName,
							attr_value
						);
					}
				}

				let y--;
			}

			let x--;
		}

		// Once more through tags to find namespaces.
		let tags = dom->getElementsByTagName("*");
		let x = 0;
		while x < tags->length {
			let nodes = xpath->query("namespace::*", tags->item(x));
			let y = 0;
			while y < nodes->length {
				string node_name = (string) strtolower(nodes->item(y)->nodeName);

				// Not xmlns?
				if (0 !== strpos(node_name, "xmlns:")) {
					\Blobfolio\Dom::removeNamespace(dom, nodes->item(y)->localName);
					let y++;
					continue;
				}

				// Validate values as the first pass would have missed
				// them.
				string node_value = (string) \Blobfolio\Dom::iriValue(nodes->item(y)->nodeValue, true);

				// Remove empties.
				if (empty node_value) {
					\Blobfolio\Dom::removeNamespace(dom, nodes->item(y)->localName);
				}
				// Update on change.
				elseif (node_value !== nodes->item(y)->nodeValue) {
					let tmp = nodes->item(y);
					let tmp->nodeValue = node_value;
				}

				let y++;
			}

			let x++;
		}

		// Let's get back to a string.
		let svg = (string) \Blobfolio\Dom::domToSvg(dom);
		if (empty svg) {
			return "";
		}

		// One more task: catch URLs in the CSS.
		let svg = preg_replace_callback(
			"/url\s*\((.*)\s*\)/Ui",
			[__CLASS__, "niceSvgCallback"],
			svg
		);

		return svg;
	}

	/**
	 * Nice SVG Callback (CSS URLs)
	 *
	 * @param array $match Matches.
	 * @return string Replacement.
	 */
	private static function niceSvgCallback(array match) -> string {
		string str = (string) \Blobfolio\Strings::quotes(match[1], true);
		let str = str_replace(["'", "\""], "", str);
		let str = (string) \Blobfolio\Dom::iriValue(str, true);

		if (empty str) {
			return "none";
		}

		return "url('" . str . "')";
	}


	// -----------------------------------------------------------------
	// Dimensions
	// -----------------------------------------------------------------

	/**
	 * Wrapper for getimagesize()
	 *
	 * @param string $file File.
	 * @return array|bool Info or false.
	 */
	public static function size(string file) -> bool | array {
		if (!is_file(file)) {
			return false;
		}

		// Make sure it is an image-like thing.
		string mime = (string) \Blobfolio\Files::getMimeType(file);
		if (0 !== strpos(mime, "image/")) {
			return false;
		}

		// If this is an SVG, steal results from our SVG size method.
		if ("image/svg+xml" === mime) {
			var tmp = self::svgSize(file, true);
			if (false === tmp) {
				return false;
			}

			return [
				tmp["width"],
				tmp["height"],
				-1,
				sprintf(
					"width=\"%d\" height=\"%d\"",
					tmp["width"],
					tmp["height"]
				),
				"mime": "image/svg+xml"
			];
		}

		var info = getimagesize(file);
		if (false !== info) {
			return info;
		}

		// This shouldn't be needed, but just in case a local PHP is
		// wonky, we can calculate WebP dimensions manually.
		if ("image/webp" === mime) {
			var tmp = self::webpSize(file, true);
			if (false === tmp) {
				return false;
			}

			return [
				tmp["width"],
				tmp["height"],
				18,
				sprintf(
					"width=\"%d\" height=\"%d\"",
					tmp["width"],
					tmp["height"]
				),
				"mime": "image/webp"
			];
		}

		return false;
	}

	/**
	 * Determine SVG Dimensions
	 *
	 * @param string $svg SVG content or file path.
	 * @param bool $trusted Trusted.
	 * @return array|bool Dimensions or false.
	 */
	public static function svgSize(string svg, const bool trusted=false) -> bool | array {
		if (!trusted) {
			let svg = \Blobfolio\Strings::utf8(svg);
		}

		// Make sure this is SVG-looking.
		var start = stripos(svg, "<svg");
		if (false === start) {
			if (is_file(svg)) {
				let svg = file_get_contents(svg);
				let start = stripos(svg, "<svg");
				if (false === start) {
					return false;
				}
			}
			else {
				return false;
			}
		}

		// Chop the code to the opening <svg> tag.
		if (0 !== start) {
			let svg = substr(svg, start);
		}
		var end = strpos(svg, '>');
		if (false === end) {
			return false;
		}
		let svg = strtolower(substr(svg, 0, end + 1));

		// Hold our values.
		array out = [
			"width": null,
			"height": null
		];
		var viewbox = null;

		// Search for width, height, and viewbox.
		let svg = \Blobfolio\Strings::whitespace(svg, 0, true);
		var match;
		preg_match_all(
			"/(height|width|viewbox)\s*=\s*([\"'])((?:(?!\2).)*)\2/",
			svg,
			match,
			PREG_SET_ORDER
		);

		var k, v;
		if (("array" === typeof match) && count(match)) {
			for v in match {
				switch (v[1]) {
					case "width":
					case "height":
						let v[3] = \Blobfolio\Cast::toFloat($v[3], true);
						if (v[3] > 0.0) {
							let out[v[1]] = v[3];
						}

						break;
					case "viewbox":
						// Defer processing for later.
						let viewbox = v[3];
						break;
				}
			}
		}

		// If we have a width and height, we're done!
		if (!empty out["width"] && !empty out["height"]) {
			return out;
		}

		// Maybe pull from viewbox?
		if (!empty viewbox) {
			// Sometimes these are comma-separated.
			let viewbox = trim(str_replace(",", " ", viewbox));
			let viewbox = explode(" ", viewbox);

			for k, v in viewbox {
				let viewbox[k] = \Blobfolio\Cast::toFloat(v, true);
				if (viewbox[k] < 0.0) {
					let viewbox[k] = 0.0;
				}
			}
			if ((count(viewbox) === 4) && viewbox[2] > 0.0 && viewbox[3] > 0.0) {
				let out["width"] = viewbox[2];
				let out["height"] = viewbox[3];
				return out;
			}
		}

		return false;
	}

	/**
	 * WebP Size
	 *
	 * @param string $webp WebP file path.
	 * @param bool $trusted Trusted.
	 * @return array|bool Dimensions or false.
	 */
	public static function webpSize(string webp, const bool trusted=false) -> bool | array {
		if (!is_file(webp)) {
			return false;
		}

		if (!trusted) {
			string mime = (string) \Blobfolio\Files::getMimeType(webp);
			if ("image/webp" !== mime) {
				return false;
			}
		}

		var handle;
		let handle = fopen(webp, "rb");
		if (false !== handle) {
			var magic = fread(handle, 40);
			fclose(handle);

			// We should have 40 bytes of goods.
			if (strlen(magic) < 40) {
				return false;
			}

			int width = 0;
			int height = 0;
			array parts;

			switch(substr(magic, 12, 4)) {
				// Lossy WebP.
				case "VP8 ":
					let parts = (array) unpack("v2", substr(magic, 26, 4));
					let width = (intval(parts[1]) & 0x3FFF);
					let height = (intval(parts[2]) & 0x3FFF);
					break;
				// Lossless WebP.
				case "VP8L":
					let parts = (array) unpack("C4", substr(magic, 21, 4));
					let width = (intval(parts[1]) | ((intval(parts[2]) & 0x3F) << 8)) + 1;
					let height = (((intval(parts[2]) & 0xC0) >> 6) | (intval(parts[3]) << 2) | ((intval(parts[4]) & 0x03) << 10)) + 1;
					break;
				// Animated/Alpha WebP.
				case "VP8X":
					// Pad 24-bit int.
					let parts = (array) unpack("V", substr(magic, 24, 3) . "\x00");
					let width = (intval(parts[1]) & 0xFFFFFF) + 1;

					// Pad 24-bit int.
					let parts = (array) unpack("V", substr(magic, 27, 3) . "\x00");
					let height = (intval(parts[1]) & 0xFFFFFF) + 1;
					break;
			}

			if (width && height) {
				return [
					"width": width,
					"height": height
				];
			}
		}

		return false;
	}

	// -----------------------------------------------------------------
	// End dimensions.



	// -----------------------------------------------------------------
	// Conversion
	// -----------------------------------------------------------------

	/**
	 * Clean SVG for Inline Embedding
	 *
	 * @param string $path SVG path.
	 * @param int $flags Flags.
	 * @param string $output Output format.
	 *
	 * @arg bool $clean_styles Fix <style> formatting, combine tags.
	 * @arg bool $fix_dimensions Fix missing width, height, viewBox.
	 * @arg bool $namespace Generate an xmlns:svg namespace.
	 * @arg bool $random_id Randomize IDs.
	 * @arg bool $rewrite_styles Redo class assignments to group like rules.
	 * @arg bool $sanitize Sanitize content.
	 * @arg bool $save Save cleaned file for faster repeat processing.
	 * @arg bool $strip_data Remove data-x attributes.
	 * @arg bool $strip_id Remove IDs.
	 * @arg bool $strip_style Remove styles.
	 * @arg bool $strip_title Remove titles.
	 *
	 * @return string|bool Clean SVG code. False on failure.
	 */
	public static function cleanSvg(const string path, const uint flags = self::SVG_SANITIZE) -> string {
		if (!is_file(path)) {
			return "";
		}

		string svg = file_get_contents(path);
		var start = stripos(svg, "<svg");
		if (false === start) {
			return "";
		}
		elseif (start > 0) {
			let svg = substr(svg, start);
		}

		// Parse flags.
		bool flagCleanStyles = (flags & self::SVG_CLEAN_STYLES);
		bool flagFixDimensions = (flags & self::SVG_FIX_DIMENSIONS);
		bool flagNamespace = (flags & self::SVG_NAMESPACE);
		bool flagRandomId = (flags & self::SVG_RANDOM_ID);
		bool flagRewriteStyles = (flags & self::SVG_REWRITE_STYLES);
		bool flagSanitize = (flags & self::SVG_SANITIZE);
		bool flagSave = (flags & self::SVG_SAVE);
		bool flagStripData = (flags & self::SVG_STRIP_DATA);
		bool flagStripId = (flags & self::SVG_STRIP_ID);
		bool flagStripStyle = (flags & self::SVG_STRIP_STYLE);
		bool flagStripTitle = (flags & self::SVG_STRIP_TITLE);

		// Some options imply or override others.
		if (flagStripStyle) {
			let flagCleanStyles = false;
			let flagNamespace = false;
			let flagRewriteStyles = false;
		}
		if (flagStripId) {
			let flagRandomId = false;
		}
		if (flagRewriteStyles) {
			let flagCleanStyles = true;
		}

		// Skip the hard stuff maybe.
		if (false !== strpos(svg, "data-cleaned=\"" . strval(flags) . "\"")) {
			return svg;
		}

		// Convert this to a DOMDocument.
		var dom;
		let dom = \Blobfolio\Dom::svgToDom(svg);
		if (false === dom) {
			return "";
		}

		var tags;
		let tags = <\DOMNodeList> dom->getElementsByTagName("svg");
		if (!tags->length) {
			return "";
		}

		// Make sure all SVGs are tagged with the right version.
		int x = 0;
		while x < tags->length {
			tags->item(x)->setAttribute("version", "1.1");
			let x++;
		}

		// Strip title or style?
		if (flagStripStyle) {
			\Blobfolio\Dom::removeNodes(dom->getElementsByTagName("style"));
		}
		if (flagStripTitle) {
			\Blobfolio\Dom::removeNodes(dom->getElementsByTagName("title"));
		}

		// Temporarily convert the SVG back to a string.
		let svg = (string) \Blobfolio\Dom::domToSvg(dom);
		if (empty svg) {
			return "";
		}

		// Strip some more things.
		if (flagStripData) {
			let svg = preg_replace(
				"/(\sdata\-[a-z\d_\-]+\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}
		if (flagStripId) {
			let svg = preg_replace(
			"/(\sid\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}
		if (flagStripStyle) {
			let svg = preg_replace(
				"/(\s(style|class)\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}

		if (flagSanitize) {
			let svg = self::niceSvg(svg, true);
		}

		array matches;
		var k;
		var v;

		// Randomize IDs?
		if (flagRandomId) {
			preg_match_all("/\sid\s*=\s*\"([^\"]*)\"/i", svg, matches);
			if (count(matches[0])) {
				for k, v in matches[0] {
					string id_string = (string) v;
					string id_value = (string) matches[1][k];
					string id_new = "s" . strtolower(\Blobfolio\Strings::random(4));
					while (isset(self::_svg_ids[id_new])) {
						let id_new = "s" . strtolower(\Blobfolio\Strings::random(4));
					}
					let self::_svg_ids[id_new] = true;

					// Replace just the first occurrence.
					let svg = preg_replace(
						"/" . preg_quote(id_string, "/") . "/",
						" id=\"" . id_new . "\"",
						svg,
						1
					);
				}
			}
		}

		// Fix dimensions?
		if (flagFixDimensions) {
			// Before we dive in, let's fix viewbox values.
			array matches;
			array parts;

			preg_match_all("/\sviewbox\s*=\s*\"([^\"]*)\"/i", svg, matches);
			if (("array" === typeof matches) && count(matches[0])) {
				for k, v in matches[0] {
					string vbPattern = "/" . preg_quote(v, "/") . "/";
					string vbValue = (string) matches[1][k];

					if (false !== strpos(vbValue, ",")) {
						let parts = (array) explode(",", vbValue);
					}
					else {
						let parts = (array) explode(" ", vbValue);
					}

					let parts = array_map("trim", parts);
					let parts = array_filter(parts, "is_numeric");

					// Remove invalid entries.
					if (count(parts) !== 4) {
						let svg = preg_replace(
							vbPattern,
							"",
							svg,
							1
						);
					}
					else {
						string vbValue2 = (string) implode(" ", parts);
						if (vbValue !== vbValue2) {
							let svg = preg_replace(
								vbPattern,
								"viewBox=\"" . vbValue2 . "\"",
								svg,
								1
							);
						}
					}
				}
			}

			// Back to a DOM.
			let dom = \Blobfolio\Dom::svgToDom(svg, true);
			for v in ["svg", "pattern"] {
				let tags = dom->getElementsByTagName(v);
				if (!tags->length) {
					continue;
				}

				let x = 0;
				while x < tags->length {
					int width = 0;
					int height = 0;
					string vb = "";

					if (tags->item(x)->hasAttribute("width")) {
						let width = (int) preg_replace(
							"/[^\d.]/",
							"",
							tags->item(x)->getAttribute("width")
						);
					}
					if (tags->item(x)->hasAttribute("height")) {
						let height = (int) preg_replace(
							"/[^\d.]/",
							"",
							tags->item(x)->getAttribute("height")
						);
					}
					if (tags->item(x)->hasAttribute("viewBox")) {
						let vb = (string) tags->item(x)->getAttribute("viewBox");
					}

					// Everything or nothing, we're done.
					if (
						(width && height && !empty vb) ||
						(!width && !height && empty vb)
					) {
						let x++;
						continue;
					}

					// Set width and height from VB.
					if (!empty vb) {
						let parts = (array) explode(" ", vb);
						if (count(vb) === 4) {
							tags->item(x)->setAttribute("width", parts[2]);
							tags->item(x)->setAttribute("height", parts[3]);
						}
					}
					// Viewbox from width and height.
					elseif (width && height) {
						tags->item(x)->setAttribute("viewBox", "0 0 " . strval(width) . " " . strval(height));
					}

					let x++;
				}
			}

			// Back to string.
			let svg = (string) \Blobfolio\Dom::domToSvg(dom);
			if (empty svg) {
				return "";
			}
		}

		// This is so big it is pushed off to another function. Haha.
		if (flagCleanStyles) {
			let svg = (string) self::restyleSvg(svg, flagRewriteStyles);
		}

		// Namespacing is a bitch.
		if (flagNamespace) {
			let dom = \Blobfolio\Dom::svgToDom(svg, true);
			let tags = dom->getElementsByTagName("svg");
			if (!tags->length) {
				return "";
			}

			let x = 0;
			while x < tags->length {
				// Add the namespace.
				tags->item(x)->setAttribute("xmlns:svg", \Blobfolio\Dom::SVG_NAMESPACE);

				var styles;
				var style;
				int y = 0;
				int z = 0;

				// Copy style tags to the new namespace.
				let styles = tags->item(x)->getElementsByTagName("style");
				let y = 0;
				while y < styles->length {
					let style = dom->createElement("svg:style");
					let z = 0;
					while z < styles->item(y)->childNodes->length {
						style->appendChild(styles->item(y)->childNodes->item(z)->cloneNode(true));
						let z++;
					}

					styles->item(y)->parentNode->appendChild(style);
					let y++;
				}

				let x++;
			}

			let svg = (string) \Blobfolio\Dom::domToSvg(dom);
			if (empty svg) {
				return "";
			}
		}

		// Save it?
		if (flagSave) {
			// Add/update our passthrough key.
			let dom = \Blobfolio\Dom::svgToDom(svg, true);
			let tags = dom->getElementsByTagName("svg");
			tags->item(0)->setAttribute("data-cleaned", flags);
			let svg = (string) \Blobfolio\Dom::domToSvg(dom);

			string path_old = path . ".dirty." . microtime(true);
			let x = 0;
			while (file_exists(path_old)) {
				let x++;
				let path_old = path . ".dirty." . microtime(true) . "-" . x;
			}

			rename(path, path_old);
			file_put_contents(path, \Blobfolio\Dom::SVG_HEADER . "\n" . svg);
		}

		// Finally done!
		return svg;
	}

	/**
	 * Restyle SVGs
	 *
	 * This code has been abstracted from cleanSvg due to its
	 * complexity.
	 *
	 * @param string $str SVG.
	 * @param bool $rewrite Rewrite.
	 * @return string SVG.
	 */
	private static function restyleSvg(string svg, const bool rewrite=false) -> string {
		// Start by merging styles. This is much easier to achieve with
		// string matching than DOM match.
		var styleMatch;
		preg_match_all(
			"#<style[^>]*>(.*)</style>#iU",
			svg,
			styleMatch
		);

		// Early abort.
		if (!is_array(styleMatch) || !count(styleMatch[1])) {
			return svg;
		}

		string styleMerged = "";
		var dom;
		var k;
		var tags;
		var v;

		for v in styleMatch[1] {
			let styleMerged .= " " . v;
		}

		// We'll need our DOM at some point.
		let dom = \Blobfolio\Dom::svgToDom(svg, true);
		if (false === dom) {
			return "";
		}

		// Let's go ahead and nuke style tags. We don't need them any
		// more.
		let tags = dom->getElementsByTagName("style");
		if (tags->length) {
			\Blobfolio\Dom::removeNodes(tags);
		}

		// If no styles were parsed, let's quickly remove styles and get
		// on with life.
		array parsed = (array) \Blobfolio\Dom::parseCss(styleMerged);
		if (!count(parsed)) {
			return (string) \Blobfolio\Dom::domToSvg(dom);
		}

		array styleCleaned = [];
		array classesOld = [];
		var matches;
		var nodes;
		var node;

		// Life is so much easier if we aren't rewriting anything.
		if (!rewrite) {
			for v in parsed {
				let styleCleaned[] = v["raw"];
			}
		}
		// Otherwise we have to parse a ton of data. Haha.
		else {
			array rules = [];
			var rule;
			var selector;
			var selectors;

			for v in parsed {
				// Copy @ rules wholesale.
				if (false !== v["@"]) {
					let rules[v["raw"]] = [];
				}
				else {
					var rk, rv;
					string r;
					for rk, rv in v["rules"] {
						let r = rk . ":" . rv;

						if (!isset(rules[r])) {
							let rules[r] = [];
						}

						let rules[r] = array_merge(
							rules[r],
							array_values(v["selectors"])
						);
					}
				}
			} // End parsed.

			// Clean up and build output.
			for rule, selectors in rules {
				// Selectorless rules get added as is.
				if (!count(selectors)) {
					let styleCleaned[] = rule;
					continue;
				}

				// Clean up selectors a touch.
				let selectors = array_unique(selectors);
				sort(selectors);

				// Look for selectors.
				array classes = [];
				for k, selector in selectors {
					// A valid class.
					if (preg_match("/^\.[a-z\d_\-]+$/i", selector)) {
						let classes[] = selector;
						unset(selectors[k]);
					}
					// A broken Adobe class,
					// e.g. .\38 ab9678e-54ee-493d-b19f-2215c5549034.
					else {
						let selector = str_replace(".\\3", ".", selector);
						// Fix weird adobe rules.
						preg_match_all("/^\.([\d]) ([a-z\d\-]+)$/", selector, matches);
						if (count(matches[0])) {
							let classes[] = preg_replace("/\s/", "", matches[0][0]);
							unset(selectors[k]);
						}
					}
				} // Each selector.

				// We have classes!
				if (count(classes)) {
					string classNew = strtolower("c" . \Blobfolio\Strings::random(4));
					while (isset(self::_svg_classes[classNew])) {
						let classNew = strtolower("c" . \Blobfolio\Strings::random(4));
					}
					let selectors[] = "." . classNew;

					// Add the class to all affected nodes.
					let nodes = \Blobfolio\Dom::getNodesByClass(dom, classes);
					for node in nodes {
						string classValue = (string) node->getAttribute("class");
						let classValue .= " " . classNew;
						node->setAttribute("class", classValue);
					}

					// And note our old classes.
					for v in classes {
						let classesOld[] = ltrim(v, ".");
					}
				}

				let styleCleaned[] = implode(",", selectors) . "{" . rule . "}";
			} // Each rule.
		}

		// We should finally have styles!
		let styleMerged = (string) implode("", styleCleaned);

		// We need to inject our new style tag.
		var def;
		var style;
		let def = dom->createElement("def");
		let style = dom->createElement("style");
		let style->nodeValue = styleMerged;
		def->appendChild(style);

		// Add it.
		dom->getElementsByTagName("svg")->item(0)->insertBefore(
			def,
			dom->getElementsByTagName("svg")->item(0)->childNodes->item(0)
		);

		// We have to go through and remove old classes.
		if (count(classesOld)) {
			let classesOld = array_unique(classesOld);
			sort(classesOld);

			let nodes = \Blobfolio\Dom::getNodesByClass(dom, classesOld);
			for node in nodes {
				var tmp;
				let tmp = (string) node->getAttribute("class");
				let tmp = (string) \Blobfolio\Strings::whitespace(tmp, 0, true);
				let tmp = (array) explode(" ", tmp);
				let tmp = array_unique(tmp);
				let tmp = array_diff(tmp, classesOld);
				node->setAttribute("class", implode(" ", tmp));
			}
		}

		let svg = (string) \Blobfolio\Dom::domToSvg(dom);
		return svg;
	}

	/**
	 * To WebP
	 *
	 * @param string $from From.
	 * @param string $to To.
	 * @param bool $refresh Refresh.
	 * @return bool True/false.
	 */
	public static function toWebp(string from, string to="", const bool refresh=false) -> bool {
		let from = \Blobfolio\Files::path(from, true);
		if (empty from) {
			return false;
		}

		// We can only convert JPEG, PNG, and GIF sources.
		array finfo = (array) \Blobfolio\Files::finfo(from);
		if (
			("image/jpeg" !== finfo["mime"]) &&
			("image/gif" !== finfo["mime"]) &&
			("image/png" !== finfo["mime"])
		) {
			return false;
		}

		// Just make a sister file.
		if (empty to) {
			let to = finfo["dirname"] . "/" . finfo["filename"] . ".webp";
		}
		else {
			let to = \Blobfolio\Files::path(to, false);

			// If only a file name was passed, throw it in the dir.
			if (false === strpos(to, "/")) {
				let to = finfo["dirname"] . "/" . to;
			}
			if (".webp" !== substr(strtolower(to), -5)) {
				return false;
			}
		}

		// Early abort.
		if (!refresh && file_exists(to)) {
			return true;
		}

		// We need somewhere to shove errors.
		string tmp_dir = (string) sys_get_temp_dir();
		if (empty tmp_dir || !is_dir(tmp_dir)) {
			return false;
		}

		string error_log = (string) tempnam(tmp_dir, "webp");
		if (empty error_log) {
			return false;
		}

		// The WebP binaries are here.
		string bin_dir = \Blobfolio\Blobfolio::getDataDir() . "webp/bin/";

		// Gifs get their own binary.
		string cmd;
		if ("image/gif" === finfo["mime"]) {
			let cmd = (string) sprintf(
				"%s -m 6 -quiet %s -o %s",
				escapeshellcmd(bin_dir . "gif2webp"),
				escapeshellarg(from),
				escapeshellarg(to)
			);
		}
		else {
			let cmd = (string) sprintf(
				"%s -mt -quiet -jpeg_like %s -o %s",
				escapeshellcmd(bin_dir . "cwebp"),
				escapeshellarg(from),
				escapeshellarg(to)
			);
		}

		pclose(popen(cmd, "r"));

		// If the file didn't end up a thing, we're done.
		if (!file_exists(to)) {
			return false;
		}

		// Try to give it the same permissions as the original.
		var tmp;
		let tmp = fileperms(from);
		if (false !== tmp) {
			chmod(to, tmp);
		}
		let tmp = fileowner(from);
		if (false !== tmp) {
			chown(to, tmp);
		}
		let tmp = filegroup(from);
		if (false !== tmp) {
			chgrp(to, tmp);
		}

		return true;
	}

	// -----------------------------------------------------------------
	// End conversion.
}
