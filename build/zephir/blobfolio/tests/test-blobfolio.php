<?php
/**
 * Blobfolio\Blobfolio
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class blobfolio_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: getDataDir
	 *
	 * @dataProvider data_getDataDir
	 *
	 * @param string $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_getDataDir($value, $expected) {
		$result = \Blobfolio\Blobfolio::getDataDir($value);

		if (!$value) {
			$this->assertSame($expected, $result);
		}
		else {
			$this->assertSame($expected, strlen($result) > 0);
		}
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: getDataDir
	 *
	 * @return array Values.
	 */
	function data_getDataDir() {
		return array(
			array(
				'blob-domains.json',
				true,
			),
			array(
				'blob-mimes.json',
				true,
			),
			array(
				'blob-phone.json',
				true,
			),
			array(
				'geo.json',
				true,
			),
			array(
				'fake.json',
				false,
			),
		);
	}
}
