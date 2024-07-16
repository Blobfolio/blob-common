<?php
/**
 * Sanitize tests.
 *
 * PHPUnit tests for sanitize.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use blobfolio\common\constants;
use blobfolio\common\sanitize;

/**
 * Test Suite
 */
class sanitize_tests extends TestCase {
	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_accents')]
	/**
	 * ::accents()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_accents($value, $expected) {
		$this->assertEquals($expected, sanitize::accents($value));
	}

	#[Test]
	#[DataProvider('data_attribute_value')]
	/**
	 * ::attribute_value()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_attribute_value($value, $expected) {
		$this->assertEquals($expected, sanitize::attribute_value($value));
	}

	#[Test]
	#[DataProvider('data_ca_postal_code')]
	/**
	 * ::ca_postal_code()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_ca_postal_code($value, $expected) {
		$this->assertEquals($expected, sanitize::ca_postal_code($value));
	}

	#[Test]
	#[DataProvider('data_cc')]
	/**
	 * ::cc()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_cc($value, $expected) {
		$this->assertSame($expected, sanitize::cc($value));
	}

	#[Test]
	#[DataProvider('data_control_characters')]
	/**
	 * ::control_characters()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_control_characters($value, $expected) {
		$this->assertEquals($expected, sanitize::control_characters($value));
	}

	#[Test]
	#[DataProvider('data_country')]
	/**
	 * ::country()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_country($value, $expected) {
		$this->assertSame($expected, sanitize::country($value));
	}

	#[Test]
	#[DataProvider('data_csv')]
	/**
	 * ::csv()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_csv($value, $expected) {
		$this->assertEquals($expected, sanitize::csv($value));
	}

	#[Test]
	#[DataProvider('data_datetime')]
	/**
	 * ::datetime()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_datetime($value, $expected) {
		$this->assertEquals($expected, sanitize::datetime($value));
	}

	#[Test]
	#[DataProvider('data_datetime')]
	/**
	 * ::date()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_date($value, $expected) {
		// We can use the datetime test data, but need to cut
		// it to date-size.
		if (\is_array($expected)) {
			foreach ($expected as $k=>$v) {
				$expected[$k] = \substr($v, 0, 10);
			}
		}
		else {
			$expected = \substr($expected, 0, 10);
		}

		$this->assertEquals($expected, sanitize::date($value));
	}

	#[Test]
	#[DataProvider('data_domain')]
	/**
	 * ::domain()
	 *
	 * @param string $value Value.
	 * @param bool $unicode Unicode.
	 * @param string $expected Expected.
	 */
	public function test_domain($value, $unicode, $expected) {
		$this->assertEquals($expected, sanitize::domain($value, $unicode));
	}

	#[Test]
	#[DataProvider('data_ean')]
	/**
	 * ::ean()
	 *
	 * @param string $value Value.
	 * @param bool $formatted Formatted.
	 * @param string $expected Expected.
	 */
	public function test_ean($value, $formatted, $expected) {
		$this->assertEquals($expected, sanitize::ean($value, $formatted));
	}

	#[Test]
	#[DataProvider('data_email')]
	/**
	 * ::email()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_email($value, $expected) {
		$this->assertEquals($expected, sanitize::email($value));
	}

	#[Test]
	#[DataProvider('data_file_extension')]
	/**
	 * ::file_extension()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_file_extension($value, $expected) {
		$this->assertEquals($expected, sanitize::file_extension($value));
	}

	#[Test]
	#[DataProvider('data_html')]
	/**
	 * ::html()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_html($value, $expected) {
		$this->assertEquals($expected, sanitize::html($value));
	}

	#[Test]
	#[DataProvider('data_hostname')]
	/**
	 * ::hostname()
	 *
	 * @param string $value Value.
	 * @param bool $www Keep www.
	 * @param bool $unicode Unicode.
	 * @param string $expected Expected.
	 */
	public function test_hostname($value, $www, $unicode, $expected) {
		$this->assertSame($expected, sanitize::hostname($value, $www, $unicode));
	}

