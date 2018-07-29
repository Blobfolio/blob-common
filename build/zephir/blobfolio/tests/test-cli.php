<?php
/**
 * Blobfolio\Cli
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class cli_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: colorize
	 *
	 * @dataProvider data_colorize
	 */
	function test_colorize() {
		$args = func_get_args();
		$expected = array_pop($args);

		$result = call_user_func_array(
			array('\\Blobfolio\\Cli', 'colorize'),
			$args
		);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: isCli
	 */
	function test_isCli() {
		// This should always be true when running phpunit. Haha.
		$this->assertTrue(\Blobfolio\Cli::isCli());
	}

	/**
	 * Test: isRoot
	 */
	function test_isRoot() {
		if (!function_exists('posix_getuid')) {
			$this->markTestSkipped('POSIX functions are missing.');
		}

		$root = (0 === posix_getuid());
		$this->assertSame($root, \Blobfolio\Cli::isRoot());
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: colorize
	 *
	 * @return array Values.
	 */
	function data_colorize() {
		// The last value in each set is the $expected.
		return array(
			array(
				array('Error:', 31, 1),
				' Just kidding.',
				"\033[31;1mError:\033[0m Just kidding.",
			),
			array(
				array('Error:', array(31, 1)),
				' Just kidding.',
				"\033[31;1mError:\033[0m Just kidding.",
			),
			array(
				array('¡', 33, 1),
				'Hello yellow',
				array('!', 33, 1),
				"\033[33;1m¡\033[0mHello yellow\033[33;1m!\033[0m",
			),
		);
	}
}
