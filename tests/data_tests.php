<?php
/**
 * Data tests.
 *
 * PHPUnit tests for data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use blobfolio\common\data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test Suite
 */
class data_tests extends TestCase {
	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_array_compare')]
	/**
	 * ::array_compare()
	 *
	 * @param array $arr1 Array 1.
	 * @param array $arr2 Array 2.
	 * @param array $expected Expected.
	 */
	public function test_array_compare($arr1, $arr2, $expected) {
		$this->assertSame($expected, data::array_compare($arr1, $arr2));
	}

	#[Test]
	#[DataProvider('data_array_idiff')]
	/**
	 * ::array_idiff()
	 *
	 * @param array $expected Expected.
	 */
	public function test_array_idiff($expected) {
		// This takes a variable number of arguments.
		$arrays = \func_get_args();
		if (! \is_array($arrays) || \count($arrays) < 2) {
			return;
		}
		unset($arrays[0]);

		$result = \call_user_func_array(array('\\blobfolio\\common\\data', 'array_idiff'), $arrays);
		$this->assertSame($expected, $result);
	}

	#[Test]
	#[DataProvider('data_array_iintersect')]
	/**
	 * ::array_iintersect()
	 *
	 * @param array $expected Expected.
	 */
	public function test_array_iintersect($expected) {
		// This takes a variable number of arguments.
		$arrays = \func_get_args();
		if (! \is_array($arrays) || \count($arrays) < 2) {
			return;
		}
		unset($arrays[0]);

		$result = \call_user_func_array(array('\\blobfolio\\common\\data', 'array_iintersect'), $arrays);
		$this->assertSame($expected, $result);
	}

	#[Test]
	#[DataProvider('data_array_ikey_exists')]
	/**
	 * ::array_ikey_exists()
	 *
	 * @param mixed $key Key.
	 * @param array $arr Array.
	 * @param bool $expected Expected.
	 */
	public function test_array_ikey_exists($key, $arr, $expected) {
		$this->assertSame($expected, data::array_ikey_exists($key, $arr));
	}

	#[Test]
	#[DataProvider('data_array_isearch')]
	/**
	 * ::array_isearch()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @param bool $expected Expected.
	 */
	public function test_array_isearch($needle, $haystack, $strict, $expected) {
		$this->assertSame($expected, data::array_isearch($needle, $haystack, $strict));
	}

	#[Test]
	#[DataProvider('data_array_map_recurisve')]
	/**
	 * ::array_map_recursive()
	 *
	 * @param string $callback Callback.
	 * @param mixed $value Value.
	 * @param array $expected Expected.
	 */
	public function test_array_map_recursive($callback, $value, $expected) {
		$this->assertEquals($expected, data::array_map_recursive($callback, $value));
	}

	#[Test]
	#[DataProvider('data_array_otherize')]
	/**
	 * ::array_otherize()
	 *
	 * @param array $arr Array.
	 * @param int $length Length.
	 * @param string $label Label.
	 * @param array $expected Expected.
	 */
	public function test_array_otherize($arr, $length, $label, $expected) {
		$this->assertEquals($expected, data::array_otherize($arr, $length, $label));
	}

	#[Test]
	#[DataProvider('data_array_pop')]
	/**
	 * ::array_pop()
	 *
	 * @param array $arr Array.
	 * @param array $expected Expected.
	 */
	public function test_array_pop($arr, $expected) {
		$this->assertEquals($expected, data::array_pop($arr));
	}

	#[Test]
	#[DataProvider('data_array_pop_rand')]
	/**
	 * ::array_pop_rand()
	 *
	 * @param array $arr Array.
	 */
	public function test_array_pop_rand($arr) {
		if (! \count($arr)) {
			$this->assertSame(false, data::array_pop_rand($arr));
		}
		elseif (1 === \count($arr)) {
			$this->assertSame(data::array_pop_top($arr), data::array_pop_rand($arr));
		}
		else {
			// Since this is random, we are basically just looking for a
			// case where the previous value is not the same as the last.
			$different = false;
			$last = data::array_pop_rand($arr);
			while (! $different) {
				$v = data::array_pop_rand($arr);
				if ($last !== $v) {
					$different = true;
					break;
				}
				$last = $v;
			}

			$this->assertSame(true, $different);
		}
	}

