<?php
/**
 * Multibyte tests.
 *
 * PHPUnit tests for \blobfolio\common\mb.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class mb_tests extends \PHPUnit\Framework\TestCase {

	/**
	 * ::parse_url()
	 *
	 * @return void Nothing.
	 */
	function test_parse_url() {
		$things = array(
			'http://☺.com',
			'//☺.com',
			'☺.com'
		);

		foreach ($things as $thing) {
			$host = \blobfolio\common\mb::parse_url($thing, PHP_URL_HOST);
			if (function_exists('idn_to_ascii')) {
				$this->assertEquals('xn--74h.com', $host);
			}
			else {
				$this->assertEquals('☺.com', $host);
			}
		}

		$thing = 'https://foo.bar/apples';
		$result = \blobfolio\common\mb::parse_url($thing);
		$this->assertEquals(
			array(
				'scheme'=>'https',
				'host'=>'foo.bar',
				'path'=>'/apples'
			),
			$result
		);

		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$result = \blobfolio\common\mb::parse_url($thing, PHP_URL_HOST);
		$this->assertEquals('[2600:3c00::f03c:91ff:feae:ff2]', $result);

		$thing = '[2600:3c00::f03c:91ff:feae:0ff2]';
		$result = \blobfolio\common\mb::parse_url($thing, PHP_URL_HOST);
		$this->assertEquals('[2600:3c00::f03c:91ff:feae:ff2]', $result);
	}

	/**
	 * ::parse_str()
	 *
	 * @return void Nothing.
	 */
	function test_parse_str() {
		$thing = 'foo=BjöRk&bar=Ⅷ loVes';
		\blobfolio\common\mb::parse_str($thing, $results);
		$this->assertEquals(array('foo'=>'BjöRk', 'bar'=>'Ⅷ loVes'), $results);
	}

	/**
	 * ::str_split()
	 *
	 * @return void Nothing.
	 */
	function test_str_split() {
		$thing = 'BjöRk';
		$this->assertEquals(array('B','j','ö','R','k'), \blobfolio\common\mb::str_split($thing));
		$this->assertEquals(array('Bjö','Rk'), \blobfolio\common\mb::str_split($thing, 3));
	}

	/**
	 * ::strlen()
	 *
	 * @return void Nothing.
	 */
	function test_strlen() {
		$thing = 'BjöRk';
		$this->assertEquals(5, \blobfolio\common\mb::strlen($thing));
	}

	/**
	 * ::str_pad()
	 *
	 * @return void Nothing.
	 */
	function test_str_pad() {
		$thing = 'BjöRk';

		$this->assertEquals('~~~~~BjöRk', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_LEFT));
		$this->assertEquals('~~~BjöRk~~', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_BOTH));
		$this->assertEquals('BjöRk~~~~~', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_RIGHT));
	}

	/**
	 * ::strpos()
	 *
	 * @return void Nothing.
	 */
	function test_strpos() {
		$thing = 'AöA';

		$this->assertEquals(false, false !== \blobfolio\common\mb::strpos($thing, 'E'));
		$this->assertEquals(0, \blobfolio\common\mb::strpos($thing, 'A'));
		$this->assertEquals(1, \blobfolio\common\mb::strpos($thing, 'ö'));
	}

	/**
	 * ::strrpos()
	 *
	 * @return void Nothing.
	 */
	function test_strrpos() {
		$thing = 'AöA';

		$this->assertEquals(false, false !== \blobfolio\common\mb::strrpos($thing, 'E'));
		$this->assertEquals(2, \blobfolio\common\mb::strrpos($thing, 'A'));
		$this->assertEquals(1, \blobfolio\common\mb::strrpos($thing, 'ö'));
	}

	/**
	 * ::strtolower()
	 *
	 * @return void Nothing.
	 */
	function test_strtolower() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('queen björk ⅷ loves 3 apples.', \blobfolio\common\mb::strtolower($thing));
	}

	/**
	 * ::strtoupper()
	 *
	 * @return void Nothing.
	 */
	function test_strtoupper() {
		$thing = 'THE lazY Rex ⅸ eAtS f00d.';

		$this->assertEquals('THE LAZY REX Ⅸ EATS F00D.', \blobfolio\common\mb::strtoupper($thing));
	}

	/**
	 * ::substr()
	 *
	 * @return void Nothing.
	 */
	function test_substr() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('quEen BjöRk', \blobfolio\common\mb::substr($thing, 0, 11));
		$this->assertEquals('BjöRk Ⅷ loVes 3 aPplEs.', \blobfolio\common\mb::substr($thing, 6));
		$this->assertEquals('aPplEs.', \blobfolio\common\mb::substr($thing, -7));
	}

	/**
	 * ::substr_count()
	 *
	 * @return void Nothing.
	 */
	function test_substr_count() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals(1, \blobfolio\common\mb::substr_count($thing, 'BjöRk'));
		$this->assertEquals(0, \blobfolio\common\mb::substr_count($thing, 'Nick'));
	}

	/**
	 * ::trim()
	 *
	 * @return void Nothing.
	 */
	function test_trim() {
		$things = array(
			array(
				'key'=>' 	test ',
				'value'=>'test'
			),
			array(
				'key'=>"\ntest",
				'value'=>'test'
			),
			array(
				'key'=>chr(0xA0) . ' test' . chr(0xA0),
				'value'=>'test'
			)
		);
		foreach ($things as $thing) {
			$this->assertEquals($thing['value'], \blobfolio\common\mb::trim($thing['key']));
		}
	}

	/**
	 * ::ucfirst()
	 *
	 * @return void Nothing.
	 */
	function test_ucfirst() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ loVes 3 aPplEs.', \blobfolio\common\mb::ucfirst($thing));
	}

	/**
	 * ::ucwords()
	 *
	 * @return void Nothing.
	 */
	function test_ucwords() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ LoVes 3 APplEs.', \blobfolio\common\mb::ucwords($thing));
	}
}


