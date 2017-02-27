<?php
//---------------------------------------------------------------------
// sanitize:: tests
//---------------------------------------------------------------------

class sanitize_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';

	//-------------------------------------------------
	// sanitize::accents()

	function test_accents() {
		$thing = 'Björk';
		$this->assertEquals('Bjork', \blobfolio\common\sanitize::accents($thing));
	}

	//-------------------------------------------------
	// sanitize::cc()

	function test_cc() {
		$thing = '4242424242424242';
		$this->assertEquals($thing, \blobfolio\common\sanitize::cc($thing));

		$thing = '4242424242424241';
		$this->assertEquals(false, \blobfolio\common\sanitize::cc($thing));
	}

	//-------------------------------------------------
	// sanitize::country()

	function test_country() {
		$thing = 'USA';
		$this->assertEquals('US', \blobfolio\common\sanitize::country($thing));

		$thing = 'US';
		$this->assertEquals('US', \blobfolio\common\sanitize::country($thing));

		$thing = 'Nobody';
		$this->assertEquals('', \blobfolio\common\sanitize::country($thing));
	}

	//-------------------------------------------------
	// sanitize::csv()

	function test_csv() {
		$thing = 'Hello"';
		$this->assertEquals('Hello""', \blobfolio\common\sanitize::csv($thing));

		$thing = "New\nLine";
		$this->assertEquals('New Line', \blobfolio\common\sanitize::csv($thing));
	}

	//-------------------------------------------------
	// sanitize::datetime()

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

	//-------------------------------------------------
	// sanitize::date()

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

	//-------------------------------------------------
	// sanitize::domain()

	function test_domain() {
		$thing = 'https://www.Google.com';
		$this->assertEquals('google.com', \blobfolio\common\sanitize::domain($thing));

		$thing = 'www.Google.com';
		$this->assertEquals('google.com', \blobfolio\common\sanitize::domain($thing));

		$thing = '50.116.18.174';
		$this->assertEquals('', \blobfolio\common\sanitize::domain($thing));
	}

	//-------------------------------------------------
	// sanitize::email()

	function test_email() {
		$thing = 'Hello@Blo"bfolio.Com';
		$this->assertEquals('hello@blobfolio.com', \blobfolio\common\sanitize::email($thing));

		$thing = 'Hello@Blobfolio';
		$this->assertEquals(false, \blobfolio\common\sanitize::email($thing));
	}

	//-------------------------------------------------
	// sanitize::file_extension()

	function test_file_extension() {
		$thing = 'JPEG';
		$this->assertEquals('jpeg', \blobfolio\common\sanitize::file_extension($thing));
	}

	//-------------------------------------------------
	// sanitize::html()

	function test_html() {
		$thing = '<b>"';
		$this->assertEquals('&lt;b&gt;&quot;', \blobfolio\common\sanitize::html($thing));
	}

	//-------------------------------------------------
	// sanitize::hostname()

	function test_hostname() {
		$thing = 'https://www.Google.com';
		$this->assertEquals('www.google.com', \blobfolio\common\sanitize::hostname($thing, true));

		$thing = 'https://www.Google.com';
		$this->assertEquals('google.com', \blobfolio\common\sanitize::hostname($thing));

		$thing = 'www.Google.com';
		$this->assertEquals('google.com', \blobfolio\common\sanitize::hostname($thing));

		$thing = '50.116.18.174';
		$this->assertEquals($thing, \blobfolio\common\sanitize::hostname($thing));
	}

	//-------------------------------------------------
	// sanitize::ip()

	function test_ip() {
		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals('2600:3c00::f03c:91ff:feae:ff2', \blobfolio\common\sanitize::ip($thing));

		$thing = '127.00.0.1';
		$this->assertEquals('', \blobfolio\common\sanitize::ip($thing));
	}

	//-------------------------------------------------
	// sanitize::js()

	function test_js() {
		$thing = "What's up?";
		$this->assertEquals("What\'s up?", \blobfolio\common\sanitize::js($thing));

		$thing = "What's up?";
		$this->assertEquals("What's up?", \blobfolio\common\sanitize::js($thing, '"'));
	}

	//-------------------------------------------------
	// sanitize::name()

	function test_name() {
		$thing = "åsa-britt\nkjellén";
		$this->assertEquals('Åsa-Britt Kjellén', \blobfolio\common\sanitize::name($thing));
	}

	//-------------------------------------------------
	// sanitize::password()

	function test_password() {
		$thing = " test\t ing";
		$this->assertEquals('test ing', \blobfolio\common\sanitize::password($thing));
	}

	//-------------------------------------------------
	// sanitize::printable()

	function test_printable() {
		$thing = " test\t ing";
		$this->assertEquals(' test ing', \blobfolio\common\sanitize::printable($thing));
	}

	//-------------------------------------------------
	// sanitize::province()

	function test_province() {
		$thing = 'Nowhere';
		$this->assertEquals('', \blobfolio\common\sanitize::province($thing));

		$thing = 'ontario';
		$this->assertEquals('ON', \blobfolio\common\sanitize::province($thing));

		$thing = 'ab';
		$this->assertEquals('AB', \blobfolio\common\sanitize::province($thing));
	}

	//-------------------------------------------------
	// sanitize::quotes()

	function test_quotes() {
		$thing = '“T’was the night before Christmas...”';
		$this->assertEquals('"T\'was the night before Christmas..."', \blobfolio\common\sanitize::quotes($thing));
	}

	//-------------------------------------------------
	// sanitize::state()

	function test_state() {
		$thing = 'puerto rico';
		$this->assertEquals('PR', \blobfolio\common\sanitize::state($thing));

		$thing = 'tx';
		$this->assertEquals('TX', \blobfolio\common\sanitize::state($thing));

		$thing = 'Nowhere';
		$this->assertEquals('', \blobfolio\common\sanitize::state($thing));
	}

	//-------------------------------------------------
	// sanitize::svg()

	function test_svg() {
		$svg = file_get_contents(self::ASSETS . 'enshrined.svg');

		//before
		$this->assertEquals(true, false !== strpos($svg, '<svg'));
		$this->assertEquals(true, strpos($svg, '<script'));

		$svg = \blobfolio\common\sanitize::svg($svg);

		$this->assertEquals(true, false !== strpos($svg, '<svg'));
		$this->assertEquals(false, strpos($svg, '<script'));
	}

	//-------------------------------------------------
	// sanitize::timezone()

	function test_timezone() {
		$thing = 'Notime';
		$this->assertEquals('UTC', \blobfolio\common\sanitize::timezone($thing));

		$thing = 'america/los_angeles';
		$this->assertEquals('America/Los_Angeles', \blobfolio\common\sanitize::timezone($thing));

		$thing = 'GMT';
		$this->assertEquals('UTC', \blobfolio\common\sanitize::timezone($thing));
	}

	//-------------------------------------------------
	// sanitize::to_range()

	function test_to_range() {

		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, 1, 5));
		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, 1));
		$this->assertEquals(3, \blobfolio\common\sanitize::to_range(3, null, 5));

		$this->assertEquals('2015-01-15', \blobfolio\common\sanitize::to_range('2015-01-01', '2015-01-15', '2015-02-01'));
	}

	//-------------------------------------------------
	// sanitize::url()

	function test_url() {
		$this->assertEquals('', \blobfolio\common\sanitize::url('google.com'));
		$this->assertEquals('https://google.com', \blobfolio\common\sanitize::url('//google.com'));
		$this->assertEquals('http://google.com', \blobfolio\common\sanitize::url('http://google.com'));
	}

	//-------------------------------------------------
	// sanitize::utf8()

	function test_utf8() {
		$thing = 'Björk Guðmundsdóttir';

		$thing = \blobfolio\common\sanitize::utf8($thing);
		$this->assertEquals('UTF-8', mb_detect_encoding($thing));
	}

	//-------------------------------------------------
	// sanitize::whitespace()

	function test_whitespace() {
		$thing = "Björk  Guðmundsdóttir\n";

		$this->assertEquals('Björk Guðmundsdóttir', \blobfolio\common\sanitize::whitespace($thing));
		$this->assertEquals('Björk Guðmundsdóttir', \blobfolio\common\sanitize::whitespace($thing, 1));

		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", \blobfolio\common\sanitize::whitespace($thing, 2));
	}

	//-------------------------------------------------
	// sanitize::whitespace()

	function test_whitespace_multiline() {
		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", \blobfolio\common\sanitize::whitespace_multiline($thing, 2));
	}

	//-------------------------------------------------
	// sanitize::zip5()

	function test_zip5() {
		$this->assertEquals('00123', \blobfolio\common\sanitize::zip5(123));
		$this->assertEquals('12345', \blobfolio\common\sanitize::zip5(12345));
		$this->assertEquals('', \blobfolio\common\sanitize::zip5('no'));
		$this->assertEquals('', \blobfolio\common\sanitize::zip5(0));
	}
}

?>