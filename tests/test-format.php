<?php
/**
 * Format tests.
 *
 * PHPUnit tests for format.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\format;

/**
 * Test Suite
 */
class format_tests extends \PHPUnit\Framework\TestCase {

	// --------------------------------------------------------------------
	// Tests
	// --------------------------------------------------------------------

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
	 * @return void Nothing.
	 */
	function test_excerpt() {
		$thing = 'It ẉẩṩ a dark and stormy night.';

		$this->assertEquals('It ẉẩṩ a!', format::excerpt($thing, array('unit'=>'word', 'length'=>3, 'suffix'=>'!')));
		$this->assertEquals('It ẉẩṩ a…', format::excerpt($thing, array('unit'=>'word', 'length'=>3)));
		$this->assertEquals('It ẉẩṩ…', format::excerpt($thing, array('unit'=>'char', 'length'=>6)));
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

		$this->assertEquals('1 book', format::inflect($count, $single, $plural));

		$count = 2;
		$this->assertEquals('2 books', format::inflect($count, $single, $plural));

		$count = 0;
		$this->assertEquals('0 books', format::inflect($count, $single, $plural));
	}

	/**
	 * ::ip_to_number()
	 *
	 * @return void Nothing.
	 */
	function test_ip_to_number() {
		$thing = '50.116.18.174';
		$this->assertEquals(846467758, format::ip_to_number($thing));

		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals(50511880784403022287880976722111107058, format::ip_to_number($thing));
	}

	/**
	 * ::json()
	 *
	 * @return void Nothing.
	 */
	function test_json() {
		$things = array(
			array(
				'key'=>"{'hello':'there' } ",
				'value'=>'{"hello":"there"}'
			),
			array(
				'key'=>"{hello:'there' } ",
				'value'=>'{"hello":"there"}'
			),
			array(
				'key'=>"['hello','there' ] ",
				'value'=>'["hello","there"]'
			),
			array(
				'key'=>'["hello","\"there" ] ',
				'value'=>'["hello","\"there"]'
			),
			array(
				'key'=>"['\"there']",
				'value'=>'["\"there"]'
			),
			array(
				'key'=>23,
				'value'=>'23'
			),
			array(
				'key'=>array('there'),
				'value'=>'["there"]'
			),
			array(
				'key'=>array('hello'=>array('there')),
				'value'=>'{"hello":["there"]}'
			),
		);

		foreach ($things as $thing) {
			$this->assertEquals($thing['value'], format::json($thing['key'], false));
		}

		$expected = '[
    "hello",
    "there"
]';
		$this->assertEquals($expected, format::json("['hello','there' ] ", true));
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
			'smiley@☺.com'=>'<a href="mailto:smiley@xn--74h.com">smiley@☺.com</a>',
			'+12015550123'=>'<a href="tel:+12015550123">+12015550123</a>',
			'+1 201-555-0123'=>'<a href="tel:+12015550123">+1 201-555-0123</a>',
			'201-555-0123'=>'<a href="tel:+12015550123">201-555-0123</a>',
			'(201) 555-0123'=>'<a href="tel:+12015550123">(201) 555-0123</a>',
			'201.555.0123'=>'<a href="tel:+12015550123">201.555.0123</a>',
			'I ate 234234234 apples!'=>'I ate 234234234 apples!',
			'Call me at (201) 555-0123.'=>'Call me at <a href="tel:+12015550123">(201) 555-0123</a>.'
		);

		foreach ($things as $k=>$v) {
			$this->assertEquals($v, format::links($k));
		}

		$args = array(
			'class'=>array('link', 'nav'),
			'rel'=>'apples',
			'target'=>'_blank'
		);
		$thing = format::links('blobfolio.com', $args);
		$this->assertEquals('<a href="http://blobfolio.com" class="link nav" rel="apples" target="_blank">blobfolio.com</a>', $thing);

		$thing = format::links('me@blobfolio.com', $args);
		$this->assertEquals('<a href="mailto:me@blobfolio.com" class="link nav" rel="apples" target="_blank">me@blobfolio.com</a>', $thing);

		$args = array(
			'class'=>'link'
		);
		$thing = format::links('blobfolio.com', $args);
		$this->assertEquals('<a href="http://blobfolio.com" class="link">blobfolio.com</a>', $thing);
	}

	/**
	 * ::money()
	 *
	 * @return void Nothing.
	 */
	function test_money() {
		$thing = 2.5;
		$this->assertEquals('$2.50', format::money($thing));

		$thing = '1';
		$this->assertEquals('$1.00', format::money($thing));

		$this->assertEquals('$1', format::money($thing, false, ',', true));

		$thing = 2500;
		$this->assertEquals('$2500.00', format::money($thing));

		$thing = 2500;
		$this->assertEquals('$2,500.00', format::money($thing, false, ','));

		$thing = .23;
		$this->assertEquals('$0.23', format::money($thing, false));
		$this->assertEquals('23¢', format::money($thing, true));
	}

	/**
	 * ::phone()
	 *
	 * @return void Nothing.
	 */
	function test_phone() {
		$thing = '2015550123';
		$this->assertEquals('+1 201-555-0123', format::phone($thing));

		$this->assertEquals('+1 201-555-0123', format::phone($thing, 'US'));
	}

	/**
	 * ::to_csv()
	 *
	 * @return void Nothing.
	 */
	function test_to_csv() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = format::to_csv($data);
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = format::to_csv($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));

		$csv = format::to_csv($data, $headers, "\t");
		$this->assertEquals(true, false !== strpos($csv, "\t"));
	}

	/**
	 * ::to_timezone()
	 *
	 * @return void Nothing.
	 */
	function test_to_timezone() {
		$thing = '2015-01-15 01:12:23';

		$this->assertEquals('2015-01-15 09:12:23', format::to_timezone($thing, 'America/Los_Angeles'));
		$this->assertEquals('2015-01-14 17:12:23', format::to_timezone($thing, 'UTC', 'America/Los_Angeles'));
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
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = format::to_xls($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));
	}

	// -------------------------------------------------------------------- end tests



	// --------------------------------------------------------------------
	// Data
	// --------------------------------------------------------------------

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
						'value'=>1
					)
				)
			),
			array(
				array(),
				array()
			),
			array(
				array('Foo'=>'Bar'),
				array(
					array(
						'key'=>'Foo',
						'value'=>'Bar'
					)
				)
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
					'max'=>'50.116.19.173'
				)
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2/64',
				array(
					'min'=>'2600:3c00::f03c:91ff:feae:ff2',
					'max'=>'2600:3c00::ffff:ffff:ffff:ffff'
				)
			),
			array(
				'26G0:3c00::f03c:91ff:feae:0ff2/64',
				false
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
				'Happy & Healthy'
			),
			array(
				'5&#48;&cent;',
				'50¢'
			),
			array(
				'50&amp;cent;',
				'50¢'
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
				"\nhelloÁ"
			),
			array(
				'\\u75',
				'u'
			),
			array(
				'\\\\u75\\u30\\u30\\u63\\u31',
				'Á'
			),
		);
	}

	// -------------------------------------------------------------------- end data
}


