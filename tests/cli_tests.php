<?php
/**
 * CLI tests.
 *
 * PHPUnit tests for cli.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use blobfolio\common\cli;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test Suite
 */
class cli_tests extends TestCase {
	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_colorize')]
	/**
	 * ::colorize()
	 *
	 * @param array $value Value.
	 * @param array $expected Expected.
	 */
	public function test_colorize($value, $expected) {
		$result = \call_user_func_array(
			array('\\blobfolio\\common\\cli', 'colorize'),
			$value
		);
		$this->assertEquals($expected, $result);
	}

	#[Test]
	/**
	 * ::is_cli()
	 */
	public function test_is_cli() {
		$this->assertTrue(cli::is_cli());
	}

	#[Test]
	/**
	 * ::is_root()
	 */
	public function test_is_root() {
		if (! \function_exists('posix_getuid')) {
			$this->markTestSkipped('POSIX functions are missing.');
		}

		$root = (0 === \posix_getuid());
		$this->assertSame($root, cli::is_root());
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::colorize()
	 *
	 * @return array Data.
	 */
	static function data_colorize() {
		return array(
			array(
				array(
					array('Error:', 31, 1),
					' Just kidding.',
				),
				"\033[31;1mError:\033[0m Just kidding.",
			),
			array(
				array(
					array('Error:', array(31, 1)),
					' Just kidding.',
				),
				"\033[31;1mError:\033[0m Just kidding.",
			),
			array(
				array(
					array('¡', 33, 1),
					'Hello yellow',
					array('!', 33, 1),
				),
				"\033[33;1m¡\033[0mHello yellow\033[33;1m!\033[0m",
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


