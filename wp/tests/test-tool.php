<?php
/**
 * Class ToolTests
 *
 * @package blob-common
 */

/**
 * Test functions-tool.php.
 */
class ToolTests extends WP_UnitTestCase {

	/**
	 * Array Type
	 *
	 * @return void Nothing.
	 */
	function test_common_array_type() {

	}

	/**
	 * Array Compare
	 *
	 * @return void Nothing.
	 */
	function test_common_array_compare() {

	}

	/**
	 * Case-Insensitive in_array()
	 *
	 * @return void Nothing.
	 */
	function test_common_iin_array() {
		$thing = array('Apples', 'Bananas');

		$this->assertEquals(false, \in_array('apples', $thing, true));
		$this->assertEquals(true, \common_iin_array('apples', $thing));
	}

	/**
	 * Case-Insensitive array_key_exists()
	 *
	 * @return void Nothing.
	 */
	function test_common_iarray_key_exists() {
		$thing = array('Apples'=>'Green', 'Bananas'=>'Yellow');

		$this->assertEquals(false, \array_key_exists('apples', $thing));
		$this->assertEquals(true, \common_iarray_key_exists('apples', $thing));
	}

	/**
	 * Case-Insensitive substr_count()
	 *
	 * @return void Nothing.
	 */
	function test_common_isubstr_count() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals(0, \common_substr_count($thing, 'björk'));
		$this->assertEquals(1, \common_isubstr_count($thing, 'björk'));
	}

	/**
	 * Strlen
	 *
	 * @return void Nothing.
	 */
	function test_common_strlen() {
		$thing = 'BjöRk';
		$this->assertEquals(5, \common_strlen($thing));
	}

	/**
	 * Strpos
	 *
	 * @return void Nothing.
	 */
	function test_common_strpos() {
		$thing = 'AöA';

		$this->assertEquals(false, false !== \common_strpos($thing, 'E'));
		$this->assertEquals(0, \common_strpos($thing, 'A'));
		$this->assertEquals(1, \common_strpos($thing, 'ö'));
	}

	/**
	 * Substr
	 *
	 * @return void Nothing.
	 */
	function test_common_substr() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('quEen BjöRk', \common_substr($thing, 0, 11));
		$this->assertEquals('BjöRk Ⅷ loVes 3 aPplEs.', \common_substr($thing, 6));
		$this->assertEquals('aPplEs.', \common_substr($thing, -7));
	}

	/**
	 * Substr Count
	 *
	 * @return void Nothing.
	 */
	function test_common_substr_count() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals(1, \common_substr_count($thing, 'BjöRk'));
		$this->assertEquals(0, \common_substr_count($thing, 'Nick'));
	}

	/**
	 * To Char Array
	 *
	 * @return void Nothing.
	 */
	function test_common_to_char_array() {
		$thing = 'BjöRk';
		$this->assertEquals(array('B', 'j', 'ö', 'R', 'k'), \common_to_char_array($thing));
	}

	/**
	 * Recursive Array Map
	 *
	 * @return void Nothing.
	 */
	function test_common_array_map_recursive() {
		$thing = array(1, 2, 3, 4, 5);
		$this->assertEquals(array('1', '2', '3', '4', '5'), \common_array_map_recursive('strval', $thing));

		$thing = array(1, array(1));
		$this->assertEquals(array('1', array('1')), \common_array_map_recursive('strval', $thing));
	}

	/**
	 * Random Int
	 *
	 * @return void Nothing.
	 */
	function test_common_random_int() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = \common_random_int(0, 10);
		}

		$this->assertEquals(true, \count($thing) === 20);
		$this->assertEquals(true, \count(\array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if ($t >= 0 && $t <= 10) {
				$thing2[] = $t;
			}
		}

		$this->assertEquals(true, \count($thing) === \count($thing2));
	}

	/**
	 * Array Pop
	 *
	 * @return void Nothing.
	 */
	function test_common_array_pop() {
		$thing = array(1, 2, 3, 4, 5);
		$this->assertEquals(5, \common_array_pop($thing));

		$thing = array();
		$this->assertEquals(false, \common_array_pop($thing));
	}

	/**
	 * Array Pop Top
	 *
	 * @return void Nothing.
	 */
	function test_common_array_pop_top() {
		$thing = array(1, 2, 3, 4, 5);
		$this->assertEquals(1, \common_array_pop_top($thing));

		$thing = array();
		$this->assertEquals(false, \common_array_pop_top($thing));
	}

	/**
	 * Switcheroo
	 *
	 * @return void Nothing.
	 */
	function test_common_switcheroo() {
		$var1 = 5;
		$var2 = 10;

		\common_switcheroo($var1, $var2);

		$this->assertEquals(5, $var2);
		$this->assertEquals(10, $var1);
	}

	/**
	 * Parse Args
	 *
	 * @return void Nothing.
	 */
	function test_common_parse_args() {
		$thing = null;
		$default = array('fruit'=>'pear', 'animal'=>array('name'=>'Oscar', 'price'=>5.5));

		$this->assertEquals($default, \common_parse_args($thing, $default));

		$thing = array('weapon'=>'spear');
		$this->assertEquals($default, \common_parse_args($thing, $default));

		$thing = array('fruit'=>12);
		$this->assertEquals(array('fruit'=>12, 'animal'=>array('name'=>'Oscar', 'price'=>5.5)), \common_parse_args($thing, $default));

		$this->assertEquals(array('fruit'=>'12', 'animal'=>array('name'=>'Oscar', 'price'=>5.5)), \common_parse_args($thing, $default, true));

		$thing = array('animal'=>array('price'=>'1'));
		$this->assertEquals(array('fruit'=>'pear', 'animal'=>array('price'=>'1')), \common_parse_args($thing, $default, false));

		$this->assertEquals(array('fruit'=>'pear', 'animal'=>array('price'=>1)), \common_parse_args($thing, $default, true, false));
	}

	/**
	 * Parse Args JSON
	 *
	 * @return void Nothing.
	 */
	function test_common_parse_json_args() {
		$thing = '';
		$this->assertEquals(array(), \common_parse_json_args($thing));

		$thing = '{"animal":"dog"}';
		$this->assertEquals(array('animal'=>'dog'), \common_parse_json_args($thing));

		$default = array('animal'=>'dog', 'fruit'=>'banana');
		$this->assertEquals($default, \common_parse_json_args($thing, $default));

		$thing = '{"animal":12}';
		$this->assertEquals(array('animal'=>12, 'fruit'=>'banana'), \common_parse_json_args($thing, $default, false));

		$this->assertEquals(array('animal'=>'12', 'fruit'=>'banana'), \common_parse_json_args($thing, $default));
	}

	/**
	 * Generate Random String
	 *
	 * @return void Nothing.
	 */
	function test_common_generate_random_string() {
		$thing = array();
		for ($x = 0; $x < 20; $x++) {
			$thing[] = \common_generate_random_string(10);
		}

		$this->assertEquals(true, \count($thing) === 20);
		$this->assertEquals(true, \count(\array_unique($thing)) > 1);

		$thing2 = array();
		foreach ($thing as $t) {
			if (\strlen($t) === 10) {
				$thing2[] = $t;
			}
		}

		$this->assertEquals(true, \count($thing) === \count($thing2));
	}

	/**
	 * CC Exp Months
	 *
	 * @return void Nothing.
	 */
	function test_common_get_cc_exp_months() {
		$thing = \common_get_cc_exp_months();
		$values = \array_values($thing);
		$keys = \array_keys($thing);

		$this->assertEquals('01 - Jan', $values[0]);
		$this->assertEquals(1, $keys[0]);

		$thing = \common_get_cc_exp_months('F');
		$this->assertEquals('January', $thing[1]);
	}

	/**
	 * CC Exp Years
	 *
	 * @return void Nothing.
	 */
	function test_common_get_cc_exp_years() {
		$thing = \common_get_cc_exp_years();
		$year = (int) \date('Y');

		$this->assertEquals(10, \count($thing));
		$this->assertEquals(true, \in_array($year, $thing, true));

		$thing = \common_get_cc_exp_years(3);
		$this->assertEquals(3, \count($thing));
	}
}
