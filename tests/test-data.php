<?php
/**
 * Data tests.
 *
 * PHPUnit tests for \blobfolio\common\data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class data_tests extends \PHPUnit\Framework\TestCase {

	/**
	 * ::array_compare()
	 *
	 * @return void Nothing.
	 */
	function test_array_compare() {
		$arr1 = array(1,2,3,4,5);
		$arr2 = array(1,2,3,4,5);
		$this->assertEquals(true, \blobfolio\common\data::array_compare($arr1, $arr2));

		$arr2 = array(1,2,4,5,6);
		$this->assertEquals(false, \blobfolio\common\data::array_compare($arr1, $arr2));

		$arr1 = array('animal'=>'dog', 'peach'=>array(1,2,3));
		$arr2 = array('animal'=>'dog', 'peach'=>array(1,2,3));
		$this->assertEquals(true, \blobfolio\common\data::array_compare($arr1, $arr2));

		$arr2 = array('animal'=>'cat', 'peach'=>array(1,2,3));
		$this->assertEquals(false, \blobfolio\common\data::array_compare($arr1, $arr2));

		$arr2 = array('animal'=>'dog', 'peach'=>array(1,2));
		$this->assertEquals(false, \blobfolio\common\data::array_compare($arr1, $arr2));
	}

	/**
	 * ::array_map_recursive()
	 *
	 * @return void Nothing.
	 */
	function test_array_map_recursive() {
		$thing = array(1,2,3,4,5);
		$this->assertEquals(array('1','2','3','4','5'), \blobfolio\common\data::array_map_recursive('strval', $thing));

		$thing = array(1, array(1));
		$this->assertEquals(array('1',array('1')), \blobfolio\common\data::array_map_recursive('strval', $thing));
	}

	/**
	 * ::array_pop()
	 *
	 * @return void Nothing.
	 */
	function test_array_pop() {
		$thing = array(1,2,3,4,5);
		$this->assertEquals(5, \blobfolio\common\data::array_pop($thing));

		$thing = array();
		$this->assertEquals(false, \blobfolio\common\data::array_pop($thing));
	}

	/**
	 * ::array_pop_top()
	 *
	 * @return void Nothing.
	 */
	function test_array_pop_top() {
		$thing = array(1,2,3,4,5);
		$this->assertEquals(1, \blobfolio\common\data::array_pop_top($thing));

		$thing = array();
		$this->assertEquals(false, \blobfolio\common\data::array_pop_top($thing));
	}

	/**
	 * ::cc_exp_months()
	 *
	 * @return void Nothing.
	 */
	function test_cc_exp_months() {
		$thing = \blobfolio\common\data::cc_exp_months();
		$values = array_values($thing);
		$keys = array_keys($thing);

		$this->assertEquals('01 - Jan', $values[0]);
		$this->assertEquals(1, $keys[0]);

		$thing = \blobfolio\common\data::cc_exp_months('F');
		$this->assertEquals('January', $thing[1]);
	}

	/**
	 * ::cc_exp_years()
	 *
	 * @return void Nothing.
	 */
	function test_cc_exp_years() {
		$thing = \blobfolio\common\data::cc_exp_years();
		$year = (int) date('Y');

		$this->assertEquals(10, count($thing));
		$this->assertEquals(true, in_array($year, $thing, true));

		$thing = \blobfolio\common\data::cc_exp_years(3);
		$this->assertEquals(3, count($thing));
	}

	/**
	 * ::datediff()
	 *
	 * @return void Nothing.
	 */
	function test_datediff() {
		$date1 = '2015-01-15';
		$date2 = '2015-01-17';

		$this->assertEquals(2, \blobfolio\common\data::datediff($date1, $date2));
		$this->assertEquals(2, \blobfolio\common\data::datediff($date2, $date1));
		$this->assertEquals(2, \blobfolio\common\data::datediff(strtotime($date2), $date1));
	}

	/**
	 * ::in_range()
	 *
	 * @return void Nothing.
	 */
	function test_in_range() {
		$thing = 1;

		$this->assertEquals(true, \blobfolio\common\data::in_range($thing, -1, 5));
		$this->assertEquals(false, \blobfolio\common\data::in_range($thing, 2, 5));
		$this->assertEquals(false, \blobfolio\common\data::in_range($thing, -2, 0));

		$thing = '2015-01-02';
		$this->assertEquals(true, \blobfolio\common\data::in_range($thing, '2015-01-01', '2015-01-15'));
		$this->assertEquals(false, \blobfolio\common\data::in_range($thing, '2015-01-15', '2015-01-20'));
	}

	/**
	 * ::is_json()
	 *
	 * @return void Nothing.
	 */
	function test_is_json() {
		$this->assertEquals(false, \blobfolio\common\data::is_json(1));
		$this->assertEquals(false, \blobfolio\common\data::is_json('yes'));
		$this->assertEquals(false, \blobfolio\common\data::is_json(''));
		$this->assertEquals(true, \blobfolio\common\data::is_json('{"happy":"days"}'));
		$this->assertEquals(true, \blobfolio\common\data::is_json('[]'));
		$this->assertEquals(true, \blobfolio\common\data::is_json('[1,2]'));
		$this->assertEquals(false, \blobfolio\common\data::is_json('{"happy":"'));

		$this->assertEquals(true, \blobfolio\common\data::is_json('', true));
	}

	/**
	 * ::is_utf8()
	 *
	 * @return void Nothing.
	 */
	function test_is_utf8() {
		$thing = 'hello';
		$this->assertEquals(true, \blobfolio\common\data::is_utf8($thing));

		$thing = 50;
		$this->assertEquals(true, \blobfolio\common\data::is_utf8($thing));

		$thing = "\xc3\x28";
		$this->assertEquals(false, \blobfolio\common\data::is_utf8($thing));
	}

	/**
	 * ::json_decode_array()
	 *
	 * @return void Nothing.
	 */
	function test_json_decode_array() {
		$thing = '';
		$this->assertEquals(array(), \blobfolio\common\data::json_decode_array($thing));

		$thing = '{"animal":"dog"}';
		$this->assertEquals(array('animal'=>'dog'), \blobfolio\common\data::json_decode_array($thing));

		$default = array('animal'=>'dog', 'fruit'=>'banana');
		$this->assertEquals($default, \blobfolio\common\data::json_decode_array($thing, $default));

		$thing = '{"animal":12}';
		$this->assertEquals(array('animal'=>12, 'fruit'=>'banana'), \blobfolio\common\data::json_decode_array($thing, $default, false));

		$this->assertEquals(array('animal'=>'12', 'fruit'=>'banana'), \blobfolio\common\data::json_decode_array($thing, $default));
	}

	/**
	 * ::length_in_range()
	 *
	 * @return void Nothing.
	 */
	function test_length_in_range() {
		$thing = 'cat';
		$this->assertEquals(true, \blobfolio\common\data::length_in_range($thing, 1, 5));
		$this->assertEquals(true, \blobfolio\common\data::length_in_range($thing, 3, 3));

		$thing = 'Ḉẩt';
		$this->assertEquals(true, \blobfolio\common\data::length_in_range($thing, 3, 3));
	}

	/**
	 * ::parse_args()
	 *
	 * @return void Nothing.
	 */
	function test_parse_args() {
		$thing = null;
		$default = array('fruit'=>'pear', 'animal'=>array('name'=>'Oscar', 'price'=>5.5));

		$this->assertEquals($default, \blobfolio\common\data::parse_args($thing, $default));

		$thing = array('weapon'=>'spear');
		$this->assertEquals($default, \blobfolio\common\data::parse_args($thing, $default));

		$thing = array('fruit'=>12);
		$this->assertEquals(array('fruit'=>'12', 'animal'=>array('name'=>'Oscar', 'price'=>5.5)), \blobfolio\common\data::parse_args($thing, $default));

		$this->assertEquals(array('fruit'=>12, 'animal'=>array('name'=>'Oscar', 'price'=>5.5)), \blobfolio\common\data::parse_args($thing, $default, false));

		$thing = array('animal'=>array('price'=>'1'));
		$this->assertEquals(array('fruit'=>'pear', 'animal'=>array('name'=>'Oscar', 'price'=>'1')), \blobfolio\common\data::parse_args($thing, $default, false));

		$this->assertEquals(array('fruit'=>'pear', 'animal'=>array('price'=>1)), \blobfolio\common\data::parse_args($thing, $default, true, false));
	}

	/**
	 * ::random_int()
	 *
	 * @return void Nothing.
	 */
	function test_random_int() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = \blobfolio\common\data::random_int(0, 10);
		}

		$this->assertEquals(true, count($thing) === 20);
		$this->assertEquals(true, count(array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if ($t >= 0 && $t <= 10) {
				$thing2[] = $t;
			}
		}

		$this->assertEquals(true, count($thing) === count($thing2));
	}

	/**
	 * ::random_string()
	 *
	 * @return void Nothing.
	 */
	function test_random_string() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = \blobfolio\common\data::random_string(10);
		}

		$this->assertEquals(true, count($thing) === 20);
		$this->assertEquals(true, count(array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if (strlen($t) === 10) {
				$thing2[] = $t;
			}
		}

		$this->assertEquals(true, count($thing) === count($thing2));
	}

	/**
	 * ::switcheroo()
	 *
	 * @return void Nothing.
	 */
	function test_switcheroo() {
		$var1 = 5;
		$var2 = 10;

		\blobfolio\common\data::switcheroo($var1, $var2);

		$this->assertEquals(5, $var2);
		$this->assertEquals(10, $var1);
	}
}


