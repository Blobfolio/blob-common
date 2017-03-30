<?php
/**
 * Format tests.
 *
 * PHPUnit tests for \blobfolio\common\format.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class format_tests extends \PHPUnit\Framework\TestCase {

	/**
	 * ::array_to_indexed()
	 *
	 * @return void Nothing.
	 */
	function test_array_to_indexed() {
		$thing = array(1);
		$this->assertEquals(array(array('key'=>0, 'value'=>1)), \blobfolio\common\format::array_to_indexed($thing));
	}

	/**
	 * ::cidr_to_range()
	 *
	 * @return void Nothing.
	 */
	function test_cidr_to_range() {
		$thing = '50.116.18.174/24';
		$match = array('min'=>'50.116.18.0', 'max'=>'50.116.19.173');
		$this->assertEquals($match, \blobfolio\common\format::cidr_to_range($thing));

		$thing = '2600:3c00::f03c:91ff:feae:0ff2/64';
		$match = array('min'=>'2600:3c00::f03c:91ff:feae:ff2', 'max'=>'2600:3c00::ffff:ffff:ffff:ffff');
		$this->assertEquals($match, \blobfolio\common\format::cidr_to_range($thing));
	}

	/**
	 * ::decode_entities()
	 *
	 * @return void Nothing.
	 */
	function test_decode_entities() {
		$thing = 'Happy & Healthy';
		for ($x = 0; $x < 3; $x++) {
			$thing = htmlentities($thing);
		}
		$this->assertEquals('Happy & Healthy', \blobfolio\common\format::decode_entities($thing));
	}

	/**
	 * ::excerpt()
	 *
	 * @return void Nothing.
	 */
	function test_excerpt() {
		$thing = 'It ẉẩṩ a dark and stormy night.';

		$this->assertEquals('It ẉẩṩ a!', \blobfolio\common\format::excerpt($thing, array('unit'=>'word', 'length'=>3, 'suffix'=>'!')));
		$this->assertEquals('It ẉẩṩ a…', \blobfolio\common\format::excerpt($thing, array('unit'=>'word', 'length'=>3)));
		$this->assertEquals('It ẉẩṩ…', \blobfolio\common\format::excerpt($thing, array('unit'=>'char', 'length'=>6)));
	}

	/**
	 * ::inflect()
	 *
	 * @return void Nothing.
	 */
	function test_inflect() {
		$count = 1;
		$single = '%d book';
		$plural = '%d books';

		$this->assertEquals('1 book', \blobfolio\common\format::inflect($count, $single, $plural));

		$count = 2;
		$this->assertEquals('2 books', \blobfolio\common\format::inflect($count, $single, $plural));

		$count = 0;
		$this->assertEquals('0 books', \blobfolio\common\format::inflect($count, $single, $plural));
	}

	/**
	 * ::ip_to_number()
	 *
	 * @return void Nothing.
	 */
	function test_ip_to_number() {
		$thing = '50.116.18.174';
		$this->assertEquals(846467758, \blobfolio\common\format::ip_to_number($thing));

		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals(50511880784403022287880976722111107058, \blobfolio\common\format::ip_to_number($thing));
	}

	/**
	 * ::links()
	 *
	 * @return void Nothing.
	 */
	function test_links() {
		$things = array(
			'blobfolio.com'=>'<a href="http://blobfolio.com">blobfolio.com</a>',
			'https://blobfolio.com/'=>'<a href="https://blobfolio.com/">https://blobfolio.com/</a>',
			'Welcome to blobfolio.com!'=>'Welcome to <a href="http://blobfolio.com">blobfolio.com</a>!',
			'bad.sch.uk'=>'bad.sch.uk',
			'www.blobfolio.com'=>'<a href="http://www.blobfolio.com">www.blobfolio.com</a>',
			'me@localhost'=>'me@localhost',
			'me@bad.sch.uk'=>'me@bad.sch.uk',
			'"blobfolio.com"'=>'"<a href="http://blobfolio.com">blobfolio.com</a>"',
			'(blobfolio.com)'=>'(<a href="http://blobfolio.com">blobfolio.com</a>)',
			'[blobfolio.com]'=>'[<a href="http://blobfolio.com">blobfolio.com</a>]',
			'{blobfolio.com}'=>'{<a href="http://blobfolio.com">blobfolio.com</a>}',
			'me@blobfolio.com'=>'<a href="mailto:me@blobfolio.com">me@blobfolio.com</a>',
			'Email me@blobfolio.com for more.'=>'Email <a href="mailto:me@blobfolio.com">me@blobfolio.com</a> for more.',
			'blobfolio.com me@blobfolio.com'=>'<a href="http://blobfolio.com">blobfolio.com</a> <a href="mailto:me@blobfolio.com">me@blobfolio.com</a>',
			'ftp://user:pass@☺.com'=>'<a href="ftp://user:pass@xn--74h.com">ftp://user:pass@☺.com</a>',
			'smiley@☺.com'=>'<a href="mailto:smiley@xn--74h.com">smiley@☺.com</a>'
		);

		foreach($things as $k=>$v){
			$this->assertEquals($v, \blobfolio\common\format::links($k));
		}

		$args = array(
			'class'=>array('link', 'nav'),
			'rel'=>'apples',
			'target'=>'_blank'
		);
		$thing = \blobfolio\common\format::links('blobfolio.com', $args);
		$this->assertEquals('<a href="http://blobfolio.com" class="link nav" rel="apples" target="_blank">blobfolio.com</a>', $thing);

		$thing = \blobfolio\common\format::links('me@blobfolio.com', $args);
		$this->assertEquals('<a href="mailto:me@blobfolio.com" class="link nav" rel="apples" target="_blank">me@blobfolio.com</a>', $thing);

		$args = array(
			'class'=>'link'
		);
		$thing = \blobfolio\common\format::links('blobfolio.com', $args);
		$this->assertEquals('<a href="http://blobfolio.com" class="link">blobfolio.com</a>', $thing);
	}

	/**
	 * ::money()
	 *
	 * @return void Nothing.
	 */
	function test_money() {
		$thing = 2.5;
		$this->assertEquals('$2.50', \blobfolio\common\format::money($thing));

		$thing = '1';
		$this->assertEquals('$1.00', \blobfolio\common\format::money($thing));

		$thing = 2500;
		$this->assertEquals('$2500.00', \blobfolio\common\format::money($thing));

		$thing = 2500;
		$this->assertEquals('$2,500.00', \blobfolio\common\format::money($thing, false, ','));

		$thing = .23;
		$this->assertEquals('$0.23', \blobfolio\common\format::money($thing, false));
		$this->assertEquals('23¢', \blobfolio\common\format::money($thing, true));
	}

	/**
	 * ::phone()
	 *
	 * @return void Nothing.
	 */
	function test_phone() {
		$thing = '2015550123';
		$this->assertEquals('+1 201-555-0123', \blobfolio\common\format::phone($thing));

		$this->assertEquals('+1 201-555-0123', \blobfolio\common\format::phone($thing, 'US'));
	}

	/**
	 * ::to_csv()
	 *
	 * @return void Nothing.
	 */
	function test_to_csv() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = \blobfolio\common\format::to_csv($data);
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = \blobfolio\common\format::to_csv($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));

		$csv = \blobfolio\common\format::to_csv($data, $headers, "\t");
		$this->assertEquals(true, false !== strpos($csv, "\t"));
	}

	/**
	 * ::to_timezone()
	 *
	 * @return void Nothing.
	 */
	function test_to_timezone() {
		$thing = '2015-01-15 01:12:23';

		$this->assertEquals('2015-01-15 09:12:23', \blobfolio\common\format::to_timezone($thing, 'America/Los_Angeles'));
		$this->assertEquals('2015-01-14 17:12:23', \blobfolio\common\format::to_timezone($thing, 'UTC', 'America/Los_Angeles'));
	}

	/**
	 * ::to_xls()
	 *
	 * @return void Nothing.
	 */
	function test_to_xls() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = \blobfolio\common\format::to_xls($data);
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = \blobfolio\common\format::to_xls($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));
	}
}


