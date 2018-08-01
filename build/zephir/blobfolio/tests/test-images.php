<?php
/**
 * Blobfolio\Images
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class images_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: getBlankImage
	 */
	function test_getBlankImage() {
		$result = \Blobfolio\Images::getBlankImage();
		$expected = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=';

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: niceSvg
	 *
	 * @dataProvider data_niceSvg
	 *
	 * @param string $value Value.
	 */
	function test_niceSvg(string $value) {
		$result = \Blobfolio\Images::niceSvg($value);

		// Rather than bloat the test file with exact copy, we'll run
		// down a list of things we should *not* see in the result.
		$tests = array(
			'&#109;',
			'&#123',
			'//hello',
			'<script',
			'comment',
			'data:',
			'Gotcha',
			'http://example.com',
			'max:volume',
			'onclick',
			'onload',
			'xmlns:foobar',
			'XSS',
		);

		foreach ($tests as $v) {
			$this->assertFalse(strpos($result, $v));
		}
	}

	/**
	 * Test: size
	 *
	 * @dataProvider data_size
	 *
	 * @param string $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_size(string $value, $expected) {
		$result = \Blobfolio\Images::size($value);

		// A bool should match exactly.
		if (is_bool($expected)) {
			$this->assertSame($expected, $result);
		}
		// If an array is returned, let's just compare the keys we
		// happened to pass.
		else {
			$this->assertSame('array', gettype($result));
			$this->assertSame('array', gettype($expected));
			foreach ($expected as $k=>$v) {
				$this->assertTrue(array_key_exists($k, $result));
				$this->assertSame($v, $result[$k]);
			}
		}
	}

	/**
	 * Test: svgSize
	 *
	 * @dataProvider data_svgSize
	 *
	 * @param string $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_svgSize(string $value, $expected) {
		$result = \Blobfolio\Images::svgSize($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: webpSize
	 *
	 * @dataProvider data_webpSize
	 *
	 * @param string $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_webpSize(string $value, $expected) {
		$result = \Blobfolio\Images::webpSize($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: cleanSVG
	 *
	 * @dataProvider data_cleanSvg
	 *
	 * @param string $value Value.
	 * @param array $args Arguments.
	 * @param array $expected_true Expected positive bits.
	 * @param array $expected_false Expected negative bits.
	 */
	function test_cleanSvg(string $value, array $args, array $expected_true, array $expected_false) {
		$result = \Blobfolio\Images::cleanSvg($value, $args);

		// If we aren't looking for anything, we are looking for
		// nothing.
		if (!count($expected_true) && !count($expected_false)) {
			$this->assertTrue(!strlen($result));
		}
		else {
			foreach ($expected_true as $v) {
				$this->assertTrue(false !== strpos($result, $v));
			}
			foreach ($expected_false as $v) {
				$this->assertFalse(false !== strpos($result, $v));
			}
		}
	}

	/**
	 * Test: toWebp
	 */
	function test_toWebp() {
		$in = self::ASSETS . 'space.jpg';
		$out = self::ASSETS . 'space.webp';

		if (is_file($out)) {
			unlink($out);
			if (is_file($out)) {
				$this->markTestSkipped('Could not remove old test WebP.');
			}
		}

		// Try it without specifying an output file.
		$result = \Blobfolio\Images::toWebp($in);
		$this->assertTrue($result);
		$this->assertTrue(is_file($out));
		unlink($out);

		// Try to specify a file now.
		$out = self::ASSETS . 'space2.webp';

		if (is_file($out)) {
			unlink($out);
			if (is_file($out)) {
				$this->markTestSkipped('Could not remove old test WebP.');
			}
		}

		$result = \Blobfolio\Images::toWebp($in, $out);
		$this->assertTrue($result);
		$this->assertTrue(is_file($out));
		unlink($out);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: niceSvg
	 *
	 * @return array Values.
	 */
	function data_niceSvg() {
		return array(
			array(file_get_contents(self::ASSETS . 'monogram-inkscape.svg')),
			array(file_get_contents(self::ASSETS . 'enshrined.svg')),
			array(file_get_contents(self::ASSETS . 'pi.svg')),
			array(file_get_contents(self::ASSETS . 'minus.svg')),
		);
	}

	/**
	 * Data: size
	 *
	 * @return array Values.
	 */
	function data_size() {
		return array(
			array(
				self::ASSETS . 'space.jpg',
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
				self::ASSETS . 'space.png',
				array(
					0=>3000,
					1=>750,
					2=>2,
					3=>'width="3000" height="750"',
					'mime'=>'image/jpeg',
				),
			),
			array(
				self::ASSETS . 'space-real.png',
				array(
					0=>3000,
					1=>750,
					2=>3,
					3=>'width="3000" height="750"',
					'mime'=>'image/png',
				),
			),
			array(
				self::ASSETS . 'space-real.webp',
				array(
					0=>3000,
					1=>750,
					2=>18,
					3=>'width="3000" height="750"',
					'mime'=>'image/webp',
				),
			),
			array(
				self::ASSETS . 'monogram-inkscape.svg',
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

	/**
	 * Data: svgSize
	 *
	 * @return array Values.
	 */
	function data_svgSize() {
		return array(
			array(
				self::ASSETS . 'space.png',
				false,
			),
			array(
				self::ASSETS . 'enshrined.svg',
				false,
			),
			array(
				self::ASSETS . 'pi.svg',
				array(
					'width'=>173.457,
					'height'=>177.5,
				),
			),
			array(
				self::ASSETS . 'minus.svg',
				array(
					'width'=>533.334,
					'height'=>133.333,
				),
			),
		);
	}

	/**
	 * Data: webpSize
	 *
	 * @return array Values.
	 */
	function data_webpSize() {
		return array(
			array(
				self::ASSETS . 'space.png',
				false,
			),
			array(
				self::ASSETS . 'space-real.webp',
				array(
					'width'=>3000,
					'height'=>750,
				),
			),
		);
	}

	/**
	 * Data: cleanSvg
	 *
	 * @return array Values.
	 */
	function data_cleanSvg() {
		return array(
			array(
				self::ASSETS . 'monogram-inkscape.svg',
				array(),
				array(
					'<svg',
					'.bleerkk3 { fill: currentColor; }',
					'id="',
				),
				array(
					'<sodipodi',
				),
			),
			array(
				self::ASSETS . 'monogram-inkscape.svg',
				array(
					'strip_id'=>true,
				),
				array(
					'<svg',
					'.bleerkk3 { fill: currentColor; }',
				),
				array(
					'id="',
					'<sodipodi',
				),
			),
			array(
				self::ASSETS . 'monogram-inkscape.svg',
				array(
					'clean_styles'=>true,
				),
				array(
					'<svg',
					'{fill:currentColor;}',
					'id="',
				),
				array(
					'<sodipodi',
				),
			),
			array(
				self::ASSETS . 'monogram-inkscape.svg',
				array(
					'clean_styles'=>true,
					'rewrite_styles'=>true,
				),
				array(
					'<svg',
					'{fill:currentColor;}',
					'id="',
				),
				array(
					'<sodipodi',
					'.bleerkk3',
				),
			),
			array(
				self::ASSETS . 'minus.svg',
				array(
					'clean_styles'=>true,
					'rewrite_styles'=>true,
				),
				array(
					'<svg',
					'{fill:currentColor;}',
					'{fill:red;}',
				),
				array(
					'.abc',
					'&#109;',
				),
			),
		);
	}
}
