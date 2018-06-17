<?php
/**
 * Image tests.
 *
 * PHPUnit tests for image.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\constants;
use \blobfolio\common\image;

/**
 * Test Suite
 */
class image_tests extends \PHPUnit\Framework\TestCase {

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
	 * ::clean_svg()
	 *
	 * @return void Nothing.
	 */
	function test_clean_svg() {
		$svg = image::clean_svg(self::ASSETS . 'enshrined.svg');

		$this->assertEquals(true, false !== strpos($svg, '<svg'));
		$this->assertSame(false, strpos($svg, '<script'));
	}

	/**
	 * ::getimagesize()
	 *
	 * @dataProvider data_getimagesize
	 *
	 * @param string $file File.
	 * @param mixed $expected Expected.
	 * @return void Nothing.
	 */
	function test_getimagesize(string $file, $expected) {
		$result = image::getimagesize($file);

		// Make sure the return type matches.
		$this->assertSame(gettype($expected), gettype($result));

		// If we were expecting an array, check the keys we passed.
		if (is_array($expected)) {
			foreach ($expected as $k=>$v) {
				$this->assertTrue(isset($result[$k]));
				$this->assertSame($v, $result[$k]);
			}
		}
		// Otherwise they should match.
		else {
			$this->assertSame($expected, $result);
		}
	}

	/**
	 * ::has_webp()
	 *
	 * @return void Nothing.
	 */
	function test_has_webp() {
		$cwebp = self::ASSETS . 'webp/bin/cwebp';
		$gif2webp = self::ASSETS . 'webp/bin/gif2webp';

		$this->assertSame(true, image::has_webp($cwebp, $gif2webp));
	}

	/**
	 * ::svg_dimensions()
	 *
	 * @return void Nothing.
	 */
	function test_svg_dimensions() {
		$svg = static::ASSETS . 'monogram-inkscape.svg';
		$dimensions = array('width'=>330.056, 'height'=>495.558);

		$this->assertEquals($dimensions, image::svg_dimensions($svg));
		$this->assertEquals($dimensions, image::svg_dimensions(file_get_contents($svg)));
	}

	/**
	 * ::to_webp()
	 *
	 * @return void Nothing.
	 */
	function test_to_webp() {
		$in = static::ASSETS . 'space.jpg';

		if (!image::has_webp()) {
			$this->markTestSkipped('Native WebP binaries not detected.');
		}

		image::to_webp($in, null);
		$this->assertEquals(true, file_exists(self::ASSETS . 'space.webp'));
		@unlink(self::ASSETS . 'space.webp');

		$out = static::ASSETS . 'space2.webp';
		image::to_webp($in, $out);
		$this->assertEquals(true, file_exists($out));
		@unlink($out);
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::getimagesize()
	 *
	 * @return array Data.
	 */
	function data_getimagesize() {
		return array(
			array(
				static::ASSETS . 'space.jpg',
				array(
					0=>3000,
					1=>750,
					2=>2,
					3=>'width="3000" height="750"',
					'mime'=>'image/jpeg',
				),
			),
			array(
				// This is actually a JPEG.
				static::ASSETS . 'space.png',
				array(
					0=>3000,
					1=>750,
					2=>2,
					3=>'width="3000" height="750"',
					'mime'=>'image/jpeg',
				),
			),
			array(
				static::ASSETS . 'space-real.png',
				array(
					0=>3000,
					1=>750,
					2=>3,
					3=>'width="3000" height="750"',
					'mime'=>'image/png',
				),
			),
			array(
				static::ASSETS . 'space-real.webp',
				array(
					0=>3000,
					1=>750,
					2=>18,
					3=>'width="3000" height="750"',
					'mime'=>'image/webp',
				),
			),
			array(
				static::ASSETS . 'monogram-inkscape.svg',
				array(
					0=>330.056,
					1=>495.558,
					2=>-1,
					3=>'width="330" height="495"',
					'mime'=>'image/svg+xml',
				),
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


