<?php
/**
 * Format tests.
 *
 * PHPUnit tests for format.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use blobfolio\common\format;

/**
 * Test Suite
 */
class format_tests extends \PHPUnit\Framework\TestCase {
	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * ::array_flatten()
	 *
	 * @dataProvider data_array_flatten
	 *
	 * @param array $arr Array.
	 * @param array $expected Expected.
	 */
	function test_array_flatten($arr, $expected) {
		$this->assertSame($expected, format::array_flatten($arr));
	}

	/**
	 * ::array_to_indexed()
	 *
	 * @dataProvider data_array_to_indexed
	 *
	 * @param array $value Value.
	 * @param array $expected Expected.
	 */
	function test_array_to_indexed($value, $expected) {
		$this->assertEquals($expected, format::array_to_indexed($value));
	}

	/**
	 * ::ceil()
	 *
	 * @dataProvider data_ceil
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @param mixed $expected Expected.
	 */
	function test_ceil($num, $precision, $expected) {
		$this->assertSame($expected, format::ceil($num, $precision));
	}

	/**
	 * ::cidr_to_range()
	 *
	 * @dataProvider data_cidr_to_range
	 *
	 * @param string $cidr CIDR.
	 * @param array $expected Expected.
	 */
	function test_cidr_to_range($cidr, $expected) {
		$this->assertEquals($expected, format::cidr_to_range($cidr));
	}

	/**
	 * ::decode_entities()
	 *
	 * @dataProvider data_decode_entities
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_decode_entities($value, $expected) {
		$this->assertEquals($expected, format::decode_entities($value));
	}

	/**
	 * ::decode_js_entities()
	 *
	 * @dataProvider data_decode_js_entities
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_decode_js_entities($value, $expected) {
		$this->assertEquals($expected, format::decode_js_entities($value));
	}

	/**
	 * ::excerpt()
	 *
	 * @dataProvider data_excerpt
	 *
	 * @param string $value Value.
	 * @param array $args Args.
	 * @param string $expected Expected.
	 */
	function test_excerpt($value, $args, $expected) {
		$this->assertEquals($expected, format::excerpt($value, $args));
	}

	/**
	 * ::floor()
	 *
	 * @dataProvider data_floor
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @param mixed $expected Expected.
	 */
	function test_floor($num, $precision, $expected) {
		$this->assertSame($expected, format::floor($num, $precision));
	}

	/**
	 * ::fraction()
	 *
	 * @dataProvider data_fraction
	 *
	 * @param mixed $num Number.
	 * @param int $tolerance Tolerance.
	 * @param mixed $expected Expected.
	 */
	function test_fraction($num, $tolerance, $expected) {
		$this->assertSame($expected, format::fraction($num, $tolerance));
	}

	/**
	 * ::inflect()
	 *
	 * @dataProvider data_inflect
	 *
	 * @param mixed $count Count.
	 * @param string $single Single.
	 * @param string $plural Plural.
	 * @param string $expected Expected.
	 */
	function test_inflect($count, $single, $plural, $expected) {
		$this->assertEquals($expected, format::inflect($count, $single, $plural));
	}

	/**
	 * ::ip_to_number()
	 *
	 * @dataProvider data_ip_to_number
	 *
	 * @param string $ip IP.
	 * @param string $expected Expected.
	 */
	function test_ip_to_number($ip, $expected) {
		$this->assertEquals($expected, format::ip_to_number($ip));
	}

	/**
	 * ::ip_to_subnet()
	 *
	 * @dataProvider data_ip_to_subnet
	 *
	 * @param string $ip IP.
	 * @param string $expected Expected.
	 */
	function test_ip_to_subnet($ip, $expected) {
		$this->assertEquals($expected, format::ip_to_subnet($ip));
	}

	/**
	 * ::json()
	 *
	 * @dataProvider data_json
	 *
	 * @param string $json JSON.
	 * @param bool $pretty Pretty.
	 * @param string $expected Expected.
	 */
	function test_json($json, $pretty, $expected) {
		$this->assertSame($expected, format::json($json, $pretty));
	}

