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
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_toBool($value, $flags, $expected) {
		$result = \Blobfolio\Cast::toBool($value, $flags);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toFloat
	 *
	 * @dataProvider data_toFloat
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_toFloat($value, $flags, $expected) {
		$result = \Blobfolio\Cast::toFloat($value, $flags);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toInt
	 *
	 * @dataProvider data_toInt
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_toInt($value, $flags, $expected) {
		$result = \Blobfolio\Cast::toInt($value, $flags);
		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: toString
	 *
	 * @dataProvider data_toString
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_toString($value, $flags, $expected) {
		$result = \Blobfolio\Cast::toString($value, $flags);
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
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_toType($value, string $type, $flags, $expected) {
		$result = \Blobfolio\Cast::toType($value, $type, $flags);
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
				\Blobfolio\Blobfolio::FLATTEN,
				true,
			),
			array(
				'string',
				0,
				true,
			),
			array(
				'off',
				0,
				false,
			),
			array(
				'FALSE',
				0,
				false,
			),
			array(
				1,
				0,
				true,
			),
			array(
				array(1, 'Off', false),
				0,
				array(true, false, false),
			),
			array(
				array(1, 'Off', false),
				\Blobfolio\Blobfolio::FLATTEN,
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
				\Blobfolio\Blobfolio::FLATTEN,
				0.0,
			),
			array(
				'$2.50',
				0,
				2.5,
			),
			array(
				1,
				0,
				1.0,
			),
			array(
				'50%',
				0,
				.5,
			),
			array(
				'67¢',
				0,
				.67,
			),
			array(
				array(1, '2.5', false),
				0,
				array(1.0, 2.5, 0.0),
			),
			array(
				array(1, '2.5', false),
				\Blobfolio\Blobfolio::FLATTEN,
				0.0,
			),
			array(
				array(500),
				\Blobfolio\Blobfolio::FLATTEN,
				500.0,
			),
			array(
				array(500),
				0,
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
				\Blobfolio\Blobfolio::FLATTEN,
				0,
			),
			array(
				'$2.50',
				0,
				2,
			),
			array(
				'on',
				0,
				1,
			),
			array(
				'50%',
				0,
				0,
			),
			array(
				array(1, '2.5', false),
				0,
				array(1, 2, 0),
			),
			array(
				array(1, '2.5', false),
				\Blobfolio\Blobfolio::FLATTEN,
				0,
			),
			array(
				array(500),
				\Blobfolio\Blobfolio::FLATTEN,
				500,
			),
			array(
				array(500),
				0,
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
				\Blobfolio\Blobfolio::FLATTEN,
				"Hello\nWorld",
			),
			array(
				2,
				0,
				'2',
			),
			array(
				null,
				0,
				'',
			),
			array(
				array(1, '2.5', false),
				0,
				array('1', '2.5', ''),
			),
			array(
				array(1, '2.5', false),
				\Blobfolio\Blobfolio::FLATTEN,
				'',
			),
			array(
				array('Hi Judy'),
				\Blobfolio\Blobfolio::FLATTEN,
				'Hi Judy',
			),
			array(
				array(500),
				0,
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
				0,
				array(),
			),
			array(
				array('off'),
				'bool',
				0,
				array(false),
			),
			array(
				'off',
				'boolean',
				0,
				false,
			),
			array(
				2,
				'double',
				0,
				2.0,
			),
			array(
				array(2),
				'float',
				0,
				array(2.0),
			),
			array(
				'500',
				'int',
				0,
				500,
			),
			array(
				array('2', 3),
				'integer',
				0,
				array(2, 3),
			),
			array(
				2.3,
				'string',
				0,
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
				\Blobfolio\Blobfolio::PARSE_STRICT,
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
				\Blobfolio\Blobfolio::PARSE_STRICT | \Blobfolio\Blobfolio::PARSE_RECURSIVE,
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
