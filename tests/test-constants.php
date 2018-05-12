<?php
/**
 * Constants tests.
 *
 * These are looking more for form than function.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\constants;

/**
 * Test Suite
 */
class constants_tests extends \PHPUnit\Framework\TestCase {



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * ::COUNTRIES
	 *
	 * @dataProvider data_countries
	 *
	 * @param string $code Code.
	 * @param array $data Data.
	 */
	function test_countries(string $code, $data) {
		// The country code should be 2 uppercase characters.
		$this->assertTrue(!!preg_match('/^[A-Z]{2}$/', $code));

		// There should be a name.
		$this->assertTrue(isset($data['name']));
		$this->assertTrue(!!$data['name']);

		// There should be a region.
		$this->assertTrue(isset($data['region']));
		$this->assertTrue(in_array($data['region'], constants::REGIONS, true));

		// There should be a currency.
		$this->assertTrue(isset($data['currency']));
		$this->assertTrue(!!preg_match('/^[A-Z]{3}$/', $data['currency']));
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::COUNTRIES
	 *
	 * @return array Data.
	 */
	function data_countries() {
		$out = array();
		foreach (constants::COUNTRIES as $k=>$v) {
			$out[] = array($k, $v);
		}
		return $out;
	}

	// ----------------------------------------------------------------- end data
}