	/**
	 * ::links()
	 *
	 * @dataProvider data_links
	 *
	 * @param string $value Value.
	 * @param array $args Args.
	 * @param string $expected Expected.
	 */
	function test_links($value, $args, $expected) {
		$this->assertEquals($expected, format::links($value, $args));
	}

	/**
	 * ::list_to_array()
	 *
	 * @dataProvider data_list_to_array
	 *
	 * @param string $value Value.
	 * @param array $args Args.
	 * @param string $expected Expected.
	 */
	function test_list_to_array($value, $args, $expected) {
		$this->assertEquals($expected, format::list_to_array($value, $args));
	}

	/**
	 * ::money()
	 *
	 * @dataProvider data_money
	 *
	 * @param mixed $value Value.
	 * @param bool $cents Cents.
	 * @param string $separator Separator.
	 * @param bool $no00 No ~.00.
	 * @param string $expected Expected.
	 */
	function test_money($value, $cents, $separator, $no00, $expected) {
		$this->assertEquals($expected, format::money($value, $cents, $separator, $no00));
	}

	/**
	 * ::number_to_ip()
	 *
	 * @dataProvider data_number_to_ip
	 *
	 * @param string $ip IP.
	 * @param string $expected Expected.
	 */
	function test_number_to_ip($ip, $expected) {
		$this->assertEquals($expected, format::number_to_ip($ip));
	}

	/**
	 * ::oxford_join()
	 *
	 * @dataProvider data_oxford_join
	 *
	 * @param array $value Value.
	 * @param string $separator Separator.
	 * @param string $expected Expected.
	 */
	function test_oxford_join($value, string $separator, string $expected) {
		$this->assertSame($expected, format::oxford_join($value, $separator));
	}

	/**
	 * ::phone()
	 *
	 * @dataProvider data_phone
	 *
	 * @param mixed $value Value.
	 * @param string $country Country.
	 * @param string $expected Expected.
	 */
	function test_phone($value, $country, $expected) {
		$this->assertEquals($expected, format::phone($value, $country));
	}

	/**
	 * ::round()
	 *
	 * @dataProvider data_round
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @param int $mode Mode.
	 * @param mixed $expected Expected.
	 */
	function test_round($num, $precision, $mode, $expected) {
		$this->assertSame($expected, format::round($num, $precision, $mode));
	}

	/**
	 * Test: ::to_csv()
	 *
	 * @dataProvider data_to_csv
	 *
	 * @param mixed $value Value.
	 * @param mixed $headers Headers.
	 * @param string $delimiter Delimiter.
	 * @param string $eol Eol.
	 * @param mixed $expected Expected.
	 */
	function test_to_csv($value, $headers, string $delimiter, string $eol, $expected) {
		$result = format::to_csv($value, $headers, $delimiter, $eol);
		$this->assertSame($expected, $result);
		$this->assertSame('string', \gettype($result));
	}

	/**
	 * ::to_timezone()
	 *
	 * @dataProvider data_to_timezone
	 *
	 * @param mixed $date Date.
	 * @param string $from From.
	 * @param string $to To.
	 * @param string $expected Expected.
	 */
	function test_to_timezone($date, $from, $to, $expected) {
		$this->assertEquals($expected, format::to_timezone($date, $from, $to));
	}

	/**
	 * ::to_xls()
	 *
	 * @return void Nothing.
	 */
	function test_to_xls() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = format::to_xls($data);
		$this->assertSame(true, false !== \strpos($csv, 'NAME'));

		$csv = format::to_xls($data, $headers);
		$this->assertSame(true, false !== \strpos($csv, 'FIRST NAME'));
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::array_flatten()
	 *
	 * @return array Data.
	 */
	function data_array_flatten() {
		return array(
			array(
				array(1, 2, 3, 4),
				array(1, 2, 3, 4),
			),
			array(
				array(
					1,
					array(
						2,
						array(3, 4),
					),
				),
				array(1, 2, 3, 4),
			),
		);
	}

