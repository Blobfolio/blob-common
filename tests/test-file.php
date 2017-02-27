<?php
//---------------------------------------------------------------------
// file:: tests
//---------------------------------------------------------------------

class file_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';

	//-------------------------------------------------
	// file::data_uri()

	function test_data_uri() {
		$svg = self::ASSETS . 'pi.svg';

		$data = \blobfolio\common\file::data_uri($svg);

		$this->assertEquals(true, false !== strpos($data, 'image/svg+xml'));

		$this->assertEquals(false, \blobfolio\common\file::data_uri('does_not_exist.txt'));
	}

	//-------------------------------------------------
	// file::empty_dir()

	function test_empty_dir() {
		$this->assertEquals(false, \blobfolio\common\file::empty_dir(self::ASSETS));

		$new = self::ASSETS . 'empty';
		mkdir($new);
		$this->assertEquals(true, \blobfolio\common\file::empty_dir($new));
		rmdir($new);
	}

	//-------------------------------------------------
	// file::leadingslash()

	function test_leadingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::leadingslash($thing));

		$thing = 'hello/there';
		$this->assertEquals('/hello/there', \blobfolio\common\file::leadingslash($thing));
	}

	//-------------------------------------------------
	// file::path()

	function test_path() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::path($thing, false));
		$this->assertEquals(false, \blobfolio\common\file::path($thing, true));

		$thing = rtrim(self::ASSETS, '/');
		$this->assertEquals(self::ASSETS, \blobfolio\common\file::path($thing));
	}

	//-------------------------------------------------
	// file::trailingslash()

	function test_trailingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing . '/', \blobfolio\common\file::trailingslash($thing));

		$thing = 'hello/there/';
		$this->assertEquals($thing, \blobfolio\common\file::trailingslash($thing));
	}

	//-------------------------------------------------
	// file::unixslash()

	function test_unixslash() {

		$thing = 'C:\Windows\Fonts';
		$this->assertEquals('C:/Windows/Fonts', \blobfolio\common\file::unixslash($thing));

		$thing = '/path/./to/foobar';
		$this->assertEquals('/path/to/foobar', \blobfolio\common\file::unixslash($thing));
	}

	//-------------------------------------------------
	// file::unleadingslash()

	function test_unleadingslash() {

		$thing = '/hello/there';
		$this->assertEquals('hello/there', \blobfolio\common\file::unleadingslash($thing));

		$thing = 'hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::unleadingslash($thing));
	}

	//-------------------------------------------------
	// file::unparse_url()

	function test_unparse_url() {

		$thing = 'https://google.com/search';
		$parsed = parse_url($thing);
		$unparsed = \blobfolio\common\file::unparse_url($parsed);
		$this->assertEquals($thing, $unparsed);
	}

	//-------------------------------------------------
	// file::untrailingslash()

	function test_untrailingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::untrailingslash($thing));

		$thing = 'hello/there/';
		$this->assertEquals('hello/there', \blobfolio\common\file::untrailingslash($thing));
	}
}

?>