	#[Test]
	#[DataProvider('data_ip')]
	/**
	 * ::ip()
	 *
	 * @param string $value Value.
	 * @param bool $restricted Allow reserved.
	 * @param bool $condense Condense IPv6.
	 * @param string $expected Expected.
	 */
	public function test_ip($value, $restricted, $condense, $expected) {
		$this->assertEquals($expected, sanitize::ip($value, $restricted, $condense));
	}

	#[Test]
	#[DataProvider('data_iri_value')]
	/**
	 * ::iri_value()
	 *
	 * @param string $value Value.
	 * @param mixed $protocols Allowed Protocols.
	 * @param mixed $domains Allowed domains.
	 * @param string $expected Expected.
	 */
	public function test_iri_value($value, $protocols, $domains, $expected) {
		$this->assertEquals($expected, sanitize::iri_value($value, $protocols, $domains));
	}

	#[Test]
	#[DataProvider('data_isbn')]
	/**
	 * ::isbn()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_isbn($value, $expected) {
		$this->assertEquals($expected, sanitize::isbn($value));
	}

	#[Test]
	#[DataProvider('data_js')]
	/**
	 * ::js()
	 *
	 * @param string $value Value.
	 * @param string $quote Quote.
	 * @param string $expected Expected.
	 */
	public function test_js($value, $quote, $expected) {
		$this->assertEquals($expected, sanitize::js($value, $quote));
	}

	#[Test]
	#[DataProvider('data_mime')]
	/**
	 * ::mime()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_mime($value, $expected) {
		$this->assertEquals($expected, sanitize::mime($value));
	}

	#[Test]
	#[DataProvider('data_name')]
	/**
	 * ::name()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_name($value, $expected) {
		$this->assertEquals($expected, sanitize::name($value));
	}

	#[Test]
	#[DataProvider('data_password')]
	/**
	 * ::password()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_password($value, $expected) {
		$this->assertEquals($expected, sanitize::password($value));
	}

	#[Test]
	#[DataProvider('data_printable')]
	/**
	 * ::printable()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_printable($value, $expected) {
		$this->assertEquals($expected, sanitize::printable($value));
	}

	#[Test]
	#[DataProvider('data_province')]
	/**
	 * ::province()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_province($value, $expected) {
		$this->assertEquals($expected, sanitize::province($value));
	}

	#[Test]
	#[DataProvider('data_quotes')]
	/**
	 * ::quotes()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_quotes($value, $expected) {
		$this->assertEquals($expected, sanitize::quotes($value));
	}

	#[Test]
	#[DataProvider('data_state')]
	/**
	 * ::state()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_state($value, $expected) {
		$this->assertEquals($expected, sanitize::state($value));
	}

	#[Test]
	#[DataProvider('data_au_state')]
	/**
	 * ::au_state()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_au_state($value, $expected) {
		$this->assertEquals($expected, sanitize::au_state($value));
	}

	#[Test]
	#[DataProvider('data_svg')]
	/**
	 * ::svg()
	 *
	 * @param string $svg SVG.
	 */
	public function test_svg($svg) {
		if (! \class_exists('DOMDocument') || ! \class_exists('DOMXPath')) {
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
			$this->assertSame(false, \strpos($result, $v));
		}
	}

	#[Test]
	#[DataProvider('data_timezone')]
	/**
	 * ::timezone()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_timezone($value, $expected) {
		$this->assertEquals($expected, sanitize::timezone($value));
	}

	#[Test]
	#[DataProvider('data_to_range')]
	/**
	 * ::to_range()
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param mixed $expected Expected.
	 */
	public function test_to_range($value, $min, $max, $expected) {
		$this->assertEquals($expected, sanitize::to_range($value, $min, $max));
	}