	#[Test]
	#[DataProvider('data_array_pop_top')]
	/**
	 * ::array_pop_top()
	 *
	 * @param array $arr Array.
	 * @param array $expected Expected.
	 */
	public function test_array_pop_top($arr, $expected) {
		$this->assertEquals($expected, data::array_pop_top($arr));
	}

	#[Test]
	#[DataProvider('data_cc_exp_months')]
	/**
	 * ::cc_exp_months()
	 *
	 * @param string $format Format.
	 * @param array $expected Expected.
	 */
	public function test_cc_exp_months($format, $expected) {
		$this->assertEquals($expected, data::cc_exp_months($format));
	}

	#[Test]
	#[DataProvider('data_cc_exp_years')]
	/**
	 * ::cc_exp_years()
	 *
	 * @param int $length Length.
	 * @param array $expected Expected.
	 */
	public function test_cc_exp_years($length, $expected) {
		$this->assertEquals($expected, data::cc_exp_years($length));
	}

	#[Test]
	#[DataProvider('data_datediff')]
	/**
	 * ::datediff()
	 *
	 * @param mixed $date1 Date 1.
	 * @param mixed $date2 Date 2.
	 * @param array $expected Expected.
	 */
	public function test_datediff($date1, $date2, $expected) {
		$this->assertSame($expected, data::datediff($date1, $date2));
	}

	#[Test]
	#[DataProvider('data_iin_array')]
	/**
	 * ::iin_array()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @param bool $expected Expected.
	 */
	public function test_iin_array($needle, $haystack, $strict, $expected) {
		$this->assertSame($expected, data::iin_array($needle, $haystack, $strict));
	}

	#[Test]
	#[DataProvider('data_in_range')]
	/**
	 * ::in_range()
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param array $expected Expected.
	 */
	public function test_in_range($value, $min, $max, $expected) {
		$this->assertSame($expected, data::in_range($value, $min, $max));
	}

	#[Test]
	#[DataProvider('data_ip_in_range')]
	/**
	 * ::ip_in_range()
	 *
	 * @param mixed $ip IP.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param array $expected Expected.
	 */
	public function test_ip_in_range($ip, $min, $max, $expected) {
		$this->assertSame($expected, data::ip_in_range($ip, $min, $max));
	}

	#[Test]
	#[DataProvider('data_is_json')]
	/**
	 * ::is_json()
	 *
	 * @param mixed $value Value.
	 * @param bool $empty Allow empty.
	 * @param array $expected Expected.
	 */
	public function test_is_json($value, $empty, $expected) {
		$this->assertSame($expected, data::is_json($value, $empty));
	}

	#[Test]
	#[DataProvider('data_is_utf8')]
	/**
	 * ::is_utf8()
	 *
	 * @param mixed $value Value.
	 * @param array $expected Expected.
	 */
	public function test_is_utf8($value, $expected) {
		$this->assertEquals($expected, data::is_utf8($value));
	}

	#[Test]
	#[DataProvider('data_json_decode_array')]
	/**
	 * ::json_decode_array()
	 *
	 * @param mixed $json JSON.
	 * @param array $default Template.
	 * @param bool $strict Strict.
	 * @param bool $recursive Recursive.
	 * @param array $expected Expected.
	 */
	public function test_json_decode_array($json, $default, $strict, $recursive, $expected) {
		$this->assertEquals($expected, data::json_decode_array($json, $default, $strict, $recursive));
	}

	#[Test]
	#[DataProvider('data_length_in_range')]
	/**
	 * ::length_in_range()
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param array $expected Expected.
	 */
	public function test_length_in_range($value, $min, $max, $expected) {
		$this->assertSame($expected, data::length_in_range($value, $min, $max));
	}

	#[Test]
	#[DataProvider('data_parse_args')]
	/**
	 * ::parse_args()
	 *
	 * @param mixed $args Args.
	 * @param array $default Template.
	 * @param bool $strict Strict.
	 * @param bool $recursive Recursive.
	 * @param array $expected Expected.
	 */
	public function test_parse_args($args, $default, $strict, $recursive, $expected) {
		$this->assertEquals($expected, data::parse_args($args, $default, $strict, $recursive));
	}

