<?php
/**
 * Multibyte tests.
 *
 * PHPUnit tests for mb.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

use blobfolio\common\mb;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test Suite
 */
class mb_tests extends TestCase {
	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	#[Test]
	#[DataProvider('data_parse_url')]
	/**
	 * ::parse_url()
	 *
	 * @param string $url URL.
	 * @param int $component Component.
	 * @param array $expected Expected.
	 */
	public function test_parse_url($url, $component, $expected) {
		$this->assertEquals($expected, mb::parse_url($url, $component));
	}

	#[Test]
	#[DataProvider('data_parse_str')]
	/**
	 * ::parse_str()
	 *
	 * @param string $str String.
	 * @param array $expected Expected.
	 */
	public function test_parse_str($str, $expected) {
		mb::parse_str($str, $result);
		$this->assertEquals($expected, $result);
	}

	#[Test]
	#[DataProvider('data_str_split')]
	/**
	 * ::str_split()
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @param array $expected Expected.
	 */
	public function test_str_split($str, $split_length, $expected) {
		$this->assertEquals($expected, mb::str_split($str, $split_length));
	}

	#[Test]
	#[DataProvider('data_strlen')]
	/**
	 * ::strlen()
	 *
	 * @param string $str String.
	 * @param int $expected Expected.
	 */
	public function test_strlen($str, $expected) {
		$this->assertEquals($expected, mb::strlen($str));
	}

	#[Test]
	#[DataProvider('data_str_pad')]
	/**
	 * ::str_pad()
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @param string $expected Expected.
	 */
	public function test_str_pad($str, $pad_length, $pad_string, $pad_type, $expected) {
		$this->assertEquals($expected, mb::str_pad($str, $pad_length, $pad_string, $pad_type));
	}

	#[Test]
	#[DataProvider('data_strpos')]
	/**
	 * ::strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	public function test_strpos($haystack, $needle, $offset, $expected) {
		$this->assertSame($expected, mb::strpos($haystack, $needle, $offset));
	}

	#[Test]
	#[DataProvider('data_strrev')]
	/**
	 * ::strrev()
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	public function test_strrev($str, $expected) {
		$this->assertEquals($expected, mb::strrev($str));
	}

	#[Test]
	#[DataProvider('data_strrpos')]
	/**
	 * ::strrpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	public function test_strrpos($haystack, $needle, $offset, $expected) {
		$this->assertSame($expected, mb::strrpos($haystack, $needle, $offset));
	}

	#[Test]
	#[DataProvider('data_strtolower')]
	/**
	 * ::strtolower()
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @param string $expected Expected.
	 */
	public function test_strtolower($str, $strict, $expected) {
		$this->assertSame($expected, mb::strtolower($str, $strict));
	}

	#[Test]
	#[DataProvider('data_strtoupper')]
	/**
	 * ::strtoupper()
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @param string $expected Expected.
	 */
	public function test_strtoupper($str, $strict, $expected) {
		$this->assertSame($expected, mb::strtoupper($str, $strict));
	}

	#[Test]
	#[DataProvider('data_substr')]
	/**
	 * ::substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @param string $expected Expected.
	 */
	public function test_substr($str, $start, $length, $expected) {
		$this->assertEquals($expected, mb::substr($str, $start, $length));
	}

	#[Test]
	#[DataProvider('data_substr_count')]
	/**
	 * ::substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param string $expected Expected.
	 */
	public function test_substr_count($haystack, $needle, $expected) {
		$this->assertEquals($expected, mb::substr_count($haystack, $needle));
	}

	#[Test]
	#[DataProvider('data_trim')]
	/**
	 * ::trim()
	 *
	 * @param string $str String.
	 * @param string $expected Expected.
	 */
	public function test_trim($str, $expected) {
		$this->assertEquals($expected, mb::trim($str));
	}

	#[Test]
	#[DataProvider('data_ucfirst')]
	/**
	 * ::ucfirst()
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @param string $expected Expected.
	 */
	public function test_ucfirst($str, $strict, $expected) {
		$this->assertSame($expected, mb::ucfirst($str, $strict));
	}

	#[Test]
	#[DataProvider('data_ucwords')]
	/**
	 * ::ucwords()
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @param string $expected Expected.
	 */
	public function test_ucwords($str, $strict, $expected) {
		$this->assertSame($expected, mb::ucwords($str, $strict));
	}

