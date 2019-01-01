<?php
/**
 * Blobfolio\Phones
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class phones_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: nicePhone
	 *
	 * @dataProvider data_nicePhone
	 *
	 * @param mixed $value Value.
	 * @param string $country Country.
	 * @param array $types Types.
	 * @param mixed $expected.
	 */
	function test_nicePhone($value, string $country, array $types, string $expected) {
		$result = \Blobfolio\Phones::nicePhone($value, $country, $types);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: parsePhone
	 *
	 * @dataProvider data_parsePhone
	 *
	 * @param mixed $value Value.
	 * @param string $country Country.
	 * @param mixed $expected.
	 */
	function test_parsePhone($value, string $country, $expected) {
		$result = \Blobfolio\Phones::parsePhone($value, $country);

		$this->assertSame($expected, $result);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: nicePhone
	 *
	 * @return array Values.
	 */
	function data_nicePhone() {
		return array(
			array(
				2015550123,
				"",
				array(),
				'+1 201-555-0123',
			),
			array(
				'(201) 555-0123',
				'US',
				array(),
				'+1 201-555-0123',
			),
			array(
				'(775) 990-3138',
				'US',
				array(),
				'+1 775-990-3138',
			),
			array(
				'+1 775-990-3138',
				'US',
				array(),
				'+1 775-990-3138',
			),
			array(
				'(201) 555-0123',
				'US',
				array('voip'),
				'',
			),
			array(
				'2015550123',
				'CA',
				array('fixed'),
				'+1 201-555-0123',
			),
			array(
				1012345678,
				'CN',
				array(),
				'+86 101 234 5678',
			),
			array(
				'2042345678',
				'US',
				array(),
				'+1 204-234-5678',
			),
		);
	}

	/**
	 * Data: parsePhone
	 *
	 * @return array Values.
	 */
	function data_parsePhone() {
		return array(
			// Valid Chinese number w/ Country.
			array(
				1012345678,
				'CN',
				array(
					'country'=>'CN',
					'prefix'=>86,
					'region'=>'Asia',
					'types'=>array('fixed'),
					'number'=>'+86 101 234 5678',
				),
			),
			// Canadian number w/ wrong country.
			array(
				2042345678,
				'US',
				array(
					'country'=>'CA',
					'prefix'=>1,
					'region'=>'North America',
					'types'=>array(
						'fixed',
						'mobile',
					),
					'number'=>'+1 204-234-5678',
				),
			),
			// Uruguay.
			array(
				94231234,
				'UY',
				array(
					'country'=>'UY',
					'prefix'=>598,
					'region'=>'South America',
					'types'=>array('mobile'),
					'number'=>'+598 9423 1234',
				),
			),
			// US w/o country.
			array(
				2015550123,
				"",
				array(
					'country'=>'US',
					'prefix'=>1,
					'region'=>'North America',
					'types'=>array(
						'fixed',
						'mobile',
					),
					'number'=>'+1 201-555-0123',
				),
			),
			// US w/ country.
			array(
				2015550123,
				'US',
				array(
					'country'=>'US',
					'prefix'=>1,
					'region'=>'North America',
					'types'=>array(
						'fixed',
						'mobile',
					),
					'number'=>'+1 201-555-0123',
				),
			),
			// Same as above but pre-formatted.
			array(
				'(201) 555.0123',
				'US',
				array(
					'country'=>'US',
					'prefix'=>1,
					'region'=>'North America',
					'types'=>array(
						'fixed',
						'mobile',
					),
					'number'=>'+1 201-555-0123',
				),
			),
			// An ambiguous number.
			array(
				'+1 201 555 0123',
				'US',
				array(
					'country'=>'US',
					'prefix'=>1,
					'region'=>'North America',
					'types'=>array(
						'fixed',
						'mobile',
					),
					'number'=>'+1 201-555-0123',
				),
			),
			// Same as above but with a different country passed.
			array(
				'+1 201 555 0123',
				'AT',
				array(
					'country'=>'AT',
					'prefix'=>43,
					'region'=>'Europe',
					'types'=>array('fixed'),
					'number'=>'+43 1 2015550123',
				),
			),
		);
	}
}
