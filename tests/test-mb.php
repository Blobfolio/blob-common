<?php
/**
 * Multibyte tests.
 *
 * PHPUnit tests for mb.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use \blobfolio\common\mb;

/**
 * Test Suite
 */
class mb_tests extends \PHPUnit\Framework\TestCase {

	// --------------------------------------------------------------------
	// Tests
	// --------------------------------------------------------------------

	/**
	 * ::parse_url()
	 *
	 * @dataProvider data_parse_url
	 *
	 * @param string $url URL.
	 * @param int $component Component.
	 * @param array $expected Expected.
	 */
	function test_parse_url($url, $component, $expected) {
		$this->assertEquals($expected, mb::parse_url($url, $component));
	}

	/**
	 * ::parse_str()
	 *
	 * @dataProvider data_parse_str
	 *
	 * @param string $str String.
	 * @param array $expected Expected.
	 */
	function test_parse_str($str, $expected) {
		mb::parse_str($str, $result);
		$this->assertEquals($expected, $result);
	}

	/**
	 * ::str_split()
	 *
	 * @dataProvider data_str_split
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @param array $expected Expected.
	 */
	function test_str_split($str, $split_length, $expected) {
		$this->assertEquals($expected, mb::str_split($str, $split_length));
	}

	/**
	 * ::strlen()
	 *
	 * @dataProvider data_strlen
	 *
	 * @param string $str String.
	 * @param int $expected Expected.
	 */
	function test_strlen($str, $expected) {
		$this->assertEquals($expected, mb::strlen($str));
	}

	/**
	 * ::str_pad()
	 *
	 * @dataProvider data_str_pad
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @param string $expected Expected.
	 */
	function test_str_pad($str, $pad_length, $pad_string, $pad_type, $expected) {
		$this->assertEquals($expected, mb::str_pad($str, $pad_length, $pad_string, $pad_type));
	}

	/**
	 * ::strpos()
	 *
	 * @dataProvider data_strpos
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	function test_strpos($haystack, $needle, $offset, $expected) {
		$this->assertSame($expected, mb::strpos($haystack, $needle, $offset));
	}

	/**
	 * ::strrev()
	 *
	 * @dataProvider data_strrev
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_strrev($str, $expected) {
		$this->assertEquals($expected, mb::strrev($str));
	}

	/**
	 * ::strrpos()
	 *
	 * @dataProvider data_strrpos
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	function test_strrpos($haystack, $needle, $offset, $expected) {
		$this->assertSame($expected, mb::strrpos($haystack, $needle, $offset));
	}

	/**
	 * ::strtolower()
	 *
	 * @dataProvider data_strtolower
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_strtolower($str, $expected) {
		$this->assertEquals($expected, mb::strtolower($str));
	}

	/**
	 * ::strtoupper()
	 *
	 * @dataProvider data_strtoupper
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_strtoupper($str, $expected) {
		$this->assertEquals($expected, mb::strtoupper($str));
	}

	/**
	 * ::substr()
	 *
	 * @dataProvider data_substr
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @param string $expected Expected.
	 */
	function test_substr($str, $start, $length, $expected) {
		$this->assertEquals($expected, mb::substr($str, $start, $length));
	}

	/**
	 * ::substr_count()
	 *
	 * @dataProvider data_substr_count
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param string $expected Expected.
	 */
	function test_substr_count($haystack, $needle, $expected) {
		$this->assertEquals($expected, mb::substr_count($haystack, $needle));
	}

