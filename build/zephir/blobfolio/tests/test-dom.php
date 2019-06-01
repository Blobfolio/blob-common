<?php
/**
 * Blobfolio\Dom
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class dom_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: attributeValue
	 *
	 * @dataProvider data_attributeValue
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_attributeValue(string $value, string $expected) {
		$result = \Blobfolio\Dom::attributeValue($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: iriValue
	 *
	 * @dataProvider data_iriValue
	 *
	 * @param string $value Value.
	 * @param mixed $protocols Protocols.
	 * @param mixed $domains Domains.
	 * @param string $expected Expected.
	 */
	function test_iriValue(string $value, $protocols, $domains, string $expected) {
		// Set protocols and/or domains.
		\Blobfolio\Dom::$whitelistProtocols = $protocols;
		\Blobfolio\Dom::$whitelistDomains = $domains;

		$result = \Blobfolio\Dom::iriValue($value);

		// Unset protocols and/or domains.
		\Blobfolio\Dom::$whitelistProtocols = null;
		\Blobfolio\Dom::$whitelistDomains = null;

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: linkify
	 *
	 * @dataProvider data_linkify
	 *
	 * @param string $value Value.
	 * @param mixed $args Args.
	 * @param string $expected Expected.
	 */
	function test_linkify(string $value, $args, string $expected) {
		$result = \Blobfolio\Dom::linkify($value, $args);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: decodeEntities
	 *
	 * @dataProvider data_decodeEntities
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_decodeEntities(string $value, string $expected) {
		$result = \Blobfolio\Dom::decodeEntities($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: decodeJsEntities
	 *
	 * @dataProvider data_decodeJsEntities
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_decodeJsEntities(string $value, string $expected) {
		$result = \Blobfolio\Dom::decodeJsEntities($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: html
	 *
	 * @dataProvider data_html
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_html(string $value, string $expected) {
		$result = \Blobfolio\Dom::html($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: js
	 *
	 * @dataProvider data_js
	 *
	 * @param string $value Value.
	 * @param string $quote Quote.
	 * @param string $expected Expected.
	 */
	function test_js($value, $quote, string $expected) {
		$result = \Blobfolio\Dom::js($value, $quote);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: svgToDom
	 *
	 * @dataProvider data_svgToDom
	 *
	 * @param string $value Value.
	 * @param bool $expected Expected.
	 */
	function test_svgToDom(string $value, bool $expected) {
		$result = \Blobfolio\Dom::svgToDom($value);

		$this->assertSame($expected, is_a($result, '\\DOMDocument'));
	}

	/**
	 * Test: domToSvg
	 *
	 * @dataProvider data_domToSvg
	 *
	 * @param string $value Value.
	 * @param bool $expected Expected.
	 */
	function test_domToSvg(string $value, bool $expected) {
		// First we need a DOM object.
		$dom = \Blobfolio\Dom::svgToDom($value);
		if (!is_a($dom, '\\DOMDocument')) {
			$this->markTestSkipped('A DOMDocument could not be created from the source SVG.');
		}

		// Now we can run the method we're actually testing.
		$result = \Blobfolio\Dom::domToSvg($dom);

		$this->assertSame($expected, false !== strpos($result, '<svg'));
	}

	/**
	 * Test: getNodesByClass
	 *
	 * @dataProvider data_getNodesByClass
	 *
	 * @param string $value Value.
	 * @param array $classes Classes.
	 * @param int $expected Expected count.
	 */
	function test_getNodesByClass($value, array $classes, int $expected) {
		$result = \Blobfolio\Dom::getNodesByClass($value, $classes);

		$this->assertSame('array', gettype($result));
		$this->assertSame($expected, count($result));
	}

	/**
	 * Test: innerHtml
	 */
	function test_innerHtml() {
		$str = '<div><span><br/><strong>hello</strong>world</span></div>';
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = false;
		$dom->preserveWhiteSpace = false;
		$dom->loadHTML($str);

		$div = $dom->getElementsByTagName('div')->item(0);
		$innerhtml = \Blobfolio\Dom::innerHtml($div);

		$this->assertEquals(
			'<span><br><strong>hello</strong>world</span>',
			$innerhtml
		);

		$innerhtml = \Blobfolio\Dom::innerHtml($div, true, LIBXML_NOEMPTYTAG);
		$this->assertEquals(
			'<span><br></br><strong>hello</strong>world</span>',
			$innerhtml
		);
	}

	/**
	 * Test: mergeClasses
	 *
	 * @dataProvider data_mergeClasses
	 */
	function test_mergeClasses() {
		$args = func_get_args();
		$expected = array_pop($args);

		$result = call_user_func_array(
			array('\\Blobfolio\\Dom', 'mergeClasses'),
			$args
		);

		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: parseCss
	 */
	function test_parseCss() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \Blobfolio\Dom::svgToDom($svg);
		$style = $dom->getElementsByTagName('style');
		$style = $style->item(0);

		$parsed = \Blobfolio\Dom::parseCss($style->nodeValue);

		$this->assertSame("array", gettype($parsed));
		$this->assertEquals(1, count($parsed));
		$this->assertEquals('.k3xzp{fill:currentColor;}', $parsed[0]['raw']);
	}

	/**
	 * Test: removeNamespace
	 *
	 * @return void Nothing.
	 */
	function test_removeNamespace() {
		$svg = file_get_contents(self::ASSETS . 'minus.svg');

		// Before.
		$this->assertTrue(false !== strpos($svg, 'svg:'));
		$this->assertTrue(false !== strpos($svg, 'xmlns:svg'));

		$dom = \Blobfolio\Dom::svgToDom($svg);

		// After.
		\Blobfolio\Dom::removeNamespace($dom, 'svg');
		$svg = \Blobfolio\Dom::domToSvg($dom);

		$this->assertTrue(false === strpos($svg, 'svg:'));
		$this->assertTrue(false === strpos($svg, 'xmlns:svg'));
	}

	/**
	 * Test: removeNodes
	 *
	 * @return void Nothing.
	 */
	function test_removeNodes() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \Blobfolio\Dom::svgToDom($svg);

		// Before.
		$paths = $dom->getElementsByTagName('path');
		$this->assertEquals(1, $paths->length);

		// After.
		\Blobfolio\Dom::removeNodes($paths);
		$this->assertEquals(0, $paths->length);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: attributeValue
	 *
	 * @return array Values.
	 */
	function data_attributeValue() {
		return array(
			array(
				'&nbsp;Björk"&amp;quot; ',
				'Björk""',
			),
			array(
				"\033" . '&nbsp;Björk"&amp;quot; ',
				'Björk""',
			),
		);
	}

	/**
	 * Data: iriValue
	 *
	 * @return array Values.
	 */
	function data_iriValue() {
		$image = \Blobfolio\Images::getBlankImage();

		return array(
			array(
				'#example',
				null,
				null,
				'#example',
			),
			array(
				'//w3.org',
				null,
				null,
				'https://w3.org',
			),
			array(
				'http://blobfolio.com',
				null,
				null,
				'',
			),
			array(
				'http://blobfolio.com',
				null,
				array('blobfolio.com'),
				'http://blobfolio.com',
			),
			array(
				'ftp://w3.org',
				null,
				null,
				'',
			),
			array(
				'ftp://w3.org',
				array('ftp', 'ftps'),
				null,
				'ftp://w3.org',
			),
			array(
				' script: alert(hi);',
				null,
				null,
				'',
			),
			array(
				$image,
				null,
				null,
				'',
			),
			array(
				$image,
				array('data'),
				null,
				$image,
			),
			array(
				$image,
				array('data:'),
				null,
				$image,
			),
		);
	}

	/**
	 * Data: linkify
	 *
	 * @return array Values.
	 */
	function data_linkify() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'blobfolio.com',
				null,
				'<a href="http://blobfolio.com">blobfolio.com</a>',
			),
			array(
				'https://blobfolio.com/',
				null,
				'<a href="https://blobfolio.com/">https://blobfolio.com/</a>',
			),
			array(
				'Welcome to blobfolio.com!',
				null,
				'Welcome to <a href="http://blobfolio.com">blobfolio.com</a>!',
			),
			array(
				'bad.sch.uk',
				null,
				'bad.sch.uk',
			),
			array(
				'www.blobfolio.com',
				null,
				'<a href="http://www.blobfolio.com">www.blobfolio.com</a>',
			),
			array(
				'me@localhost',
				null,
				'me@localhost',
			),
			array(
				'me@bad.sch.uk',
				null,
				'me@bad.sch.uk',
			),
			array(
				'"blobfolio.com"',
				null,
				'"<a href="http://blobfolio.com">blobfolio.com</a>"',
			),
			array(
				'(blobfolio.com)',
				null,
				'(<a href="http://blobfolio.com">blobfolio.com</a>)',
			),
			array(
				'[blobfolio.com]',
				null,
				'[<a href="http://blobfolio.com">blobfolio.com</a>]',
			),
			array(
				'{blobfolio.com}',
				null,
				'{<a href="http://blobfolio.com">blobfolio.com</a>}',
			),
			array(
				'me@blobfolio.com',
				null,
				'<a href="mailto:me@blobfolio.com">me@blobfolio.com</a>',
			),
			array(
				'Email me@blobfolio.com for more.',
				null,
				'Email <a href="mailto:me@blobfolio.com">me@blobfolio.com</a> for more.',
			),
			array(
				'blobfolio.com me@blobfolio.com',
				null,
				'<a href="http://blobfolio.com">blobfolio.com</a> <a href="mailto:me@blobfolio.com">me@blobfolio.com</a>',
			),
			array(
				'ftp://user:pass@☺.com',
				null,
				'<a href="ftp://user:pass@' . $smiley_host . '">ftp://user:pass@☺.com</a>',
			),
			array(
				'smiley@☺.com',
				null,
				'<a href="mailto:smiley@' . $smiley_host . '">smiley@☺.com</a>',
			),
			array(
				'+12015550123',
				null,
				'<a href="tel:+12015550123">+12015550123</a>',
			),
			array(
				'+1 201-555-0123',
				null,
				'<a href="tel:+12015550123">+1 201-555-0123</a>',
			),
			array(
				'201-555-0123',
				null,
				'<a href="tel:+12015550123">201-555-0123</a>',
			),
			array(
				'(201) 555-0123',
				null,
				'<a href="tel:+12015550123">(201) 555-0123</a>',
			),
			array(
				'201.555.0123',
				null,
				'<a href="tel:+12015550123">201.555.0123</a>',
			),
			array(
				'I ate 234234234 apples!',
				null,
				'I ate 234234234 apples!',
			),
			array(
				'Call me at (201) 555-0123.',
				null,
				'Call me at <a href="tel:+12015550123">(201) 555-0123</a>.',
			),
			array(
				'blobfolio.com',
				array(
					'class'=>array('link', 'nav'),
					'rel'=>'apples',
					'target'=>'_blank',
				),
				'<a href="http://blobfolio.com" class="link nav" rel="apples" target="_blank">blobfolio.com</a>',
			),
			array(
				'me@blobfolio.com',
				array(
					'class'=>array('link', 'nav'),
					'rel'=>'apples',
					'target'=>'_blank',
				),
				'<a href="mailto:me@blobfolio.com" class="link nav" rel="apples" target="_blank">me@blobfolio.com</a>',
			),
			array(
				'blobfolio.com',
				array(
					'class'=>'link',
				),
				'<a href="http://blobfolio.com" class="link">blobfolio.com</a>',
			),
		);
	}

	/**
	 * Data: decodeEntities
	 *
	 * @return array Data.
	 */
	function data_decodeEntities() {
		return array(
			array(
				'Happy & Healthy',
				'Happy & Healthy',
			),
			array(
				'5&#48;&cent;',
				'50¢',
			),
			array(
				'50&amp;cent;',
				'50¢',
			),
			array(
				'I don&#8217;t like slanty quotes.',
				'I don’t like slanty quotes.',
			),
		);
	}

	/**
	 * Data: decodeJsEntities
	 *
	 * @return array Data.
	 */
	function data_decodeJsEntities() {
		return array(
			array(
				'\\nhello\\u00c1',
				"\nhelloÁ",
			),
			array(
				'\\u0075',
				'u',
			),
			array(
				'\\\\u0075\\u0030\\u0030\\u0063\\u0031',
				'Á',
			),
			array(
				'Hi\\\\bb There',
				'Hi\\' . chr(0x08) . 'b There',
			),
		);
	}

	/**
	 * Data: html
	 *
	 * @return array Data.
	 */
	function data_html() {
		return array(
			array(
				'<b>"Björk"</b>',
				'&lt;b&gt;&quot;Björk&quot;&lt;/b&gt;',
			),
			array(
				'<b>',
				'&lt;b&gt;',
			),
		);
	}

	/**
	 * Data: js
	 *
	 * @return array Data.
	 */
	function data_js() {
		return array(
			array(
				"What's up, doc?",
				\Blobfolio\Blobfolio::JS_FOR_APOSTROPHES,
				'What\\\'s up, doc?',
			),
			array(
				"What's up, doc?",
				\Blobfolio\Blobfolio::JS_FOR_QUOTES,
				"What's up, doc?",
			),
			array(
				'"Hello"',
				\Blobfolio\Blobfolio::JS_FOR_QUOTES,
				'\"Hello\"',
			),
			array(
				'"Hello"',
				\Blobfolio\Blobfolio::JS_FOR_APOSTROPHES,
				'"Hello"',
			),
			array(
				'"Hello"' . "\n\n",
				\Blobfolio\Blobfolio::JS_FOR_QUOTES,
				'\"Hello\"',
			),
			array(
				'</script>><script>prompt(1)</script>',
				\Blobfolio\Blobfolio::JS_FOR_APOSTROPHES,
				'<\/script>><script>prompt(1)<\/script>',
			),
		);
	}

	/**
	 * Data: svgToDom
	 *
	 * @return array Values.
	 */
	function data_svgToDom() {
		return array(
			array(
				file_get_contents(self::ASSETS . 'pi.svg'),
				true,
			),
			array(
				file_get_contents(self::ASSETS . 'minus.svg'),
				true,
			),
			array(
				file_get_contents(self::ASSETS . 'roles.csv'),
				false,
			),
		);
	}

	/**
	 * Data: domToSvg
	 *
	 * @return array Values.
	 */
	function data_domToSvg() {
		return array(
			array(
				file_get_contents(self::ASSETS . 'pi.svg'),
				true,
			),
			array(
				file_get_contents(self::ASSETS . 'minus.svg'),
				true,
			),
		);
	}

	/**
	 * Data: getNodesByClass
	 *
	 * @return array Values.
	 */
	function data_getNodesByClass() {
		return array(
			array(
				\Blobfolio\Dom::svgToDom(file_get_contents(self::ASSETS . 'pi.svg')),
				array('k3xzp'),
				1,
			),
			array(
				\Blobfolio\Dom::svgToDom(file_get_contents(self::ASSETS . 'pi.svg')),
				array('nobody'),
				0,
			),
		);
	}

	/**
	 * Data: mergeClasses
	 *
	 * @return array Values.
	 */
	function data_mergeClasses() {
		// The last value in each set is the $expected.
		return array(
			array(
				'foo',
				'bar',
				array('foo', 'bar'),
			),
			array(
				'foo   .bar',
				'bar',
				array('foo', 'bar'),
			),
			array(
				array('foo', array('bar')),
				'bar',
				array('cabbage'),
				array('foo', 'bar', 'cabbage'),
			),
			array(
				array('foo', array('bar', 'cabbage', array('pineapple'))),
				'bar',
				array('cabbage'),
				array('foo', 'bar', 'cabbage', 'pineapple'),
			),
		);
	}
}
