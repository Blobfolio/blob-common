<?php
/**
 * Blobfolio\Geo
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class geo_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: niceAddress
	 *
	 * @dataProvider data_niceAddress
	 *
	 * @param array $value Value.
	 * @param array $expected Expected.
	 */
	function test_niceAddress(array $value, int $flags, array $expected) {
		$result = \Blobfolio\Geo::niceAddress($value, $flags);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: niceCountry
	 *
	 * @dataProvider data_niceCountry
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceCountry(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceCountry($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceAuState
	 *
	 * @dataProvider data_niceAuState
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceAuState(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceAuState($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCaPostalCode
	 *
	 * @dataProvider data_niceCaPostalCode
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceCaPostalCode(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceCaPostalCode($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCaProvince
	 *
	 * @dataProvider data_niceCaProvince
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceCaProvince(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceCaProvince($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceUsState
	 *
	 * @dataProvider data_niceUsState
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceUsState(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceUsState($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceZip5
	 *
	 * @dataProvider data_niceZip5
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceZip5(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceZip5($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceDatetime
	 *
	 * @dataProvider data_niceDatetime
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceDatetime($value, string $expected) {
		$result = \Blobfolio\Geo::niceDatetime($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceDate
	 *
	 * @dataProvider data_niceDate
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceDate($value, string $expected) {
		$result = \Blobfolio\Geo::niceDate($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceTimezone
	 *
	 * @dataProvider data_niceTimezone
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_niceTimezone(string $value, string $expected) {
		$result = \Blobfolio\Geo::niceTimezone($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: dateDiff
	 *
	 * @dataProvider data_dateDiff
	 *
	 * @param mixed $date1 Date1.
	 * @param mixed $date2 Date2.
	 * @param int $expected Expected.
	 */
	function test_dateDiff($date1, $date2, int $expected) {
		$result = \Blobfolio\Geo::dateDiff($date1, $date2);

		$this->assertSame($expected, $result);
		$this->assertSame('integer', gettype($result));
	}

	/**
	 * Test: toTimezone
	 *
	 * @dataProvider data_toTimezone
	 *
	 * @param mixed $value Date.
	 * @param string $from TZ from.
	 * @param string $to TZ to.
	 * @param string $expected Expected.
	 */
	function test_toTimezone($value, string $from, string $to, string $expected) {
		$result = \Blobfolio\Geo::toTimezone($value, $from, $to);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: getAuStates
	 */
	function test_getAuStates() {
		$result = \Blobfolio\Geo::getAuStates();

		$this->assertTrue(is_array($result));
		$this->assertTrue(array_key_exists('NSW', $result));
	}

	/**
	 * Test: getCaProvinces
	 */
	function test_getCaProvinces() {
		$result = \Blobfolio\Geo::getCaProvinces();

		$this->assertTrue(is_array($result));
		$this->assertTrue(array_key_exists('ON', $result));
	}

	/**
	 * Test: getCountries
	 */
	function test_getCountries() {
		$result = \Blobfolio\Geo::getCountries();

		$this->assertTrue(is_array($result));
		$this->assertTrue(array_key_exists('US', $result));
	}

	/**
	 * Test: getNeighborCountries
	 *
	 * @dataProvider data_getNeighborCountries
	 *
	 * @param string $value Value.
	 * @param int $limit Limit.
	 * @param array $expected Expected.
	 */
	function test_getNeighborCountries(string $value, int $limit, $expected) {
		$result = \Blobfolio\Geo::getNeighborCountries($value, $limit);

		$this->assertTrue(is_array($result));

		// If limiting, it should be the right size.
		if ($limit > 0) {
			$this->assertTrue(count($result) <= $limit);
		}

		// Rather than bloat the world, let's just check what was
		// actually passed.
		foreach ($expected as $k=>$v) {
			$this->assertTrue(isset($result[$k]));
			$this->assertSame($v, $result[$k]);
		}
	}

	/**
	 * Test: getUsStates
	 */
	function test_getUsStates() {
		$result = \Blobfolio\Geo::getUsStates();

		$this->assertTrue(is_array($result));
		$this->assertTrue(array_key_exists('IL', $result));
		$this->assertTrue(array_key_exists('PR', $result));

		$result = \Blobfolio\Geo::getUsStates(0);
		$this->assertTrue(is_array($result));
		$this->assertTrue(array_key_exists('IL', $result));
		$this->assertFalse(array_key_exists('PR', $result));
	}

	/**
	 * Test: getRegions
	 */
	function test_getRegions() {
		$result = \Blobfolio\Geo::getRegions();

		$this->assertTrue(is_array($result));
		$this->assertTrue(in_array('North America', $result, true));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: niceAddress
	 *
	 * @return array Values.
	 */
	function data_niceAddress() {
		return array(
			array(
				array(),
				0,
				array(
					'name'=>'',
					'street'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
					'country'=>'US',
				),
			),
			array(
				array(),
				\Blobfolio\Blobfolio::ADDRESS_FIELD_ALL,
				array(
					'name'=>'',
					'street'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
					'country'=>'US',
					'company'=>'',
					'phone'=>'',
					'email'=>'',
				),
			),
			array(
				array(),
				\Blobfolio\Blobfolio::ADDRESS_FIELD_EMAIL,
				array(
					'name'=>'',
					'street'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
					'country'=>'US',
					'email'=>'',
				),
			),
			array(
				array(),
				\Blobfolio\Blobfolio::ADDRESS_FIELD_PHONE,
				array(
					'name'=>'',
					'street'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
					'country'=>'US',
					'phone'=>'',
				),
			),
			array(
				array(),
				\Blobfolio\Blobfolio::ADDRESS_FIELD_COMPANY,
				array(
					'name'=>'',
					'street'=>'',
					'city'=>'',
					'state'=>'',
					'zip'=>'',
					'country'=>'US',
					'company'=>'',
				),
			),
			array(
				array(
					'firstname'=>'Josh',
					'lastname'=>'stoik',
					'zip'=>'123',
					'address'=>'123 candy cane lane',
					'city'=>' bögland',
					'state'=>'Puerto Rico',
					'email'=>'Foo+bAr@gmail.com',
					'phone'=>'(702) 405-0001',
				),
				\Blobfolio\Blobfolio::ADDRESS_FIELD_ALL,
				array(
					'name'=>'Josh Stoik',
					'street'=>'123 CANDY CANE LANE',
					'city'=>'BÖGLAND',
					'state'=>'PR',
					'zip'=>'00123',
					'country'=>'US',
					'company'=>'',
					'phone'=>'+1 702-405-0001',
					'email'=>'foo+bar@gmail.com',
				),
			),
		);
	}

	/**
	 * Data: niceCountry
	 *
	 * @return array Values.
	 */
	function data_niceCountry() {
		return array(
			array(
				'USA',
				'US',
			),
			array(
				'us',
				'US',
			),
			array(
				'United States of America',
				'US',
			),
			array(
				'Nobody',
				'',
			),
			array(
				'CANADA',
				'CA',
			),
		);
	}

	/**
	 * Data: niceAuState
	 *
	 * @return array Values.
	 */
	function data_niceAuState() {
		return array(
			array(
				'Texas',
				'',
			),
			array(
				'new soUTH wales',
				'NSW',
			),
			array(
				'QLD',
				'QLD',
			),
		);
	}

	/**
	 * Data: niceCaPostalCode
	 *
	 * @return array Values.
	 */
	function data_niceCaPostalCode() {
		return array(
			array(
				'f3f3f3',
				'',
			),
			array(
				'w2w2w2',
				'',
			),
			array(
				'e3w3w3',
				'E3W 3W3',
			),
			array(
				'L3Y-6B1',
				'L3Y 6B1',
			),
			array(
				'L3Y6B1R',
				'',
			),
		);
	}

	/**
	 * Data: niceCaProvince
	 *
	 * @return array Values.
	 */
	function data_niceCaProvince() {
		return array(
			array(
				'Texas',
				'',
			),
			array(
				'ontario',
				'ON',
			),
			array(
				'ab',
				'AB',
			),
		);
	}

	/**
	 * Data: niceUsState
	 *
	 * @return array Values.
	 */
	function data_niceUsState() {
		return array(
			array(
				'Texas',
				'TX',
			),
			array(
				'ontario',
				'',
			),
			array(
				'il',
				'IL',
			),
			array(
				'puerto RICO',
				'PR',
			),
		);
	}

	/**
	 * Data: niceZip5
	 *
	 * @return array Values.
	 */
	function data_niceZip5() {
		return array(
			array(
				'Björk',
				'',
			),
			array(
				'123',
				'00123',
			),
			array(
				'000',
				'',
			),
			array(
				'12345',
				'12345',
			),
			array(
				'12345-6789',
				'12345',
			),
		);
	}

	/**
	 * Data: niceDatetime
	 *
	 * @return array Values.
	 */
	function data_niceDatetime() {
		return array(
			array(
				'2015-01-02',
				'2015-01-02 00:00:00',
			),
			array(
				'2015-01-02 13:23:11',
				'2015-01-02 13:23:11',
			),
			array(
				strtotime('2015-01-02 13:23:11'),
				'2015-01-02 13:23:11',
			),
			array(
				'20150102',
				'2015-01-02 00:00:00',
			),
			array(
				20150102,
				'2015-01-02 00:00:00',
			),
			array(
				'Not Time',
				'0000-00-00 00:00:00',
			),
			array(
				'0000-00-00 12:30:30',
				'0000-00-00 00:00:00',
			),
		);
	}

	/**
	 * Data: niceDate
	 *
	 * @return array Values.
	 */
	function data_niceDate() {
		// We can use the same dataset as niceDatetime, except the
		// expected values should be just the date bit.
		$out = $this->data_niceDatetime();
		foreach ($out as $k=>$v) {
			$out[$k][1] = substr($out[$k][1], 0, 10);
		}
		return $out;
	}

	/**
	 * Data: niceTimezone
	 *
	 * @return array Values.
	 */
	function data_niceTimezone() {
		return array(
			array(
				'Notime',
				'UTC',
			),
			array(
				'america/Los_angeles',
				'America/Los_Angeles',
			),
			array(
				'GMT',
				'UTC',
			),
			array(
				'utc',
				'UTC',
			),
		);
	}

	/**
	 * Data: dateDiff
	 *
	 * @return array Values.
	 */
	function data_dateDiff() {
		return array(
			array(
				'2015-01-01',
				'2015-01-01',
				0,
			),
			array(
				'2015-01-03 12:13:14',
				'2015-01-01',
				2,
			),
			array(
				'2015-01-01',
				'2015-01-03',
				2,
			),
			array(
				strtotime('2015-01-03'),
				'2015-01-01',
				2,
			),
		);
	}

	/**
	 * Data: toTimezone
	 *
	 * @return array Values.
	 */
	function data_toTimezone() {
		return array(
			array(
				'2015-01-15 01:12:23',
				'America/Los_Angeles',
				"",
				'2015-01-15 09:12:23',
			),
			array(
				'2015-01-15 01:12:23',
				'America/Los_Angeles',
				'UTC',
				'2015-01-15 09:12:23',
			),
			array(
				strtotime('2015-01-15 01:12:23'),
				'America/Los_Angeles',
				"",
				'2015-01-15 09:12:23',
			),
			array(
				'2015-01-15 01:12:23',
				'UTC',
				'America/Los_Angeles',
				'2015-01-14 17:12:23',
			),
		);
	}

	/**
	 * Data: getNeighborCountries
	 *
	 * @return array Values.
	 */
	function data_getNeighborCountries() {
		return array(
			array(
				'US',
				5,
				array(
					'US',
					'CA',
					'MX',
					'BS',
					'BZ',
				),
			),
			array(
				'US',
				0,
				array(
					'US',
					'CA',
					'MX',
					'BS',
					'BZ',
				),
			),
			array(
				'CA',
				3,
				array(
					'CA',
					'US',
					'GL',
				),
			),
		);
	}
}