	#[Test]
	/**
	 * ::random_int()
	 *
	 * @return void Nothing.
	 */
	public function test_random_int() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = data::random_int(0, 10);
		}

		$this->assertSame(true, \count($thing) === 20);
		$this->assertSame(true, \count(\array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if ($t >= 0 && $t <= 10) {
				$thing2[] = $t;
			}
		}

		$this->assertSame(true, \count($thing) === \count($thing2));
	}

	#[Test]
	/**
	 * ::random_string()
	 *
	 * @return void Nothing.
	 */
	public function test_random_string() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = data::random_string(10);
		}

		$this->assertSame(true, \count($thing) === 20);
		$this->assertSame(true, \count(\array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if (\strlen($t) === 10) {
				$thing2[] = $t;
			}
		}

		$this->assertSame(true, \count($thing) === \count($thing2));

		// Test a custom soup.
		$thing = array('a', 'b', 'c', 'd', 'e');
		for ($x = 0; $x < 20; $x++) {
			$result = data::random_string(10, $thing);
			$this->assertSame(true, !! \preg_match('/^[a-e]{10}$/', $result));
		}
	}

	#[Test]
	/**
	 * ::switcheroo()
	 *
	 * @return void Nothing.
	 */
	public function test_switcheroo() {
		$var1 = 5;
		$var2 = 10;

		data::switcheroo($var1, $var2);

		$this->assertEquals(5, $var2);
		$this->assertEquals(10, $var1);
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::array_compare()
	 *
	 * @return array Data.
	 */
	static function data_array_compare() {
		$arr1 = array(1, 2, 3);
		$arr2 = array(2, 3, 1);
		$arr3 = array(
			'Foo'=>'Bar',
			'Bar'=>array(1, 2, 3),
		);
		$arr4 = array();

		return array(
			array($arr1, $arr1, true),
			array($arr1, $arr2, true),
			array($arr1, $arr3, false),
			array($arr3, $arr3, true),
			array($arr4, $arr4, true),
		);
	}

	/**
	 * Data for ::array_idiff()
	 *
	 * @return array Data.
	 */
	static function data_array_idiff() {
		return array(
			array(
				array(
					3=>'Sat',
					4=>'Mat',
					5=>'Matt',
					6=>'Tat',
					7=>800,
				),
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat'),
				array('BAT', '800'),
			),
			array(
				array(),
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat', 'bat', 'sat'),
				array('mat', 'matt', 'tat', 800),
			),
		);
	}

	/**
	 * Data for ::array_iintersect()
	 *
	 * @return array Data.
	 */
	static function data_array_iintersect() {
		return array(
			array(
				array(),
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat'),
				array('BAT', '800'),
			),
			array(
				array(
					1=>'Cat',
					3=>'Sat',
				),
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat', 'sat'),
				array('BAT', 'CAT', 'SAT'),
			),
		);
	}

	/**
	 * Data for ::array_ikey_exists()
	 *
	 * @return array Data.
	 */
	static function data_array_ikey_exists() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
			),
			array(
				'food',
				array('Foo'=>'Bar'),
				false,
			),
			array(
				1,
				array(2, 3, 4),
				true,
			),
			array(
				18,
				array(2, 3, 4),
				false,
			),
		);
	}

	/**
	 * Data for ::array_isearch()
	 *
	 * @return array Data.
	 */
	static function data_array_isearch() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
				false,
			),
			array(
				'BJÖRK',
				array('Foo'=>'Björk'),
				true,
				'Foo',
			),
			array(
				2,
				array(2, 3, 4),
				true,
				0,
			),
			array(
				'2',
				array(2, 3, 4),
				false,
				0,
			),
			array(
				'2',
				array(2, 3, 4),
				true,
				false,
			),
		);
	}

	/**
	 * Data for ::array_map_recursive()
	 *
	 * @return array Data.
	 */
	static function data_array_map_recurisve() {
		return array(
			array(
				'strval',
				array(1, 2, 3),
				array('1', '2', '3'),
			),
			array(
				'strval',
				array(
					'foo'=>array(1, 2, 3),
					'bar'=>1,
				),
				array(
					'foo'=>array('1', '2', '3'),
					'bar'=>'1',
				),
			),
		);
	}

	/**
	 * Data for ::array_otherize()
	 *
	 * @return array Data.
	 */
	static function data_array_otherize() {
		$arr = array(
			'US'=>100,
			'CA'=>200,
			'CN'=>5,
			'GB'=>10,
			'MX'=>30,
		);

		return array(
			array(
				$arr,
				3,
				'Other',
				array(
					'CA'=>200,
					'US'=>100,
					'Other'=>45,
				),
			),
			array(
				$arr,
				6,
				'Other',
				array(
					'CA'=>200,
					'US'=>100,
					'MX'=>30,
					'GB'=>10,
					'CN'=>5,
				),
			),
			array(
				$arr,
				1,
				'Other',
				array(
					'Other'=>345,
				),
			),
			array(
				array(
					'US'=>'5%',
					'CA'=>'10¢',
					'MX'=>'$1.32',
					'TX'=>'hotdogs',
				),
				4,
				'Other',
				array(
					'MX'=>1.32,
					'CA'=>.1,
					'US'=>.05,
					'TX'=>0.0,
				),
			),
		);
	}

	/**
	 * Data for ::array_pop()
	 *
	 * @return array Data.
	 */
	static function data_array_pop() {
		return array(
			array(
				array(1, 2, 3),
				3,
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
				),
				'Foo',
			),
			array(
				array(),
				false,
			),
		);
	}

	/**
	 * Data for ::array_pop_rand()
	 *
	 * @return array Data.
	 */
	static function data_array_pop_rand() {
		return array(
			array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
					'You'=>'Hoo',
					'apples',
					'carrots',
					'oranges',
					8,
					9,
					0,
					'hello',
				),
			),
			array(
				array(),
			),
			array(
				array('foobar'),
			),
		);
	}

	/**
	 * Data for ::array_pop_top()
	 *
	 * @return array Data.
	 */
	static function data_array_pop_top() {
		return array(
			array(
				array(1, 2, 3),
				1,
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
				),
				'Bar',
			),
			array(
				array(),
				false,
			),
		);
	}

	/**
	 * Data for ::cc_exp_months()
	 *
	 * @return array Data.
	 */
	static function data_cc_exp_months() {
		return array(
			array(
				'F',
				array(
					1=>'January',
					2=>'February',
					3=>'March',
					4=>'April',
					5=>'May',
					6=>'June',
					7=>'July',
					8=>'August',
					9=>'September',
					10=>'October',
					11=>'November',
					12=>'December',
				),
			),
		);
	}

	/**
	 * Data for ::cc_exp_years()
	 *
	 * @return array Data.
	 */
	static function data_cc_exp_years() {
		$year = (int) \date('Y');

		$arr1 = array();
		for ($x = 0; $x < 5; $x++) {
			$key = $year + $x;
			$arr1[$key] = $key;
		}

		$arr2 = array();
		for ($x = 0; $x < 10; $x++) {
			$key = $year + $x;
			$arr2[$key] = $key;
		}

		return array(
			array(
				5,
				$arr1,
			),
			array(
				10,
				$arr2,
			),
		);
	}

	/**
	 * Data for ::datediff()
	 *
	 * @return array Data.
	 */
	static function data_datediff() {
		return array(
			array(
				'2015-01-01',
				'2015-01-01',
				0,
			),
			array(
				'2015-01-03',
				'2015-01-01',
				2,
			),
			array(
				'2015-01-01',
				'2015-01-03',
				2,
			),
			array(
				\strtotime('2015-01-03'),
				'2015-01-01',
				2,
			),
		);
	}

	/**
	 * Data for ::iin_array()
	 *
	 * @return array Data.
	 */
	static function data_iin_array() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
				false,
			),
			array(
				'BAR',
				array('Foo'=>'Bar'),
				true,
				true,
			),
			array(
				2,
				array(2, 3, 4),
				true,
				true,
			),
			array(
				'2',
				array(2, 3, 4),
				false,
				true,
			),
			array(
				'2',
				array(2, 3, 4),
				true,
				false,
			),
		);
	}

	/**
	 * Data for ::in_range()
	 *
	 * @return array Data.
	 */
	static function data_in_range() {
		return array(
			array(
				'2015-01-15',
				'2015-01-01',
				'2015-01-20',
				true,
			),
			array(
				'2015-01-15',
				'2015-01-01',
				'2015-01-05',
				false,
			),
			array(
				5,
				2,
				null,
				true,
			),
			array(
				'F',
				null,
				'F',
				true,
			),
			array(
				'F',
				null,
				'E',
				false,
			),
		);
	}

	/**
	 * Data for ::ip_in_range()
	 *
	 * @return array Data.
	 */
	static function data_ip_in_range() {
		return array(
			array(
				'127.0.0.1',
				'127.0.0.0',
				'127.0.0.2',
				true,
			),
			array(
				'127.0.0.1',
				'127.0.0.0/24',
				null,
				true,
			),
			array(
				'127.0.0.1',
				'192.168.1.0/24',
				null,
				false,
			),
			array(
				'2600:3c00::f03c:91ff:FEAE:0ff2',
				'2600:3c00::/64',
				null,
				true,
			),
		);
	}

	/**
	 * Data for ::is_json()
	 *
	 * @return array Data.
	 */
	static function data_is_json() {
		return array(
			array(
				1,
				false,
				false,
			),
			array(
				'yes',
				false,
				false,
			),
			array(
				'',
				false,
				false,
			),
			array(
				'{"happy":"days"}',
				false,
				true,
			),
			array(
				'[]',
				false,
				true,
			),
			array(
				'[1,2,3]',
				false,
				true,
			),
			array(
				'{"happy":"',
				false,
				false,
			),
			array(
				'',
				true,
				true,
			),
		);
	}

	/**
	 * Data for ::is_utf8()
	 *
	 * @return array Data.
	 */
	static function data_is_utf8() {
		return array(
			array(
				1,
				true,
			),
			array(
				'Hello World',
				true,
			),
			array(
				"\xc3\x28",
				false,
			),
		);
	}

	/**
	 * Data for ::json_decode_array()
	 *
	 * @return array Data.
	 */
	static function data_json_decode_array() {
		return array(
			array(
				'',
				null,
				true,
				true,
				array(),
			),
			array(
				'{"animal":"dog"}',
				null,
				true,
				true,
				array('animal'=>'dog'),
			),
			array(
				'{animal:"dog"}',
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				true,
				true,
				array(
					'animal'=>'dog',
					'fruit'=>'banana',
				),
			),
			array(
				'{animal:{"dog":"wolf"}}',
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				false,
				true,
				array(
					'animal'=>array('dog'=>'wolf'),
					'fruit'=>'banana',
				),
			),
			array(
				'{animal:"dog"}',
				array(
					'animal'=>array('bear'),
					'fruit'=>'banana',
				),
				true,
				true,
				array(
					'animal'=>array('dog'),
					'fruit'=>'banana',
				),
			),
			array(
				'{price:{animal:2}}',
				array(
					'price'=>array(
						'animal'=>12.0,
						'fruit'=>15.0,
					),
				),
				true,
				true,
				array(
					'price'=>array(
						'animal'=>2.0,
						'fruit'=>15.0,
					),
				),
			),
		);
	}

	/**
	 * Data for ::length_in_range()
	 *
	 * @return array Data.
	 */
	static function data_length_in_range() {
		return array(
			array(
				'Ḉẩt',
				1,
				3,
				true,
			),
			array(
				'Ḉẩt',
				4,
				null,
				false,
			),
			array(
				'Cat',
				1,
				3,
				true,
			),
			array(
				'Cat',
				null,
				4,
				true,
			),
		);
	}

	/**
	 * Data for ::parse_args()
	 *
	 * @return array Data.
	 */
	static function data_parse_args() {
		return array(
			array(
				'',
				array(''),
				false,
				false,
				array(''),
			),
			array(
				null,
				array('dog'=>'wolf'),
				false,
				false,
				array('dog'=>'wolf'),
			),
			array(
				array('animal'=>'dog'),
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				false,
				false,
				array(
					'animal'=>'dog',
					'fruit'=>'banana',
				),
			),
			array(
				array(
					'animal'=>array('dog'=>'wolf'),
				),
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				false,
				false,
				array(
					'animal'=>array('dog'=>'wolf'),
					'fruit'=>'banana',
				),
			),
			array(
				array('animal'=>'dog'),
				array(
					'animal'=>array('bear'),
					'fruit'=>'banana',
				),
				true,
				false,
				array(
					'animal'=>array('dog'),
					'fruit'=>'banana',
				),
			),
			array(
				array(
					'price'=>array('animal'=>'67¢'),
				),
				array(
					'price'=>array(
						'animal'=>12.0,
						'fruit'=>15.0,
					),
				),
				true,
				true,
				array(
					'price'=>array(
						'animal'=>.67,
						'fruit'=>15.0,
					),
				),
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


