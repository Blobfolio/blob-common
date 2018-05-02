<?php
/**
 * DOM tests.
 *
 * PHPUnit tests for dom.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\constants;
use \blobfolio\common\dom;

/**
 * Test Suite
 */
class dom_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Set up
	// -----------------------------------------------------------------

	// Store some information for us.
	protected $lock;

	/**
	 * Before Test
	 *
	 * Note the string casting lock state before we start the test.
	 *
	 * @return void Nothing.
	 */
	protected function setUp() {
		$this->lock = constants::$str_lock;
	}

	/**
	 * After Test
	 *
	 * Compare the string casting lock state after the test. (It should
	 * match. Haha.)
	 *
	 * @return void Nothing.
	 */
	protected function tearDown() {
		$lock = constants::$str_lock;
		$this->assertSame($lock, $this->lock);
	}

	// ----------------------------------------------------------------- end setup



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * ::load_svg()
	 *
	 * @return void Nothing.
	 */
	function test_load_svg() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = dom::load_svg($svg);

		$this->assertSame(true, is_a($dom, 'DOMDocument'));
	}

	/**
	 * ::save_svg()
	 *
	 * @return void Nothing.
	 */
	function test_save_svg() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = dom::load_svg($svg);
		$svg = dom::save_svg($dom);

		$this->assertSame(true, false !== strpos($svg, '<svg'));
	}

	/**
	 * ::get_nodes_by_class()
	 *
	 * @return void Nothing.
	 */
	function test_get_nodes_by_class() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = dom::load_svg($svg);
		$class = 'k3xzp';

		$nodes = dom::get_nodes_by_class($dom, $class);

		$this->assertEquals(1, count($nodes));
	}

	/**
	 * ::innerhtml()
	 *
	 * @return void Nothing.
	 */
	function test_innerhtml() {
		$str = '<div><span><br/><strong>hello</strong>world</span></div>';
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = false;
		$dom->preserveWhiteSpace = false;
		$dom->loadHTML($str);

		$div = $dom->getElementsByTagName('div')->item(0);
		$innerhtml = dom::innerhtml($div);

		$this->assertEquals('<span><br><strong>hello</strong>world</span>', $innerhtml);

		$innerhtml = dom::innerhtml($div, true, LIBXML_NOEMPTYTAG);
		$this->assertEquals('<span><br></br><strong>hello</strong>world</span>', $innerhtml);
	}

	/**
	 * ::parse_css()
	 *
	 * @return void Nothing.
	 */
	function test_parse_css() {
		$svg = file_get_contents(self::ASSETS . 'pi.svg');
		$dom = dom::load_svg($svg);
		$style = $dom->getElementsByTagName('style');
		$style = $style->item(0);

		$parsed = dom::parse_css($style->nodeValue);

		$this->assertSame(true, is_array($parsed));
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
		$dom = dom::load_svg($svg);

		// Before.
		$paths = $dom->getElementsByTagName('path');
		$this->assertEquals(1, $paths->length);

		// After.
		dom::remove_nodes($paths);
		$this->assertEquals(0, $paths->length);
	}

	// ----------------------------------------------------------------- end tests
}


