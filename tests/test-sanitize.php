<?php
/**
 * Sanitize tests.
 *
 * PHPUnit tests for \blobfolio\common\sanitize.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

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
		$this->assertEquals('Bjork', \blobfolio\common\sanitize::accents($thing));
	}

	/**
	 * ::attribute_value()
	 *
	 * @return void Nothing.
	 */
	function test_attribute_value() {
		$thing = '&nbsp;Björk"&quot; ';
		$this->assertEquals('Björk""', \blobfolio\common\sanitize::attribute_value($thing));
	}

	/**
	 * ::cc()
	 *
	 * @return void Nothing.
	 */
	function test_cc() {
		$thing = '4242424242424242';
		$this->assertEquals($thing, \blobfolio\common\sanitize::cc($thing));

		$thing = '4242424242424241';
		$this->assertEquals(false, \blobfolio\common\sanitize::cc($thing));
	}

	/**
	 * ::control_characters()
	 *
	 * @return void Nothing.
	 */
	function test_control_characters() {
		$thing = '\0Björk';
		$this->assertEquals('Björk', \blobfolio\common\sanitize::control_characters($thing));
	}

	/**
	 * ::country()
	 *
	 * @return void Nothing.
	 */
	function test_country() {
		$thing = 'USA';
		$this->assertEquals('US', \blobfolio\common\sanitize::country($thing));

		$thing = 'US';
		$this->assertEquals('US', \blobfolio\common\sanitize::country($thing));

		$thing = 'Nobody';
		$this->assertEquals('', \blobfolio\common\sanitize::country($thing));
	}

	/**
	 * ::csv()
	 *
	 * @return void Nothing.
	 */
	function test_csv() {
		$thing = 'Hello"';
		$this->assertEquals('Hello""', \blobfolio\common\sanitize::csv($thing));

		$thing = "New\nLine";
		$this->assertEquals('New Line', \blobfolio\common\sanitize::csv($thing));
	}

	/**
	 * ::datetime()
	 *
	 * @return void Nothing.
	 */
	function test_datetime() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02 00:00:00', \blobfolio\common\sanitize::datetime($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02 13:23:11', \blobfolio\common\sanitize::datetime($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02 13:23:11', \blobfolio\common\sanitize::datetime($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00 00:00:00', \blobfolio\common\sanitize::datetime($thing));
	}

	/**
	 * ::date()
	 *
	 * @return void Nothing.
	 */
	function test_date() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02', \blobfolio\common\sanitize::date($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02', \blobfolio\common\sanitize::date($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02', \blobfolio\common\sanitize::date($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00', \blobfolio\common\sanitize::date($thing));
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
			$this->assertEquals($v, \blobfolio\common\sanitize::domain($k));
		}

		$this->assertEquals('☺.com', \blobfolio\common\sanitize::domain('☺.com', true));
	}

	/**
	 * ::email()
	 *
	 * @return void Nothing.
	 */
	function test_email() {
		$thing = 'Hello@Blo"bfolio.Com';
		$this->assertEquals('hello@blobfolio.com', \blobfolio\common\sanitize::email($thing));

		$thing = 'Hello@Blobfolio';
		$this->assertEquals(false, \blobfolio\common\sanitize::email($thing));
	}

	/**
	 * ::file_extension()
	 *
	 * @return void Nothing.
	 */
	function test_file_extension() {
		$thing = 'JPEG';
		$this->assertEquals('jpeg', \blobfolio\common\sanitize::file_extension($thing));
	}

	/**
	 * ::html()
	 *
	 * @return void Nothing.
	 */
	function test_html() {
		$thing = '<b>"';
		$this->assertEquals('&lt;b&gt;&quot;', \blobfolio\common\sanitize::html($thing));
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
			$this->assertEquals($v, \blobfolio\common\sanitize::hostname($k));
		}

		$this->assertEquals('☺.com', \blobfolio\common\sanitize::hostname('☺.com', false, true));

		$this->assertEquals('www.google.com', \blobfolio\common\sanitize::hostname('https://www.Google.com', true));
	}

	/**
	 * ::ip()
	 *
	 * @return void Nothing.
	 */
	function test_ip() {
		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals('2600:3c00::f03c:91ff:feae:ff2', \blobfolio\common\sanitize::ip($thing));

		$thing = '127.00.0.1';
		$this->assertEquals('', \blobfolio\common\sanitize::ip($thing));
	}

	/**
	 * ::iri_value()
	 *
	 * @return void Nothing.
	 */
	function test_iri_value() {
		$thing = '#example';
		$this->assertEquals('#example', \blobfolio\common\sanitize::iri_value($thing));

		$thing = '//w3.org';
		$this->assertEquals('https://w3.org', \blobfolio\common\sanitize::iri_value($thing));

		$thing = 'ftp://w3.org';
		$this->assertEquals('', \blobfolio\common\sanitize::iri_value($thing));

		$thing = ' script: alert(hi);';
		$this->assertEquals('', \blobfolio\common\sanitize::iri_value($thing));

		$thing = \blobfolio\common\constants::BLANK_IMAGE;
		$this->assertEquals('', \blobfolio\common\sanitize::iri_value($thing));
		$this->assertEquals($thing, \blobfolio\common\sanitize::iri_value($thing, 'data'));
	}

	/**
	 * ::js()
	 *
	 * @return void Nothing.
	 */
	function test_js() {
		$thing = "What's up?";
		$this->assertEquals("What\'s up?", \blobfolio\common\sanitize::js($thing));

		$thing = "What's up?";
		$this->assertEquals("What's up?", \blobfolio\common\sanitize::js($thing, '"'));
	}

	/**
	 * ::name()
	 *
	 * @return void Nothing.
	 */
	function test_name() {
		$thing = "åsa-britt\nkjellén";
		$this->assertEquals('Åsa-Britt Kjellén', \blobfolio\common\sanitize::name($thing));
	}

	/**
	 * ::password()
	 *
	 * @return void Nothing.
	 */
	function test_password() {
		$thing = " test\t ing";
		$this->assertEquals('test ing', \blobfolio\common\sanitize::password($thing));
	}

	/**
	 * ::printable()
	 *
	 * @return void Nothing.
	 */
	function test_printable() {
		$thing = " test\t ing";
		$this->assertEquals(' test ing', \blobfolio\common\sanitize::printable($thing));
	}

	/**
	 * ::province()
	 *
	 * @return void Nothing.
	 */
	function test_province() {
		$thing = 'Nowhere';
		$this->assertEquals('', \blobfolio\common\sanitize::province($thing));

		$thing = 'ontario';
		$this->assertEquals('ON', \blobfolio\common\sanitize::province($thing));

		$thing = 'ab';
		$this->assertEquals('AB', \blobfolio\common\sanitize::province($thing));
	}

	/**
	 * ::quotes()
	 *
	 * @return void Nothing.
	 */
	function test_quotes() {
		$thing = '“T’was the night before Christmas...”';
		$this->assertEquals('"T\'was the night before Christmas..."', \blobfolio\common\sanitize::quotes($thing));
	}

	/**
	 * ::state()
	 *
	 * @return void Nothing.
	 */
	function test_state() {
		$thing = 'puerto rico';
		$this->assertEquals('PR', \blobfolio\common\sanitize::state($thing));

		$thing = 'tx';
		$this->assertEquals('TX', \blobfolio\common\sanitize::state($thing));

		$thing = 'Nowhere';
		$this->assertEquals('', \blobfolio\common\sanitize::state($thing));
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

		$thing = \blobfolio\common\sanitize::svg($svg);

		// After.
		$this->assertEquals(true, false !== strpos($thing, '<svg'));
		$this->assertEquals(false, strpos($thing, 'onload'));
		$this->assertEquals(false, strpos($thing, 'data:'));
		$this->assertEquals(false, strpos($thing, '<script'));
		$this->assertEquals(false, strpos($thing, 'http://example.com'));
		$this->assertEquals(false, strpos($thing, 'XSS'));

		// Check whitelisted domains.
		$thing = \blobfolio\common\sanitize::svg($svg, null, null, null, 'example.com');
		$this->assertEquals(true, strpos($thing, 'http://example.com'));

		// Make sure styles get decoded too.
		$svg = file_get_contents(self::ASSETS . 'minus.svg');

		// Pre Validate.
		$this->assertEquals(true, strpos($svg, '&#109;'));
		$this->assertEquals(true, strpos($svg, '&#123;'));

		$thing = \blobfolio\common\sanitize::svg($svg);

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
		$this->assertEquals('UTC', \blobfolio\common\sanitize::timezone($thing));

		$thing = 'america/los_angeles';
		$this->assertEquals('America/Los_Angeles', \blobfolio\common\sanitize::timezone($thing));

		$thing = 'GMT';
		$this->assertEquals('UTC', \blobfolio\common\sanitize::timezone($thing));
	}

	/**
	 * ::to_range()
	 *
	 * @return void Nothing.
	 */
	function test_to_range() {

		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, 1, 5));
		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, 1));
		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, null, 5));

		$this->assertEquals('2015-01-15', \blobfolio\common\sanitize::to_range('2015-01-01', '2015-01-15', '2015-02-01'));
	}

	/**
	 * ::url()
	 *
	 * @return void Nothing.
	 */
	function test_url() {
		$this->assertEquals('', \blobfolio\common\sanitize::url('google.com'));
		$this->assertEquals('https://google.com', \blobfolio\common\sanitize::url('//google.com'));
		$this->assertEquals('http://google.com', \blobfolio\common\sanitize::url('http://google.com'));
	}

	/**
	 * ::utf8()
	 *
	 * @return void Nothing.
	 */
	function test_utf8() {
		$thing = 'Björk Guðmundsdóttir';

		$thing = \blobfolio\common\sanitize::utf8($thing);
		$this->assertEquals('UTF-8', mb_detect_encoding($thing));
	}

	/**
	 * ::whitespace()
	 *
	 * @return void Nothing.
	 */
	function test_whitespace() {
		$thing = "Björk  Guðmundsdóttir\n";

		$this->assertEquals('Björk Guðmundsdóttir', \blobfolio\common\sanitize::whitespace($thing));
		$this->assertEquals('Björk Guðmundsdóttir', \blobfolio\common\sanitize::whitespace($thing, 1));

		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", \blobfolio\common\sanitize::whitespace($thing, 2));
	}

	/**
	 * ::whitespace_multiline()
	 *
	 * @return void Nothing.
	 */
	function test_whitespace_multiline() {
		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", \blobfolio\common\sanitize::whitespace_multiline($thing, 2));
	}

	/**
	 * ::zip5()
	 *
	 * @return void Nothing.
	 */
	function test_zip5() {
		$this->assertEquals('00123', \blobfolio\common\sanitize::zip5(123));
		$this->assertEquals('12345', \blobfolio\common\sanitize::zip5(12345));
		$this->assertEquals('', \blobfolio\common\sanitize::zip5('no'));
		$this->assertEquals('', \blobfolio\common\sanitize::zip5(0));
	}
}


