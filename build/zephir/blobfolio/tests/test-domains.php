<?php
/**
 * Blobfolio\Domains
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class domains_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: parseUrl
	 *
	 * @dataProvider data_parseUrl
	 *
	 * @param mixed $value Value.
	 * @param mixed $args Args.
	 * @param mixed $expected Expected.
	 */
	function test_parseUrl($value, $args, $expected) {
		$result = \Blobfolio\Domains::parseUrl($value, $args);

		$this->assertSame($expected, $result);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: parseUrl
	 *
	 * @return array Values.
	 */
	function data_parseUrl() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'http://☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'//☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'//google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'http://google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]',
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]',
			),
			array(
				'https://foo.bar/apples',
				-1,
				array(
					'scheme'=>'https',
					'host'=>'foo.bar',
					'path'=>'/apples',
				),
			),
		);
	}
}
