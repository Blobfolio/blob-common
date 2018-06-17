<?php
/**
 * File tests.
 *
 * PHPUnit tests for file.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\constants;
use \blobfolio\common\file;
use \blobfolio\common\mb;

/**
 * Test Suite
 */
class file_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Set up
	// -----------------------------------------------------------------

	/**
	 * Before Test
	 *
	 * String cast bypass should be off before the test.
	 *
	 * @return void Nothing.
	 */
	protected function setUp() {
		$this->assertFalse(constants::$str_lock);
	}

	/**
	 * After Test
	 *
	 * String cast bypass should still be off after the test.
	 *
	 * @return void Nothing.
	 */
	protected function tearDown() {
		$this->assertFalse(constants::$str_lock);
	}

	// ----------------------------------------------------------------- end setup



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * ::copy()
	 *
	 * @return void Nothing.
	 */
	function test_copy() {
		$from = self::ASSETS;
		$base = dirname(self::ASSETS) . '/copied/';
		$to = dirname(self::ASSETS) . '/copied/assets/';

		if (@file_exists($base)) {
			file::rmdir($base);
			if (@file_exists($base)) {
				$this->markTestSkipped('The test directory already exists.');
			}
		}

		file::copy($from, $to);
		$this->assertEquals(true, @is_dir($to));
		$this->assertEquals(false, file::empty_dir($to));

		file::rmdir($base);
		$this->assertEquals(false, @is_dir($base));
	}

	/**
	 * ::csv_headers()
	 *
	 * @dataProvider data_csv_headers
	 *
	 * @param string $file File.
	 * @param mixed $cols Columns.
	 * @param string $delimiter Delimiter.
	 * @param mixed $expected Expected.
	 * @return void Nothing.
	 */
	function test_csv_headers(string $file, $cols, string $delimiter, $expected) {
		$this->assertSame($expected, file::csv_headers($file, $cols, $delimiter));
	}


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
	 * ::dirsize()
	 *
	 * @dataProvider data_dirsize
	 *
	 * @param string $dir Directory.
	 * @param int $expected Expected.
	 * @return void Nothing.
	 */
	function test_dirsize(string $dir, int $expected) {
		$this->assertSame($expected, file::dirsize($dir));
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
	 * ::dir_hash
	 *
	 * @return void Nothing.
	 */
	function test_hash_dir() {
		$path = dirname(__FILE__);

		// Try something made up.
		$this->assertSame(false, file::hash_dir($path, 'foobar'));

		// Try MD5.
		$hash = file::hash_dir($path);
		$this->assertSame(32, strlen($hash));

		// Add a file.
		$file = $path . '/hashdir.file';
		@file_put_contents($file, 'Hello World');
		$hash2 = file::hash_dir($path);
		$this->assertSame(32, strlen($hash2));
		$this->assertSame(false, ($hash === $hash2));

		// Remove the file.
		@unlink($file);
		$hash3 = file::hash_dir($path);
		$this->assertSame(32, strlen($hash3));
		$this->assertSame(true, ($hash === $hash3));

		// Try an alternative file hash.
		$hash4 = file::hash_dir($path, 'md5', 'crc32b');
		$this->assertSame(32, strlen($hash4));
		$this->assertSame(false, ($hash === $hash4));

		// Try an alternative dir hash.
		$hash5 = file::hash_dir($path, 'crc32b', 'md5');
		$this->assertSame(8, strlen($hash5));
		$this->assertSame(false, ($hash4 === $hash5));
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
	 * ::line_count()
	 *
	 * @dataProvider data_line_count
	 *
	 * @param string $file File.
	 * @param bool $trim Only count printable lines.
	 * @param int $expected Expected.
	 * @return void Nothing.
	 */
	function test_line_count(string $file, bool $trim, int $expected) {
		$this->assertSame($expected, file::line_count($file, $trim));
	}

	/**
	 * ::mkdir() and ::rmdir()
	 */
	function test_mkdir_rmdir() {
		$base = static::ASSETS . 'rmdir-test/';
		$path = static::ASSETS . 'rmdir-test/subdir/';
		$file = $path . 'test.txt';

		if (@file_exists($base)) {
			file::rmdir($base);
			if (@file_exists($base)) {
				$this->markTestSkipped('The test directory already exists.');
			}
		}

		// Make the directory.
		file::mkdir($path, 0755);
		$this->assertSame(true, @is_dir($path));
		clearstatcache();
		$this->assertSame(decoct(0755), decoct(@fileperms($path) & 0777));

		// Add a file.
		@file_put_contents($file, 'Hello World');
		$this->assertSame(true, @file_exists($file));

		// Remove it the directories and file.
		file::rmdir($base);
		$this->assertSame(false, @is_dir($base));
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
	 * ::scandir()
	 *
	 * @return void Nothing.
	 */
	function test_scandir() {
		$path = dirname(__FILE__);

		// Files and directories.
		$files = file::scandir($path);
		$this->assertSame(true, in_array("{$path}/assets", $files, true));
		$this->assertSame(true, in_array("{$path}/assets/pi.svg", $files, true));
		$this->assertSame(true, in_array("{$path}/test-file.php", $files, true));

		// Only files.
		$files = file::scandir($path, true, false);
		$this->assertSame(false, in_array("{$path}/assets", $files, true));
		$this->assertSame(true, in_array("{$path}/assets/pi.svg", $files, true));
		$this->assertSame(true, in_array("{$path}/test-file.php", $files, true));

		// Only directories.
		$files = file::scandir($path, false, true);
		$this->assertSame(true, in_array("{$path}/assets", $files, true));
		$this->assertSame(false, in_array("{$path}/assets/pi.svg", $files, true));
		$this->assertSame(false, in_array("{$path}/test-file.php", $files, true));
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

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::csv_headers()
	 *
	 * @return array Data.
	 */
	function data_csv_headers() {
		return array(
			// Regular CSV.
			array(
				static::ASSETS . 'roles.csv',
				null,
				',',
				array(
					'Type'=>0,
					'Name'=>1,
					'Age'=>2,
				),
			),
			// Filter columns.
			array(
				static::ASSETS . 'roles.csv',
				array(
					'Name',
					'Age',
				),
				',',
				array(
					'Name'=>1,
					'Age'=>2,
				),
			),
			// Filter w/ alternate names.
			array(
				static::ASSETS . 'roles.csv',
				array(
					'name'=>'Name',
					'age'=>'Age',
				),
				',',
				array(
					'name'=>1,
					'age'=>2,
				),
			),
			// One with leading whitespace.
			array(
				static::ASSETS . 'roles2.csv',
				null,
				',',
				array(
					'Type'=>0,
					'Name'=>1,
					'Age'=>2,
				),
			),
			// Tab-delimited.
			array(
				static::ASSETS . 'roles3.csv',
				null,
				"\t",
				array(
					'Type'=>0,
					'Name'=>1,
					'Age'=>2,
				),
			),
		);
	}

	/**
	 * Data for ::dirsize()
	 *
	 * @return array Data.
	 */
	function data_dirsize() {
		return array(
			array(
				static::ASSETS . 'size',
				30720,
			),
			array(
				static::ASSETS . 'size/subdir',
				10240,
			),
			array(
				static::ASSETS . 'invalid',
				0,
			),
		);
	}

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
	 * Data for ::line_count()
	 *
	 * @return array Data.
	 */
	function data_line_count() {
		return array(
			array(
				static::ASSETS . 'roles.csv',
				true,
				4,
			),
			array(
				static::ASSETS . 'roles.csv',
				false,
				4,
			),
			array(
				static::ASSETS . 'roles2.csv',
				true,
				4,
			),
			array(
				static::ASSETS . 'roles2.csv',
				false,
				5,
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
				'/file/here',
			),
			array(
				'/file/here',
				true,
				false,
			),
			array(
				'\\file\\here',
				false,
				'/file/here',
			),
			array(
				static::ASSETS,
				true,
				static::ASSETS,
			),
			array(
				rtrim(static::ASSETS, '/'),
				true,
				static::ASSETS,
			),
			array(
				static::ASSETS . '/pi.svg',
				true,
				static::ASSETS . 'pi.svg',
			),
			array(
				'file://' . static::ASSETS,
				true,
				static::ASSETS,
			),
			array(
				'htTps://google.com/',
				true,
				'https://google.com/',
			),
			array(
				array('htTps://google.com/'),
				true,
				array('https://google.com/'),
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
				array('/path/to/foobar'),
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
				array('file/here'),
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

	// ----------------------------------------------------------------- end data
}


