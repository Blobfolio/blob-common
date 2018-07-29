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
				let tmp->textContent = strip_tags(\Blobfolio\Dom::attributeValue(tmp->textContent, true));
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
			var tmp = self::svgSize(file);
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

		// Manually parse WebP dimensions in case GD couldn't do it.
		if ("image/webp" === mime) {
			var tmp = self::webpSize(file);
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
	 * @return array|bool Dimensions or false.
	 */
	public static function svgSize(string svg) -> bool | array {
		let svg = \Blobfolio\Strings::utf8(svg);

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
	 * @return array|bool Dimensions or false.
	 */
	public static function webpSize(string webp) -> bool | array {
		if (!is_file(webp)) {
			return false;
		}

		string mime = (string) \Blobfolio\Files::getMimeType(webp);
		if ("image/webp" !== mime) {
			return false;
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
	 * @param mixed $args Arguments.
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
	public static function cleanSvg(const string path, var args = null) -> string {
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

		// Default options.
		array template = [
			"clean_styles": false,		// Clean styles.
			"fix_dimensions": false,	// Fix missing width, height, viewBox.
			"namespace": false,			// Add an svg: namespace.
			"random_id": false,			// Randomize IDs.
			"rewrite_styles": false,	// Merge and rewrite styles.
			"sanitize": true,			// Remove invalid/dangerous bits.
			"save": false,				// Replace file with clean copy.
			"strip_data": false,		// Remove data-X attributes.
			"strip_id": false,			// Remove all IDs.
			"strip_style": false,		// Remove all styles.
			"strip_title": false		// Remove all titles.
		];

		// Deprecated $strip_js is now sanitize.
		if (isset(args["strip_js"]) && !isset(args["sanitize"])) {
			let args["sanitize"] = args["strip_js"];
		}

		// Crunch the rest.
		let args = \Blobfolio\Cast::parseArgs(args, template);

		// Some options imply or override others.
		if (args["strip_style"]) {
			let args["clean_styles"] = false;
			let args["namespace"] = false;
			let args["rewrite_styles"] = false;
		}
		if (args["strip_id"]) {
			let args["random_id"] = false;
		}
		if (args["rewrite_styles"]) {
			let args["clean_styles"] = true;
		}

		// Skip the hard stuff maybe.
		string passthrough_key = (string) crc32(json_encode(args));
		if (false !== strpos(svg, "data-cleaned=\"" . passthrough_key . "\"")) {
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
		if (args["strip_style"]) {
			\Blobfolio\Dom::removeNodes(dom->getElementsByTagName("style"));
		}
		if (args["strip_title"]) {
			\Blobfolio\Dom::removeNodes(dom->getElementsByTagName("title"));
		}

		// Temporarily convert the SVG back to a string.
		let svg = (string) \Blobfolio\Dom::domToSvg(dom);
		if (empty svg) {
			return "";
		}

		// Strip some more things.
		if (args["strip_data"]) {
			let svg = preg_replace(
				"/(\sdata\-[a-z\d_\-]+\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}
		if (args["strip_id"]) {
			let svg = preg_replace(
			"/(\sid\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}
		if (args["strip_style"]) {
			let svg = preg_replace(
				"/(\s(style|class)\s*=\s*\"[^\"]*\")/i",
				"",
				svg
			);
		}

		if (args["sanitize"]) {
			let svg = self::niceSvg(svg, true);
		}

		array matches;
		var k;
		var v;

		// Randomize IDs?
		if (args["random_id"]) {
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
		if (args["fix_dimensions"]) {
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

		// Namespacing is a bitch.
		if (args["clean_styles"] || args["namespace"]) {
			let dom = \Blobfolio\Dom::svgToDom(svg, true);
			let tags = dom->getElementsByTagName("svg");
			if (!tags->length) {
				return "";
			}

			let x = 0;
			while x < tags->length {
				// Add namespace.
				if (args["namespace"]) {
					tags->item(x)->setAttribute("xmlns:svg", \Blobfolio\Dom::SVG_NAMESPACE);
				}

				// Store a list of classes to rewrite, if any.
				array classes_old = [];
				int y;
				int z;
				var node;
				var nodes;
				var style;
				var styles;
				var tmp;

				// TODO clean styles.
				if (args["clean_styles"]) {
					// First, combine styles.
					let styles = tags->item(x)->getElementsByTagName("style");
					if (styles->length) {
						var style_parent = styles->item(0)->parentNode;
						let style = dom->createElement("style");
						while (styles->length) {
							// Collect children.
							let y = 0;
							while (y < styles->item(0)->childNodes->length) {
								let node = styles->item(0)->childNodes->item(y);
								let node->nodeValue .= " ";
								style->appendChild(node);

								let y++;
							}

							\Blobfolio\Dom::removeNode(styles->item(0));
						}

						style_parent->appendChild(style);

						// Now to fix the formatting.
						let style = tags->item(x)->getElementsByTagName("style")->item(0);

						array parsed = (array) \Blobfolio\Dom::parseCss(style->nodeValue);
						if (count(parsed)) {
							array style_new = [];

							// Rewriting.
							if (args["rewrite_styles"]) {
								// Try to group rules.
								array rules = [];

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
								var rule;
								var selectors;
								var selector;
								for rule, selectors in rules {
									// Selectorless rules get added as is.
									if (!count(selectors)) {
										let style_new[] = rule;
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
										string class_new = strtolower("c" . \Blobfolio\Strings::random(4));
										while (isset(self::_svg_classes[class_new])) {
											let class_new = strtolower("c" . \Blobfolio\Strings::random(4));
										}
										let selectors[] = "." . class_new;

										// Add the class to all affected nodes.
										let nodes = \Blobfolio\Dom::getNodesByClass(tags->item(x), classes);
										for node in nodes {
											string class_value = (string) node->getAttribute("class");
											let class_value .= " " . class_new;
											node->setAttribute("class", class_value);
										}

										// And note our old classes.
										for v in classes {
											let classes_old[] = ltrim(v, ".");
										}
									}

									let style_new[] = implode(",", selectors) . "{" . rule . "}";
								} // Each rule.
							}
							// Not rewriting.
							else {
								for v in parsed {
									let style_new[] = v["raw"];
								}
							}

							// Push our rules back!
							let style->nodeValue = implode("", style_new);
						}
					}
				} // End cleaning styles.

				// Fix styles too.
				if (args["namespace"]) {
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
				}

				if (count(classes_old)) {
					let classes_old = array_unique(classes_old);
					sort(classes_old);

					let nodes = \Blobfolio\Dom::getNodesByClass(tags->item(x), classes_old);
					for node in nodes {
						let tmp = node->getAttribute("class");
						let tmp = \Blobfolio\Strings::whitespace(tmp, 0, true);
						let tmp = (array) explode(" ", tmp);
						let tmp = array_unique(tmp);
						let tmp = array_diff(tmp, classes_old);
						node->setAttribute("class", implode(" ", tmp));
					}
				}

				let x++;
			}

			let svg = (string) \Blobfolio\Dom::domToSvg(dom);
			if (empty svg) {
				return "";
			}
		}

		// Save it?
		if (args["save"]) {
			// Add/update our passthrough key.
			let dom = \Blobfolio\Dom::svgToDom(svg, true);
			let tags = dom->getElementsByTagName("svg");
			tags->item(0)->setAttribute("data-cleaned", passthrough_key);
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

		array descriptors = [
			0: ["pipe", "w"],
			1: ["file", error_log, "a"]
		];
		array pipes = [];
		var life;
		var process;
		var response;

		try {
			// Open a process to run our binary.
			let process = proc_open(
				cmd,
				descriptors,
				pipes,
				tmp_dir
			);

			// If we didn't get a resource, something is wrong.
			if (!is_resource(process)) {
				return false;
			}

			// Pull the stream contents.
			let life = stream_get_contents(pipes[0]);
			fclose(pipes[0]);
			let response = proc_close(process);

			// We don't actually want the error log.
			if (file_exists(error_log)) {
				unlink(error_log);
			}

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

		return false;
	}

	// -----------------------------------------------------------------
	// End conversion.
}