	#[Test]
	#[DataProvider('data_upc')]
	/**
	 * ::upc()
	 *
	 * @param string $value Value.
	 * @param bool $formatted Formatted.
	 * @param string $expected Expected.
	 */
	public function test_upc($value, $formatted, $expected) {
		$this->assertEquals($expected, sanitize::upc($value, $formatted));
	}

	#[Test]
	#[DataProvider('data_url')]
	/**
	 * ::url()
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	public function test_url($value, $expected) {
		$this->assertEquals($expected, sanitize::url($value));
	}

	#[Test]
	#[DataProvider('data_utf8')]
	/**
	 * ::utf8()
	 *
	 * @param mixed $value Value.
	 */
	public function test_utf8($value) {
		$encoding = \strtoupper(\mb_detect_encoding(sanitize::utf8($value)));
		$this->assertSame(true, \in_array($encoding, array('ASCII', 'UTF-8'), true));
	}

	#[Test]
	#[DataProvider('data_whitespace')]
	/**
	 * ::whitespace()
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	public function test_whitespace($value, $newlines, $expected) {
		$this->assertEquals($expected, sanitize::whitespace($value, $newlines));
	}

	#[Test]
	#[DataProvider('data_whitespace')]
	/**
	 * ::whitespace_multiline()
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	public function test_whitespace_multiline($value, $newlines, $expected) {
		$this->assertEquals($expected, sanitize::whitespace_multiline($value, $newlines));
	}

	#[Test]
	#[DataProvider('data_zip5')]
	/**
	 * ::zip5()
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	public function test_zip5($value, $expected) {
		$this->assertEquals($expected, sanitize::zip5($value));
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::accents()
	 *
	 * @return array Data.
	 */
	static function data_accents() {
		return array(
			array(
				'Björk Guðmundsdóttir, best þekkt sem Björk (fædd 21. nóvember 1965 í Reykjavík) er íslenskur popptónlistarmaður, sem hefur náð alþjóðlegri hylli.',
				'Bjork Gudmundsdottir, best thekkt sem Bjork (faedd 21. november 1965 i Reykjavik) er islenskur popptonlistarmadur, sem hefur nad althjodlegri hylli.',
			),
			array(
				'Nabokov explore plusieurs thèmes, dont certains déjà présents dans ses ouvrages précédents.',
				'Nabokov explore plusieurs themes, dont certains deja presents dans ses ouvrages precedents.',
			),
			array(
				array('Björk'),
				array('Bjork'),
			),
			array(
				'Rosé the Day Away',
				'Rose the Day Away',
			),
		);
	}

	/**
	 * Data for ::attribute_value()
	 *
	 * @return array Data.
	 */
	static function data_attribute_value() {
		return array(
			array(
				'&nbsp;Björk"&amp;quot; ',
				'Björk""',
			),
			array(
				array('&nbsp;Björk"&amp;quot; '),
				array('Björk""'),
			),
		);
	}

	/**
	 * Data for ::ca_postal_code()
	 *
	 * @return array Data.
	 */
	static function data_ca_postal_code() {
		return array(
			array(
				'f3f3f3',
				'',
			),
			array(
				'w2w2w2',
				'',
			),
			array(
				'e3w3w3',
				'E3W 3W3',
			),
			array(
				'L3Y-6B1',
				'L3Y 6B1',
			),
			array(
				'L3Y6B1R',
				'',
			),
			array(
				array('L3Y6B1R', 'L3Y6B1'),
				array('', 'L3Y 6B1'),
			),
		);
	}

	/**
	 * Data for ::cc()
	 *
	 * @return array Data.
	 */
	static function data_cc() {
		return array(
			array(
				'4242424242424242',
				'4242424242424242',
			),
			array(
				4242424242424242,
				'4242424242424242',
			),
			array(
				'4242424242424241',
				false,
			),
			array(
				'5555555555554444',
				'5555555555554444',
			),
			array(
				'378282246310005',
				'378282246310005',
			),
			array(
				'378734493671000',
				'378734493671000',
			),
			array(
				'6011111111111117',
				'6011111111111117',
			),
			array(
				'4012888888881881',
				'4012888888881881',
			),
			array(
				'4222222222222',
				'4222222222222',
			),
		);
	}

