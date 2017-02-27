<?php
//---------------------------------------------------------------------
// image:: tests
//---------------------------------------------------------------------

class image_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';

	//-------------------------------------------------
	// image::clean_svg()

	function test_clean_svg() {
		$svg = \blobfolio\common\image::clean_svg(self::ASSETS . 'enshrined.svg');

		$this->assertEquals(true, false !== strpos($svg, '<svg'));
		$this->assertEquals(false, strpos($svg, '<script'));
	}

	//-------------------------------------------------
	// image::has_webp()

	function test_has_webp() {
		$cwebp = self::ASSETS . 'webp/bin/cwebp';
		$gif2webp = self::ASSETS . 'webp/bin/gif2webp';

		$this->assertEquals(true, \blobfolio\common\image::has_webp($cwebp, $gif2webp));
		$this->assertEquals(true, \blobfolio\common\image::has_webp());
	}

	//-------------------------------------------------
	// image::svg_dimensions()

	function test_svg_dimensions() {
		$svg = static::ASSETS . 'monogram-inkscape.svg';
		$dimensions = array('width'=>330.056,'height'=>495.558);

		$this->assertEquals($dimensions, \blobfolio\common\image::svg_dimensions($svg));
		$this->assertEquals($dimensions, \blobfolio\common\image::svg_dimensions(file_get_contents($svg)));
	}

	//-------------------------------------------------
	// image::to_webp()

	function test_to_webp() {
		$in = static::ASSETS . 'space.jpg';

		\blobfolio\common\image::to_webp($in);
		$this->assertEquals(true, file_exists(self::ASSETS . 'space.webp'));
		@unlink(self::ASSETS . 'space.webp');

		$out = static::ASSETS . 'space2.webp';
		\blobfolio\common\image::to_webp($in, $out);
		$this->assertEquals(true, file_exists($out));
		@unlink($out);
	}
}

?>