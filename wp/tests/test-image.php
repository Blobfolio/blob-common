<?php
/**
 * Class ImageTests
 *
 * @package blob-common
 */

/**
 * Test functions-image.php.
 */
class ImageTests extends WP_UnitTestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * Get Clean SVG
	 *
	 * @return void Nothing.
	 */
	function test_common_get_clean_svg() {
		$file = static::ASSETS . 'monogram.svg';

		// Original.
		$svg = \file_get_contents($file);

		$this->assertEquals(true, false !== \strpos($svg, '<svg'));
		$this->assertEquals(true, false !== \strpos($svg, 'id="svg2"'));
		$this->assertEquals(true, false !== \strpos($svg, '<style'));
		$this->assertEquals(true, false === \strpos($svg, 'svg:style'));
		$this->assertEquals(true, false !== \strpos($svg, 'bleerkk3'));

		// Strip IDs.
		$svg = \common_get_clean_svg($file, array('strip_id'=>true));
		$this->assertEquals(true, false !== \strpos($svg, '<svg'));
		$this->assertEquals(false, false !== \strpos($svg, 'id="svg2"'));

		// Stripe styles.
		$svg = \common_get_clean_svg($file, array('strip_style'=>true));
		$this->assertEquals(true, false !== \strpos($svg, '<svg'));
		$this->assertEquals(false, false !== \strpos($svg, '<style'));

		// Add namespace.
		$svg = \common_get_clean_svg($file, array('namespace'=>true));
		$this->assertEquals(true, false !== \strpos($svg, '<svg'));
		$this->assertEquals(false, false === \strpos($svg, 'svg:style'));

		// Rewrite styles.
		$svg = \common_get_clean_svg($file, array('rewrite_styles'=>true));
		$this->assertEquals(true, false !== \strpos($svg, '<svg'));
		$this->assertEquals(false, false !== \strpos($svg, 'bleerkk3'));
	}

	/**
	 * Get SVG Dimensions
	 *
	 * @return void Nothing.
	 */
	function test_common_get_svg_dimensions() {
		$file = static::ASSETS . 'monogram.svg';

		$dimensions = \common_get_svg_dimensions($file);

		$this->assertEquals(330.056, $dimensions['width']);
		$this->assertEquals(495.558, $dimensions['height']);
	}

	/**
	 * Get Blank Image
	 *
	 * @return void Nothing.
	 */
	function test_common_get_blank_image() {
		$this->assertEquals(true, \strlen(\common_get_blank_image()) > 0);
	}
}
