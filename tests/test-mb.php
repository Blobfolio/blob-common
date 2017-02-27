<?php
//---------------------------------------------------------------------
// mb:: tests
//---------------------------------------------------------------------

class mb_tests extends \PHPUnit\Framework\TestCase {

	//-------------------------------------------------
	// mb::parse_str()

	function test_parse_str() {
		$thing = 'foo=BjöRk&bar=Ⅷ loVes';
		\blobfolio\common\mb::parse_str($thing, $results);
		$this->assertEquals(array('foo'=>'BjöRk', 'bar'=>'Ⅷ loVes'), $results);
	}

	//-------------------------------------------------
	// mb::str_split()

	function test_str_split() {
		$thing = 'BjöRk';
		$this->assertEquals(array('B','j','ö','R','k'), \blobfolio\common\mb::str_split($thing));
		$this->assertEquals(array('Bjö','Rk'), \blobfolio\common\mb::str_split($thing, 3));
	}

	//-------------------------------------------------
	// mb::strlen()

	function test_strlen() {
		$thing = 'BjöRk';
		$this->assertEquals(5, \blobfolio\common\mb::strlen($thing));
	}

	//-------------------------------------------------
	// mb::strpad()

	function test_strpad() {
		$thing = 'BjöRk';

		$this->assertEquals('~~~~~BjöRk', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_LEFT));
		$this->assertEquals('~~~BjöRk~~', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_BOTH));
		$this->assertEquals('BjöRk~~~~~', \blobfolio\common\mb::str_pad($thing, 10, '~', STR_PAD_RIGHT));
	}

	//-------------------------------------------------
	// mb::strpos()

	function test_strpos() {
		$thing = 'AöA';

		$this->assertEquals(false, false !== \blobfolio\common\mb::strpos($thing, 'E'));
		$this->assertEquals(0, \blobfolio\common\mb::strpos($thing, 'A'));
		$this->assertEquals(1, \blobfolio\common\mb::strpos($thing, 'ö'));
	}

	//-------------------------------------------------
	// mb::strrpos()

	function test_strrpos() {
		$thing = 'AöA';

		$this->assertEquals(false, false !== \blobfolio\common\mb::strrpos($thing, 'E'));
		$this->assertEquals(2, \blobfolio\common\mb::strrpos($thing, 'A'));
		$this->assertEquals(1, \blobfolio\common\mb::strrpos($thing, 'ö'));
	}

	//-------------------------------------------------
	// mb::strtolower()

	function test_strtolower() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('queen björk ⅷ loves 3 apples.', \blobfolio\common\mb::strtolower($thing));
	}

	//-------------------------------------------------
	// mb::strtoupper()

	function test_strtoupper() {
		$thing = 'THE lazY Rex ⅸ eAtS f00d.';

		$this->assertEquals('THE LAZY REX Ⅸ EATS F00D.', \blobfolio\common\mb::strtoupper($thing));
	}

	//-------------------------------------------------
	// mb::substr()

	function test_substr() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('quEen BjöRk', \blobfolio\common\mb::substr($thing, 0, 11));
		$this->assertEquals('BjöRk Ⅷ loVes 3 aPplEs.', \blobfolio\common\mb::substr($thing, 6));
		$this->assertEquals('aPplEs.', \blobfolio\common\mb::substr($thing, -7));
	}

	//-------------------------------------------------
	// mb::substr_count()

	function test_substr_count() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals(1, \blobfolio\common\mb::substr_count($thing, 'BjöRk'));
		$this->assertEquals(0, \blobfolio\common\mb::substr_count($thing, 'Nick'));
	}

	//-------------------------------------------------
	// mb::ucfirst()

	function test_ucfirst() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ loVes 3 aPplEs.', \blobfolio\common\mb::ucfirst($thing));
	}

	//-------------------------------------------------
	// mb::ucwords()

	function test_ucwords() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ LoVes 3 APplEs.', \blobfolio\common\mb::ucwords($thing));
	}
}

?>