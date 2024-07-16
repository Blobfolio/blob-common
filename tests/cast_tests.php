<?php
/**
 * Cast tests.
 *
 * PHPUnit tests for v_cast.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use blobfolio\common\cast as v_cast;
use blobfolio\common\ref\cast as r_cast;

/**
 * Test Suite
 */
class cast_tests extends TestCase {
	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_array')]
	/**
	 * ::array()
	 *
	 * @param mixed $value Value.
	 * @param array $expected Expected.
	 */
	public function test_array($value, $expected) {
		$this->assertEquals($expected, v_cast::to_array($value));
		$this->assertEquals('array', \gettype(v_cast::to_array($value)));
	}

	#[Test]
	#[DataProvider('data_array')]
	/**
	 * ::array() alias
	 *
	 * @param mixed $value Value.
	 * @param array $expected Expected.
	 */
	public function test_array_alias($value, $expected) {
		if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		// Stupid PHP 5.6 will blow up even though this code isn't
		// something we want to execute for them. Gotta hide it in
		// variables.
		$class = '\\blobfolio\\common\\cast::array';
		$this->assertEquals($expected, $class($value));

		$class = '\\blobfolio\\common\\ref\\cast::array';
		$class($value);
		$this->assertEquals($expected, $value);
	}

	#[Test]
	#[DataProvider('data_array_type')]
	/**
	 * ::array_type()
	 *
	 * @param mixed $value Value.
	 * @param string $expected Expected.
	 */
	public function test_array_type($value, $expected) {
		$this->assertEquals($expected, v_cast::array_type($value));
	}