	/**
	 * Data for ::control_characters()
	 *
	 * @return array Data.
	 */
	static function data_control_characters() {
		return array(
			array(
				'\0Björk',
				'Björk',
			),
			array(
				array('\0Björk'),
				array('Björk'),
			),
		);
	}

	/**
	 * Data for ::country()
	 *
	 * @return array Data.
	 */
	static function data_country() {
		return array(
			array(
				'USA',
				'US',
			),
			array(
				'us',
				'US',
			),
			array(
				'United States of America',
				'US',
			),
			array(
				'Nobody',
				'',
			),
			array(
				'CANADA',
				'CA',
			),
			array(
				array('CANADA'),
				array('CA'),
			),
		);
	}

	/**
	 * Data for ::csv()
	 *
	 * @return array Data.
	 */
	static function data_csv() {
		return array(
			array(
				'\\\'hello"',
				'\\\'hello""',
			),
			array(
				"Hello\nWorld",
				'Hello World',
			),
			array(
				array("Hello\nWorld"),
				array('Hello World'),
			),
		);
	}

	/**
	 * Data for ::datetime() and ::date()
	 *
	 * @return array Data.
	 */
	static function data_datetime() {
		return array(
			array(
				'2015-01-02',
				'2015-01-02 00:00:00',
			),
			array(
				'2015-01-02 13:23:11',
				'2015-01-02 13:23:11',
			),
			array(
				\strtotime('2015-01-02 13:23:11'),
				'2015-01-02 13:23:11',
			),
			array(
				'20150102',
				'2015-01-02 00:00:00',
			),
			array(
				20150102,
				'2015-01-02 00:00:00',
			),
			array(
				'Not Time',
				'0000-00-00 00:00:00',
			),
			array(
				'0000-00-00 12:30:30',
				'0000-00-00 00:00:00',
			),
			array(
				array(20150102),
				array('2015-01-02 00:00:00'),
			),
		);
	}

