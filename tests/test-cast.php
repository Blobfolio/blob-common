<?php
/**
 * Cast tests.
 *
 * PHPUnit tests for \blobfolio\common\cast.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class cast_tests extends \PHPUnit\Framework\TestCase {

	/**
	 * ::array()
	 *
	 * @return void Nothing.
	 */
	function test_array() {
		$thing = 'string';
		$this->assertEquals(array('string'), \blobfolio\common\cast::to_array($thing));

		$this->assertEquals('array', gettype(\blobfolio\common\cast::to_array($thing)));

		$thing = array('string');
		$this->assertEquals(array('string'), \blobfolio\common\cast::to_array($thing));

		$thing = null;
		$this->assertEquals(array(), \blobfolio\common\cast::to_array($thing));
	}

	/**
	 * ::array_type()
	 *
	 * @return void Nothing.
	 */
	function test_array_type() {
		$thing = 'string';
		$this->assertEquals(false, \blobfolio\common\cast::array_type($thing));

		$thing = array();
		$this->assertEquals(false, \blobfolio\common\cast::array_type($thing));

		$thing = array('string','thing');
		$this->assertEquals('sequential', \blobfolio\common\cast::array_type($thing));

		$thing = array('foo'=>'bar');
		$this->assertEquals('associative', \blobfolio\common\cast::array_type($thing));

		$thing = array('apples','bananas','pears');
		unset($thing[1]);
		$this->assertEquals('indexed', \blobfolio\common\cast::array_type($thing));
	}

	/**
	 * ::bool()
	 *
	 * @return void Nothing.
	 */
	function test_bool() {
		$thing = 'string';
		$this->assertEquals(true, \blobfolio\common\cast::to_bool($thing));

		$this->assertEquals('boolean', gettype(\blobfolio\common\cast::to_bool($thing)));

		$thing = 'off';
		$this->assertEquals(false, \blobfolio\common\cast::to_bool($thing));

		$thing = 'FALSE';
		$this->assertEquals(false, \blobfolio\common\cast::to_bool($thing));

		$thing = 1;
		$this->assertEquals(true, \blobfolio\common\cast::to_bool($thing));

		$thing = array(1, 'Off', false);
		$this->assertEquals(array(true, false, false), \blobfolio\common\cast::to_bool($thing));

		$this->assertEquals(true, \blobfolio\common\cast::to_bool($thing, true));
	}

	/**
	 * ::float()
	 *
	 * @return void Nothing.
	 */
	function test_float() {
		$thing = 'string';
		$this->assertEquals(0.0, \blobfolio\common\cast::to_float($thing));

		$this->assertEquals('double', gettype(\blobfolio\common\cast::to_float($thing)));

		$thing = '$2.50';
		$this->assertEquals(2.5, \blobfolio\common\cast::to_float($thing));

		$thing = 1;
		$this->assertEquals(1.0, \blobfolio\common\cast::to_float($thing));

		$thing = '50%';
		$this->assertEquals(.5, \blobfolio\common\cast::to_float($thing));

		$thing = '67¢';
		$this->assertEquals(.67, \blobfolio\common\cast::to_float($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1.0, 2.5, 0.0), \blobfolio\common\cast::to_float($thing));

		$this->assertEquals(0, \blobfolio\common\cast::to_float($thing, true));
	}

	/**
	 * ::int()
	 *
	 * @return void Nothing.
	 */
	function test_int() {
		$thing = 'string';
		$this->assertEquals(0, \blobfolio\common\cast::to_int($thing));

		$this->assertEquals('integer', gettype(\blobfolio\common\cast::to_int($thing)));

		$thing = 2.5;
		$this->assertEquals(2, \blobfolio\common\cast::to_int($thing));

		$thing = '33';
		$this->assertEquals(33, \blobfolio\common\cast::to_int($thing));

		$thing = 'on';
		$this->assertEquals(1, \blobfolio\common\cast::to_int($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1, 2, 0), \blobfolio\common\cast::to_int($thing));

		$this->assertEquals(0, \blobfolio\common\cast::to_int($thing, true));
	}

	/**
	 * ::string()
	 *
	 * @return void Nothing.
	 */
	function test_string() {
		$thing = 'string';
		$this->assertEquals('string', \blobfolio\common\cast::to_string($thing));

		$this->assertEquals('string', gettype(\blobfolio\common\cast::to_string($thing)));

		$thing = 2.5;
		$this->assertEquals('2.5', \blobfolio\common\cast::to_string($thing));

		$thing = false;
		$this->assertEquals('', \blobfolio\common\cast::to_string($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array('1', '2.5', ''), \blobfolio\common\cast::to_string($thing));

		$this->assertEquals('', \blobfolio\common\cast::to_string($thing, true));
	}

	/**
	 * Aliases
	 *
	 * Make sure our various alias cast functions work.
	 *
	 * @return void Nothing.
	 */
	function test_aliases() {
		if (version_compare(PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		// Stupid PHP 5.6 will blow up even though this code isn't
		// something we want to execute for them. Gotta hide it in
		// variables.
		$thing = 'string';
		$class = '\blobfolio\common\cast::array';
		$this->assertEquals(array('string'), $class($thing));
		$class = '\blobfolio\common\ref\cast::array';
		$class($thing);
		$this->assertEquals(array('string'), $thing);

		$thing = 'true';
		$this->assertEquals(true, \blobfolio\common\cast::bool($thing));
		\blobfolio\common\ref\cast::bool($thing);
		$this->assertEquals(true, $thing);

		$thing = 'true';
		$this->assertEquals(true, \blobfolio\common\cast::boolean($thing));
		\blobfolio\common\ref\cast::boolean($thing);
		$this->assertEquals(true, $thing);

		$thing = '1';
		$this->assertEquals(1.0, \blobfolio\common\cast::double($thing));
		\blobfolio\common\ref\cast::double($thing);
		$this->assertEquals(1.0, $thing);

		$thing = '1';
		$this->assertEquals(1.0, \blobfolio\common\cast::float($thing));
		\blobfolio\common\ref\cast::float($thing);
		$this->assertEquals(1.0, $thing);

		$thing = '1';
		$this->assertEquals(1, \blobfolio\common\cast::int($thing));
		\blobfolio\common\ref\cast::int($thing);
		$this->assertEquals(1, $thing);

		$thing = '1';
		$this->assertEquals(1, \blobfolio\common\cast::integer($thing));
		\blobfolio\common\ref\cast::integer($thing);
		$this->assertEquals(1, $thing);

		$thing = '1';
		$this->assertEquals(1.0, \blobfolio\common\cast::number($thing));
		\blobfolio\common\ref\cast::number($thing);
		$this->assertEquals(1.0, $thing);

		$thing = 2.5;
		$this->assertEquals('2.5', \blobfolio\common\cast::string($thing));
		\blobfolio\common\ref\cast::string($thing);
		$this->assertEquals('2.5', $thing);
	}

	/**
	 * ::to_type()
	 *
	 * @return void Nothing.
	 */
	function test_to_type() {
		$thing = array('false', 2.5, true, 1);

		$this->assertEquals(array('false', 2.5, true, 1), \blobfolio\common\cast::to_type($thing, 'array'));

		$this->assertEquals(array(false, true, true, true), \blobfolio\common\cast::to_type($thing, 'bool'));
		$this->assertEquals(array(false, true, true, true), \blobfolio\common\cast::to_type($thing, 'boolean'));

		$this->assertEquals(array(0, 2.5, 1.0, 1.0), \blobfolio\common\cast::to_type($thing, 'float'));
		$this->assertEquals(array(0, 2.5, 1.0, 1.0), \blobfolio\common\cast::to_type($thing, 'double'));

		$this->assertEquals(array(0, 2, 1, 1), \blobfolio\common\cast::to_type($thing, 'integer'));
		$this->assertEquals(array(0, 2, 1, 1), \blobfolio\common\cast::to_type($thing, 'int'));

		$this->assertEquals(array('false', '2.5', '1', '1'), \blobfolio\common\cast::to_type($thing, 'string'));

		$this->assertEquals(true, \blobfolio\common\cast::to_type($thing, 'bool', true));
		$this->assertEquals(0.0, \blobfolio\common\cast::to_type($thing, 'float', true));
		$this->assertEquals(0, \blobfolio\common\cast::to_type($thing, 'integer', true));
		$this->assertEquals('', \blobfolio\common\cast::to_type($thing, 'string', true));
	}
}


