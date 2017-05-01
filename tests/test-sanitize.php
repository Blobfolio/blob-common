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

	/**
	 * ::accents()
	 *
	 * @return void Nothing.
	 */
	function test_accents() {
		$thing = 'Björk';
		$this->assertEquals('Bjork', sanitize::accents($thing));
	}

	/**
	 * ::attribute_value()
	 *
	 * @return void Nothing.
	 */
	function test_attribute_value() {
		$thing = '&nbsp;Björk"&quot; ';
		$this->assertEquals('Björk""', sanitize::attribute_value($thing));
	}

	/**
	 * ::cc()
	 *
	 * @return void Nothing.
	 */
	function test_cc() {
		$thing = '4242424242424242';
		$this->assertEquals($thing, sanitize::cc($thing));

		$thing = '4242424242424241';
		$this->assertEquals(false, sanitize::cc($thing));
	}

	/**
	 * ::control_characters()
	 *
	 * @return void Nothing.
	 */
	function test_control_characters() {
		$thing = '\0Björk';
		$this->assertEquals('Björk', sanitize::control_characters($thing));
	}

	/**
	 * ::country()
	 *
	 * @return void Nothing.
	 */
	function test_country() {
		$thing = 'USA';
		$this->assertEquals('US', sanitize::country($thing));

		$thing = 'US';
		$this->assertEquals('US', sanitize::country($thing));

		$thing = 'Nobody';
		$this->assertEquals('', sanitize::country($thing));
	}

	/**
	 * ::csv()
	 *
	 * @return void Nothing.
	 */
	function test_csv() {
		$thing = 'Hello"';
		$this->assertEquals('Hello""', sanitize::csv($thing));

		$thing = "New\nLine";
		$this->assertEquals('New Line', sanitize::csv($thing));
	}

	/**
	 * ::datetime()
	 *
	 * @return void Nothing.
	 */
	function test_datetime() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02 00:00:00', sanitize::datetime($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02 13:23:11', sanitize::datetime($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02 13:23:11', sanitize::datetime($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00 00:00:00', sanitize::datetime($thing));
	}

	/**
	 * ::date()
	 *
	 * @return void Nothing.
	 */
	function test_date() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02', sanitize::date($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02', sanitize::date($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02', sanitize::date($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00', sanitize::date($thing));
	}

	/**
	 * ::domain()
	 *
	 * @return void Nothing.
	 */
	function test_domain() {
		$things = array(
			'https://www.Google.com'=>'google.com',
			'www.Google.com'=>'google.com',
			'50.116.18.174'=>'',
			'//☺.com'=>'xn--74h.com'
		);

		foreach ($things as $k=>$v) {
			$this->assertEquals($v, sanitize::domain($k));
		}

		$this->assertEquals('☺.com', sanitize::domain('☺.com', true));
	}

	/**
	 * ::email()
	 *
	 * @return void Nothing.
	 */
	function test_email() {
		$thing = 'Hello@Blo"bfolio.Com';
		$this->assertEquals('hello@blobfolio.com', sanitize::email($thing));

		$thing = 'Hello@Blobfolio';
		$this->assertEquals(false, sanitize::email($thing));
	}

	/**
	 * ::file_extension()
	 *
	 * @return void Nothing.
	 */
	function test_file_extension() {
		$thing = 'JPEG';
		$this->assertEquals('jpeg', sanitize::file_extension($thing));
	}

	/**
	 * ::html()
	 *
	 * @return void Nothing.
	 */
	function test_html() {
		$thing = '<b>"';
		$this->assertEquals('&lt;b&gt;&quot;', sanitize::html($thing));
	}

	/**
	 * ::hostname()
	 *
	 * @return void Nothing.
	 */
	function test_hostname() {
		$things = array(
			'https://www.Google.com'=>'google.com',
			'www.Google.com'=>'google.com',
			'50.116.18.174'=>'50.116.18.174',
			'//☺.com'=>'xn--74h.com',
			'[2600:3c00::f03c:91ff:feae:0ff2]'=>'2600:3c00::f03c:91ff:feae:ff2',
			'localhost'=>'localhost'
		);

		foreach ($things as $k=>$v) {
			$this->assertEquals($v, sanitize::hostname($k));
		}

		$this->assertEquals('☺.com', sanitize::hostname('☺.com', false, true));

		$this->assertEquals('www.google.com', sanitize::hostname('https://www.Google.com', true));
	}

	/**
	 * ::ip()
	 *
	 * @return void Nothing.
	 */
	function test_ip() {
		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals('2600:3c00::f03c:91ff:feae:ff2', sanitize::ip($thing));

		$thing = '127.00.0.1';
		$this->assertEquals('', sanitize::ip($thing));
	}

	/**
	 * ::iri_value()
	 *
	 * @return void Nothing.
	 */
	function test_iri_value() {
		$thing = '#example';
		$this->assertEquals('#example', sanitize::iri_value($thing));

		$thing = '//w3.org';
		$this->assertEquals('https://w3.org', sanitize::iri_value($thing));

		$thing = 'ftp://w3.org';
		$this->assertEquals('', sanitize::iri_value($thing));

		$thing = ' script: alert(hi);';
		$this->assertEquals('', sanitize::iri_value($thing));

		$thing = constants::BLANK_IMAGE;
		$this->assertEquals('', sanitize::iri_value($thing));
		$this->assertEquals($thing, sanitize::iri_value($thing, 'data'));
	}

	/**
	 * ::js()
	 *
	 * @return void Nothing.
	 */
	function test_js() {
		$thing = "What's up?";
		$this->assertEquals("What\'s up?", sanitize::js($thing));

		$thing = "What's up?";
		$this->assertEquals("What's up?", sanitize::js($thing, '"'));
	}

	/**
	 * ::name()
	 *
	 * @return void Nothing.
	 */
	function test_name() {
		$thing = "åsa-britt\nkjellén";
		$this->assertEquals('Åsa-Britt Kjellén', sanitize::name($thing));
	}

	/**
	 * ::password()
	 *
	 * @return void Nothing.
	 */
	function test_password() {
		$thing = " test\t ing";
		$this->assertEquals('test ing', sanitize::password($thing));
	}

	/**
	 * ::printable()
	 *
	 * @return void Nothing.
	 */
	function test_printable() {
		$thing = " test\t ing";
		$this->assertEquals(' test ing', sanitize::printable($thing));
	}

	/**
	 * ::province()
	 *
	 * @return void Nothing.
	 */
	function test_province() {
		$thing = 'Nowhere';
		$this->assertEquals('', sanitize::province($thing));

		$thing = 'ontario';
		$this->assertEquals('ON', sanitize::province($thing));

		$thing = 'ab';
		$this->assertEquals('AB', sanitize::province($thing));
	}

	/**
	 * ::quotes()
	 *
	 * @return void Nothing.
	 */
	function test_quotes() {
		$thing = '“T’was the night before Christmas...”';
		$this->assertEquals('"T\'was the night before Christmas..."', sanitize::quotes($thing));
	}

	/**
	 * ::state()
	 *
	 * @return void Nothing.
	 */
	function test_state() {
		$thing = 'puerto rico';
		$this->assertEquals('PR', sanitize::state($thing));

		$thing = 'tx';
		$this->assertEquals('TX', sanitize::state($thing));

		$thing = 'Nowhere';
		$this->assertEquals('', sanitize::state($thing));
	}

	/**
	 * ::svg()
	 *
	 * @return void Nothing.
	 */
	function test_svg() {
		$svg = file_get_contents(self::ASSETS . 'enshrined.svg');

		// Before.
		$this->assertEquals(true, strpos($svg, 'onload'));
		$this->assertEquals(true, strpos($svg, 'data:'));
		$this->assertEquals(true, strpos($svg, '<script'));
		$this->assertEquals(true, strpos($svg, 'http://example.com'));
		$this->assertEquals(true, strpos($svg, 'XSS'));

		$thing = sanitize::svg($svg);

		// After.
		$this->assertEquals(true, false !== strpos($thing, '<svg'));
		$this->assertEquals(false, strpos($thing, 'onload'));
		$this->assertEquals(false, strpos($thing, 'data:'));
		$this->assertEquals(false, strpos($thing, '<script'));
		$this->assertEquals(false, strpos($thing, 'http://example.com'));
		$this->assertEquals(false, strpos($thing, 'XSS'));

		// Check whitelisted domains.
		$thing = sanitize::svg($svg, null, null, null, 'example.com');
		$this->assertEquals(true, strpos($thing, 'http://example.com'));

		// Make sure styles get decoded too.
		$svg = file_get_contents(self::ASSETS . 'minus.svg');

		// Pre Validate.
		$this->assertEquals(true, strpos($svg, '&#109;'));
		$this->assertEquals(true, strpos($svg, '&#123;'));

		$thing = sanitize::svg($svg);

		// Missing bad stuff.
		$this->assertEquals(true, false !== strpos($thing, '<svg'));
		$this->assertEquals(false, strpos($thing, '&#109;'));
		$this->assertEquals(false, strpos($thing, '&#123;'));
	}

	/**
	 * ::timezone()
	 *
	 * @return void Nothing.
	 */
	function test_timezone() {
		$thing = 'Notime';
		$this->assertEquals('UTC', sanitize::timezone($thing));

		$thing = 'america/los_angeles';
		$this->assertEquals('America/Los_Angeles', sanitize::timezone($thing));

		$thing = 'GMT';
		$this->assertEquals('UTC', sanitize::timezone($thing));
	}

	/**
	 * ::to_range()
	 *
	 * @return void Nothing.
	 */
	function test_to_range() {
		$this->assertEquals(3, sanitize::to_range(3, 1, 5));
		$this->assertEquals(3, sanitize::to_range(3, 1));
		$this->assertEquals(3, sanitize::to_range(3, null, 5));

		$this->assertEquals('2015-01-15', sanitize::to_range('2015-01-01', '2015-01-15', '2015-02-01'));
	}

	/**
	 * ::url()
	 *
	 * @return void Nothing.
	 */
	function test_url() {
		$things = array(
			'google.com'=>'',
			'//google.com'=>'https://google.com',
			'http://google.com'=>'http://google.com',
			'http://user:pass@domain.com'=>'http://user:pass@domain.com',
			'//☺.com/hello?awesome'=>'https://xn--74h.com/hello?awesome'
		);
		foreach ($things as $k=>$v) {
			$this->assertEquals($v, sanitize::url($k));
		}
	}

	/**
	 * ::utf8()
	 *
	 * @return void Nothing.
	 */
	function test_utf8() {
		$thing = 'Björk Guðmundsdóttir';

		$thing = sanitize::utf8($thing);
		$this->assertEquals('UTF-8', mb_detect_encoding($thing));
	}

	/**
	 * ::whitespace()
	 *
	 * @return void Nothing.
	 */
	function test_whitespace() {
		$thing = "Björk  Guðmundsdóttir\n";

		$this->assertEquals('Björk Guðmundsdóttir', sanitize::whitespace($thing));
		$this->assertEquals('Björk Guðmundsdóttir', sanitize::whitespace($thing, 1));

		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", sanitize::whitespace($thing, 2));
	}

	/**
	 * ::whitespace_multiline()
	 *
	 * @return void Nothing.
	 */
	function test_whitespace_multiline() {
		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", sanitize::whitespace_multiline($thing, 2));
	}

	/**
	 * ::zip5()
	 *
	 * @return void Nothing.
	 */
	function test_zip5() {
		$this->assertEquals('00123', sanitize::zip5(123));
		$this->assertEquals('12345', sanitize::zip5(12345));
		$this->assertEquals('', sanitize::zip5('no'));
		$this->assertEquals('', sanitize::zip5(0));
	}
}


