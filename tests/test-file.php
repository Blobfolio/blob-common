<?php
/**
 * File tests.
 *
 * PHPUnit tests for file.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\file;
use \blobfolio\common\mb;

/**
 * Test Suite
 */
class file_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// --------------------------------------------------------------------
	// Tests
	// --------------------------------------------------------------------

	/**
	 * ::data_uri()
	 *
	 * @return void Nothing.
	 */
	function test_data_uri() {
		$svg = self::ASSETS . 'pi.svg';
		$data = file::data_uri($svg);

		$this->assertEquals(true, false !== strpos($data, 'image/svg+xml'));
		$this->assertEquals(false, file::data_uri('does_not_exist.txt'));
	}

	/**
	 * ::empty_dir()
	 *
	 * @return void Nothing.
	 */
	function test_empty_dir() {
		$this->assertSame(false, file::empty_dir(self::ASSETS));

		$new = self::ASSETS . 'empty';
		if (!file_exists($new)) {
			mkdir($new);
		}
		$this->assertSame(true, file::empty_dir($new));
		rmdir($new);
	}

	/**
	 * ::leadingslash()
	 *
	 * @dataProvider data_leadingslash
	 *
	 * @param string $path Path.
	 * @param string $expected Expected.
	 */
	function test_leadingslash($path, $expected) {
		$this->assertEquals($expected, file::leadingslash($path));
	}

	/**
	 * ::path()
	 *
	 * @dataProvider data_path
	 *
	 * @param string $path Path.
	 * @param bool $validate Validate.
	 * @param string $expected Expected.
	 */
	function test_path($path, $validate, $expected) {
		$this->assertEquals($expected, file::path($path, $validate));
	}

	/**
	 * ::trailingslash()
	 *
	 * @dataProvider data_trailingslash
	 *
	 * @param string $path Path.
	 * @param string $expected Expected.
	 */
	function test_trailingslash($path, $expected) {
		$this->assertEquals($expected, file::trailingslash($path));
	}

	/**
	 * ::unixslash()
	 *
	 * @dataProvider data_unixslash
	 *
	 * @param string $path Path.
	 * @param string $expected Expected.
	 */
	function test_unixslash($path, $expected) {
		$this->assertEquals($expected, file::unixslash($path));
	}

	/**
	 * ::unleadingslash()
	 *
	 * @dataProvider data_unleadingslash
	 *
	 * @param string $path Path.
	 * @param string $expected Expected.
	 */
	function test_unleadingslash($path, $expected) {
		$this->assertEquals($expected, file::unleadingslash($path));
	}

	/**
	 * ::unparse_url()
	 *
	 * @dataProvider data_unparse_url
	 *
	 * @param string $url URL.
	 */
	function test_unparse_url($url) {
		$parsed = mb::parse_url($url);
		$unparsed = file::unparse_url($parsed);
		$this->assertEquals($url, $unparsed);
	}

	/**
	 * ::untrailingslash()
	 *
	 * @dataProvider data_untrailingslash
	 *
	 * @param string $path Path.
	 * @param string $expected Expected.
	 */
	function test_untrailingslash($path, $expected) {
		$this->assertEquals($expected, file::untrailingslash($path));
	}

	// -------------------------------------------------------------------- end tests



	// --------------------------------------------------------------------
	// Data
	// --------------------------------------------------------------------

	/**
	 * Data for ::leadingslash()
	 *
	 * @return array Data.
	 */
	function data_leadingslash() {
		return array(
			array(
				'/file/here',
				'/file/here',
			),
			array(
				'file/here',
				'/file/here',
			),
			array(
				array('file/here'),
				array('/file/here'),
			),
		);
	}

	/**
	 * Data for ::path()
	 *
	 * @return array Data.
	 */
	function data_path() {
		return array(
			array(
				'/file/here',
				false,
				'/file/here'
			),
			array(
				'/file/here',
				true,
				false
			),
			array(
				'\\file\\here',
				false,
				'/file/here'
			),
			array(
				static::ASSETS,
				true,
				static::ASSETS
			),
			array(
				rtrim(static::ASSETS, '/'),
				true,
				static::ASSETS
			),
			array(
				static::ASSETS . '/pi.svg',
				true,
				static::ASSETS . 'pi.svg'
			),
			array(
				'file://' . static::ASSETS,
				true,
				static::ASSETS
			),
			array(
				'htTps://google.com/',
				true,
				'https://google.com/'
			),
			array(
				array('htTps://google.com/'),
				true,
				array('https://google.com/')
			),
		);
	}

	/**
	 * Data for ::trailingslash()
	 *
	 * @return array Data.
	 */
	function data_trailingslash() {
		return array(
			array(
				'/file/here/',
				'/file/here/',
			),
			array(
				'file/here',
				'file/here/',
			),
			array(
				array('file/here'),
				array('file/here/'),
			),
		);
	}

	/**
	 * Data for ::unixslash()
	 *
	 * @return array Data.
	 */
	function data_unixslash() {
		return array(
			array(
				'/file/here',
				'/file/here',
			),
			array(
				'C:\Windows\Fonts',
				'C:/Windows/Fonts',
			),
			array(
				'/path/./to/foobar',
				'/path/to/foobar',
			),
			array(
				'/path//to/foobar',
				'/path/to/foobar',
			),
			array(
				array('/path//to/foobar'),
				array('/path/to/foobar',)
			),
		);
	}

	/**
	 * Data for ::unleadingslash()
	 *
	 * @return array Data.
	 */
	function data_unleadingslash() {
		return array(
			array(
				'/file/here',
				'file/here',
			),
			array(
				'file/here',
				'file/here',
			),
			array(
				array('file/here'),
				array('file/here')
			),
		);
	}

	/**
	 * Data for ::unparse_url()
	 *
	 * @return array Data.
	 */
	function data_unparse_url() {
		return array(
			array('https://google.com/search?hello#foo'=>'https://google.com/search?hello#foo'),
			array('google.com/apples'=>'google.com/apples'),
			array('//☺.com'=>'https://xn--74h.com'),
			array('ftp://user:pass@ftp.com:123'=>'ftp://user:pass@ftp.com:123'),
		);
	}

	/**
	 * Data for ::untrailingslash()
	 *
	 * @return array Data.
	 */
	function data_untrailingslash() {
		return array(
			array(
				'/file/here/',
				'/file/here',
			),
			array(
				'file/here',
				'file/here',
			),
			array(
				array('file/here'),
				array('file/here'),
			),
		);
	}

	// -------------------------------------------------------------------- end data
}


