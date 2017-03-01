<?php
/**
 * DOM tests.
 *
 * PHPUnit tests for \blobfolio\common\dom.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class dom_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * ::load_svg()
	 *
	 * @return void Nothing.
	 */
	function test_load_svg() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');

		$dom = \blobfolio\common\dom::load_svg($svg);

		$this->assertEquals(true, is_a($dom, 'DOMDocument'));
	}

	/**
	 * ::save_svg()
	 *
	 * @return void Nothing.
	 */
	function test_save_svg() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \blobfolio\common\dom::load_svg($svg);
		$svg = \blobfolio\common\dom::save_svg($dom);

		$this->assertEquals(true, false !== strpos($svg, '<svg'));
	}

	/**
	 * ::get_nodes_by_class()
	 *
	 * @return void Nothing.
	 */
	function test_get_nodes_by_class() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \blobfolio\common\dom::load_svg($svg);
		$class = 'k3xzp';

		$nodes = \blobfolio\common\dom::get_nodes_by_class($dom, $class);

		$this->assertEquals(1, count($nodes));
	}

	/**
	 * ::parse_css()
	 *
	 * @return void Nothing.
	 */
	function test_parse_css() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \blobfolio\common\dom::load_svg($svg);
		$style = $dom->getElementsByTagName('style');
		$style = $style->item(0);

		$parsed = \blobfolio\common\dom::parse_css($style->nodeValue);

		$this->assertEquals(true, is_array($parsed));
		$this->assertEquals(1, count($parsed));
		$this->assertEquals('.k3xzp{fill:currentColor;}', $parsed[0]['raw']);
	}

	/**
	 * ::remove_nodes()
	 *
	 * @return void Nothing.
	 */
	function test_remove_nodes() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = \blobfolio\common\dom::load_svg($svg);

		// Before.
		$paths = $dom->getElementsByTagName('path');
		$this->assertEquals(1, $paths->length);

		// After.
		\blobfolio\common\dom::remove_nodes($paths);
		$this->assertEquals(0, $paths->length);
	}
}