	#[Test]
	#[DataProvider('data_bool')]
	/**
	 * ::bool()
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_bool($value, $flatten, $expected) {
		$this->assertSame($expected, v_cast::to_bool($value, $flatten));
	}

	#[Test]
	#[DataProvider('data_bool')]
	/**
	 * ::bool() alias
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_bool_alias($value, $flatten, $expected) {
		if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		$this->assertSame($expected, v_cast::bool($value, $flatten));
		$this->assertSame($expected, v_cast::boolean($value, $flatten));

		$value2 = $value;
		r_cast::bool($value2, $flatten);
		$this->assertSame($expected, $value2);

		$value2 = $value;
		r_cast::boolean($value2, $flatten);
		$this->assertSame($expected, $value2);
	}

	#[Test]
	#[DataProvider('data_float')]
	/**
	 * ::float()
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_float($value, $flatten, $expected) {
		$this->assertSame($expected, v_cast::to_float($value, $flatten));
	}

	#[Test]
	#[DataProvider('data_float')]
	/**
	 * ::float() alias
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_float_alias($value, $flatten, $expected) {
		if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		$this->assertSame($expected, v_cast::float($value, $flatten));
		$this->assertSame($expected, v_cast::double($value, $flatten));

		$value2 = $value;
		r_cast::float($value2, $flatten);
		$this->assertSame($expected, $value2);

		$value2 = $value;
		r_cast::double($value2, $flatten);
		$this->assertSame($expected, $value2);
	}

	#[Test]
	#[DataProvider('data_int')]
	/**
	 * ::int()
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_int($value, $flatten, $expected) {
		$this->assertSame($expected, v_cast::to_int($value, $flatten));
	}

	#[Test]
	#[DataProvider('data_int')]
	/**
	 * ::int() alias
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	function test_int_alias($value, $flatten, $expected) {
		if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		$this->assertSame($expected, v_cast::int($value, $flatten));
		$this->assertSame($expected, v_cast::integer($value, $flatten));

		$value2 = $value;
		r_cast::int($value2, $flatten);
		$this->assertSame($expected, $value2);

		$value2 = $value;
		r_cast::integer($value2, $flatten);
		$this->assertSame($expected, $value2);
	}

	#[Test]
	#[DataProvider('data_string')]
	/**
	 * ::string()
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_string($value, $flatten, $expected) {
		$this->assertSame($expected, v_cast::to_string($value, $flatten));
	}

	#[Test]
	#[DataProvider('data_string')]
	/**
	 * ::string() alias
	 *
	 * @param mixed $value Value.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	public function test_string_alias($value, $flatten, $expected) {
		if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
			$this->markTestSkipped('Aliases are only supported in PHP 7+.');
		}

		$this->assertSame($expected, v_cast::string($value, $flatten));

		r_cast::string($value, $flatten);
		$this->assertSame($expected, $value);
	}

	#[Test]
	#[DataProvider('data_to_type')]
	/**
	 * ::to_type()
	 *
	 * @param mixed $value Value.
	 * @param string $type Type.
	 * @param bool $flatten Flatten.
	 * @param string $expected Expected.
	 */
	function test_to_type($value, $type, $flatten, $expected) {
		$this->assertSame($expected, v_cast::to_type($value, $type, $flatten));
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::array()
	 *
	 * @return array Data.
	 */
	static function data_array() {
		return array(
			array(
				'string',
				array('string'),
			),
			array(
				5,
				array(5),
			),
			array(
				null,
				array(),
			),
			array(
				array('string'),
				array('string'),
			),
		);
	}

	/**
	 * Data for ::array_type()
	 *
	 * @return array Data.
	 */
	static function data_array_type() {
		return array(
			array(
				'string',
				false,
			),
			array(
				array(),
				false,
			),
			array(
				array(1, 2, 3),
				'sequential',
			),
			array(
				array(
					0=>1,
					1=>2,
					2=>3,
				),
				'sequential',
			),
			array(
				array(
					2=>3,
					0=>1,
				),
				'indexed',
			),
			array(
				array(
					0=>1,
					'bat'=>2,
				),
				'associative',
			),
			array(
				array(
					'foo'=>'bar',
				),
				'associative',
			),
		);
	}

	/**
	 * Data for ::bool()
	 *
	 * @return array Data.
	 */
	static function data_bool() {
		return array(
			array(
				'string',
				true,
				true,
			),
			array(
				'string',
				false,
				true,
			),
			array(
				'off',
				false,
				false,
			),
			array(
				'FALSE',
				false,
				false,
			),
			array(
				1,
				false,
				true,
			),
			array(
				array(1, 'Off', false),
				false,
				array(true, false, false),
			),
			array(
				array(1, 'Off', false),
				true,
				true,
			),
		);
	}

	/**
	 * Data for ::float()
	 *
	 * @return array Data.
	 */
	static function data_float() {
		return array(
			array(
				'string',
				true,
				0.0,
			),
			array(
				'$2.50',
				false,
				2.5,
			),
			array(
				1,
				false,
				1.0,
			),
			array(
				'50%',
				false,
				.5,
			),
			array(
				'67¢',
				false,
				.67,
			),
			array(
				array(1, '2.5', false),
				false,
				array(1.0, 2.5, 0.0),
			),
			array(
				array(1, '2.5', false),
				true,
				0.0,
			),
			array(
				array(500),
				true,
				500.0,
			),
			array(
				array(500),
				false,
				array(500.0),
			),
		);
	}

	/**
	 * Data for ::int()
	 *
	 * @return array Data.
	 */
	static function data_int() {
		return array(
			array(
				'string',
				true,
				0,
			),
			array(
				'$2.50',
				false,
				2,
			),
			array(
				'on',
				false,
				1,
			),
			array(
				'50%',
				false,
				0,
			),
			array(
				array(1, '2.5', false),
				false,
				array(1, 2, 0),
			),
			array(
				array(1, '2.5', false),
				true,
				0,
			),
			array(
				array(500),
				true,
				500,
			),
			array(
				array(500),
				false,
				array(500),
			),
		);
	}

	/**
	 * Data for ::string()
	 *
	 * @return array Data.
	 */
	static function data_string() {
		return array(
			array(
				"Hello\nWorld",
				true,
				"Hello\nWorld",
			),
			array(
				2,
				false,
				'2',
			),
			array(
				null,
				false,
				'',
			),
			array(
				array(1, '2.5', false),
				false,
				array('1', '2.5', ''),
			),
			array(
				array(1, '2.5', false),
				true,
				'',
			),
			array(
				array('Hi Judy'),
				true,
				'Hi Judy',
			),
			array(
				array(500),
				false,
				array('500'),
			),
		);
	}

	/**
	 * Data for ::to_type()
	 *
	 * @return array Data.
	 */
	static function data_to_type() {
		return array(
			array(
				null,
				'array',
				false,
				array(),
			),
			array(
				array('off'),
				'bool',
				false,
				array(false),
			),
			array(
				'off',
				'boolean',
				false,
				false,
			),
			array(
				2,
				'double',
				false,
				2.0,
			),
			array(
				array(2),
				'float',
				false,
				array(2.0),
			),
			array(
				'500',
				'int',
				false,
				500,
			),
			array(
				array('2', 3),
				'integer',
				false,
				array(2, 3),
			),
			array(
				2.3,
				'string',
				false,
				'2.3',
			),
		);
	}

	// ----------------------------------------------------------------- end data

}