	/**
	 * Data for ::to_csv()
	 *
	 * @return array Data.
	 */
	function data_to_csv() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		return array(
			array(
				$data,
				null,
				',',
				"\n",
				"\"NAME\",\"PHONE\"\n\"John\",\"+1 201-555-0123\"",
			),
			array(
				$data,
				$headers,
				',',
				"\n",
				"\"FIRST NAME\",\"PHONE NUMBER\"\n\"John\",\"+1 201-555-0123\"",
			),
			array(
				$data,
				null,
				"\t",
				"\r\n",
				"\"NAME\"\t\"PHONE\"\r\n\"John\"\t\"+1 201-555-0123\"",
			),
			array(
				array(array('NAME'=>'John "Cool" Dude')),
				$headers,
				',',
				"\n",
				"\"FIRST NAME\",\"PHONE NUMBER\"\n\"John \"\"Cool\"\" Dude\"",
			),
		);
	}

	/**
	 * Data for ::array_to_indexed()
	 *
	 * @return array Data.
	 */
	function data_array_to_indexed() {
		return array(
			array(
				array(1),
				array(
					array(
						'key'=>0,
						'value'=>1,
					),
				),
			),
			array(
				array(),
				array(),
			),
			array(
				array('Foo'=>'Bar'),
				array(
					array(
						'key'=>'Foo',
						'value'=>'Bar',
					),
				),
			),
		);
	}

	/**
	 * Data for ::ceil()
	 *
	 * @return array Data.
	 */
	function data_ceil() {
		return array(
			array(
				1,
				1,
				1.0,
			),
			array(
				1.234,
				1,
				1.3,
			),
			array(
				1.234,
				2,
				1.24,
			),
			array(
				array(1.234, '4.567'),
				2,
				array(1.24, 4.57),
			),
		);
	}

	/**
	 * Data for ::cidr_to_range()
	 *
	 * @return array Data.
	 */
	function data_cidr_to_range() {
		return array(
			array(
				'50.116.18.174/24',
				array(
					'min'=>'50.116.18.0',
					'max'=>'50.116.18.255',
				),
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2/64',
				array(
					'min'=>'2600:3c00::',
					'max'=>'2600:3c00::ffff:ffff:ffff:ffff',
				),
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2/96',
				array(
					'min'=>'2600:3c00::f03c:91ff:0:0',
					'max'=>'2600:3c00::f03c:91ff:ffff:ffff',
				),
			),
			array(
				'26G0:3c00::f03c:91ff:feae:0ff2/64',
				false,
			),
		);
	}

	/**
	 * Data for ::decode_entities()
	 *
	 * @return array Data.
	 */
	function data_decode_entities() {
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
	 * Data for ::decode_js_entities()
	 *
	 * @return array Data.
	 */
	function data_decode_js_entities() {
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
				'Hi\\' . \chr(0x08) . 'b There',
			),
		);
	}

	/**
	 * Data for ::excerpt()
	 *
	 * @return array Data.
	 */
	function data_excerpt() {
		return array(
			array(
				'It ẉẩṩ a dark and stormy night.',
				array(
					'unit'=>'word',
					'length'=>3,
					'suffix'=>'!',
				),
				'It ẉẩṩ a!',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				array(
					'unit'=>'word',
					'length'=>30,
					'suffix'=>'!',
				),
				'It ẉẩṩ a dark and stormy night.',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				array(
					'unit'=>'word',
					'length'=>3,
				),
				'It ẉẩṩ a…',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				array(
					'unit'=>'char',
					'length'=>6,
				),
				'It ẉẩṩ…',
			),
		);
	}

	/**
	 * Data for ::floor()
	 *
	 * @return array Data.
	 */
	function data_floor() {
		return array(
			array(
				1,
				1,
				1.0,
			),
			array(
				1.234,
				1,
				1.2,
			),
			array(
				1.234,
				2,
				1.23,
			),
			array(
				array(1.234, '4.567'),
				2,
				array(1.23, 4.56),
			),
		);
	}

	/**
	 * Data for ::fraction()
	 *
	 * @return array Data.
	 */
	function data_fraction() {
		return array(
			array(
				0.5,
				0.0001,
				'1/2',
			),
			array(
				1.5,
				0.0001,
				'3/2',
			),
			array(
				0.33,
				0.0001,
				'33/100',
			),
			array(
				0.33,
				0.1,
				'1/3',
			),
			array(
				-0.33,
				0.1,
				'-1/3',
			),
			array(
				array(0.33, 0.66),
				0.1,
				array('1/3', '2/3'),
			),
			array(
				3,
				0.1,
				'3',
			),
			array(
				0.714285714,
				0.0001,
				'5/7',
			),
		);
	}

	/**
	 * Data for ::inflect()
	 *
	 * @return array Data.
	 */
	function data_inflect() {
		return array(
			array(
				1,
				'%d book',
				'%d books',
				'1 book',
			),
			array(
				0,
				'%d book',
				'%d books',
				'0 books',
			),
			array(
				1.5,
				'%.01f book',
				'%.01f books',
				'1.5 books',
			),
			array(
				array(1, 2, 3),
				'%d book',
				'%d books',
				'3 books',
			),
		);
	}

	/**
	 * Data for ::ip_to_number()
	 *
	 * @return array Data.
	 */
	function data_ip_to_number() {
		return array(
			array(
				'50.116.18.174',
				846467758,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				50511880784403022287880976722111107058,
			),
			array(
				'2600:3c00::f03c:91ff:feae:ff2',
				50511880784403022287880976722111107058,
			),
			array(
				'127.0.0.1',
				2130706433,
			),
		);
	}

	/**
	 * Data for ::ip_to_subnet()
	 *
	 * @return array Data.
	 */
	function data_ip_to_subnet() {
		return array(
			array(
				'50.116.18.174',
				'50.116.18.0/24',
			),
			array(
				'2600:3c00::f03c:91ff:FEAE:0ff2',
				'2600:3c00::/64',
			),
			array(
				'127.0.0.1',
				'127.0.0.0/24',
			),
		);
	}

	/**
	 * Data for ::json()
	 *
	 * @return array Data.
	 */
	function data_json() {
		return array(
			array(
				"{'hello':'there' } ",
				false,
				'{"hello":"there"}',
			),
			array(
				"{hello:'there' } ",
				false,
				'{"hello":"there"}',
			),
			array(
				"['hello','there' ] ",
				false,
				'["hello","there"]',
			),
			array(
				'["hello","\"there" ] ',
				false,
				'["hello","\"there"]',
			),
			array(
				"['\"there']",
				false,
				'["\"there"]',
			),
			array(
				23,
				false,
				'23',
			),
			array(
				array('there'),
				false,
				'["there"]',
			),
			array(
				array('hello'=>array('there')),
				false,
				'{"hello":["there"]}',
			),
			array(
				'["hello","there" ]',
				true,
				"[\n    \"hello\",\n    \"there\"\n]",
			),
		);
	}

	/**
	 * Data for ::links()
	 *
	 * @return array Data.
	 */
	function data_links() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

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
	 * Data for ::list_to_array()
	 *
	 * @return array Data.
	 */
	function data_list_to_array() {
		return array(
			array(
				'1,2,3',
				null,
				array('1', '2', '3'),
			),
			array(
				array(0, '1,2,3'),
				null,
				array('0', '1', '2', '3'),
			),
			array(
				array('1,2;3,4'),
				';',
				array('1,2', '3,4'),
			),
			array(
				array('1, 2, 3,, 3, 4, 4'),
				array(
					'delimiter'=>',',
					'trim'=>true,
					'unique'=>true,
					'sort'=>false,
					'cast'=>'int',
					'min'=>2,
					'max'=>3,
				),
				array(2, 3),
			),
			array(
				array('1, 2, 3,, 3, 4, 4'),
				array(
					'unique'=>false,
					'cast'=>'int',
					'min'=>2,
					'max'=>3,
				),
				array(2, 3, 3),
			),
			array(
				array('2015-01-01', array(array('2010-01-01,2014-06-01'))),
				array(
					'unique'=>true,
					'min'=>'2011',
					'sort'=>true,
				),
				array('2014-06-01', '2015-01-01'),
			),
		);
	}

	/**
	 * Data for ::money()
	 *
	 * @return array Data.
	 */
	function data_money() {
		return array(
			array(2.5, false, '', false, '$2.50'),
			array('1', false, '', false, '$1.00'),
			array('1', false, '', true, '$1'),
			array(2500, false, '', false, '$2500.00'),
			array(2500, false, ',', false, '$2,500.00'),
			array(.23, false, '', false, '$0.23'),
			array(.23, true, '', false, '23¢'),
			array(array(.23), true, '', false, array('23¢')),
		);
	}

	/**
	 * Data for ::number_to_ip()
	 *
	 * @return array Data.
	 */
	function data_number_to_ip() {
		return array(
			array(
				846467758,
				'50.116.18.174',
			),
			array(
				'50511880784403022287880976722111107058',
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				2130706433,
				'127.0.0.1',
			),
		);
	}

	/**
	 * Data for ::oxford_join()
	 *
	 * @return array Values.
	 */
	function data_oxford_join() {
		return array(
			array(
				array('apples', 'bananas', 'oranges'),
				'and',
				'apples, bananas, and oranges',
			),
			array(
				array('apples', 'bananas', 'oranges'),
				'and/or',
				'apples, bananas, and/or oranges',
			),
			array(
				array('apples', 'bananas'),
				'and',
				'apples and bananas',
			),
			array(
				array('apples'),
				'and',
				'apples',
			),
			array(
				array(array('apples'), 1, 'bananas'),
				'and',
				'1 and bananas',
			),
		);
	}

	/**
	 * Data for ::phone()
	 *
	 * @return array Data.
	 */
	function data_phone() {
		return array(
			array(2015550123, null, '+1 201-555-0123'),
			array(2015550123, 'US', '+1 201-555-0123'),
			array(2015550123, 'CA', '+1 201-555-0123'),
			array(array(2015550123), 'CA', array('+1 201-555-0123')),
		);
	}

	/**
	 * Data for ::round()
	 *
	 * @return array Data.
	 */
	function data_round() {
		return array(
			array(
				1,
				1,
				\PHP_ROUND_HALF_UP,
				1.0,
			),
			array(
				1.234,
				1,
				\PHP_ROUND_HALF_UP,
				1.2,
			),
			array(
				1.234,
				2,
				\PHP_ROUND_HALF_UP,
				1.23,
			),
			array(
				array(1.234, '4.567'),
				2,
				\PHP_ROUND_HALF_UP,
				array(1.23, 4.57),
			),
		);
	}

	/**
	 * Data for ::to_timezone()
	 *
	 * @return array Data.
	 */
	function data_to_timezone() {
		return array(
			array(
				'2015-01-15 01:12:23',
				'America/Los_Angeles',
				null,
				'2015-01-15 09:12:23',
			),
			array(
				'2015-01-15 01:12:23',
				'America/Los_Angeles',
				'UTC',
				'2015-01-15 09:12:23',
			),
			array(
				\strtotime('2015-01-15 01:12:23'),
				'America/Los_Angeles',
				null,
				'2015-01-15 09:12:23',
			),
			array(
				'2015-01-15 01:12:23',
				'UTC',
				'America/Los_Angeles',
				'2015-01-14 17:12:23',
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


