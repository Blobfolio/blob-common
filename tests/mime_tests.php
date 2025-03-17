<?php
/**
 * MIME tests.
 *
 * PHPUnit tests for mime.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use blobfolio\common\mime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test Suite
 */
class mime_tests extends TestCase {
	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_get_mime')]
	/**
	 * ::get_mime()
	 *
	 * @param string $mime MIME.
	 * @param string $expected_mime Expected MIME.
	 * @param string $expected_ext Expected extension.
	 */
	public function test_get_mime($mime, $expected_mime, $expected_ext) {
		$result = mime::get_mime($mime);

		$this->assertSame(true, \is_array($result));
		$this->assertEquals($expected_mime, $result['mime']);
		$this->assertSame(true, \in_array($expected_ext, $result['ext'], true));
	}

	#[Test]
	#[DataProvider('data_get_extension')]
	/**
	 * ::get_extension()
	 *
	 * @param string $ext Extension.
	 * @param string $expected_ext Expected extension.
	 * @param string $expected_mime Expected MIME.
	 */
	public function test_get_extension($ext, $expected_ext, $expected_mime) {
		$result = mime::get_extension($ext);

		$this->assertSame(true, \is_array($result));
		$this->assertEquals($expected_ext, $result['ext']);
		$this->assertSame(true, \in_array($expected_mime, $result['mime'], true));
	}

	#[Test]
	#[DataProvider('data_finfo')]
	/**
	 * ::finfo()
	 *
	 * @param string $file File.
	 * @param mixed $expected Expected.
	 * @param mixed $suggestion Suggestion.
	 */
	public function test_finfo($file, $expected, $suggestion) {
		$result = mime::finfo($file);
		$suggested = $result['suggested_filename'];
		unset($result['suggested_filename']);

		$this->assertEquals($expected, $result);
		if ($suggestion) {
			$this->assertSame(true, \in_array($suggestion, $suggested, true));
		}
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::get_mime()
	 *
	 * @return array Data.
	 */
	static function data_get_mime() {
		return array(
			array(
				'audio/Mp3',
				'audio/mp3',
				'mp3',
			),
			array(
				'image/jpeg',
				'image/jpeg',
				'jpeg',
			),
		);
	}

	/**
	 * Data for ::get_extension()
	 *
	 * @return array Data.
	 */
	static function data_get_extension() {
		return array(
			array(
				'mp3',
				'mp3',
				'audio/mp3',
			),
			array(
				'Xls',
				'xls',
				'application/vnd.ms-office',
			),
			array(
				'.XLS',
				'xls',
				'application/vnd.ms-excel',
			),
		);
	}

	/**
	 * Data for ::finfo()
	 *
	 * @return array Data.
	 */
	static function data_finfo() {
		return array(
			array(
				static::ASSETS . 'space.jpg',
				array(
					'dirname'=>\rtrim(static::ASSETS, '/'),
					'basename'=>'space.jpg',
					'extension'=>'jpg',
					'filename'=>'space',
					'path'=>static::ASSETS . 'space.jpg',
					'mime'=>'image/jpeg',
				),
				null,
			),
			// Incorrect name.
			array(
				static::ASSETS . 'space.png',
				array(
					'dirname'=>\rtrim(static::ASSETS, '/'),
					'basename'=>'space.png',
					'extension'=>'jpeg',
					'filename'=>'space',
					'path'=>static::ASSETS . 'space.png',
					'mime'=>'image/jpeg',
				),
				'space.jpg',
			),
			// Just a file name.
			array(
				'pkcs12-test-keystore.tar.gz',
				array(
					'dirname'=>\getcwd(),
					'basename'=>'pkcs12-test-keystore.tar.gz',
					'extension'=>'gz',
					'filename'=>'pkcs12-test-keystore.tar',
					'path'=>\getcwd() . '/pkcs12-test-keystore.tar.gz',
					'mime'=>'application/gzip',
				),
				null,
			),
			// Remote file.
			array(
				'https://upload.wikimedia.org/wikipedia/commons/7/76/Mozilla_Firefox_logo_2013.svg',
				array(
					'dirname'=>'https://upload.wikimedia.org/wikipedia/commons/7/76',
					'basename'=>'Mozilla_Firefox_logo_2013.svg',
					'extension'=>'svg',
					'filename'=>'Mozilla_Firefox_logo_2013',
					'path'=>'https://upload.wikimedia.org/wikipedia/commons/7/76/Mozilla_Firefox_logo_2013.svg',
					'mime'=>'image/svg+xml',
				),
				null,
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


