<?php
/**
 * Sanitize tests.
 *
 * PHPUnit tests for sanitize.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\sanitize;
use \blobfolio\common\constants;

/**
 * Test Suite
 */
class sanitize_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// --------------------------------------------------------------------
	// Tests
	// --------------------------------------------------------------------

	/**
	 * ::accents()
	 *
	 * @dataProvider data_accents
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_accents($value, $expected) {
		$this->assertEquals($expected, sanitize::accents($value));
	}

	/**
	 * ::attribute_value()
	 *
	 * @dataProvider data_attribute_value
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_attribute_value($value, $expected) {
		$this->assertEquals($expected, sanitize::attribute_value($value));
	}

	/**
	 * ::cc()
	 *
	 * @dataProvider data_cc
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_cc($value, $expected) {
		$this->assertSame($expected, sanitize::cc($value));
	}

	/**
	 * ::control_characters()
	 *
	 * @dataProvider data_control_characters
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_control_characters($value, $expected) {
		$this->assertEquals($expected, sanitize::control_characters($value));
	}

	/**
	 * ::country()
	 *
	 * @dataProvider data_country
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_country($value, $expected) {
		$this->assertSame($expected, sanitize::country($value));
	}

	/**
	 * ::csv()
	 *
	 * @dataProvider data_csv
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_csv($value, $expected) {
		$this->assertEquals($expected, sanitize::csv($value));
	}

	/**
	 * ::datetime()
	 *
	 * @dataProvider data_datetime
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_datetime($value, $expected) {
		$this->assertEquals($expected, sanitize::datetime($value));
	}

	/**
	 * ::date()
	 *
	 * @dataProvider data_datetime
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_date($value, $expected) {
		// We can use the datetime test data, but need to cut
		// it to date-size.
		if (is_array($expected)) {
			foreach ($expected as $k=>$v) {
				$expected[$k] = substr($v, 0, 10);
			}
		}
		else {
			$expected = substr($expected, 0, 10);
		}

		$this->assertEquals($expected, sanitize::date($value));
	}

	/**
	 * ::domain()
	 *
	 * @dataProvider data_domain
	 *
	 * @param string $value Value.
	 * @param bool $unicode Unicode.
	 * @param string $expected Expected.
	 */
	function test_domain($value, $unicode, $expected) {
		$this->assertEquals($expected, sanitize::domain($value, $unicode));
	}

	/**
	 * ::ean()
	 *
	 * @dataProvider data_ean
	 *
	 * @param string $value Value.
	 * @param bool $formatted Formatted.
	 * @param string $expected Expected.
	 */
	function test_ean($value, $formatted, $expected) {
		$this->assertEquals($expected, sanitize::ean($value, $formatted));
	}

	/**
	 * ::email()
	 *
	 * @dataProvider data_email
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_email($value, $expected) {
		$this->assertEquals($expected, sanitize::email($value));
	}

	/**
	 * ::file_extension()
	 *
	 * @dataProvider data_file_extension
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_file_extension($value, $expected) {
		$this->assertEquals($expected, sanitize::file_extension($value));
	}

	/**
	 * ::html()
	 *
	 * @dataProvider data_html
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_html($value, $expected) {
		$this->assertEquals($expected, sanitize::html($value));
	}

	/**
	 * ::hostname()
	 *
	 * @dataProvider data_hostname
	 *
	 * @param string $value Value.
	 * @param bool $www Keep www.
	 * @param bool $unicode Unicode.
	 * @param string $expected Expected.
	 */
	function test_hostname($value, $www, $unicode, $expected) {
		$this->assertSame($expected, sanitize::hostname($value, $www, $unicode));
	}

	/**
	 * ::ip()
	 *
	 * @dataProvider data_ip
	 *
	 * @param string $value Value.
	 * @param bool $restricted Allow reserved.
	 * @param bool $condense Condense IPv6.
	 * @param string $expected Expected.
	 */
	function test_ip($value, $restricted, $condense, $expected) {
		$this->assertEquals($expected, sanitize::ip($value, $restricted, $condense));
	}

	/**
	 * ::iri_value()
	 *
	 * @dataProvider data_iri_value
	 *
	 * @param string $value Value.
	 * @param mixed $protocols Allowed Protocols.
	 * @param mixed $domains Allowed domains.
	 * @param string $expected Expected.
	 */
	function test_iri_value($value, $protocols, $domains, $expected) {
		$this->assertEquals($expected, sanitize::iri_value($value, $protocols, $domains));
	}

	/**
	 * ::isbn()
	 *
	 * @dataProvider data_isbn
	 *
	 * @param string $value Value.
	 * @param bool $formatted Formatted.
	 * @param string $expected Expected.
	 */
	function test_isbn($value, $formatted, $expected) {
		$this->assertEquals($expected, sanitize::isbn($value, $formatted));
	}

	/**
	 * ::js()
	 *
	 * @dataProvider data_js
	 *
	 * @param string $value Value.
	 * @param string $quote Quote.
	 * @param string $expected Expected.
	 */
	function test_js($value, $quote, $expected) {
		$this->assertEquals($expected, sanitize::js($value, $quote));
	}

	/**
	 * ::mime()
	 *
	 * @dataProvider data_mime
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_mime($value, $expected) {
		$this->assertEquals($expected, sanitize::mime($value));
	}

	/**
	 * ::name()
	 *
	 * @dataProvider data_name
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_name($value, $expected) {
		$this->assertEquals($expected, sanitize::name($value));
	}

	/**
	 * ::password()
	 *
	 * @dataProvider data_password
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_password($value, $expected) {
		$this->assertEquals($expected, sanitize::password($value));
	}

	/**
	 * ::printable()
	 *
	 * @dataProvider data_printable
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_printable($value, $expected) {
		$this->assertEquals($expected, sanitize::printable($value));
	}

	/**
	 * ::province()
	 *
	 * @dataProvider data_province
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_province($value, $expected) {
		$this->assertEquals($expected, sanitize::province($value));
	}

	/**
	 * ::quotes()
	 *
	 * @dataProvider data_quotes
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_quotes($value, $expected) {
		$this->assertEquals($expected, sanitize::quotes($value));
	}

	/**
	 * ::state()
	 *
	 * @dataProvider data_state
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_state($value, $expected) {
		$this->assertEquals($expected, sanitize::state($value));
	}

	/**
	 * ::svg()
	 *
	 * @dataProvider data_svg
	 *
	 * @param string $svg SVG.
	 */
	function test_svg($svg) {
		if (!class_exists('DOMDocument') || !class_exists('DOMXPath')) {
			$this->markTestSkipped('DOM is not installed.');
		}

		$tests = array(
			'&#109;',
			'&#123',
			'//hello',
			'<script',
			'comment',
			'data:',
			'Gotcha',
			'http://example.com',
			'max:volume',
			'onclick',
			'onload',
			'xmlns:foobar',
			'XSS',
		);

		$result = sanitize::svg($svg);

		foreach ($tests as $v) {
			$this->assertSame(false, strpos($result, $v));
		}
	}

	/**
	 * ::timezone()
	 *
	 * @dataProvider data_timezone
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_timezone($value, $expected) {
		$this->assertEquals($expected, sanitize::timezone($value));
	}

	/**
	 * ::to_range()
	 *
	 * @dataProvider data_to_range
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param mixed $expected Expected.
	 */
	function test_to_range($value, $min, $max, $expected) {
		$this->assertEquals($expected, sanitize::to_range($value, $min, $max));
	}

	/**
	 * ::upc()
	 *
	 * @dataProvider data_upc
	 *
	 * @param string $value Value.
	 * @param bool $formatted Formatted.
	 * @param string $expected Expected.
	 */
	function test_upc($value, $formatted, $expected) {
		$this->assertEquals($expected, sanitize::upc($value, $formatted));
	}

	/**
	 * ::url()
	 *
	 * @dataProvider data_url
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_url($value, $expected) {
		$this->assertEquals($expected, sanitize::url($value));
	}

	/**
	 * ::utf8()
	 *
	 * @dataProvider data_utf8
	 *
	 * @param mixed $value Value.
	 */
	function test_utf8($value) {
		$encoding = strtoupper(mb_detect_encoding(sanitize::utf8($value)));
		$this->assertSame(true, in_array($encoding, array('ASCII', 'UTF-8'), true));
	}

	/**
	 * ::whitespace()
	 *
	 * @dataProvider data_whitespace
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	function test_whitespace($value, $newlines, $expected) {
		$this->assertEquals($expected, sanitize::whitespace($value, $newlines));
	}

	/**
	 * ::whitespace_multiline()
	 *
	 * @dataProvider data_whitespace
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	function test_whitespace_multiline($value, $newlines, $expected) {
		$this->assertEquals($expected, sanitize::whitespace_multiline($value, $newlines));
	}

	/**
	 * ::zip5()
	 *
	 * @dataProvider data_zip5
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_zip5($value, $expected) {
		$this->assertEquals($expected, sanitize::zip5($value));
	}

	// -------------------------------------------------------------------- end tests



	// --------------------------------------------------------------------
	// Data
	// --------------------------------------------------------------------

	/**
	 * Data for ::accents()
	 *
	 * @return array Data.
	 */
	function data_accents() {
		return array(
			array(
				'Björk Guðmundsdóttir, best þekkt sem Björk (fædd 21. nóvember 1965 í Reykjavík) er íslenskur popptónlistarmaður, sem hefur náð alþjóðlegri hylli.',
				'Bjork Gudmundsdottir, best thekkt sem Bjork (faedd 21. november 1965 i Reykjavik) er islenskur popptonlistarmadur, sem hefur nad althjodlegri hylli.'
			),
			array(
				'Nabokov explore plusieurs thèmes, dont certains déjà présents dans ses ouvrages précédents.',
				'Nabokov explore plusieurs themes, dont certains deja presents dans ses ouvrages precedents.'
			),
			array(
				array('Björk'),
				array('Bjork')
			),
		);
	}

	/**
	 * Data for ::attribute_value()
	 *
	 * @return array Data.
	 */
	function data_attribute_value() {
		return array(
			array(
				'&nbsp;Björk"&amp;quot; ',
				'Björk""'
			),
			array(
				array('&nbsp;Björk"&amp;quot; '),
				array('Björk""')
			),
		);
	}

	/**
	 * Data for ::cc()
	 *
	 * @return array Data.
	 */
	function data_cc() {
		return array(
			array(
				'4242424242424242',
				'4242424242424242'
			),
			array(
				4242424242424242,
				'4242424242424242'
			),
			array(
				'4242424242424241',
				false
			),
			array(
				'5555555555554444',
				'5555555555554444'
			),
			array(
				'378282246310005',
				'378282246310005'
			),
			array(
				'378734493671000',
				'378734493671000'
			),
			array(
				'6011111111111117',
				'6011111111111117'
			),
			array(
				'4012888888881881',
				'4012888888881881'
			),
			array(
				'4222222222222',
				'4222222222222'
			),
		);
	}

	/**
	 * Data for ::control_characters()
	 *
	 * @return array Data.
	 */
	function data_control_characters() {
		return array(
			array(
				'\0Björk',
				'Björk'
			),
			array(
				array('\0Björk'),
				array('Björk')
			),
		);
	}

	/**
	 * Data for ::country()
	 *
	 * @return array Data.
	 */
	function data_country() {
		return array(
			array(
				'USA',
				'US'
			),
			array(
				'us',
				'US'
			),
			array(
				'Nobody',
				''
			),
			array(
				'CANADA',
				'CA'
			),
			array(
				array('CANADA'),
				array('CA')
			),
		);
	}

	/**
	 * Data for ::csv()
	 *
	 * @return array Data.
	 */
	function data_csv() {
		return array(
			array(
				'\\\'hello"',
				'\\\'hello""'
			),
			array(
				"Hello\nWorld",
				'Hello World'
			),
			array(
				array("Hello\nWorld"),
				array('Hello World')
			),
		);
	}

	/**
	 * Data for ::datetime() and ::date()
	 *
	 * @return array Data.
	 */
	function data_datetime() {
		return array(
			array(
				'2015-01-02',
				'2015-01-02 00:00:00'
			),
			array(
				'2015-01-02 13:23:11',
				'2015-01-02 13:23:11'
			),
			array(
				strtotime('2015-01-02 13:23:11'),
				'2015-01-02 13:23:11'
			),
			array(
				'20150102',
				'2015-01-02 00:00:00'
			),
			array(
				20150102,
				'2015-01-02 00:00:00'
			),
			array(
				'Not Time',
				'0000-00-00 00:00:00'
			),
			array(
				'0000-00-00 12:30:30',
				'0000-00-00 00:00:00'
			),
			array(
				array(20150102),
				array('2015-01-02 00:00:00')
			),
		);
	}

	/**
	 * Data for ::domain()
	 *
	 * @return array Data.
	 */
	function data_domain() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				false,
				'google.com'
			),
			array(
				'www.Google.com',
				false,
				'google.com'
			),
			array(
				'☺.com',
				true,
				'☺.com'
			),
			array(
				'50.116.18.174',
				false,
				''
			),
			array(
				'//☺.com',
				false,
				$smiley_host
			),
			array(
				array('www.Google.com'),
				false,
				array('google.com')
			),
		);
	}

	/**
	 * Data for ::ean()
	 *
	 * @return array Data.
	 */
	function data_ean() {
		return array(
			array(
				'0',
				false,
				''
			),
			array(
				'074299160691',
				false,
				'0074299160691'
			),
			array(
				'00709077260149',
				false,
				'0709077260149'
			),
			array(
				'709077260149',
				false,
				'0709077260149'
			),
			array(
				'0709077260555',
				false,
				''
			),
			array(
				'0709077260149',
				true,
				'0-709077-260149'
			),
			array(
				array(
					'0709077260149',
					'0051511500275'
				),
				true,
				array(
					'0-709077-260149',
					'0-051511-500275'
				)
			),
		);
	}

	/**
	 * Data for ::email()
	 *
	 * @return array Data.
	 */
	function data_email() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'Hello@Blo"bfolio.Com',
				'hello@blobfolio.com'
			),
			array(
				'helo@blobfolio',
				''
			),
			array(
				'hello@☺.com',
				"hello@$smiley_host"
			),
			array(
				'hello+me@blobfolio.com',
				'hello+me@blobfolio.com'
			),
			array(
				' .hello(comment)+me@blobfolio.com',
				'hello+me@blobfolio.com'
			),
			array(
				array(' .hello(comment)+me@blobfolio.com'),
				array('hello+me@blobfolio.com')
			),
		);
	}

	/**
	 * Data for ::file_extension()
	 *
	 * @return array Data.
	 */
	function data_file_extension() {
		return array(
			array(
				'  .JPEG ',
				'jpeg'
			),
			array(
				'.tar.gz',
				'tar.gz'
			),
			array(
				array('.tar.gz'),
				array('tar.gz')
			),
		);
	}

	/**
	 * Data for ::html()
	 *
	 * @return array Data.
	 */
	function data_html() {
		return array(
			array(
				'<b>"Björk"</b>',
				'&lt;b&gt;&quot;Björk&quot;&lt;/b&gt;'
			),
			array(
				array('<b>'),
				array('&lt;b&gt;')
			),
		);
	}

	/**
	 * Data for ::hostname()
	 *
	 * @return array Data.
	 */
	function data_hostname() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				false,
				false,
				'google.com'
			),
			array(
				'www.Google.com',
				false,
				false,
				'google.com'
			),
			array(
				'www.☺.com',
				false,
				true,
				'☺.com'
			),
			array(
				'http://www.☺.com',
				true,
				true,
				'www.☺.com'
			),
			array(
				'50.116.18.174',
				false,
				false,
				'50.116.18.174'
			),
			array(
				'//☺.com',
				false,
				false,
				$smiley_host
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				false,
				false,
				'2600:3c00::f03c:91ff:feae:ff2'
			),
			array(
				'localhost',
				true,
				true,
				'localhost'
			),
		);
	}

	/**
	 * Data for ::ip()
	 *
	 * @return array Data.
	 */
	function data_ip() {
		return array(
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				false,
				true,
				'2600:3c00::f03c:91ff:feae:ff2'
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				false,
				true,
				'2600:3c00::f03c:91ff:feae:ff2'
			),
			array(
				'2600:3c00::f03c:91ff:feae:ff2',
				false,
				false,
				'2600:3c00:0000:0000:f03c:91ff:feae:0ff2'
			),
			array(
				'127.0.0.1',
				false,
				true,
				''
			),
			array(
				'127.0.0.1',
				true,
				true,
				'127.0.0.1'
			),
			array(
				'::127.0.0.1',
				true,
				true,
				'127.0.0.1'
			),
			array(
				'[::127.0.0.1]',
				true,
				true,
				'127.0.0.1'
			),
			array(
				'::1',
				false,
				true,
				''
			),
			array(
				'[::1]',
				true,
				true,
				'::1'
			),
			array(
				array('[::1]'),
				true,
				true,
				array('::1')
			),
			array(
				array('[::1]'),
				true,
				false,
				array('0000:0000:0000:0000:0000:0000:0000:0001')
			),
		);
	}

	/**
	 * Data for ::iri_value()
	 *
	 * @return array Data.
	 */
	function data_iri_value() {
		return array(
			array(
				'#example',
				null,
				null,
				'#example'
			),
			array(
				'//w3.org',
				null,
				null,
				'https://w3.org'
			),
			array(
				'http://blobfolio.com',
				null,
				null,
				''
			),
			array(
				'http://blobfolio.com',
				null,
				array('blobfolio.com'),
				'http://blobfolio.com'
			),
			array(
				'ftp://w3.org',
				null,
				null,
				''
			),
			array(
				'ftp://w3.org',
				array('ftp', 'ftps'),
				null,
				'ftp://w3.org'
			),
			array(
				' script: alert(hi);',
				null,
				null,
				''
			),
			array(
				constants::BLANK_IMAGE,
				null,
				null,
				''
			),
			array(
				constants::BLANK_IMAGE,
				'data',
				null,
				constants::BLANK_IMAGE
			),
			array(
				array(constants::BLANK_IMAGE),
				'data',
				null,
				array(constants::BLANK_IMAGE)
			),
		);
	}

	/**
	 * Data for ::isbn()
	 *
	 * @return array Data.
	 */
	function data_isbn() {
		return array(
			array(
				'0',
				false,
				''
			),
			array(
				'0939117606',
				false,
				'0939117606'
			),
			array(
				'939117606',
				false,
				'0939117606'
			),
			array(
				'9780939117604',
				false,
				'9780939117604'
			),
			array(
				'0-9752298-0-X',
				false,
				'097522980X'
			),
			array(
				'0975229800',
				false,
				''
			)
		);
	}

	/**
	 * Data for ::js()
	 *
	 * @return array Data.
	 */
	function data_js() {
		return array(
			array(
				"What's up, doc?",
				"'",
				'What\\\'s up, doc?'
			),
			array(
				"What's up, doc?",
				'"',
				"What's up, doc?"
			),
			array(
				'"Hello"',
				'"',
				'\"Hello\"'
			),
			array(
				'"Hello"',
				"'",
				'"Hello"'
			),
			array(
				array('"Hello"'),
				'"',
				array('\"Hello\"')
			),
		);
	}

	/**
	 * Data for ::mime()
	 *
	 * @return array Data.
	 */
	function data_mime() {
		return array(
			array(
				'Application/Octet-Stream',
				'application/octet-stream'
			),
			array(
				'application/vnd.MS-OFFICE',
				'application/vnd.ms-office'
			),
			array(
				'awesome/saucE',
				'awesome/sauce'
			),
			array(
				array('awesome/saucE'),
				array('awesome/sauce')
			),
		);
	}

	/**
	 * Data for ::name()
	 *
	 * @return array Data.
	 */
	function data_name() {
		return array(
			array(
				"åsa-britt\nkjellén",
				'Åsa-Britt Kjellén'
			),
			array(
				'john   doe',
				'John Doe'
			),
			array(
				array('john   doe'),
				array('John Doe')
			),
		);
	}

	/**
	 * Data for ::password()
	 *
	 * @return array Data.
	 */
	function data_password() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				'ålén ☺'
			),
			array(
				array("\t ålén\n  ☺\0"),
				array('ålén ☺')
			),
		);
	}

	/**
	 * Data for ::printable()
	 *
	 * @return array Data.
	 */
	function data_printable() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				"\t ålén\n  ☺"
			),
			array(
				array("\t ålén\n  ☺\0"),
				array("\t ålén\n  ☺")
			),
		);
	}

	/**
	 * Data for ::province()
	 *
	 * @return array Data.
	 */
	function data_province() {
		return array(
			array(
				'Texas',
				''
			),
			array(
				'ontario',
				'ON'
			),
			array(
				'ab',
				'AB'
			),
			array(
				array('ab'),
				array('AB')
			),
		);
	}

	/**
	 * Data for ::quotes()
	 *
	 * @return array Data.
	 */
	function data_quotes() {
		return array(
			array(
				'“T’was the night before Christmas...”',
				'"T\'was the night before Christmas..."'
			),
			array(
				array('“T’was the night before Christmas...”'),
				array('"T\'was the night before Christmas..."')
			),
		);
	}

	/**
	 * Data for ::state()
	 *
	 * @return array Data.
	 */
	function data_state() {
		return array(
			array(
				'Texas',
				'TX'
			),
			array(
				'ontario',
				''
			),
			array(
				'il',
				'IL'
			),
			array(
				'puerto RICO',
				'PR'
			),
			array(
				array('puerto RICO'),
				array('PR')
			),
		);
	}

	/**
	 * Data for ::svg()
	 *
	 * @return array Data.
	 */
	function data_svg() {
		return array(
			array(file_get_contents(static::ASSETS . 'monogram-inkscape.svg')),
			array(file_get_contents(static::ASSETS . 'enshrined.svg')),
			array(file_get_contents(static::ASSETS . 'pi.svg')),
			array(file_get_contents(static::ASSETS . 'minus.svg'))
		);
	}

	/**
	 * Data for ::timezone()
	 *
	 * @return array Data.
	 */
	function data_timezone() {
		return array(
			array('Notime', 'UTC'),
			array('america/Los_angeles', 'America/Los_Angeles'),
			array('GMT', 'UTC'),
			array(
				array('GMT'),
				array('UTC')
			),
		);
	}

	/**
	 * Data for ::to_range()
	 *
	 * @return array Data.
	 */
	function data_to_range() {
		return array(
			array(
				5,
				1,
				4,
				4
			),
			array(
				5,
				1,
				null,
				5
			),
			array(
				5,
				null,
				4,
				4
			),
			array(
				5.5,
				1.3,
				5.2,
				5.2
			),
			array(
				'2016-01-15',
				'2016-01-20',
				null,
				'2016-01-20'
			),
		);
	}

	/**
	 * Data for ::upc()
	 *
	 * @return array Data.
	 */
	function data_upc() {
		return array(
			array(
				'0',
				false,
				''
			),
			array(
				'089218545992',
				false,
				'089218545992'
			),
			array(
				'0089218545992',
				false,
				'089218545992'
			),
			array(
				'89218545992',
				false,
				'089218545992'
			),
			array(
				'089218545555',
				false,
				''
			),
			array(
				array(
					'89218545992',
					'075597996524'
				),
				true,
				array(
					'0-89218-54599-2',
					'0-75597-99652-4'
				)
			),
		);
	}

	/**
	 * Data for ::url()
	 *
	 * @return array Data.
	 */
	function data_url() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'google.com',
				''
			),
			array(
				'//google.com',
				'https://google.com'
			),
			array(
				'HTTP://google.com',
				'http://google.com'
			),
			array(
				'http://user:pass@domain.com/foobar?hello#there',
				'http://user:pass@domain.com/foobar?hello#there'
			),
			array(
				'//www.☺.com/hello?awesome',
				'https://www.' . $smiley_host . '/hello?awesome'
			),
			array(
				array('HTTP://google.com'),
				array('http://google.com')
			),
		);
	}

	/**
	 * Data for ::utf8()
	 *
	 * @return array Data.
	 */
	function data_utf8() {
		return array(
			array('Björk Guðmundsdóttir'),
			array("Hello\nWorld"),
			array(123),
		);
	}

	/**
	 * Data for ::whitespace()
	 *
	 * @return array Data.
	 */
	function data_whitespace() {
		return array(
			array(
				" Björk\n\n",
				0,
				'Björk'
			),
			array(
				" Björk\n\n",
				2,
				'Björk'
			),
			array(
				"Happy\n\n\nSpaces",
				2,
				"Happy\n\nSpaces"
			),
			array(
				"Happy\n\n\nSpaces\t&\tPlaces",
				0,
				'Happy Spaces & Places'
			),
			array(
				array("Happy\n\n\nSpaces"),
				2,
				array("Happy\n\nSpaces")
			),
		);
	}

	/**
	 * Data for ::zip5()
	 *
	 * @return array Data.
	 */
	function data_zip5() {
		return array(
			array(
				'Björk',
				''
			),
			array(
				'123',
				'00123'
			),
			array(
				'000',
				''
			),
			array(
				12345,
				'12345'
			),
			array(
				'12345-6789',
				'12345'
			),
			array(
				array('12345-6789'),
				array('12345')
			),
		);
	}

	// -------------------------------------------------------------------- end data
}


