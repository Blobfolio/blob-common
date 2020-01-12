<?php
/**
 * Blobfolio: DOM
 *
 * Number manipulation.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;
use DOMDocument;
use DOMNodeList;
use DOMXPath;



final class Dom {
	const SVG_HEADER = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">";
	const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';



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
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function attributeValue(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = \Blobfolio\Strings::utf8($str);
		}
		$str = \Blobfolio\Strings::controlChars($str, Shim::TRUSTED);
		$str = self::decodeEntities($str);
		return \Blobfolio\Strings::trim($str, Shim::TRUSTED);
	}

	/**
	 * Nice IRI Value
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function iriValue(string $str, int $flags=0) : string {
		// Remove vertical whitespace.
		$str = self::attributeValue(
			$str,
			($flags & Shim::TRUSTED)
		);
		$str = \Blobfolio\Strings::whitespace($str, 0, Shim::TRUSTED);

		// Early abort.
		if (empty($str)) {
			return '';
		}

		// Assign a protocol if missing.
		if (0 === \strpos($str, '//')) {
			$str = 'https:' . $str;
		}

		// Check protocols.
		$test = (string) \Blobfolio\Strings::toLower($str, Shim::TRUSTED);
		$test = \preg_replace('/\s/u', '', $test);
		if (\strpos($test, ':')) {
			$test = \strstr($test, ':', true);
			$protocols = (array) self::whitelistProtocols();
			if (! isset($protocols[$test])) {
				return '';
			}
		}

		// Abort if not URLish.
		if (\filter_var($str, \FILTER_SANITIZE_URL) !== $str) {
			return '';
		}

		// Check the domain if applicable.
		if (\preg_match('/^[\w\d]+:\/\//ui', $str)) {
			$test = (string) \Blobfolio\Domains::niceDomain($str);
			if (empty($test)) {
				return '';
			}

			$domains = (array) self::whitelistDomains();
			if (! isset($domains[$test])) {
				return '';
			}
		}

		return $str;
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
	 * @return string String.
	 */
	public static function linkify(string $str, $args=null, int $pass=1) : string {
		// Ignore bad values.
		if ($pass < 1 || $pass > 3) {
			return $str;
		}

		// Build link attributes.
		$args = (array) \Blobfolio\Cast::parseArgs(
			$args,
			array('class'=>array(), 'rel'=>'', 'target'=>'')
		);

		// Make classes easier to deal with.
		if (\count($args['class'])) {
			$args['class'] = (array) \Blobfolio\Arrays::fromList($args['class'], ' ');
			$args['class'] = (string) \implode(' ', $args['class']);
		}
		else {
			$args['class'] = '';
		}

		// Correct any weird UTF8 issues on the first pass.
		if (1 === $pass) {
			$str = \Blobfolio\Strings::utf8($str);
		}

		$chunks = (array) \preg_split(
			'/(<.+?>)/is',
			$str,
			0,
			\PREG_SPLIT_DELIM_CAPTURE
		);
		$ignoring = '';

		// Generate attributes for insertion.
		$atts = array();
		foreach ($args as $k=>$v) {
			if (! empty($v)) {
				$atts[] = $k . '="' . $v . '"';
			}
		}
		$atts = (string) \implode(' ', $atts);
		if (! empty($atts)) {
			$atts = ' ' . $atts;
		}

		// Loop the chunks!
		foreach ($chunks as $k=>$v) {
			// Even keys exist between tags.
			if (0 === $k % 2) {
				// Skip if we are waiting for a closing tag.
				if (! empty($ignoring)) {
					continue;
				}

				switch ($pass) {
					// URL bits.
					case 1:
						$chunks[$k] = (string) \preg_replace_callback(
							"/((ht|f)tps?:\/\/[^\s'\"\[\]\(\){}]+|[^\s'\"\[\]\(\){}]*xn--[^\s'\"\[\]\(\){}]+|[@]?[\w.]+\.[\w\.]{2,}[^\s]*)/ui",
							array(static::class, 'linkifyCallback1'),
							$v
						);

						break;
					// Email bits.
					case 2:
						$chunks[$k] = (string) \preg_replace_callback(
							"/([\w\.\!#\$%&\*\+\=\?_~]+@[^\s'\"\[\]\(\)\{\}@]{2,})/ui",
							array(static::class, 'linkifyCallback2'),
							$v
						);

						break;
					// Phone bits.
					case 3:
						$chunks[$k] = (string) \preg_replace_callback(
							'/(\s)?(\+\d[\d\-\s]{5,}+|\(\d{3}\)\s[\d]{3}[\-\.\s]\d{4}|\d{3}[\-\.\s]\d{3}[\-\.\s]\d{4}|\+\d{7,})/ui',
							array(static::class, 'linkifyCallback3'),
							$v
						);

						break;
				}

				$chunks[$k] = (string) \str_replace(
					'%BLOBCOMMON_ATTS%',
					$atts,
					$chunks[$k]
				);
			}
			// Odd keys indicate a tag, opening or closing.
			else {
				// We are looking for an opening tag.
				if (empty($ignoring)) {
					\preg_match(
						'/<(a|audio|button|code|embed|frame|head|link|object|picture|pre|script|select|style|svg|textarea|video).*(?<!\/)>$/is',
						$v,
						$matches
					);
					if (('array' === \gettype($matches)) && \count($matches) >= 2) {
						$ignoring = (string) \preg_quote($matches[1], '/');
					}
				}
				// Wait for a closing tag.
				elseif (\preg_match('/<\/\s*' . $ignoring . '/i', $v)) {
					$ignoring = '';
				}
			}
		}

		$str = (string) \implode('', $chunks);

		// Linkification is run in stages to prevent overlap issues.
		// Pass #1 is for URL-like bits, #2 for email addresses, and #3
		// for phone numbers.
		if ($pass < 3) {
			return self::linkify($str, $args, $pass + 1);
		}

		// We're done!
		return $str;
	}

	/**
	 * Linkify Stage One Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback1(array $matches) : string {
		$raw = (string) $matches[1];
		$suffix = '';

		// Don't do email bits.
		if (0 === \strpos($raw, '@')) {
			return $matches[1];
		}

		// We don't want trailing punctuation added to the link.
		\preg_match('/([^\w\/]+)$/ui', $raw, $match);
		if (('array' === \gettype($match)) && \count($match) >= 2) {
			$suffix = (string) $match[1];
			$raw = \preg_replace('/([^\w\/]+)$/ui', '', $raw);
		}

		// Make sure we have something URL-esque.
		$link = \Blobfolio\Domains::parseUrl($raw);
		if (('array' !== \gettype($link)) || ! isset($link['host'])) {
			return $matches[1];
		}

		// Only linkify FQDNs.
		$domain = new Domains($link['host']);
		if (! $domain->isValid() || ! $domain->isFqdn()) {
			return $matches[1];
		}

		// Supply a scheme if missing.
		if (! isset($link['scheme'])) {
			$link['scheme'] = 'http';
		}

		$link = (string) \Blobfolio\Domains::unparseUrl($link);
		if (\filter_var($link, \FILTER_SANITIZE_URL) !== $link) {
			return $matches[1];
		}

		// Finally, make a link!
		$link = self::html($link, Shim::TRUSTED);
		return '<a href="' . $link . '"%BLOBCOMMON_ATTS%>' . $raw . '</a>' . $suffix;
	}

	/**
	 * Linkify Stage Two Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback2(array $matches) : string {
		$raw = (string) $matches[1];
		$suffix = '';

		// We don't want trailing punctuation added to the link.
		\preg_match('#([^\w]+)$#ui', $raw, $match);
		if (('array' === \gettype($match)) && \count($match) >= 2) {
			$suffix = (string) $match[1];
			$raw = \preg_replace('#([^\w]+)$#ui', '', $raw);
		}

		$email = (string) \Blobfolio\Domains::niceEmail($raw);
		if (empty($email)) {
			return $matches[1];
		}

		// Finally, make a link!
		$email = self::html($email, Shim::TRUSTED);
		return '<a href="mailto:' . $email . '"%BLOBCOMMON_ATTS%>' . $raw . '</a>' . $suffix;
	}

	/**
	 * Linkify Stage Three Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function linkifyCallback3(array $matches) : string {
		$prefix = (string) $matches[1];
		$raw = (string) $matches[2];
		$suffix = '';

		\preg_match('/([^\d]+)$/ui', $raw, $match);
		if (('array' === \gettype($match)) && \count($match) >= 2) {
			$suffix = (string) $match[1];
			$raw = \preg_replace('/([^\d]+)$/ui', '', $raw);
		}

		$phone = (string) \Blobfolio\Phones::nicePhone($raw);
		$phone = \preg_replace('/[^\d]/', '', $phone);
		if (empty($phone)) {
			return $matches[1] . $matches[2];
		}

		return $prefix . '<a href="tel:+' . $phone . '"%BLOBCOMMON_ATTS%>' . $raw . '</a>' . $suffix;
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
	public static function decodeJsEntities(string $str) : string {
		$str = self::decodeUnicodeEntities($str);
		return self::decodeEscapeEntities($str);
	}

	/**
	 * Decode Escape Entities
	 *
	 * Decode \b, \f, \n, \r, \t.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeEscapeEntities(string $str) : string {
		$str = \Blobfolio\Strings::utf8($str);

		$replacements = array(
			'\b'=>\chr(0x08),
			'\f'=>\chr(0x0C),
			'\n'=>\chr(0x0A),
			'\r'=>\chr(0x0D),
			'\t'=>\chr(0x09),
		);
		return \str_replace(
			\array_keys($replacements),
			\array_values($replacements),
			$str
		);
	}

	/**
	 * Decode Unicode Entities
	 *
	 * Decode \u1234 into chars.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeUnicodeEntities(string $str) : string {
		$str = \Blobfolio\Strings::utf8($str);

		$last = '';
		while ($str !== $last) {
			$last = $str;

			$str = \preg_replace_callback(
				"/\\\u([0-9A-Fa-f]{4})/u",
				array(static::class, 'decodeHexEntities'),
				$str
			);

			$str = \Blobfolio\Strings::utf8($str);
		}

		return $str;
	}

	/**
	 * Decode HTML Entities
	 *
	 * Decode all HTML entities back into their char counterparts,
	 * recursively until every last one is captured.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function decodeEntities(string $str) : string {
		$str = \Blobfolio\Strings::utf8($str);

		$last = '';
		while ($str !== $last) {
			$last = $str;

			$str = \html_entity_decode($str, \ENT_QUOTES, 'UTF-8');
			$str = \preg_replace_callback(
				'/&#([0-9]+);/',
				array(static::class, 'decodeChrEntities'),
				$str
			);
			$str = \preg_replace_callback(
				'/&#[Xx]([0-9A-Fa-f]+);/',
				array(static::class, 'decodeHexEntities'),
				$str
			);

			$str = \Blobfolio\Strings::utf8($str);
		}

		return $str;
	}

	/**
	 * Decode HTML Entities Callback - Chr
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	private static function decodeChrEntities(array $matches) : string {
		return \chr($matches[1]);
	}

	/**
	 * Decode HTML Entities Callback - Hex
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	private static function decodeHexEntities(array $matches) : string {
		return \chr(\hexdec($matches[1]));
	}

	/**
	 * HTML
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function html(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);

		if (! $trusted) {
			$str = \Blobfolio\Strings::utf8($str);
		}

		return \htmlspecialchars($str, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
	}

	/**
	 * JS
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string JS.
	 */
	public static function js(string $str, int $flags=2) : string {
		$flagsApostrophes = !! ($flags & Shim::JS_FOR_APOSTROPHES);
		$flagsQuotes = ! $flagsApostrophes;

		$str = \Blobfolio\Strings::whitespace(
			$str,
			0,
			($flags & Shim::TRUSTED)
		);
		$str = \Blobfolio\Strings::quotes($str, Shim::TRUSTED);

		if ($flagsApostrophes) {
			$str = \str_replace(
				array('/', "'"),
				array('\\/', "\\'"),
				$str
			);
		}
		elseif ($flagsQuotes) {
			$str = \str_replace(
				array('/', '"'),
				array('\\/', '\\"'),
				$str
			);
		}

		return $str;
	}



	// -----------------------------------------------------------------
	// Conversion and Helpers
	// -----------------------------------------------------------------

	/**
	 * SVG to DOM
	 *
	 * @param string $svg SVG code.
	 * @param int $flags Flags.
	 * @return bool|DOMDocument DOM object or false.
	 */
	public static function svgToDom(string $svg, int $flags=0) {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$svg = \Blobfolio\Strings::utf8($svg);
		}

		// At the very least we expect tags.
		$start = \mb_stripos($svg, '<svg', 0, 'UTF-8');
		$end = \mb_strripos($svg, '</svg>', 0, 'UTF-8');
		if (
			(false === $start) ||
			(false === $end) ||
			($end < $start)
		) {
			return false;
		}

		// Chop it if needed.
		$svg = \mb_substr($svg, $start, ($end - $start + 6), 'UTF-8');

		// Get rid of some stupid Illustrator problems.
		$replace_keys = array(
			'xmlns="&ns_svg;"',
			'xmlns:xlink="&ns_xlink;"',
			'id="Layer_1"',
		);
		$replace_values = array(
			'xmlns="http://www.w3.org/2000/svg"',
			'xmlns:xlink="http://www.w3.org/1999/xlink"',
			'',
		);
		$svg = \str_replace($replace_keys, $replace_values, $svg);

		// Remove XML, PHP, ASP, and comments.
		$svg = \preg_replace(
			array(
				'/<\?(.*)\?>/Us',
				'/<\%(.*)\%>/Us',
				'/<!--(.*)-->/Us',
				'/\/\*(.*)\*\//Us',
			),
			'',
			$svg
		);

		if (
			(false !== \strpos($svg, '<?')) ||
			(false !== \strpos($svg, '<%')) ||
			(false !== \strpos($svg, '<!--')) ||
			(false !== \strpos($svg, '/*'))
		) {
			return false;
		}

		// Add the SVG header back to help DOMDocument correctly read
		// the file.
		$svg = self::SVG_HEADER . "\n" . $svg;

		// Open it.
		\libxml_use_internal_errors(true);
		\libxml_disable_entity_loader(true);
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = false;
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($svg);

		// Make sure there are still SVG tags.
		$svgs = $dom->getElementsByTagName('svg');
		if (! $svgs->length) {
			return false;
		}

		return $dom;
	}

	/**
	 * DOM to SVG
	 *
	 * @param DOMDocument $dom Dom.
	 * @return string SVG.
	 */
	public static function domToSvg(DOMDocument $dom) : string {
		$svgs = $dom->getElementsByTagName('svg');
		if (! $svgs->length) {
			return '';
		}
		$svg = $svgs->item(0)->ownerDocument->saveXML(
			$svgs->item(0),
			\LIBXML_NOBLANKS
		);

		// Make sure if xmlns="" exists, it is correct. Can't alter
		// that with DOMDocument, and there is only one proper value.
		$svg = \preg_replace(
			'/xmlns\s*=\s*"[^"]*"/',
			'xmlns="' . self::SVG_NAMESPACE . '"',
			$svg
		);

		// Remove XML, PHP, ASP, and comments.
		$svg = \preg_replace(
			array(
				'/<\?(.*)\?>/Us',
				'/<\%(.*)\%>/Us',
				'/<!--(.*)-->/Us',
				'/\/\*(.*)\*\//Us',
			),
			'',
			$svg
		);

		if (
			(false !== \strpos($svg, '<?')) ||
			(false !== \strpos($svg, '<%')) ||
			(false !== \strpos($svg, '<!--')) ||
			(false !== \strpos($svg, '/*'))
		) {
			return '';
		}

		// Find the start and end tags so we can cut out miscellaneous garbage.
		if (
			(false === ($start = \mb_strpos($svg, '<svg', 0, 'UTF-8'))) ||
			(false === ($end = \mb_strrpos($svg, '</svg>', 0, 'UTF-8')))
		) {
			return '';
		}

		// Chop it if needed.
		return \mb_substr($svg, $start, ($end - $start + 6), 'UTF-8');
	}

	/**
	 * Get Nodes By Class
	 *
	 * This will return an array of DOMNode objects containing the
	 * specified class(es). This does not use DOMXPath.
	 *
	 * @param mixed $parent Parent.
	 * @param array $classes Classes.
	 * @param bool $all Must match all rather than any.
	 * @return array Nodes.
	 */
	public static function getNodesByClass($parent, array $classes, bool $all=false) : array {
		if (! \method_exists($parent, 'getElementsByTagName')) {
			return array();
		}

		$classes = \Blobfolio\Arrays::flatten($classes);
		foreach ($classes as $k=>$v) {
			$classes[$k] = \ltrim($v, '.');
			if (empty($classes[$k])) {
				unset($classes[$k]);
			}
		}

		$classesLength = (int) \count($classes);
		if (! $classesLength) {
			return array();
		}

		$classes = \array_unique($classes);
		\sort($classes);

		$nodes = array();
		$tags = $parent->getElementsByTagName('*');
		if ($tags->length) {
			$x = 0;
			while ($x < $tags->length) {
				if ($tags->item($x)->hasAttribute('class')) {
					// Parse this tag's classes.
					$class_value = $tags->item($x)->getAttribute('class');
					$class_value = \Blobfolio\Strings::whitespace($class_value, 0, Shim::TRUSTED);
					$class_value = (array) \explode(' ', $class_value);

					// Find the intersect.
					$intersect = (array) \array_intersect($classes, $class_value);
					$intersectLength = (int) \count($intersect);

					if (
						$intersectLength &&
						(! $all || ($intersectLength === $classesLength))
					) {
						$nodes[] = $tags->item($x);
					}
				}

				$x++;
			}
		}

		return $nodes;
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
	public static function innerHtml($node, bool $xml=false, $flags=null) : string {
		if (
			! \is_a($node, '\\DOMElement') &&
			! \is_a($node, '\\DOMNode')
		) {
			return '';
		}

		$out = '';
		if ($node->childNodes->length) {
			$x = 0;
			if ($xml) {
				while ($x < $node->childNodes->length) {
					if (\is_int($flags)) {
						$out .= $node->ownerDocument->saveXML(
							$node->childNodes->item($x),
							$flags
						);
					}
					else {
						$out .= $node->ownerDocument->saveXml($node->childNodes->item($x));
					}

					$x++;
				}
			}
			else {
				while ($x < $node->childNodes->length) {
					$out .= $node->ownerDocument->saveHTML($node->childNodes->item($x));
					$x++;
				}
			}
		}

		return $out;
	}

	/**
	 * Merge Classes
	 *
	 * The HTML "class" attribute frequently requires sanitization and
	 * merging. This function takes any number of arguments, each either
	 * a string or array, and returns a single array containing each
	 * unique class.
	 *
	 * @return array Classes.
	 */
	public static function mergeClasses() : array {
		$args = (array) \func_get_args();
		$out = array();

		// Run through each and add as needed.
		foreach ($args as $v) {
			$v = \Blobfolio\Arrays::fromList($v, ' ');
			foreach ($v as $v2) {
				// Strip obviously bad characters.
				$v2 = \preg_replace(
					'/[^a-z\d_:\{\}-]/',
					'',
					\strtolower($v2)
				);

				if (! empty($v2)) {
					$out[$v2] = true;
				}
			}
		}

		return \array_keys($out);
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
	 * @param string $css Styles.
	 * @param int $flags Flags.
	 * @return array Parsed styles.
	 */
	public static function parseCss(string $css, int $flags=0) : array {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$css = \Blobfolio\Strings::utf8($css);
		}

		// Check for comments.
		if (false !== \strpos($css, '/*')) {
			$css = \preg_replace('/\/\*(.*)\*\//Us', '', $css);
		}
		$start = \mb_strpos($css, '/*', 0, 'UTF-8');
		if (false !== $start) {
			$css = \mb_substr($css, 0, $start, 'UTF-8');
		}

		// Get rid of non-style sister comments and markers.
		$css = \str_replace(
			array('<!--', '//-->', '//<![CDATA[', '//]]>', '<![CDATA[', ']]>'),
			'',
			$css
		);

		// Clean up characters a bit.
		$css = \Blobfolio\Strings::niceText($css, 0, Shim::TRUSTED);

		// Early bail.
		if (empty($css)) {
			return array();
		}

		// Substitute braces for unlikely characters to make parsing
		// easier hopefully nobody's using braille in their
		// stylesheets...
		$css = \preg_replace(
			'/\{(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
			'⠁',
			$css
		);
		$css = \preg_replace(
			'/\}(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
			'⠈',
			$css
		);

		// Make sure there rae spaces before and after parentheses.
		$css = \preg_replace(
			'/\s*(\()\s*(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
			' (',
			$css
		);
		$css = \preg_replace(
			'/\s*(\))\s*(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
			') ',
			$css
		);

		// Make sure {} have no whitespace on either end.
		$css = \preg_replace('/\s*(⠁|⠈|@)\s*/u', '$1', $css);

		// Push @ rules to their own lines.
		$css = \str_replace('@', "\n@", $css);

		$styles = (array) \explode("\n", $css);
		$tmp = array();

		foreach ($styles as $k=>$v) {
			$styles[$k] = \trim($v);
			if (empty($styles[$k])) {
				unset($styles[$k]);
				continue;
			}

			// An @ rule.
			if (0 === \strpos($styles[$k], '@')) {
				// Nested, like @media.
				if (false !== \strpos($styles[$k], '⠈⠈')) {
					$styles[$k] = \preg_replace(
						'/(⠈{2,})/u',
						"$1\n",
						$styles[$k]
					);
				}
				// Not nested, but has properties like @font-face.
				elseif (false !== \strpos($styles[$k], '⠈')) {
					$styles[$k] = \str_replace('⠈', "⠈\n", $styles[$k]);
				}
				// A one-liner, like @import.
				elseif (\preg_match(
					'/;(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/',
					$styles[$k]
				)) {
					$styles[$k] = \preg_replace(
						'/;(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
						";\n",
						$styles[$k],
						1
					);
				}

				// Clean up what we have.
				$tmp = (array) \explode("\n", $styles[$k]);
				$x = 1;
				while ($x < \count($tmp)) {
					$tmp[$x] = \str_replace('⠈', "⠈\n", $tmp[$x]);
					$x++;
				}
				$styles[$k] = \implode("\n", $tmp);
			}
			// Just regular stuff.
			else {
				$styles[$k] = \str_replace('⠈', "⠈\n", $styles[$k]);
			}
		}

		// Back to a string.
		$css = (string) \implode("\n", $styles);

		// One more quick formatting thing, we can get rid of spaces
		// between closing) and punctuation.
		$css = \preg_replace(
			'/\)\s(,|;)(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
			')$1',
			$css
		);

		// And between RGB/URL stuff.
		$css = \preg_replace('/(url|rgba?)\s+\(/', '$1(', $css);

		// One more time around.
		$matches = array();
		$out = array();
		$rules = array();
		$tmp2 = array();

		$styles = (array) \explode("\n", $css);
		foreach ($styles as $k=>$v) {
			$styles[$k] = \trim($v);
			if (empty($styles[$k])) {
				continue;
			}

			// Nested rule.
			if (
				(0 === \strpos($styles[$k], '@')) &&
				(false !== \strpos($styles[$k], '⠈⠈'))
			) {
				$tmp = array(
					'@'=>false,
					'nested'=>true,
					'selector'=>'',
					'nest'=>array(),
					'raw'=>'',
				);

				// What kind of @ is this?
				\preg_match_all('/^@([a-z\-]+)/ui', $styles[$k], $matches);
				$tmp['@'] = \Blobfolio\Strings::toLower(
					$matches[1][0],
					Shim::TRUSTED
				);

				$start = \mb_strpos($styles[$k], '⠁', 0, 'UTF-8');
				if (false === $start) {
					continue;
				}

				$tmp['selector'] = \Blobfolio\Strings::toLower(
					\trim(\mb_substr($styles[$k], 0, $start, 'UTF-8')),
					Shim::JS_FOR_APOSTROPHES
				);

				$chunk = (string) \mb_substr($styles[$k], $start + 1, -1, 'UTF-8');
				$chunk = \str_replace(
					array('⠁', '⠈'),
					array('{', '}'),
					$chunk
				);
				$tmp['nest'] = self::parseCss($chunk, Shim::TRUSTED);

				// And build the raw.
				$tmp['raw'] = $tmp['selector'] . '{';
				foreach ($tmp['nest'] as $v2) {
					$tmp['raw'] .= $v2['raw'];
				}
				$tmp['raw'] .= '}';
			}
			else {
				$tmp = array(
					'@'=>false,
					'nested'=>false,
					'selectors'=>array(),
					'rules'=>array(),
					'raw'=>'',
				);

				if (0 === \strpos($styles[$k], '@')) {
					// What kind of @ is this?
					\preg_match_all('/^@([a-z\-]+)/ui', $styles[$k], $matches);
					$tmp['@'] = \Blobfolio\Strings::toLower(
						$matches[1][0],
						Shim::TRUSTED
					);
				}

				// A normal {k:v, k:v}.
				\preg_match_all('/^([^⠁]+)⠁([^⠈]*)⠈/u', $styles[$k], $matches);
				if (\count($matches[0])) {
					// Sorting selectors is easy.
					$tmp['selectors'] = (array) \explode(',', $matches[1][0]);
					$tmp['selectors'] = \array_map('trim', $tmp['selectors']);

					// Rules are trickier.
					$rules = (array) \explode(';', $matches[2][0]);
					foreach ($rules as $k2=>$v2) {
						$rules[$k2] = \trim($v2);
						if (empty($rules[$k2])) {
							continue;
						}

						$rules[$k2] = \rtrim($rules[$k2], ';') . ';';
						if (\preg_match(
							'/:(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/',
							$rules[$k2]
						)) {
							$rules[$k2] = \preg_replace(
								'/:(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u',
								"\n",
								$rules[$k2],
								1
							);

							$tmp2 = (array) \explode("\n", $rules[$k2]);
							$key = (string) \Blobfolio\Strings::toLower(
								\trim($tmp2[0]),
								Shim::TRUSTED
							);
							$value = \trim($tmp2[1]);
							$tmp['rules'][$key] = $value;
						}
						else {
							$tmp['rules']['__NONE__'] = (string) $v2;
						}
					}

					// Build the raw.
					$raw = (string) \implode(',', $tmp['selectors']) . '{';
					foreach ($tmp['rules'] as $k2=>$v2) {
						if ('__NONE__' === $k2) {
							$raw .= $v2;
						}
						else {
							$raw .= $k2 . ':' . $v2;
						}
					}
					$raw .= '}';
					$tmp['raw'] = $raw;
				}
				// This is something strange.
				else {
					$styles[$k] = \str_replace(
						array('⠁', '⠈'),
						array('{', '}'),
						$styles[$k]
					);
					$styles[$k] = \trim(\rtrim($styles[$k], ';'));
					if ('}' !== \substr($styles[$k], -1)) {
						$styles[$k] .= ';';
					}
					$tmp['rules'][] = $styles[$k];
					$tmp['raw'] = $styles[$k];
				}
			}

			$out[] = $tmp;
		}

		return $out;
	}

	/**
	 * Remove namespace (and attached nodes) from a DOMDocument
	 *
	 * @param DOMDocument $dom Object.
	 * @param string $ns Namespace.
	 * @return bool True/False.
	 */
	public static function removeNamespace(DOMDocument $dom, string $ns) : bool {
		if (empty($ns)) {
			return false;
		}

		$xpath = new DOMXPath($dom);

		$nodes = $xpath->query('//*[namespace::' . $ns . ' and not(../namespace::' . $ns . ')]');

		$x = 0;
		while ($x < $nodes->length) {
			$nodes->item($x)->removeAttributeNS(
				$nodes->item($x)->lookupNamespaceURI($ns),
				$ns
			);
			$x++;
		}

		return true;
	}

	/**
	 * Remove Nodes
	 *
	 * @param DOMNodeList $nodes Nodes.
	 * @return bool True/false.
	 */
	public static function removeNodes(DOMNodeList $nodes) : bool {
		while ($nodes->length) {
			self::removeNode($nodes->item(0));
		}

		return true;
	}

	/**
	 * Remove Node
	 *
	 * @param mixed $node Node.
	 * @return bool True/false.
	 */
	public static function removeNode($node) : bool {
		if (
			! \is_a($node, 'DOMNode') &&
			! \is_a($node, 'DOMElement')
		) {
			return false;
		}

		$node->parentNode->removeChild($node);
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
	public static function iriAttributes() : array {
		return array(
			'href'=>true,
			'src'=>true,
			'xlink:arcrole'=>true,
			'xlink:href'=>true,
			'xlink:role'=>true,
			'xml:base'=>true,
			'xmlns'=>true,
			'xmlns:xlink'=>true,
		);
	}

	/**
	 * Whitelisted Domains
	 *
	 * For SVGs and other IRI-type fields, these domains are A-OK.
	 *
	 * @return array Domains.
	 */
	public static function whitelistDomains() : array {
		// Our defaults.
		$out = array(
			'creativecommons.org'=>true,
			'inkscape.org'=>true,
			'sodipodi.sourceforge.net'=>true,
			'w3.org'=>true,
		);

		// Add user domains.
		if (
			('array' === \gettype(self::$whitelistDomains)) &&
			\count(self::$whitelistDomains)
		) {
			foreach (self::$whitelistDomains as $v) {
				if (('string' === \gettype($v)) && ! empty($v)) {
					$host = (string) \Blobfolio\Domains::niceDomain($v);
					if (! empty($host)) {
						$out[$host] = true;
					}
				}
			}

			\ksort($out);
		}

		return $out;
	}

	/**
	 * Whitelisted Protocols
	 *
	 * For SVGs and other IRI-type fields, these protocols are A-OK.
	 *
	 * @return array Protocols.
	 */
	public static function whitelistProtocols() : array {
		// Our defaults.
		$out = array(
			'http'=>true,
			'https'=>true,
		);

		// Add user protocols.
		if (
			('array' === \gettype(self::$whitelistProtocols)) &&
			\count(self::$whitelistProtocols)
		) {
			foreach (self::$whitelistProtocols as $v) {
				if (('string' === \gettype($v)) && ! empty($v)) {
					$protocol = (string) \rtrim(\strtolower($v), ':');
					$protocol = \preg_replace('/[^a-z\d_-]/', '', $protocol);
					if (! empty($protocol)) {
						$out[$protocol] = true;
					}
				}
			}

			\ksort($out);
		}

		return $out;
	}

	/**
	 * Whitelisted Attributes
	 *
	 * These are the attributes allowed by SVGs.
	 *
	 * @return array Attributes.
	 */
	public static function whitelistAttributes() : array {
		// Our defaults.
		$out = array(
			'accent-height'=>true,
			'accumulate'=>true,
			'additive'=>true,
			'alignment-baseline'=>true,
			'allowreorder'=>true,
			'alphabetic'=>true,
			'amplitude'=>true,
			'arabic-form'=>true,
			'ascent'=>true,
			'attributename'=>true,
			'attributetype'=>true,
			'autoreverse'=>true,
			'azimuth'=>true,
			'basefrequency'=>true,
			'baseline-shift'=>true,
			'baseprofile'=>true,
			'bbox'=>true,
			'begin'=>true,
			'bias'=>true,
			'by'=>true,
			'calcmode'=>true,
			'cap-height'=>true,
			'class'=>true,
			'clip'=>true,
			'clip-path'=>true,
			'clip-rule'=>true,
			'clippathunits'=>true,
			'color'=>true,
			'color-interpolation'=>true,
			'color-interpolation-filters'=>true,
			'color-profile'=>true,
			'color-rendering'=>true,
			'contentstyletype'=>true,
			'cursor'=>true,
			'cx'=>true,
			'cy'=>true,
			'd'=>true,
			'decelerate'=>true,
			'descent'=>true,
			'diffuseconstant'=>true,
			'direction'=>true,
			'display'=>true,
			'divisor'=>true,
			'dominant-baseline'=>true,
			'dur'=>true,
			'dx'=>true,
			'dy'=>true,
			'edgemode'=>true,
			'elevation'=>true,
			'enable-background'=>true,
			'end'=>true,
			'exponent'=>true,
			'externalresourcesrequired'=>true,
			'fill'=>true,
			'fill-opacity'=>true,
			'fill-rule'=>true,
			'filter'=>true,
			'filterres'=>true,
			'filterunits'=>true,
			'flood-color'=>true,
			'flood-opacity'=>true,
			'font-family'=>true,
			'font-size'=>true,
			'font-size-adjust'=>true,
			'font-stretch'=>true,
			'font-style'=>true,
			'font-variant'=>true,
			'font-weight'=>true,
			'format'=>true,
			'from'=>true,
			'fx'=>true,
			'fy'=>true,
			'g1'=>true,
			'g2'=>true,
			'glyph-name'=>true,
			'glyph-orientation-horizontal'=>true,
			'glyph-orientation-vertical'=>true,
			'glyphref'=>true,
			'gradienttransform'=>true,
			'gradientunits'=>true,
			'hanging'=>true,
			'height'=>true,
			'horiz-adv-x'=>true,
			'horiz-origin-x'=>true,
			'href'=>true,
			'id'=>true,
			'ideographic'=>true,
			'image-rendering'=>true,
			'in'=>true,
			'in2'=>true,
			'intercept'=>true,
			'k'=>true,
			'k1'=>true,
			'k2'=>true,
			'k3'=>true,
			'k4'=>true,
			'kernelmatrix'=>true,
			'kernelunitlength'=>true,
			'kerning'=>true,
			'keypoints'=>true,
			'keysplines'=>true,
			'keytimes'=>true,
			'lang'=>true,
			'lengthadjust'=>true,
			'letter-spacing'=>true,
			'lighting-color'=>true,
			'limitingconeangle'=>true,
			'local'=>true,
			'marker-end'=>true,
			'marker-mid'=>true,
			'marker-start'=>true,
			'markerheight'=>true,
			'markerunits'=>true,
			'markerwidth'=>true,
			'mask'=>true,
			'maskcontentunits'=>true,
			'maskunits'=>true,
			'mathematical'=>true,
			'max'=>true,
			'media'=>true,
			'method'=>true,
			'min'=>true,
			'mode'=>true,
			'name'=>true,
			'numoctaves'=>true,
			'offset'=>true,
			'opacity'=>true,
			'operator'=>true,
			'order'=>true,
			'orient'=>true,
			'orientation'=>true,
			'origin'=>true,
			'overflow'=>true,
			'overline-position'=>true,
			'overline-thickness'=>true,
			'paint-order'=>true,
			'panose-1'=>true,
			'pathlength'=>true,
			'patterncontentunits'=>true,
			'patterntransform'=>true,
			'patternunits'=>true,
			'pointer-events'=>true,
			'points'=>true,
			'pointsatx'=>true,
			'pointsaty'=>true,
			'pointsatz'=>true,
			'preservealpha'=>true,
			'preserveaspectratio'=>true,
			'primitiveunits'=>true,
			'r'=>true,
			'radius'=>true,
			'refx'=>true,
			'refy'=>true,
			'rendering-intent'=>true,
			'repeatcount'=>true,
			'repeatdur'=>true,
			'requiredextensions'=>true,
			'requiredfeatures'=>true,
			'restart'=>true,
			'result'=>true,
			'rotate'=>true,
			'rx'=>true,
			'ry'=>true,
			'scale'=>true,
			'seed'=>true,
			'shape-rendering'=>true,
			'slope'=>true,
			'spacing'=>true,
			'specularconstant'=>true,
			'specularexponent'=>true,
			'speed'=>true,
			'spreadmethod'=>true,
			'startoffset'=>true,
			'stddeviation'=>true,
			'stemh'=>true,
			'stemv'=>true,
			'stitchtiles'=>true,
			'stop-color'=>true,
			'stop-opacity'=>true,
			'strikethrough-position'=>true,
			'strikethrough-thickness'=>true,
			'string'=>true,
			'stroke'=>true,
			'stroke-dasharray'=>true,
			'stroke-dashoffset'=>true,
			'stroke-linecap'=>true,
			'stroke-linejoin'=>true,
			'stroke-miterlimit'=>true,
			'stroke-opacity'=>true,
			'stroke-width'=>true,
			'style'=>true,
			'surfacescale'=>true,
			'systemlanguage'=>true,
			'tabindex'=>true,
			'tablevalues'=>true,
			'target'=>true,
			'targetx'=>true,
			'targety'=>true,
			'text-anchor'=>true,
			'text-decoration'=>true,
			'text-rendering'=>true,
			'textlength'=>true,
			'to'=>true,
			'transform'=>true,
			'type'=>true,
			'u1'=>true,
			'u2'=>true,
			'underline-position'=>true,
			'underline-thickness'=>true,
			'unicode'=>true,
			'unicode-bidi'=>true,
			'unicode-range'=>true,
			'units-per-em'=>true,
			'v-alphabetic'=>true,
			'v-hanging'=>true,
			'v-ideographic'=>true,
			'v-mathematical'=>true,
			'values'=>true,
			'version'=>true,
			'vert-adv-y'=>true,
			'vert-origin-x'=>true,
			'vert-origin-y'=>true,
			'viewbox'=>true,
			'viewtarget'=>true,
			'visibility'=>true,
			'width'=>true,
			'widths'=>true,
			'word-spacing'=>true,
			'writing-mode'=>true,
			'x'=>true,
			'x-height'=>true,
			'x1'=>true,
			'x2'=>true,
			'xchannelselector'=>true,
			'xlink:actuate'=>true,
			'xlink:arcrole'=>true,
			'xlink:href'=>true,
			'xlink:role'=>true,
			'xlink:show'=>true,
			'xlink:title'=>true,
			'xlink:type'=>true,
			'xml:base'=>true,
			'xml:lang'=>true,
			'xml:space'=>true,
			'xmlns'=>true,
			'xmlns:xlink'=>true,
			'xmlns:xml'=>true,
			'y'=>true,
			'y1'=>true,
			'y2'=>true,
			'ychannelselector'=>true,
			'z'=>true,
			'zoomandpan'=>true,
		);

		// Add user attributes.
		if (
			('array' === \gettype(self::$whitelistAttributes)) &&
			\count(self::$whitelistAttributes)
		) {
			foreach (self::$whitelistAttributes as $v) {
				if (('string' === \gettype($v)) && ! empty($v)) {
					$attribute = (string) \trim(\strtolower($v));
					if (! empty($attribute)) {
						$out[$attribute] = true;
					}
				}
			}

			\ksort($out);
		}

		return $out;
	}

	/**
	 * Whitelisted Tags
	 *
	 * These tags are allowed by SVGs.
	 *
	 * @return array Tags.
	 */
	public static function whitelistTags() : array {
		// Our defaults.
		$out = array(
			'a'=>true,
			'altglyph'=>true,
			'altglyphdef'=>true,
			'altglyphitem'=>true,
			'animate'=>true,
			'animatecolor'=>true,
			'animatemotion'=>true,
			'animatetransform'=>true,
			'audio'=>true,
			'canvas'=>true,
			'circle'=>true,
			'clippath'=>true,
			'color-profile'=>true,
			'cursor'=>true,
			'defs'=>true,
			'desc'=>true,
			'discard'=>true,
			'ellipse'=>true,
			'feblend'=>true,
			'fecolormatrix'=>true,
			'fecomponenttransfer'=>true,
			'fecomposite'=>true,
			'feconvolvematrix'=>true,
			'fediffuselighting'=>true,
			'fedisplacementmap'=>true,
			'fedistantlight'=>true,
			'fedropshadow'=>true,
			'feflood'=>true,
			'fefunca'=>true,
			'fefuncb'=>true,
			'fefuncg'=>true,
			'fefuncr'=>true,
			'fegaussianblur'=>true,
			'feimage'=>true,
			'femerge'=>true,
			'femergenode'=>true,
			'femorphology'=>true,
			'feoffset'=>true,
			'fepointlight'=>true,
			'fespecularlighting'=>true,
			'fespotlight'=>true,
			'fetile'=>true,
			'feturbulence'=>true,
			'filter'=>true,
			'font'=>true,
			'font-face'=>true,
			'font-face-format'=>true,
			'font-face-name'=>true,
			'font-face-src'=>true,
			'font-face-uri'=>true,
			'g'=>true,
			'glyph'=>true,
			'glyphref'=>true,
			'hatch'=>true,
			'hatchpath'=>true,
			'hkern'=>true,
			'image'=>true,
			'line'=>true,
			'lineargradient'=>true,
			'marker'=>true,
			'mask'=>true,
			'mesh'=>true,
			'meshgradient'=>true,
			'meshpatch'=>true,
			'meshrow'=>true,
			'metadata'=>true,
			'missing-glyph'=>true,
			'mpath'=>true,
			'path'=>true,
			'pattern'=>true,
			'polygon'=>true,
			'polyline'=>true,
			'radialgradient'=>true,
			'rect'=>true,
			'set'=>true,
			'solidcolor'=>true,
			'stop'=>true,
			'style'=>true,
			'svg'=>true,
			'switch'=>true,
			'symbol'=>true,
			'text'=>true,
			'textpath'=>true,
			'title'=>true,
			'tref'=>true,
			'tspan'=>true,
			'unknown'=>true,
			'use'=>true,
			'video'=>true,
			'view'=>true,
			'vkern'=>true,
		);

		// Add user tags.
		if (
			('array' === \gettype(self::$whitelistTags)) &&
			\count(self::$whitelistTags)
		) {
			foreach (self::$whitelistTags as $v) {
				if (('string' === \gettype($v)) && ! empty($v)) {
					$tag = (string) \trim(\strtolower($v));
					if (! empty($tag)) {
						$out[$tag] = true;
					}
				}
			}

			\ksort($out);
		}

		return $out;
	}
}
