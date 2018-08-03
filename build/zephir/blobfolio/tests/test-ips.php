<?php
/**
 * Blobfolio\Ips
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class ips_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: niceIp
	 *
	 * @dataProvider data_niceIp
	 *
	 * @param string $value Value.
	 * @param int $flags Flags.
	 * @param string $expected.
	 */
	function test_niceIp(string $value, int $flags, string $expected) {
		$result = \Blobfolio\Ips::niceIp($value, $flags);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: cidrToRange
	 *
	 * @dataProvider data_cidrToRange
	 *
	 * @param string $value Value.
	 * @param mixed $expected.
	 */
	function test_cidrToRange(string $value, $expected) {
		$result = \Blobfolio\Ips::cidrToRange($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: fromNumber
	 *
	 * @dataProvider data_fromNumber
	 *
	 * @param string $value Value.
	 * @param mixed $expected.
	 */
	function test_fromNumber($value, $expected) {
		$result = \Blobfolio\Ips::fromNumber($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toNumber
	 *
	 * @dataProvider data_toNumber
	 *
	 * @param string $value Value.
	 * @param mixed $expected.
	 */
	function test_toNumber(string $value, $expected) {
		$result = \Blobfolio\Ips::toNumber($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toSubnet
	 *
	 * @dataProvider data_toSubnet
	 *
	 * @param string $value Value.
	 * @param mixed $expected.
	 */
	function test_toSubnet(string $value, $expected) {
		$result = \Blobfolio\Ips::toSubnet($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: inRange
	 *
	 * @dataProvider data_inRange
	 *
	 * @param string $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param mixed $expected.
	 */
	function test_inRange(string $value, $min, $max, $expected) {
		$result = \Blobfolio\Ips::inRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}


	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: niceIp
	 *
	 * @return array Values.
	 */
	function data_niceIp() {
		return array(
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				\Blobfolio\Ips::IP_CONDENSE,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'[2600:3c00::f03c:91ff:feae:0ff2]',
				\Blobfolio\Ips::IP_CONDENSE,
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				'2600:3c00::f03c:91ff:feae:ff2',
				0,
				'2600:3c00:0000:0000:f03c:91ff:feae:0ff2',
			),
			array(
				'127.0.0.1',
				\Blobfolio\Ips::IP_CONDENSE,
				'',
			),
			array(
				'127.0.0.1',
				\Blobfolio\Ips::IP_RESTRICTED | \Blobfolio\Ips::IP_CONDENSE,
				'127.0.0.1',
			),
			array(
				'::127.0.0.1',
				\Blobfolio\Ips::IP_RESTRICTED | \Blobfolio\Ips::IP_CONDENSE,
				'127.0.0.1',
			),
			array(
				'[::127.0.0.1]',
				\Blobfolio\Ips::IP_RESTRICTED | \Blobfolio\Ips::IP_CONDENSE,
				'127.0.0.1',
			),
			array(
				'::1',
				\Blobfolio\Ips::IP_CONDENSE,
				'',
			),
			array(
				'[::1]',
				\Blobfolio\Ips::IP_RESTRICTED | \Blobfolio\Ips::IP_CONDENSE,
				'::1',
			),
			array(
				'[::1]',
				\Blobfolio\Ips::IP_RESTRICTED,
				'0000:0000:0000:0000:0000:0000:0000:0001',
			),
		);
	}

	/**
	 * Data: cidrToRange
	 *
	 * @return array Values.
	 */
	function data_cidrToRange() {
		return array(
			array(
				'50.116.18.174/24',
				array(
					'min'=>'50.116.18.0',
					'max'=>'50.116.18.255',
				),
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2/64',
				array(
					'min'=>'2600:3c00::',
					'max'=>'2600:3c00::ffff:ffff:ffff:ffff',
				),
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2/96',
				array(
					'min'=>'2600:3c00::f03c:91ff:0:0',
					'max'=>'2600:3c00::f03c:91ff:ffff:ffff',
				),
			),
			array(
				'26G0:3c00::f03c:91ff:feae:0ff2/64',
				false,
			),
		);
	}

	/**
	 * Data: fromNumber
	 *
	 * @return array Values.
	 */
	function data_fromNumber() {
		return array(
			array(
				846467758,
				'50.116.18.174',
			),
			array(
				'50511880784403022287880976722111107058',
				'2600:3c00::f03c:91ff:feae:ff2',
			),
			array(
				2130706433,
				'127.0.0.1',
			),
		);
	}

	/**
	 * Data: toNumber
	 *
	 * @return array Values.
	 */
	function data_toNumber() {
		return array(
			array(
				'50.116.18.174',
				846467758,
			),
			array(
				'2600:3c00::f03c:91ff:feae:0ff2',
				'50511880784403022287880976722111107058',
			),
			array(
				'2600:3c00::f03c:91ff:feae:ff2',
				'50511880784403022287880976722111107058',
			),
			array(
				'127.0.0.1',
				2130706433,
			),
		);
	}

	/**
	 * Data: toSubnet
	 *
	 * @return array Values.
	 */
	function data_toSubnet() {
		return array(
			array(
				'50.116.18.174',
				'50.116.18.0/24',
			),
			array(
				'2600:3c00::f03c:91ff:FEAE:0ff2',
				'2600:3c00::/64',
			),
			array(
				'127.0.0.1',
				'127.0.0.0/24',
			),
		);
	}

	/**
	 * Data: inRange
	 *
	 * @return array Values.
	 */
	function data_inRange() {
		return array(
			array(
				'127.0.0.1',
				'127.0.0.0',
				'127.0.0.2',
				true,
			),
			array(
				'127.0.0.1',
				'127.0.0.0/24',
				null,
				true,
			),
			array(
				'127.0.0.1',
				'192.168.1.0/24',
				null,
				false,
			),
			array(
				'2600:3c00::f03c:91ff:FEAE:0ff2',
				'2600:3c00::/64',
				null,
				true,
			),
		);
	}

}
