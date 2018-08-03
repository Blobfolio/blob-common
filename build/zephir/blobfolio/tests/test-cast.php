<?php
/**
 * Blobfolio\Cast
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class cast_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: toArray
	 *
	 * @dataProvider data_toArray
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_toArray($value, $expected) {
		$result = \Blobfolio\Cast::toArray($value);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: toBool
	 *
	 * @dataProvider data_toBool
	 *
	 * @param mixed $value Value.
	 * @param mixed $flatten Flatten.
	 * @param mixed $expected Expected.
	 */
	function test_toBool($value, $flatten, $expected) {
		$result = \Blobfolio\Cast::toBool($value, $flatten);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toFloat
	 *
	 * @dataProvider data_toFloat
	 *
	 * @param mixed $value Value.
	 * @param mixed $flatten Flatten.
	 * @param mixed $expected Expected.
	 */
	function test_toFloat($value, $flatten, $expected) {
		$result = \Blobfolio\Cast::toFloat($value, $flatten);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toInt
	 *
	 * @dataProvider data_toInt
	 *
	 * @param mixed $value Value.
	 * @param mixed $flatten Flatten.
	 * @param mixed $expected Expected.
	 */
	function test_toInt($value, $flatten, $expected) {
		$result = \Blobfolio\Cast::toInt($value, $flatten);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toString
	 *
	 * @dataProvider data_toString
	 *
	 * @param mixed $value Value.
	 * @param mixed $flatten Flatten.
	 * @param mixed $expected Expected.
	 */
	function test_toString($value, $flatten, $expected) {
		$result = \Blobfolio\Cast::toString($value, $flatten);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toType
	 *
	 * @dataProvider data_toType
	 *
	 * @param mixed $value Value.
	 * @param string $type Type.
	 * @param mixed $flatten Flatten.
	 * @param mixed $expected Expected.
	 */
	function test_toType($value, string $type, $flatten, $expected) {
		$result = \Blobfolio\Cast::toType($value, $type, $flatten);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: parseArgs
	 *
	 * @dataProvider data_parseArgs
	 *
	 * @param mixed $value Value.
	 * @param mixed $default Default.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_parseArgs($value, $default, int $flags, $expected) {
		$result = \Blobfolio\Cast::parseArgs($value, $default, $flags);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: toArray
	 *
	 * @return array Values.
	 */
	function data_toArray() {
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
	 * Data: toBool
	 *
	 * @return array Values.
	 */
	function data_toBool() {
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
	 * Data: toFloat
	 *
	 * @return array Values.
	 */
	function data_toFloat() {
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
	 * Data: toInt
	 *
	 * @return array Values.
	 */
	function data_toInt() {
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
	 * Data: toString
	 *
	 * @return array Values.
	 */
	function data_toString() {
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
	 * Data: toType
	 *
	 * @return array Values.
	 */
	function data_toType() {
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

	/**
	 * Data: parseArgs
	 *
	 * @return array Data.
	 */
	function data_parseArgs() {
		return array(
			array(
				'',
				array(''),
				0,
				array(''),
			),
			array(
				null,
				array('dog'=>'wolf'),
				0,
				array('dog'=>'wolf'),
			),
			array(
				array('animal'=>'dog'),
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				0,
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
				0,
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
				\Blobfolio\Cast::PARSE_STRICT,
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
				\Blobfolio\Cast::PARSE_STRICT | \Blobfolio\Cast::PARSE_RECURSIVE,
				array(
					'price'=>array(
						'animal'=>.67,
						'fruit'=>15.0,
					),
				),
			),
		);
	}
}