	#[Test]
	#[DataProvider('data_wordwrap')]
	/**
	 * ::wordwrap()
	 *
	 * @param string $str String.
	 * @param int $width Width.
	 * @param string $break Break.
	 * @param bool $cut Cut.
	 * @param string $expected Expected.
	 */
	public function test_wordwrap($str, $width, $break, $cut, $expected) {
		$this->assertEquals($expected, mb::wordwrap($str, $width, $break, $cut));
	}

	// ----------------------------------------------------------------- end tests



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data for ::parse_url()
	 *
	 * @return array Data.
	 */
	static function data_parse_url() {
		$smiley_host = \function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'http://☺.com',
				\PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'//☺.com',
				\PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'☺.com',
				\PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'google.com',
				\PHP_URL_HOST,
				'google.com',
			),
			array(
				'//google.com',
				\PHP_URL_HOST,
				'google.com',
			),
			array(
				'http://google.com',
				\PHP_URL_HOST,
				'google.com',
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				\PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]',
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				\PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]',
			),
			array(
				'https://foo.bar/apples',
				-1,
				array(
					'scheme'=>'https',
					'host'=>'foo.bar',
					'path'=>'/apples',
				),
			),
		);
	}

	/**
	 * Data for ::parse_str()
	 *
	 * @return array Data.
	 */
	static function data_parse_str() {
		return array(
			array(
				'foo=BjöRk&bar=Ⅷ loVes',
				array(
					'foo'=>'BjöRk',
					'bar'=>'Ⅷ loVes',
				),
			),
			array(
				'Björk',
				array('Björk'=>''),
			),
			array(
				'',
				array(),
			),
		);
	}

	/**
	 * Data for ::str_split()
	 *
	 * @return array Data.
	 */
	static function data_str_split() {
		return array(
			array(
				'Björk',
				1,
				array('B', 'j', 'ö', 'r', 'k'),
			),
			array(
				'Björk',
				3,
				array('Bjö', 'rk'),
			),
		);
	}

	/**
	 * Data for ::strlen()
	 *
	 * @return array Data.
	 */
	static function data_strlen() {
		return array(
			array(
				'Björk',
				5,
			),
			array(
				'Happy Days',
				10,
			),
		);
	}

	/**
	 * Data for ::str_pad()
	 *
	 * @return array Data.
	 */
	static function data_str_pad() {
		return array(
			array(
				'Björk',
				10,
				'~',
				\STR_PAD_LEFT,
				'~~~~~Björk',
			),
			array(
				'Björk',
				10,
				'~',
				\STR_PAD_BOTH,
				'~~~Björk~~',
			),
			array(
				'Björk',
				10,
				'~',
				\STR_PAD_RIGHT,
				'Björk~~~~~',
			),
			array(
				'Björk',
				3,
				'~',
				\STR_PAD_RIGHT,
				'Björk',
			),
			array(
				'Björk',
				10,
				'',
				\STR_PAD_RIGHT,
				'Björk',
			),
		);
	}

	/**
	 * Data for ::strpos()
	 *
	 * @return array Data.
	 */
	static function data_strpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				3,
			),
			array(
				'Björk Björk',
				'ö',
				0,
				2,
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8,
			),
			array(
				'Björk Björk',
				'E',
				0,
				false,
			),
		);
	}

	/**
	 * Data for ::strrev()
	 *
	 * @return array Data.
	 */
	static function data_strrev() {
		return array(
			array(
				'Björk',
				'kröjB',
			),
			array(
				"Hello-World\n",
				"\ndlroW-olleH",
			),
		);
	}

	/**
	 * Data for ::strrpos()
	 *
	 * @return array Data.
	 */
	static function data_strrpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				9,
			),
			array(
				'Björk Björk',
				'ö',
				0,
				8,
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8,
			),
			array(
				'Björk Björk',
				'E',
				0,
				false,
			),
		);
	}

	/**
	 * Data for ::strtolower()
	 *
	 * @return array Data.
	 */
	static function data_strtolower() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				false,
				'queen björk ⅷ loves 3 apples.',
			),
			array(
				"Hello-world\n",
				false,
				"hello-world\n",
			),
			array(
				array("Hello-world\n"),
				false,
				array("hello-world\n"),
			),
			array(
				13,
				false,
				'13',
			),
			array(
				13,
				true,
				13,
			),
			array(
				array(13, 'HAPPY'),
				false,
				array('13', 'happy'),
			),
			array(
				array(13, 'HAPPY'),
				true,
				array(13, 'happy'),
			),
		);
	}

	/**
	 * Data for ::strtoupper()
	 *
	 * @return array Data.
	 */
	static function data_strtoupper() {
		return array(
			array(
				'THE lazY Rex ⅸ eAtS f00d.',
				false,
				'THE LAZY REX Ⅸ EATS F00D.',
			),
			array(
				"Hello-world\n",
				false,
				"HELLO-WORLD\n",
			),
			array(
				array("Hello-world\n"),
				false,
				array("HELLO-WORLD\n"),
			),
			array(
				13,
				false,
				'13',
			),
			array(
				13,
				true,
				13,
			),
			array(
				array(13, 'happy'),
				false,
				array('13', 'HAPPY'),
			),
			array(
				array(13, 'happy'),
				true,
				array(13, 'HAPPY'),
			),
		);
	}

	/**
	 * Data for ::substr()
	 *
	 * @return array Data.
	 */
	static function data_substr() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				0,
				11,
				'quEen BjöRk',
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				6,
				null,
				'BjöRk Ⅷ loVes 3 aPplEs.',
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				-7,
				null,
				'aPplEs.',
			),
		);
	}

	/**
	 * Data for ::substr_count()
	 *
	 * @return array Data.
	 */
	static function data_substr_count() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'BjöRk',
				1,
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'töast',
				0,
			),
			array(
				"Hello\nWorld\n",
				"\n",
				2,
			),
		);
	}

	/**
	 * Data for ::trim()
	 *
	 * @return array Data.
	 */
	static function data_trim() {
		return array(
			array(
				' 	test ',
				'test',
			),
			array(
				"\ntöast",
				'töast',
			),
			array(
				\chr(0xA0) . ' test' . \chr(0xA0),
				'test',
			),
			array(
				array("\ntöast"),
				array('töast'),
			),
		);
	}

	/**
	 * Data for ::ucfirst()
	 *
	 * @return array Data.
	 */
	static function data_ucfirst() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				false,
				'QuEen BjöRk Ⅷ loVes 3 aPplEs.',
			),
			array(
				'hello-world',
				false,
				'Hello-world',
			),
			array(
				array('hello-world'),
				false,
				array('Hello-world'),
			),
			array(
				13,
				false,
				'13',
			),
			array(
				13,
				true,
				13,
			),
			array(
				array(13, 'happy place'),
				false,
				array('13', 'Happy place'),
			),
			array(
				array(13, 'happy place'),
				true,
				array(13, 'Happy place'),
			),
		);
	}

	/**
	 * Data for ::ucwords()
	 *
	 * @return array Data.
	 */
	static function data_ucwords() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				false,
				'QuEen BjöRk Ⅷ LoVes 3 APplEs.',
			),
			array(
				'hello-world',
				false,
				'Hello-World',
			),
			array(
				array('hello-world'),
				false,
				array('Hello-World'),
			),
			array(
				13,
				false,
				'13',
			),
			array(
				13,
				true,
				13,
			),
			array(
				array(13, 'happy place'),
				false,
				array('13', 'Happy Place'),
			),
			array(
				array(13, 'happy place'),
				true,
				array(13, 'Happy Place'),
			),
		);
	}

	/**
	 * Data for ::wordwrap()
	 *
	 * @return array Data.
	 */
	static function data_wordwrap() {
		return array(
			array(
				'Björk',
				2,
				"\n",
				false,
				'Björk',
			),
			array(
				'Björk',
				2,
				"\n",
				true,
				"Bj\nör\nk",
			),
			array(
				"Björk's new album is an action-packed thrill-ride—to those with taste.",
				35,
				"\n",
				false,
				"Björk's new album is an action-\npacked thrill-ride—to those with\ntaste.",
			),
			array(
				"Björk's new album is an action-packed thrill-ride—to those with taste.",
				35,
				"\n",
				true,
				"Björk's new album is an action-\npacked thrill-ride—to those with\ntaste.",
			),
			array(
				"Björk's dress is attention-getting.",
				15,
				"\n",
				true,
				"Björk's dress\nis attention-\ngetting.",
			),
			array(
				'Visit https://blobfolio.com for more information.',
				10,
				"\n",
				false,
				"Visit\nhttps://blobfolio.com\nfor more\ninformation.",
			),
			array(
				'Visit https://blobfolio.com for more information.',
				10,
				"\n",
				true,
				"Visit\nhttps://bl\nobfolio.co\nm for more\ninformatio\nn.",
			),
		);
	}

	// ----------------------------------------------------------------- end data
}