	/**
	 * ::trim()
	 *
	 * @dataProvider data_trim
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_trim($str, $expected) {
		$this->assertEquals($expected, mb::trim($str));
	}

	/**
	 * ::ucfirst()
	 *
	 * @dataProvider data_ucfirst
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_ucfirst($str, $expected) {
		$this->assertEquals($expected, mb::ucfirst($str));
	}

	/**
	 * ::ucwords()
	 *
	 * @dataProvider data_ucwords
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	function test_ucwords($str, $expected) {
		$this->assertEquals($expected, mb::ucwords($str));
	}

	// -------------------------------------------------------------------- end tests



	// --------------------------------------------------------------------
	// Data
	// --------------------------------------------------------------------

	/**
	 * Data for ::parse_url()
	 *
	 * @return array Data.
	 */
	function data_parse_url() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'http://☺.com',
				PHP_URL_HOST,
				$smiley_host
			),
			array(
				'//☺.com',
				PHP_URL_HOST,
				$smiley_host
			),
			array(
				'☺.com',
				PHP_URL_HOST,
				$smiley_host
			),
			array(
				'google.com',
				PHP_URL_HOST,
				'google.com'
			),
			array(
				'//google.com',
				PHP_URL_HOST,
				'google.com'
			),
			array(
				'http://google.com',
				PHP_URL_HOST,
				'google.com'
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]'
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]'
			),
			array(
				'https://foo.bar/apples',
				-1,
				array(
					'scheme'=>'https',
					'host'=>'foo.bar',
					'path'=>'/apples'
				)
			),
		);
	}

	/**
	 * Data for ::parse_str()
	 *
	 * @return array Data.
	 */
	function data_parse_str() {
		return array(
			array(
				'foo=BjöRk&bar=Ⅷ loVes',
				array(
					'foo'=>'BjöRk',
					'bar'=>'Ⅷ loVes'
				)
			),
			array(
				'Björk',
				array('Björk'=>'')
			),
			array(
				'',
				array()
			)
		);
	}

	/**
	 * Data for ::str_split()
	 *
	 * @return array Data.
	 */
	function data_str_split() {
		return array(
			array(
				'Björk',
				1,
				array('B','j','ö','r','k')
			),
			array(
				'Björk',
				3,
				array('Bjö','rk')
			),
		);
	}

	/**
	 * Data for ::strlen()
	 *
	 * @return array Data.
	 */
	function data_strlen() {
		return array(
			array(
				'Björk',
				5
			),
			array(
				'Happy Days',
				10
			),
		);
	}

	/**
	 * Data for ::str_pad()
	 *
	 * @return array Data.
	 */
	function data_str_pad() {
		return array(
			array(
				'Björk',
				10,
				'~',
				STR_PAD_LEFT,
				'~~~~~Björk'
			),
			array(
				'Björk',
				10,
				'~',
				STR_PAD_BOTH,
				'~~~Björk~~'
			),
			array(
				'Björk',
				10,
				'~',
				STR_PAD_RIGHT,
				'Björk~~~~~'
			),
			array(
				'Björk',
				3,
				'~',
				STR_PAD_RIGHT,
				'Björk'
			),
			array(
				'Björk',
				10,
				'',
				STR_PAD_RIGHT,
				'Björk'
			),
		);
	}

	/**
	 * Data for ::strpos()
	 *
	 * @return array Data.
	 */
	function data_strpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				3
			),
			array(
				'Björk Björk',
				'ö',
				0,
				2
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8
			),
			array(
				'Björk Björk',
				'E',
				0,
				false
			),
		);
	}

	/**
	 * Data for ::strrev()
	 *
	 * @return array Data.
	 */
	function data_strrev() {
		return array(
			array(
				'Björk',
				'kröjB'
			),
			array(
				"Hello-World\n",
				"\ndlroW-olleH"
			)
		);
	}

	/**
	 * Data for ::strrpos()
	 *
	 * @return array Data.
	 */
	function data_strrpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				9
			),
			array(
				'Björk Björk',
				'ö',
				0,
				8
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8
			),
			array(
				'Björk Björk',
				'E',
				0,
				false
			),
		);
	}

	/**
	 * Data for ::strtolower()
	 *
	 * @return array Data.
	 */
	function data_strtolower() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'queen björk ⅷ loves 3 apples.'
			),
			array(
				"Hello-world\n",
				"hello-world\n"
			),
			array(
				array("Hello-world\n"),
				array("hello-world\n")
			),
		);
	}

	/**
	 * Data for ::strtoupper()
	 *
	 * @return array Data.
	 */
	function data_strtoupper() {
		return array(
			array(
				'THE lazY Rex ⅸ eAtS f00d.',
				'THE LAZY REX Ⅸ EATS F00D.'
			),
			array(
				"Hello-world\n",
				"HELLO-WORLD\n"
			),
			array(
				array("Hello-world\n"),
				array("HELLO-WORLD\n")
			),
		);
	}

	/**
	 * Data for ::substr()
	 *
	 * @return array Data.
	 */
	function data_substr() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				0,
				11,
				'quEen BjöRk'
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				6,
				null,
				'BjöRk Ⅷ loVes 3 aPplEs.'
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				-7,
				null,
				'aPplEs.'
			)
		);
	}

	/**
	 * Data for ::substr_count()
	 *
	 * @return array Data.
	 */
	function data_substr_count() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'BjöRk',
				1
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'töast',
				0
			),
			array(
				"Hello\nWorld\n",
				"\n",
				2
			)
		);
	}

	/**
	 * Data for ::trim()
	 *
	 * @return array Data.
	 */
	function data_trim() {
		return array(
			array(
				' 	test ',
				'test'
			),
			array(
				"\ntöast",
				'töast'
			),
			array(
				chr(0xA0) . ' test' . chr(0xA0),
				'test'
			),
			array(
				array("\ntöast"),
				array('töast')
			),
		);
	}

	/**
	 * Data for ::ucfirst()
	 *
	 * @return array Data.
	 */
	function data_ucfirst() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'QuEen BjöRk Ⅷ loVes 3 aPplEs.'
			),
			array(
				'hello-world',
				'Hello-world'
			),
			array(
				array('hello-world'),
				array('Hello-world')
			),
		);
	}

	/**
	 * Data for ::ucwords()
	 *
	 * @return array Data.
	 */
	function data_ucwords() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'QuEen BjöRk Ⅷ LoVes 3 APplEs.'
			),
			array(
				'hello-world',
				'Hello-World'
			),
			array(
				array('hello-world'),
				array('Hello-World')
			),
		);
	}

	// -------------------------------------------------------------------- end data
}


