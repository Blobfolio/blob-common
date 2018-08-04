<?php
/**
 * Blobfolio\Domains
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class domains_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: Constructor
	 *
	 * @dataProvider data_constructor
	 *
	 * @param string $value Host.
	 * @param bool $www Strip www.
	 * @param array $unicode Expected Ascii.
	 * @param array $unicode Expected Unicode.
	 */
	function test_constructor(string $value, array $ascii, array $unicode) {
		$result = new \Blobfolio\Domains($value);

		// Check the ASCII versions.
		$this->assertSame($ascii, $result->getData(0));
		$this->assertSame($ascii['host'], $result->getHost(0));
		$this->assertSame($ascii['subdomain'], $result->getSubdomain(0));
		$this->assertSame($ascii['domain'], $result->getDomain(0));
		$this->assertSame($ascii['suffix'], $result->getSuffix(0));

		// Now check the Unicode.
		if (function_exists('idn_to_ascii')) {
			$this->assertSame($unicode, $result->getData(\Blobfolio\Blobfolio::UNICODE));
			$this->assertSame($unicode['host'], $result->getHost(\Blobfolio\Blobfolio::UNICODE));
			$this->assertSame($unicode['subdomain'], $result->getSubdomain(\Blobfolio\Blobfolio::UNICODE));
			$this->assertSame($unicode['domain'], $result->getDomain(\Blobfolio\Blobfolio::UNICODE));
			$this->assertSame($unicode['suffix'], $result->getSuffix(\Blobfolio\Blobfolio::UNICODE));
		}

		// And check toString.
		$this->assertSame($ascii['host'], (string) $result);
	}

	/**
	 * Test: stripWww
	 *
	 * @dataProvider data_stripWww
	 *
	 * @param string $value Host.
	 * @param string $before Before.
	 * @param string $after After.
	 */
	function test_stripWww(string $value, $before, $after) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($before, $domain->getData());

		$domain->stripWww();
		$this->assertSame($after, $domain->getData());

		// Run it a second time to make sure that doesn't break
		// anything.
		$domain->stripWww();
		$this->assertSame($after, $domain->getData());
	}

	/**
	 * Test: addWww
	 *
	 * @dataProvider data_addWww
	 *
	 * @param string $value Host.
	 * @param string $before Before.
	 * @param string $after After.
	 */
	function test_addWww(string $value, $before, $after) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($before, $domain->getData());

		$domain->addWww();
		$this->assertSame($after, $domain->getData());

		// Run it a second time to make sure that doesn't break
		// anything.
		$domain->addWww();
		$this->assertSame($after, $domain->getData());
	}

	/**
	 * Test: hasDns
	 *
	 * @dataProvider data_hasDns
	 *
	 * @param string $value Host.
	 * @param bool $expected Expected.
	 */
	function test_hasDns(string $value, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->hasDns());
	}

	/**
	 * Test: isValid
	 *
	 * @dataProvider data_isValid
	 *
	 * @param string $value Host.
	 * @param bool $dns Dns.
	 * @param bool $expected Expected.
	 */
	function test_isValid(string $value, bool $dns, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->isValid($dns));
	}

	/**
	 * Test: isAscii
	 *
	 * @dataProvider data_isAscii
	 *
	 * @param string $value Host.
	 * @param bool $expected Expected.
	 */
	function test_isAscii(string $value, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->isAscii());
	}

	/**
	 * Test: isFqdn
	 *
	 * @dataProvider data_isFqdn
	 *
	 * @param string $value Host.
	 * @param bool $expected Expected.
	 */
	function test_isFqdn(string $value, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->isFqdn());
	}

	/**
	 * Test: isIp
	 *
	 * @dataProvider data_isIp
	 *
	 * @param string $value Host.
	 * @param bool $restricted Restricted.
	 * @param bool $expected Expected.
	 */
	function test_isIp(string $value, bool $restricted, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->isIp($restricted));
	}

	/**
	 * Test: isUnicode
	 *
	 * @dataProvider data_isUnicode
	 *
	 * @param string $value Host.
	 * @param bool $expected Expected.
	 */
	function test_isUnicode(string $value, bool $expected) {
		$domain = new \Blobfolio\Domains($value);

		$this->assertSame($expected, $domain->isUnicode());
	}

	/**
	 * Test: parseUrl
	 *
	 * @dataProvider data_parseUrl
	 *
	 * @param mixed $value Value.
	 * @param mixed $args Args.
	 * @param mixed $expected Expected.
	 */
	function test_parseUrl($value, $args, $expected) {
		$result = \Blobfolio\Domains::parseUrl($value, $args);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: unparseUrl
	 *
	 * @dataProvider data_unparseUrl
	 *
	 * @param array $value Value.
	 * @param string $expected Expected.
	 */
	function test_unparseUrl(array $value, string $expected) {
		$result = \Blobfolio\Domains::unparseUrl($value);
		$this->assertSame($expected, $result);
	}

	/**
	 * Test: niceDomain
	 *
	 * @dataProvider data_niceDomain
	 *
	 * @param string $value Domain.
	 * @param bool $unicode Unicode.
	 * @param string $expected Expected.
	 */
	function test_niceDomain(string $value, int $flags, string $expected) {
		$result = \Blobfolio\Domains::niceDomain($value, $flags);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceEmail
	 *
	 * @dataProvider data_niceEmail
	 *
	 * @param string $value Email.
	 * @param string $expected Expected.
	 */
	function test_niceEmail(string $value, string $expected) {
		$result = \Blobfolio\Domains::niceEmail($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceHost
	 *
	 * @dataProvider data_niceHost
	 *
	 * @param string $value Domain.
	 * @param bool $www Strip www.
	 * @param bool $unicode Unicode.
	 * @param string|bool $expected Expected.
	 */
	function test_niceHost(string $value, int $flags, $expected) {
		$result = \Blobfolio\Domains::niceHost($value, $flags);

		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: niceUrl
	 *
	 * @dataProvider data_niceUrl
	 *
	 * @param string $value Email.
	 * @param string $expected Expected.
	 */
	function test_niceUrl(string $value, string $expected) {
		$result = \Blobfolio\Domains::niceUrl($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: parseUrl
	 *
	 * @return array Values.
	 */
	function data_parseUrl() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'http://☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'//☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'☺.com',
				PHP_URL_HOST,
				$smiley_host,
			),
			array(
				'google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'//google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'http://google.com',
				PHP_URL_HOST,
				'google.com',
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				PHP_URL_HOST,
				'[2600:3c00::f03c:91ff:feae:ff2]',
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				PHP_URL_HOST,
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
			array(
				'foobar.com',
				PHP_URL_SCHEME,
				null,
			),
			array(
				'https://foobar.com',
				PHP_URL_SCHEME,
				'https',
			),
		);
	}

	/**
	 * Data: unparseUrl
	 *
	 * @return array Values.
	 */
	public function data_unparseUrl() {
		return array(
			array(
				array(
					'scheme'=>'https',
					'host'=>'google.com',
					'path'=>'/search',
					'query'=>'hello',
					'fragment'=>'foo',
				),
				'https://google.com/search?hello#foo',
			),
			array(
				array(
					'host'=>'google.com',
					'path'=>'apples',
				),
				'google.com/apples',
			),
			array(
				array(
					'host'=>'☺.com',
					'fragment'=>'smile',
				),
				'☺.com#smile',
			),
			array(
				array(
					'host'=>'xn--74h.com',
					'fragment'=>'smile',
				),
				'xn--74h.com#smile',
			),
			array(
				array(
					'scheme'=>'ftp',
					'host'=>'ftp.com',
					'port'=>'123',
					'user'=>'user',
					'pass'=>'pass',
				),
				'ftp://user:pass@ftp.com:123',
			),
		);
	}

	/**
	 * Data: niceDomain
	 *
	 * @return array Values.
	 */
	public function data_niceDomain() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				0,
				'google.com',
			),
			array(
				'www.Google.com',
				0,
				'google.com',
			),
			array(
				'☺.com',
				\Blobfolio\Blobfolio::UNICODE,
				'☺.com',
			),
			array(
				'50.116.18.174',
				0,
				'',
			),
			array(
				'//☺.com',
				0,
				$smiley_host,
			),
		);
	}

	/**
	 * Data: niceEmail
	 *
	 * @return array Values.
	 */
	public function data_niceEmail() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'Hello@Blo"bfolio.Com',
				'hello@blobfolio.com',
			),
			array(
				'helo@blobfolio',
				'',
			),
			array(
				'hello',
				'',
			),
			array(
				'hello@☺.com',
				"hello@$smiley_host",
			),
			array(
				'hello+me@blobfolio.com',
				'hello+me@blobfolio.com',
			),
			array(
				' .hello(comment)+me@blobfolio.com',
				'hello+me@blobfolio.com',
			),
		);
	}

	/**
	 * Data: niceHost
	 *
	 * @return array Values.
	 */
	public function data_niceHost() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'https://www.Google.com',
				\Blobfolio\Blobfolio::HOST_STRIP_WWW,
				'google.com',
			),
			array(
				'www.Google.com',
				\Blobfolio\Blobfolio::HOST_STRIP_WWW,
				'google.com',
			),
			array(
				'www.☺.com',
				\Blobfolio\Blobfolio::HOST_STRIP_WWW | \Blobfolio\Blobfolio::UNICODE,
				'☺.com',
			),
			array(
				'http://www.☺.com',
				\Blobfolio\Blobfolio::UNICODE,
				'www.☺.com',
			),
			array(
				'50.116.18.174',
				0,
				'50.116.18.174',
			),
			array(
				'//☺.com',
				0,
				$smiley_host,
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				0,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'localhost',
				\Blobfolio\Blobfolio::HOST_STRIP_WWW | \Blobfolio\Blobfolio::UNICODE,
				'localhost',
			),
		);
	}

	/**
	 * Data: niceUrl
	 *
	 * @return array Values.
	 */
	public function data_niceUrl() {
		$smiley_host = function_exists('idn_to_ascii') ? 'xn--74h.com' : '☺.com';

		return array(
			array(
				'google.com',
				'',
			),
			array(
				'//google.com',
				'https://google.com',
			),
			array(
				'HTTP://google.com',
				'http://google.com',
			),
			array(
				'http://user:pass@domain.com/foobar?hello#there',
				'http://user:pass@domain.com/foobar?hello#there',
			),
			array(
				'//www.☺.com/hello?awesome',
				'https://www.' . $smiley_host . '/hello?awesome',
			),
		);
	}

	/**
	 * Data: constructor
	 *
	 * @return array Values.
	 */
	public function data_constructor() {
		return array(
			array(
				'http://www.☺.com',
				array(
					'host'=>'www.xn--74h.com',
					'subdomain'=>"www",
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
				array(
					'host'=>'www.☺.com',
					'subdomain'=>"www",
					'domain'=>'☺',
					'suffix'=>'com',
				),
			),
			array(
				'http://☺.com',
				array(
					'host'=>'xn--74h.com',
					'subdomain'=>null,
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
				array(
					'host'=>'☺.com',
					'subdomain'=>null,
					'domain'=>'☺',
					'suffix'=>'com',
				),
			),
			array(
				'localhost:2020/',
				array(
					'host'=>'localhost',
					'subdomain'=>null,
					'domain'=>'localhost',
					'suffix'=>null,
				),
				array(
					'host'=>'localhost',
					'subdomain'=>null,
					'domain'=>'localhost',
					'suffix'=>null,
				),
			),
			array(
				'http://josh:here@[2600:3c00::f03c:91ff:feae:0ff2]:443/foobar',
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
			),
		);
	}

	/**
	 * Data: stripWww
	 *
	 * @return array Values.
	 */
	public function data_stripWww() {
		return array(
			array(
				'www.example.sch.uk',
				array(
					'host'=>'www.example.sch.uk',
					'subdomain'=>null,
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
				array(
					'host'=>'www.example.sch.uk',
					'subdomain'=>null,
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
			),
			array(
				'www.www.example.sch.uk',
				array(
					'host'=>'www.www.example.sch.uk',
					'subdomain'=>"www",
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
				array(
					'host'=>'www.example.sch.uk',
					'subdomain'=>null,
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
			),
			array(
				'www.apt.blobfolio.com',
				array(
					'host'=>'www.apt.blobfolio.com',
					'subdomain'=>'www.apt',
					'domain'=>'blobfolio',
					'suffix'=>'com',
				),
				array(
					'host'=>'apt.blobfolio.com',
					'subdomain'=>"apt",
					'domain'=>'blobfolio',
					'suffix'=>'com',
				),
			),
			array(
				'www.google.com',
				array(
					'host'=>'www.google.com',
					'subdomain'=>'www',
					'domain'=>'google',
					'suffix'=>'com',
				),
				array(
					'host'=>'google.com',
					'subdomain'=>null,
					'domain'=>'google',
					'suffix'=>'com',
				),
			),
			array(
				'http://www.☺.com',
				array(
					'host'=>'www.xn--74h.com',
					'subdomain'=>"www",
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
				array(
					'host'=>'xn--74h.com',
					'subdomain'=>null,
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
			),
			array(
				'www.localhost',
				array(
					'host'=>'www.localhost',
					'subdomain'=>"www",
					'domain'=>'localhost',
					'suffix'=>null,
				),
				array(
					'host'=>'localhost',
					'subdomain'=>null,
					'domain'=>'localhost',
					'suffix'=>null,
				),
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
			),
		);
	}

	/**
	 * Data: addWww
	 *
	 * @return array Values.
	 */
	public function data_addWww() {
		return array(
			array(
				'www.example.sch.uk',
				array(
					'host'=>'www.example.sch.uk',
					'subdomain'=>null,
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
				array(
					'host'=>'www.www.example.sch.uk',
					'subdomain'=>"www",
					'domain'=>'www',
					'suffix'=>'example.sch.uk'
				),
			),
			array(
				'apt.blobfolio.com',
				array(
					'host'=>'apt.blobfolio.com',
					'subdomain'=>"apt",
					'domain'=>'blobfolio',
					'suffix'=>'com',
				),
				array(
					'host'=>'www.apt.blobfolio.com',
					'subdomain'=>'www.apt',
					'domain'=>'blobfolio',
					'suffix'=>'com',
				),
			),
			array(
				'google.com',
				array(
					'host'=>'google.com',
					'subdomain'=>null,
					'domain'=>'google',
					'suffix'=>'com',
				),
				array(
					'host'=>'www.google.com',
					'subdomain'=>'www',
					'domain'=>'google',
					'suffix'=>'com',
				),
			),
			array(
				'http://☺.com',
				array(
					'host'=>'xn--74h.com',
					'subdomain'=>null,
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
				array(
					'host'=>'www.xn--74h.com',
					'subdomain'=>"www",
					'domain'=>'xn--74h',
					'suffix'=>'com',
				),
			),
			array(
				'localhost',
				array(
					'host'=>'localhost',
					'subdomain'=>null,
					'domain'=>'localhost',
					'suffix'=>null,
				),
				array(
					'host'=>'www.localhost',
					'subdomain'=>"www",
					'domain'=>'localhost',
					'suffix'=>null,
				),
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
				array(
					'host'=>'2600:3c00::f03c:91ff:feae:ff2',
					'subdomain'=>null,
					'domain'=>'2600:3c00::f03c:91ff:feae:ff2',
					'suffix'=>null,
				),
			),
		);
	}

	/**
	 * Data: hasDns
	 *
	 * @return array Values.
	 */
	public function data_hasDns() {
		return array(
			array(
				'blobfolio.com',
				true,
			),
			array(
				'asdfasfd.blobfolio.com',
				false,
			),
			array(
				'127.0.0.1',
				false,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				true,
			),
		);
	}

	/**
	 * Data: isValid
	 *
	 * @return array Values.
	 */
	public function data_isValid() {
		return array(
			array(
				'example.com',
				false,
				true,
			),
			array(
				'com',
				false,
				false,
			),
			array(
				'blobfolio.com',
				true,
				true,
			),
			array(
				'127.0.0.1',
				false,
				true,
			),
			array(
				'127.0.0.1',
				true,
				false,
			),
			array(
				'☺.com',
				true,
				true,
			),
		);
	}

	/**
	 * Data: isAscii
	 *
	 * @return array Values.
	 */
	public function data_isAscii() {
		return array(
			array(
				'example.com',
				true,
			),
			array(
				'☺.com',
				false,
			),
			array(
				'xn--74h.com',
				false,
			),
			array(
				'127.0.0.1',
				true,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				true,
			),
			array(
				'google.com',
				true,
			),
			array(
				'com',
				false,
			),
		);
	}

	/**
	 * Data: isFqdn
	 *
	 * @return array Values.
	 */
	public function data_isFqdn() {
		return array(
			array(
				'example.com',
				true,
			),
			array(
				'com',
				false,
			),
			array(
				'localhost',
				false,
			),
			array(
				'127.0.0.1',
				false,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				true,
			),
		);
	}

	/**
	 * Data: isIp
	 *
	 * @return array Values.
	 */
	public function data_isIp() {
		return array(
			array(
				'example.com',
				false,
				false,
			),
			array(
				'example.com',
				true,
				false,
			),
			array(
				'127.0.0.1',
				true,
				true,
			),
			array(
				'127.0.0.1',
				false,
				false,
			),
			array(
				'127.0.0.1',
				true,
				true,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				false,
				true,
			),
		);
	}

	/**
	 * Data: isUnicode
	 *
	 * @return array Values.
	 */
	public function data_isUnicode() {
		return array(
			array(
				'blobfolio.com',
				false,
			),
			array(
				'☺.com',
				true,
			),
			array(
				'xn--74h.com',
				true,
			),
			array(
				'com',
				false,
			),
			array(
				'googlE.com',
				false,
			),
			array(
				'127.0.0.1',
				false,
			),
		);
	}
}
