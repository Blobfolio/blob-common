<?php
/**
 * Blobfolio\Files
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class files_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: niceFileExtension
	 *
	 * @dataProvider data_niceFileExtension
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceFileExtension(string $value, string $expected) {
		$result = \Blobfolio\Files::niceFileExtension($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceMime
	 *
	 * @dataProvider data_niceMime
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceMime(string $value, string $expected) {
		$result = \Blobfolio\Files::niceMime($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: getMimeType
	 *
	 * @dataProvider data_getMimeType
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_getMimeType(string $value, string $expected) {
		$result = \Blobfolio\Files::getMimeType($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: checkExtensionMimePair
	 *
	 * @dataProvider data_checkExtensionMimePair
	 *
	 * @param string $ext Ext.
	 * @param string $mime Mime.
	 * @param bool $soft Soft.
	 * @param bool $expected Expected.
	 */
	function test_checkExtensionMimePair(string $ext, string $mime, bool $soft, bool $expected) {
		$result = \Blobfolio\Files::checkExtensionMimePair($ext, $mime, $soft);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: finfo
	 *
	 * @dataProvider data_finfo
	 *
	 * @param string $value File.
	 * @param string $nice Nice name.
	 * @param array Expected.
	 */
	function test_finfo(string $value, string $nice, array $expected) {
		$result = \Blobfolio\Files::finfo($value, $nice);

		// The rename bits will change over time, so let's look at those
		// separately.
		$result_rename = $result['rename'];
		unset($result['rename']);
		$expected_rename = $expected['rename'];
		unset($expected['rename']);

		$this->assertSame($expected, $result);

		$this->assertTrue(is_array($result_rename));
		$this->assertTrue(is_array($expected_rename));

		// If no rename was expected, no rename should be found.
		if (!count($expected_rename)) {
			$this->assertSame(0, count($result_rename));
		}
		// Otherwise let's just look for an intersect.
		else {
			$intersect = array_intersect($expected_rename, $result_rename);
			$this->assertTrue(count($intersect) > 0);
		}
	}

	/**
	 * Test: leadingSlash
	 *
	 * @dataProvider data_leadingSlash
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_leadingSlash(string $value, string $expected) {
		$result = \Blobfolio\Files::leadingSlash($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: path
	 *
	 * @dataProvider data_path
	 *
	 * @param string $value Value.
	 * @param bool $exists Must exist.
	 * @param string $expected Expected.
	 */
	function test_path(string $value, bool $exists, $expected) {
		$result = \Blobfolio\Files::path($value, $exists);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: trailingSlash
	 *
	 * @dataProvider data_trailingSlash
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_trailingSlash(string $value, string $expected) {
		$result = \Blobfolio\Files::trailingSlash($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: unixSlash
	 *
	 * @dataProvider data_unixSlash
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_unixSlash(string $value, string $expected) {
		$result = \Blobfolio\Files::unixSlash($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: unleadingSlash
	 *
	 * @dataProvider data_unleadingSlash
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_unleadingSlash(string $value, string $expected) {
		$result = \Blobfolio\Files::unleadingSlash($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: untrailingSlash
	 *
	 * @dataProvider data_untrailingSlash
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_untrailingSlash(string $value, string $expected) {
		$result = \Blobfolio\Files::untrailingSlash($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: copy
	 */
	function test_copy() {
		$from = self::ASSETS;
		$base = dirname(self::ASSETS) . '/copied/';
		$to = dirname(self::ASSETS) . '/copied/assets/';

		if (@file_exists($base)) {
			\Blobfolio\Files::rmdir($base);
			if (@file_exists($base)) {
				$this->markTestSkipped('The test directory already exists.');
			}
		}

		\Blobfolio\Files::copy($from, $to);
		$this->assertEquals(true, @is_dir($to));
		$this->assertEquals(false, \Blobfolio\Files::isEmptyDir($to));

		\Blobfolio\Files::rmdir($base);
		$this->assertEquals(false, @is_dir($base));
	}

	/**
	 * Test: csvHeaders
	 *
	 * @dataProvider data_csvHeaders
	 *
	 * @param string $file File.
	 * @param mixed $cols Cols.
	 * @param string $delimiter Delimiter.
	 * @param array $expected Expected.
	 */
	function test_csvHeaders(string $file, $cols, string $delimiter, array $expected) {
		$result = \Blobfolio\Files::csvHeaders($file, $cols, $delimiter);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: dataUri
	 *
	 * @dataProvider data_dataUri
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_dataUri(string $value, string $expected) {
		$result = \Blobfolio\Files::dataUri($value);

		// To avoid making this test file huge, we'll calculate the
		// base64 independently and toss it on the end. Haha.
		$expected .= base64_encode(file_get_contents($value));

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: hashDir
	 */
	function test_hashDir() {
		$path = dirname(__FILE__) . '/';
		$empty = md5('empty');

		// First try something fake.
		$result = \Blobfolio\Files::hashDir($path . 'foobar/');
		$this->assertSame($empty, $result);

		// Hash the path as-is.
		$result = \Blobfolio\Files::hashDir($path);
		$this->assertTrue(!!preg_match('/^[a-f\d]{32}$/', $result));

		// Add a file and see if the hash changes.
		$file = $path . 'hashDir.file';
		file_put_contents($file, 'Hello World');
		$result2 = \Blobfolio\Files::hashDir($path);
		$this->assertTrue(!!preg_match('/^[a-f\d]{32}$/', $result2));
		$this->assertNotSame($result, $result2);

		// Remove it and try again. The new hash should match the
		// original once more.
		unlink($file);
		$result2 = \Blobfolio\Files::hashDir($path);
		$this->assertSame($result, $result2);
	}

	/**
	 * Test: dirSize
	 *
	 * @dataProvider data_dirSize
	 *
	 * @param string $value Value.
	 * @param int $expected Expected.
	 */
	function test_dirSize(string $value, int $expected) {
		$result = \Blobfolio\Files::dirSize($value);

		$this->assertSame($expected, $result);
		$this->assertSame('integer', gettype($result));
	}

	/**
	 * Test: getLineCount
	 *
	 * @dataProvider data_getLineCount
	 *
	 * @param string $value Value.
	 * @param string $trim Trim.
	 * @param int $expected Expected.
	 */
	function test_getLineCount(string $value, bool $trim, int $expected) {
		$result = \Blobfolio\Files::getLineCount($value, $trim);

		$this->assertSame($expected, $result);
		$this->assertSame('integer', gettype($result));
	}

	/**
	 * Test: isEmptyDir
	 */
	function test_isEmptyDir() {
		// We know the asset dir is not empty.
		$result = \Blobfolio\Files::isEmptyDir(self::ASSETS);
		$this->assertFalse($result);

		// Make an empty directory.
		$dir = self::ASSETS . 'empty/';
		if (!is_dir($dir)) {
			mkdir($dir);
			if (!is_dir($dir)) {
				$this->markTestSkipped('Could not create test directory.');
			}
		}

		// This should be empty.
		$result = \Blobfolio\Files::isEmptyDir($dir);
		$this->assertTrue($result);

		// Add a file to it, which should make it not empty.
		$file = $dir . 'file.file';
		file_put_contents($file, 'Hello World');
		$result = \Blobfolio\Files::isEmptyDir($dir);
		$this->assertFalse($result);

		// Clean up.
		unlink($file);
		rmdir($dir);
	}

	/**
	 * Test: mkdir and rmkdir
	 *
	 * Might as well hit these two in one go. Haha.
	 */
	function test_mkdir_rmdir() {
		$base = static::ASSETS . 'rmdir-test/';
		$path = static::ASSETS . 'rmdir-test/subdir/';
		$file = $path . 'file.file';

		// An early test!
		if (is_dir($base)) {
			\Blobfolio\Files::rmdir($base);
			if (is_dir($base)) {
				$this->markTestSkipped('An existing test directory could not be removed.');
			}
		}

		// Make the deep one.
		\Blobfolio\Files::mkdir($path, 0755);
		$this->assertTrue(is_dir($path));
		$this->assertSame(decoct(0755), decoct(fileperms($base) & 0777));
		$this->assertSame(decoct(0755), decoct(fileperms($path) & 0777));

		// Add a file.
		file_put_contents($file, 'Hello World');
		$this->assertTrue(is_file($file));

		// Remove everything.
		\Blobfolio\Files::rmdir($base);
		$this->assertFalse(is_file($file));
		$this->assertFalse(is_dir($path));
		$this->assertFalse(is_dir($base));
	}

	/**
	 * Test: scandir
	 *
	 * @dataProvider data_scandir
	 *
	 * @param string $value Value.
	 * @param bool $files Files.
	 * @param bool $dirs Directories.
	 * @param int $depth Depth.
	 * @param array $expected Expected.
	 */
	function test_scandir(string $value, bool $files, bool $directories, int $depth, array $expected) {
		$result = \Blobfolio\Files::scandir($value, $files, $directories, $depth);

		$this->assertSame($expected, $result);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: niceFileExtension
	 *
	 * @return array Values.
	 */
	function data_niceFileExtension() {
		return array(
			array(
				'  .JPEG ',
				'jpeg',
			),
			array(
				'.tar.gz',
				'tar.gz',
			),
		);
	}

	/**
	 * Data: niceMime
	 *
	 * @return array Values.
	 */
	function data_niceMime() {
		return array(
			array(
				'Application/Octet-Stream',
				'application/octet-stream',
			),
			array(
				'application/vnd.MS-OFFICE',
				'application/vnd.ms-office',
			),
			array(
				'awesome/saucE',
				'awesome/sauce',
			),
		);
	}

	/**
	 * Data: getMimeType
	 *
	 * @return array Values.
	 */
	function data_getMimeType() {
		return array(
			array(
				self::ASSETS . 'enshrined.svg',
				'image/svg+xml',
			),
			array(
				self::ASSETS . 'minus.svg',
				'image/svg+xml',
			),
			array(
				self::ASSETS . 'roles.csv',
				'text/csv',
			),
			array(
				self::ASSETS . 'space.png',
				'image/jpeg',
			),
			array(
				self::ASSETS . 'space-real.png',
				'image/png',
			),
			array(
				self::ASSETS . 'space-real.webp',
				'image/webp',
			),
		);
	}

	/**
	 * Data: checkExtensionMimePair
	 *
	 * @return array Values.
	 */
	function data_checkExtensionMimePair() {
		return array(
			array(
				'JPEG',
				'IMAGE/JPEG',
				false,
				true,
			),
			array(
				'JPEG',
				'IMAGE/x-JPEG',
				false,
				true,
			),
			array(
				'csv',
				'text/plain',
				false,
				true,
			),
			array(
				'JPEG',
				'application/octet-stream',
				false,
				false,
			),
			array(
				'JPEG',
				'application/octet-stream',
				true,
				true,
			),
		);
	}

	/**
	 * Data: finfo
	 *
	 * @return array Values.
	 */
	function data_finfo() {
		return array(
			array(
				self::ASSETS . 'space.jpeg',
				'',
				array(
					'dirname'=>dirname(self::ASSETS . 'space.jpeg'),
					'basename'=>'space.jpeg',
					'extension'=>'jpeg',
					'filename'=>'space',
					'path'=>self::ASSETS . 'space.jpeg',
					'mime'=>'image/jpeg',
					'rename'=>array(),
				),
			),
			array(
				self::ASSETS . 'space.jpeg',
				'somethingelse.jpg',
				array(
					'dirname'=>dirname(self::ASSETS . 'space.jpeg'),
					'basename'=>'space.jpeg',
					'extension'=>'jpg',
					'filename'=>'somethingelse',
					'path'=>self::ASSETS . 'space.jpeg',
					'mime'=>'image/jpeg',
					'rename'=>array(),
				),
			),
			array(
				self::ASSETS . 'space.png',
				'',
				array(
					'dirname'=>dirname(self::ASSETS . 'space.png'),
					'basename'=>'space.png',
					'extension'=>'jpeg',
					'filename'=>'space',
					'path'=>self::ASSETS . 'space.png',
					'mime'=>'image/jpeg',
					'rename'=>array(
						'space.jpeg',
					),
				),
			),
			array(
				self::ASSETS . 'space.png',
				'stars.png',
				array(
					'dirname'=>dirname(self::ASSETS . 'space.png'),
					'basename'=>'space.png',
					'extension'=>'jpeg',
					'filename'=>'stars',
					'path'=>self::ASSETS . 'space.png',
					'mime'=>'image/jpeg',
					'rename'=>array(
						'stars.jpeg',
					),
				),
			),
		);
	}

	/**
	 * Data: leadingSlash
	 *
	 * @return array Values.
	 */
	function data_leadingSlash() {
		return array(
			array(
				'/file/here',
				'/file/here',
			),
			array(
				'file/here',
				'/file/here',
			),
		);
	}

	/**
	 * Data: path
	 *
	 * @return array Values.
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
		);
	}

	/**
	 * Data: trailingSlash
	 *
	 * @return array Values.
	 */
	function data_trailingSlash() {
		return array(
			array(
				'/file/here/',
				'/file/here/',
			),
			array(
				'file/here',
				'file/here/',
			),
		);
	}

	/**
	 * Data: unixSlash
	 *
	 * @return array Values.
	 */
	function data_unixSlash() {
		return array(
			array(
				'/file/here',
				'/file/here',
			),
			array(
				'C:\\Windows\\Fonts',
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
		);
	}

	/**
	 * Data: unleadingSlash
	 *
	 * @return array Values.
	 */
	function data_unleadingSlash() {
		return array(
			array(
				'/file/here',
				'file/here',
			),
			array(
				'file/here',
				'file/here',
			),
		);
	}

	/**
	 * Data: untrailingSlash
	 *
	 * @return array Values.
	 */
	function data_untrailingSlash() {
		return array(
			array(
				'/file/here/',
				'/file/here',
			),
			array(
				'file/here',
				'file/here',
			),
		);
	}

	/**
	 * Data: csvHeaders
	 *
	 * @return array Values.
	 */
	function data_csvHeaders() {
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
	 * Data: dataUri
	 *
	 * @return array Values.
	 */
	function data_dataUri() {
		return array(
			array(
				self::ASSETS . 'space.jpg',
				'data:image/jpeg;base64,',
			),
			array(
				self::ASSETS . 'space.png',
				'data:image/jpeg;base64,',
			),
			array(
				self::ASSETS . 'pi.svg',
				'data:image/svg+xml;base64,',
			),
		);
	}

	/**
	 * Data: dirSize
	 *
	 * @return array Values.
	 */
	function data_dirSize() {
		return array(
			array(
				static::ASSETS . 'size',
				30720,
			),
			array(
				static::ASSETS . 'size/subdir/',
				10240,
			),
			array(
				static::ASSETS . 'invalid',
				0,
			),
		);
	}

	/**
	 * Data: getLineCount
	 *
	 * @return array Values.
	 */
	function data_getLineCount() {
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
	 * Data: scandir
	 *
	 * @return array Values.
	 */
	function data_scandir() {
		return array(
			array(
				self::ASSETS . 'size',
				true,
				true,
				-1,
				array(
					self::ASSETS . 'size/file1',
					self::ASSETS . 'size/file2',
					self::ASSETS . 'size/subdir/',
					self::ASSETS . 'size/subdir/file3',
				),
			),
			array(
				self::ASSETS . 'size',
				false,
				true,
				-1,
				array(
					self::ASSETS . 'size/subdir/',
				),
			),
			array(
				self::ASSETS . 'size',
				true,
				false,
				-1,
				array(
					self::ASSETS . 'size/file1',
					self::ASSETS . 'size/file2',
					self::ASSETS . 'size/subdir/file3',
				),
			),
			array(
				self::ASSETS . 'size',
				true,
				true,
				1,
				array(
					self::ASSETS . 'size/file1',
					self::ASSETS . 'size/file2',
					self::ASSETS . 'size/subdir/',
				),
			),
		);
	}
}
