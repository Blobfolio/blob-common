<?php
//---------------------------------------------------------------------
// cast:: tests
//---------------------------------------------------------------------

class cast_tests extends \PHPUnit\Framework\TestCase {

	//-------------------------------------------------
	// cast::array()

	function test_array() {
		$thing = 'string';
		$this->assertEquals(array('string'), \blobfolio\common\cast::array($thing));

		$this->assertEquals('array', gettype(\blobfolio\common\cast::array($thing)));

		$thing = array('string');
		$this->assertEquals(array('string'), \blobfolio\common\cast::array($thing));

		$thing = null;
		$this->assertEquals(array(), \blobfolio\common\cast::array($thing));
	}

	//-------------------------------------------------
	// cast::array_type()

	function test_array_type() {
		$thing = 'string';
		$this->assertEquals(false, \blobfolio\common\cast::array_type($thing));

		$thing = array();
		$this->assertEquals(false, \blobfolio\common\cast::array_type($thing));

		$thing = array('string','thing');
		$this->assertEquals('sequential', \blobfolio\common\cast::array_type($thing));

		$thing = array('foo'=>'bar');
		$this->assertEquals('associative', \blobfolio\common\cast::array_type($thing));

		$thing = array('apples','bananas','pears');
		unset($thing[1]);
		$this->assertEquals('indexed', \blobfolio\common\cast::array_type($thing));
	}

	//-------------------------------------------------
	// cast::bool()

	function test_bool() {
		$thing = 'string';
		$this->assertEquals(true, \blobfolio\common\cast::bool($thing));

		$this->assertEquals('boolean', gettype(\blobfolio\common\cast::bool($thing)));

		$thing = 'off';
		$this->assertEquals(false, \blobfolio\common\cast::bool($thing));

		$thing = 'FALSE';
		$this->assertEquals(false, \blobfolio\common\cast::bool($thing));

		$thing = 1;
		$this->assertEquals(true, \blobfolio\common\cast::bool($thing));

		$thing = array(1, 'Off', false);
		$this->assertEquals(array(true, false, false), \blobfolio\common\cast::bool($thing));

		$this->assertEquals(true, \blobfolio\common\cast::bool($thing, true));
	}

	//-------------------------------------------------
	// cast::float()

	function test_float() {
		$thing = 'string';
		$this->assertEquals(0.0, \blobfolio\common\cast::float($thing));

		$this->assertEquals('double', gettype(\blobfolio\common\cast::float($thing)));

		$thing = '$2.50';
		$this->assertEquals(2.5, \blobfolio\common\cast::float($thing));

		$thing = 1;
		$this->assertEquals(1.0, \blobfolio\common\cast::float($thing));

		$thing = '50%';
		$this->assertEquals(.5, \blobfolio\common\cast::float($thing));

		$thing = '67¢';
		$this->assertEquals(.67, \blobfolio\common\cast::float($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1.0, 2.5, 0.0), \blobfolio\common\cast::float($thing));

		$this->assertEquals(0, \blobfolio\common\cast::float($thing, true));
	}

	//-------------------------------------------------
	// cast::int()

	function test_int() {
		$thing = 'string';
		$this->assertEquals(0, \blobfolio\common\cast::int($thing));

		$this->assertEquals('integer', gettype(\blobfolio\common\cast::int($thing)));

		$thing = 2.5;
		$this->assertEquals(2, \blobfolio\common\cast::int($thing));

		$thing = '33';
		$this->assertEquals(33, \blobfolio\common\cast::int($thing));

		$thing = 'on';
		$this->assertEquals(1, \blobfolio\common\cast::int($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1, 2, 0), \blobfolio\common\cast::int($thing));

		$this->assertEquals(0, \blobfolio\common\cast::int($thing, true));
	}

	//-------------------------------------------------
	// cast::string()

	function test_string() {
		$thing = 'string';
		$this->assertEquals('string', \blobfolio\common\cast::string($thing));

		$this->assertEquals('string', gettype(\blobfolio\common\cast::string($thing)));

		$thing = 2.5;
		$this->assertEquals('2.5', \blobfolio\common\cast::string($thing));

		$thing = false;
		$this->assertEquals('', \blobfolio\common\cast::string($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array('1', '2.5', ''), \blobfolio\common\cast::string($thing));

		$this->assertEquals('', \blobfolio\common\cast::string($thing, true));
	}

	//-------------------------------------------------
	// cast::to_type()

	function test_to_type() {
		$thing = array('false', 2.5, true, 1);

		$this->assertEquals(array('false', 2.5, true, 1), \blobfolio\common\cast::to_type($thing, 'array'));

		$this->assertEquals(array(false, true, true, true), \blobfolio\common\cast::to_type($thing, 'bool'));
		$this->assertEquals(array(false, true, true, true), \blobfolio\common\cast::to_type($thing, 'boolean'));

		$this->assertEquals(array(0, 2.5, 1.0, 1.0), \blobfolio\common\cast::to_type($thing, 'float'));
		$this->assertEquals(array(0, 2.5, 1.0, 1.0), \blobfolio\common\cast::to_type($thing, 'double'));

		$this->assertEquals(array(0, 2, 1, 1), \blobfolio\common\cast::to_type($thing, 'integer'));
		$this->assertEquals(array(0, 2, 1, 1), \blobfolio\common\cast::to_type($thing, 'int'));

		$this->assertEquals(array('false', '2.5', '1', '1'), \blobfolio\common\cast::to_type($thing, 'string'));

		$this->assertEquals(true, \blobfolio\common\cast::to_type($thing, 'bool', true));
		$this->assertEquals(0.0, \blobfolio\common\cast::to_type($thing, 'float', true));
		$this->assertEquals(0, \blobfolio\common\cast::to_type($thing, 'integer', true));
		$this->assertEquals('', \blobfolio\common\cast::to_type($thing, 'string', true));
	}
}

?>