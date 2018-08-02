<?php
/**
 * Blobfolio\Numbers
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class numbers_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: ceil
	 *
	 * @dataProvider data_ceil
	 *
	 * @param mixed $value Value.
	 * @param int $precision Precision.
	 * @param mixed $expected.
	 */
	function test_ceil($value, int $precision, float $expected) {
		$result = \Blobfolio\Numbers::ceil($value, $precision);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: floor
	 *
	 * @dataProvider data_floor
	 *
	 * @param mixed $value Value.
	 * @param int $precision Precision.
	 * @param mixed $expected.
	 */
	function test_floor($value, int $precision, float $expected) {
		$result = \Blobfolio\Numbers::floor($value, $precision);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: fraction
	 *
	 * @dataProvider data_fraction
	 *
	 * @param mixed $value Value.
	 * @param float $precision Precision.
	 * @param mixed $expected.
	 */
	function test_fraction($value, float $precision, string $expected) {
		$result = \Blobfolio\Numbers::fraction($value, $precision);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: inRange
	 *
	 * @dataProvider data_inRange
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param mixed $expected.
	 */
	function test_inRange($value, $min, $max, bool $expected) {
		$result = \Blobfolio\Numbers::inRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toRange
	 *
	 * @dataProvider data_toRange
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param mixed $expected.
	 */
	function test_toRange($value, $min, $max, $expected) {
		$result = \Blobfolio\Numbers::toRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: ceil
	 *
	 * @return array Values.
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
				-1.234,
				2,
				-1.23,
			),
			array(
				'4.567',
				2,
				4.57,
			),
		);
	}

	/**
	 * Data: floor
	 *
	 * @return array Values.
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
				-1.234,
				2,
				-1.24,
			),
			array(
				'4.567',
				2,
				4.56,
			),
		);
	}

	/**
	 * Data: fraction
	 *
	 * @return array Values.
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
				0.66,
				0.1,
				'2/3',
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
	 * Data: inRange
	 *
	 * @return array Values.
	 */
	function data_inRange() {
		return array(
			array(
				5,
				2,
				null,
				true,
			),
			array(
				5,
				6,
				2,
				true,
			),
			array(
				-5,
				2,
				null,
				false,
			),
			array(
				100,
				null,
				99,
				false,
			),
			array(
				100,
				null,
				101,
				true,
			),
		);
	}

	/**
	 * Data: toRange
	 *
	 * @return array Values.
	 */
	function data_toRange() {
		return array(
			array(
				5,
				2,
				null,
				5,
			),
			array(
				5,
				6,
				2,
				5,
			),
			array(
				-5,
				2,
				null,
				2,
			),
			array(
				100,
				null,
				99.9,
				99.9,
			),
			array(
				100,
				null,
				101.99,
				100.00,
			),
		);
	}
}