	/**
	 * Data for ::domain()
	 *
	 * @return array Data.
	 */
	static function data_domain() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				false,
				'google.com',
			),
			array(
				'www.Google.com',
				false,
				'google.com',
			),
			array(
				'☺.com',
				true,
				'☺.com',
			),
			array(
				'50.116.18.174',
				false,
				'',
			),
			array(
				'//☺.com',
				false,
				$smiley_host,
			),
			array(
				array('www.Google.com'),
				false,
				array('google.com'),
			),
		);
	}

	/**
	 * Data for ::ean()
	 *
	 * @return array Data.
	 */
	static function data_ean() {
		return array(
			array(
				'0',
				false,
				'',
			),
			array(
				'074299160691',
				false,
				'0074299160691',
			),
			array(
				'00709077260149',
				false,
				'0709077260149',
			),
			array(
				'709077260149',
				false,
				'0709077260149',
			),
			array(
				'0709077260555',
				false,
				'',
			),
			array(
				'0709077260149',
				true,
				'0-709077-260149',
			),
			array(
				array(
					'0709077260149',
					'0051511500275',
				),
				true,
				array(
					'0-709077-260149',
					'0-051511-500275',
				),
			),
		);
	}

	/**
	 * Data for ::email()
	 *
	 * @return array Data.
	 */
	static function data_email() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'Hello@Blo"bfolio.Com',
				'hello@blobfolio.com',
			),
			array(
				'helo@blobfolio',
				'',
			),
			array(
				'hello@☺.com',
				"hello@$smiley_host",
			),
			array(
				'hello+me@blobfolio.com',
				'hello+me@blobfolio.com',
			),
			array(
				' .hello(comment)+me@blobfolio.com',
				'hello+me@blobfolio.com',
			),
			array(
				array(' .hello(comment)+me@blobfolio.com'),
				array('hello+me@blobfolio.com'),
			),
		);
	}

	/**
	 * Data for ::file_extension()
	 *
	 * @return array Data.
	 */
	static function data_file_extension() {
		return array(
			array(
				'  .JPEG ',
				'jpeg',
			),
			array(
				'.tar.gz',
				'tar.gz',
			),
			array(
				array('.tar.gz'),
				array('tar.gz'),
			),
		);
	}

	/**
	 * Data for ::html()
	 *
	 * @return array Data.
	 */
	static function data_html() {
		return array(
			array(
				'<b>"Björk"</b>',
				'&lt;b&gt;&quot;Björk&quot;&lt;/b&gt;',
			),
			array(
				array('<b>'),
				array('&lt;b&gt;'),
			),
		);
	}

	/**
	 * Data for ::hostname()
	 *
	 * @return array Data.
	 */
	static function data_hostname() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				false,
				false,
				'google.com',
			),
			array(
				'www.Google.com',
				false,
				false,
				'google.com',
			),
			array(
				'www.☺.com',
				false,
				true,
				'☺.com',
			),
			array(
				'http://www.☺.com',
				true,
				true,
				'www.☺.com',
			),
			array(
				'50.116.18.174',
				false,
				false,
				'50.116.18.174',
			),
			array(
				'//☺.com',
				false,
				false,
				$smiley_host,
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				false,
				false,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'localhost',
				true,
				true,
				'localhost',
			),
		);
	}

	/**
	 * Data for ::ip()
	 *
	 * @return array Data.
	 */
	static function data_ip() {
		return array(
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				false,
				true,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				false,
				true,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'2600:3c00::f03c:91ff:feae:ff2',
				false,
				false,
				'2600:3c00:0000:0000:f03c:91ff:feae:0ff2',
			),
			array(
				'127.0.0.1',
				false,
				true,
				'',
			),
			array(
				'127.0.0.1',
				true,
				true,
				'127.0.0.1',
			),
			array(
				'::127.0.0.1',
				true,
				true,
				'127.0.0.1',
			),
			array(
				'[::127.0.0.1]',
				true,
				true,
				'127.0.0.1',
			),
			array(
				'::1',
				false,
				true,
				'',
			),
			array(
				'[::1]',
				true,
				true,
				'::1',
			),
			array(
				array('[::1]'),
				true,
				true,
				array('::1'),
			),
			array(
				array('[::1]'),
				true,
				false,
				array('0000:0000:0000:0000:0000:0000:0000:0001'),
			),
		);
	}

	/**
	 * Data for ::iri_value()
	 *
	 * @return array Data.
	 */
	static function data_iri_value() {
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
				constants::BLANK_IMAGE,
				null,
				null,
				'',
			),
			array(
				constants::BLANK_IMAGE,
				'data',
				null,
				constants::BLANK_IMAGE,
			),
			array(
				array(constants::BLANK_IMAGE),
				'data',
				null,
				array(constants::BLANK_IMAGE),
			),
		);
	}

	/**
	 * Data for ::isbn()
	 *
	 * @return array Data.
	 */
	static function data_isbn() {
		return array(
			array(
				'0',
				'',
			),
			array(
				'0939117606',
				'0939117606',
			),
			array(
				'939117606',
				'0939117606',
			),
			array(
				'9780939117604',
				'9780939117604',
			),
			array(
				'0-9752298-0-X',
				'097522980X',
			),
			array(
				'0975229800',
				'',
			),
		);
	}

	/**
	 * Data for ::js()
	 *
	 * @return array Data.
	 */
	static function data_js() {
		return array(
			array(
				"What's up, doc?",
				"'",
				'What\\\'s up, doc?',
			),
			array(
				"What's up, doc?",
				'"',
				"What's up, doc?",
			),
			array(
				'"Hello"',
				'"',
				'\"Hello\"',
			),
			array(
				'"Hello"',
				"'",
				'"Hello"',
			),
			array(
				array('"Hello"'),
				'"',
				array('\"Hello\"'),
			),
			array(
				'</script>><script>prompt(1)</script>',
				"'",
				'<\/script>><script>prompt(1)<\/script>',
			),
		);
	}

	/**
	 * Data for ::mime()
	 *
	 * @return array Data.
	 */
	static function data_mime() {
		return array(
			array(
				'Application/Octet-Stream',
				'application/octet-stream',
			),
			array(
				'application/vnd.MS-OFFICE',
				'application/vnd.ms-office',
			),
			array(
				'awesome/saucE',
				'awesome/sauce',
			),
			array(
				array('awesome/saucE'),
				array('awesome/sauce'),
			),
		);
	}

	/**
	 * Data for ::name()
	 *
	 * @return array Data.
	 */
	static function data_name() {
		return array(
			array(
				"åsa-britt\nkjellén",
				'Åsa-Britt Kjellén',
			),
			array(
				'john   doe',
				'John Doe',
			),
			array(
				array('john   doe'),
				array('John Doe'),
			),
		);
	}

	/**
	 * Data for ::password()
	 *
	 * @return array Data.
	 */
	static function data_password() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				'ålén ☺',
			),
			array(
				array("\t ålén\n  ☺\0"),
				array('ålén ☺'),
			),
		);
	}

	/**
	 * Data for ::printable()
	 *
	 * @return array Data.
	 */
	static function data_printable() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				"\t ålén\n  ☺",
			),
			array(
				array("\t ålén\n  ☺\0"),
				array("\t ålén\n  ☺"),
			),
			array(
				// This starter text has some zero-width characters
				// buried in it. Depending on the code viewer, that
				// might not be obvious.
				"Confidential Announcement: ‌﻿​﻿‌﻿​﻿​﻿‌﻿​﻿‌﻿‍﻿‌﻿​﻿​﻿‌﻿‌﻿​﻿‌﻿​﻿‍﻿‌﻿​﻿​﻿​﻿‌﻿‌﻿​﻿‌﻿‍﻿‌﻿​﻿​﻿‌﻿​﻿​﻿​﻿​This is some confidential text that you really shouldn't be sharing anywhere else.",
				// This one is clean.
				"Confidential Announcement: This is some confidential text that you really shouldn't be sharing anywhere else.",
			),
		);
	}

	/**
	 * Data for ::province()
	 *
	 * @return array Data.
	 */
	static function data_province() {
		return array(
			array(
				'Texas',
				'',
			),
			array(
				'ontario',
				'ON',
			),
			array(
				'ab',
				'AB',
			),
			array(
				array('ab'),
				array('AB'),
			),
		);
	}

	/**
	 * Data for ::quotes()
	 *
	 * @return array Data.
	 */
	static function data_quotes() {
		return array(
			array(
				'“T’was the night before Christmas...”',
				'"T\'was the night before Christmas..."',
			),
			array(
				array('“T’was the night before Christmas...”'),
				array('"T\'was the night before Christmas..."'),
			),
		);
	}

	/**
	 * Data for ::state()
	 *
	 * @return array Data.
	 */
	static function data_state() {
		return array(
			array(
				'Texas',
				'TX',
			),
			array(
				'ontario',
				'',
			),
			array(
				'il',
				'IL',
			),
			array(
				'puerto RICO',
				'PR',
			),
			array(
				array('puerto RICO'),
				array('PR'),
			),
		);
	}

	/**
	 * Data for ::au_state()
	 *
	 * @return array Data.
	 */
	static function data_au_state() {
		return array(
			array(
				'Texas',
				'',
			),
			array(
				'new soUTH wales',
				'NSW',
			),
			array(
				'QLD',
				'QLD',
			),
			array(
				array('QLD', 'New South Wales'),
				array('QLD', 'NSW'),
			),
		);
	}

	/**
	 * Data for ::svg()
	 *
	 * @return array Data.
	 */
	static function data_svg() {
		return array(
			array(\file_get_contents(static::ASSETS . 'monogram-inkscape.svg')),
			array(\file_get_contents(static::ASSETS . 'enshrined.svg')),
			array(\file_get_contents(static::ASSETS . 'pi.svg')),
			array(\file_get_contents(static::ASSETS . 'minus.svg')),
		);
	}

	/**
	 * Data for ::timezone()
	 *
	 * @return array Data.
	 */
	static function data_timezone() {
		return array(
			array('Notime', 'UTC'),
			array('america/Los_angeles', 'America/Los_Angeles'),
			array('GMT', 'UTC'),
			array(
				array('GMT'),
				array('UTC'),
			),
		);
	}

	/**
	 * Data for ::to_range()
	 *
	 * @return array Data.
	 */
	static function data_to_range() {
		return array(
			array(
				5,
				1,
				4,
				4,
			),
			array(
				5,
				1,
				null,
				5,
			),
			array(
				5,
				null,
				4,
				4,
			),
			array(
				5.5,
				1.3,
				5.2,
				5.2,
			),
			array(
				'2016-01-15',
				'2016-01-20',
				null,
				'2016-01-20',
			),
		);
	}

	/**
	 * Data for ::upc()
	 *
	 * @return array Data.
	 */
	static function data_upc() {
		return array(
			array(
				'0',
				false,
				'',
			),
			array(
				'089218545992',
				false,
				'089218545992',
			),
			array(
				'0089218545992',
				false,
				'089218545992',
			),
			array(
				'89218545992',
				false,
				'089218545992',
			),
			array(
				'089218545555',
				false,
				'',
			),
			array(
				array(
					'89218545992',
					'075597996524',
				),
				true,
				array(
					'0-89218-54599-2',
					'0-75597-99652-4',
				),
			),
		);
	}

	/**
	 * Data for ::url()
	 *
	 * @return array Data.
	 */
	static function data_url() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'google.com',
				'',
			),
			array(
				'//google.com',
				'https://google.com',
			),
			array(
				'HTTP://google.com',
				'http://google.com',
			),
			array(
				'http://user:pass@domain.com/foobar?hello#there',
				'http://user:pass@domain.com/foobar?hello#there',
			),
			array(
				'//www.☺.com/hello?awesome',
				'https://www.' . $smiley_host . '/hello?awesome',
			),
			array(
				array('HTTP://google.com'),
				array('http://google.com'),
			),
		);
	}

	/**
	 * Data for ::utf8()
	 *
	 * @return array Data.
	 */
	static function data_utf8() {
		return array(
			array('Björk Guðmundsdóttir'),
			array("Hello\nWorld"),
			array(123),
			array(\file_get_contents(self::ASSETS . 'text-utf8.txt')),
			array(\file_get_contents(self::ASSETS . 'text-latin.txt')),
		);
	}

	/**
	 * Data for ::whitespace()
	 *
	 * @return array Data.
	 */
	static function data_whitespace() {
		return array(
			array(
				" Björk\n\n",
				0,
				'Björk',
			),
			array(
				" Björk\n\n",
				2,
				'Björk',
			),
			array(
				"Happy\n\n\nSpaces",
				2,
				"Happy\n\nSpaces",
			),
			array(
				"Happy\n\n\nSpaces\t&\tPlaces",
				0,
				'Happy Spaces & Places',
			),
			array(
				array("Happy\n\n\nSpaces"),
				2,
				array("Happy\n\nSpaces"),
			),
		);
	}

	/**
	 * Data for ::zip5()
	 *
	 * @return array Data.
	 */
	static function data_zip5() {
		return array(
			array(
				'Björk',
				'',
			),
			array(
				'123',
				'00123',
			),
			array(
				'000',
				'',
			),
			array(
				12345,
				'12345',
			),
			array(
				'12345-6789',
				'12345',
			),
			array(
				array('12345-6789'),
				array('12345'),
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


