<?php
/**
 * File tests.
 *
 * PHPUnit tests for \blobfolio\common\file.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class file_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * ::data_uri()
	 *
	 * @return void Nothing.
	 */
	function test_data_uri() {
		$svg = self::ASSETS . 'pi.svg';

		$data = \blobfolio\common\file::data_uri($svg);

		$this->assertEquals(true, false !== strpos($data, 'image/svg+xml'));

		$this->assertEquals(false, \blobfolio\common\file::data_uri('does_not_exist.txt'));
	}

	/**
	 * ::empty_dir()
	 *
	 * @return void Nothing.
	 */
	function test_empty_dir() {
		$this->assertEquals(false, \blobfolio\common\file::empty_dir(self::ASSETS));

		$new = self::ASSETS . 'empty';
		mkdir($new);
		$this->assertEquals(true, \blobfolio\common\file::empty_dir($new));
		rmdir($new);
	}

	/**
	 * ::leadingslash()
	 *
	 * @return void Nothing.
	 */
	function test_leadingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::leadingslash($thing));

		$thing = 'hello/there';
		$this->assertEquals('/hello/there', \blobfolio\common\file::leadingslash($thing));
	}

	/**
	 * ::path()
	 *
	 * @return void Nothing.
	 */
	function test_path() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::path($thing, false));
		$this->assertEquals(false, \blobfolio\common\file::path($thing, true));

		$thing = rtrim(self::ASSETS, '/');
		$this->assertEquals(self::ASSETS, \blobfolio\common\file::path($thing));
	}

	/**
	 * ::trailingslash()
	 *
	 * @return void Nothing.
	 */
	function test_trailingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing . '/', \blobfolio\common\file::trailingslash($thing));

		$thing = 'hello/there/';
		$this->assertEquals($thing, \blobfolio\common\file::trailingslash($thing));
	}

	/**
	 * ::unixslash()
	 *
	 * @return void Nothing.
	 */
	function test_unixslash() {

		$thing = 'C:\Windows\Fonts';
		$this->assertEquals('C:/Windows/Fonts', \blobfolio\common\file::unixslash($thing));

		$thing = '/path/./to/foobar';
		$this->assertEquals('/path/to/foobar', \blobfolio\common\file::unixslash($thing));
	}

	/**
	 * ::unleadingslash()
	 *
	 * @return void Nothing.
	 */
	function test_unleadingslash() {

		$thing = '/hello/there';
		$this->assertEquals('hello/there', \blobfolio\common\file::unleadingslash($thing));

		$thing = 'hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::unleadingslash($thing));
	}

	/**
	 * ::unparse_url()
	 *
	 * @return void Nothing.
	 */
	function test_unparse_url() {
		$things = array(
			'https://google.com/search?hello#foo'=>'https://google.com/search?hello#foo',
			'google.com/apples'=>'google.com/apples',
			'//☺.com'=>'https://xn--74h.com',
			'ftp://user:pass@ftp.com:123'=>'ftp://user:pass@ftp.com:123'
		);

		foreach($things as $k=>$v){
			$parsed = \blobfolio\common\mb::parse_url($k);
			$unparsed = \blobfolio\common\file::unparse_url($parsed);
			$this->assertEquals($v, $unparsed);
		}
	}

	/**
	 * ::untrailingslash()
	 *
	 * @return void Nothing.
	 */
	function test_untrailingslash() {

		$thing = '/hello/there';
		$this->assertEquals($thing, \blobfolio\common\file::untrailingslash($thing));

		$thing = 'hello/there/';
		$this->assertEquals('hello/there', \blobfolio\common\file::untrailingslash($thing));
	}
}


