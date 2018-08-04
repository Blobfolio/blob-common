<?php
/**
 * Blobfolio\Retail
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class retail_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: usd
	 *
	 * @dataProvider data_usd
	 *
	 * @param mixed $value Value.
	 * @param int $flags Args.
	 * @param mixed $expected Expected.
	 */
	function test_usd($value, int $flags, $expected) {
		$result = \Blobfolio\Retail::usd($value, $flags);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCarrier
	 *
	 * @dataProvider data_niceCarrier
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceCarrier($value, $expected) {
		$result = \Blobfolio\Retail::niceCarrier($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCc
	 *
	 * @dataProvider data_niceCc
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceCc($value, $expected) {
		$result = \Blobfolio\Retail::niceCc($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceEan
	 *
	 * @dataProvider data_niceEan
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_niceEan($value, int $flags, $expected) {
		$result = \Blobfolio\Retail::niceEan($value, $flags);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceIsbn
	 *
	 * @dataProvider data_niceIsbn
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceIsbn($value, $expected) {
		$result = \Blobfolio\Retail::niceIsbn($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCcBrand
	 *
	 * @dataProvider data_niceCcBrand
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceCcBrand($value, $expected) {
		$result = \Blobfolio\Retail::niceCcBrand($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceCcLast4
	 *
	 * @dataProvider data_niceCcLast4
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceCcLast4($value, $expected) {
		$result = \Blobfolio\Retail::niceCcLast4($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceName
	 *
	 * @dataProvider data_niceName
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_niceName($value, $expected) {
		$result = \Blobfolio\Retail::niceName($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: splitName
	 *
	 * @dataProvider data_splitName
	 *
	 * @param string $value Value.
	 * @param array $expected Expected.
	 */
	function test_splitName(string $value, $expected) {
		$result = \Blobfolio\Retail::splitName($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: nicePassword
	 *
	 * @dataProvider data_nicePassword
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_nicePassword($value, $expected) {
		$result = \Blobfolio\Retail::nicePassword($value);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceShippingUrl
	 *
	 * @dataProvider data_niceShippingUrl
	 *
	 * @param string $value Carrier.
	 * @param string $shipping_id Shipping ID.
	 * @param string $expected Expected.
	 */
	function test_niceShippingUrl(string $value, string $shipping_id, string $expected) {
		$result = \Blobfolio\Retail::niceShippingUrl($value, $shipping_id);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: niceUpc
	 *
	 * @dataProvider data_niceUpc
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_niceUpc($value, int $flags, $expected) {
		$result = \Blobfolio\Retail::niceUpc($value, $flags);

		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: ccExpMonths
	 *
	 * @dataProvider data_ccExpMonths
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_ccExpMonths($value, $expected) {
		$result = \Blobfolio\Retail::ccExpMonths($value);

		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: ccExpYears
	 *
	 * @dataProvider data_ccExpYears
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_ccExpYears($value, $expected) {
		$result = \Blobfolio\Retail::ccExpYears($value);

		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: usd
	 *
	 * @return array Values.
	 */
	function data_usd() {
		$thousands = \Blobfolio\Blobfolio::USD_THOUSANDS;
		$cents = \Blobfolio\Blobfolio::USD_CENTS;
		$trim = \Blobfolio\Blobfolio::USD_TRIM;

		return array(
			array(
				2.5,
				$cents | $trim,
				'$2.50',
			),
			array(
				2.555,
				$cents,
				'$2.56',
			),
			array(
				.5,
				$cents,
				'50¢',
			),
			array(
				.5,
				0,
				'$0.50',
			),
			array(
				500,
				$cents,
				'$500.00',
			),
			array(
				500,
				$trim | $cents,
				'$500',
			),
			array(
				-5000,
				$trim | $thousands,
				'-$5,000',
			),
			array(
				-5000,
				0,
				'-$5000.00',
			),
		);
	}

	/**
	 * Data: niceCarrier
	 *
	 * @return array Values.
	 */
	function data_niceCarrier() {
		return array(
			array('UPS', 'UPS'),
			array('usps', 'USPS'),
			array('Federal Express', 'FedEx'),
			array('Post Office', 'USPS'),
		);
	}

	/**
	 * Data: niceCc
	 *
	 * @return array Values.
	 */
	function data_niceCc() {
		return array(
			array(
				'4242-4242-4242-4242',
				'4242424242424242',
			),
			array(
				'4000056655665556',
				'4000056655665556',
			),
			array(
				'5555555555554444',
				'5555555555554444',
			),
			array(
				'2223003122003222',
				'2223003122003222',
			),
			array(
				'5200828282828210',
				'5200828282828210',
			),
			array(
				'5105105105105100',
				'5105105105105100',
			),
			array(
				'378282246310005',
				'378282246310005',
			),
			array(
				'371449635398431',
				'371449635398431',
			),
			array(
				'6011000990139424',
				'6011000990139424',
			),
			array(
				'6011111111111117',
				'6011111111111117',
			),
			array(
				'371449635398432',
				'',
			),
			array(
				'5105105105105107',
				'',
			),
		);
	}

	/**
	 * Data: niceEan
	 *
	 * @return array Values.
	 */
	function data_niceEan() {
		return array(
			array(
				'0',
				0,
				'',
			),
			array(
				'074299160691',
				0,
				'0074299160691',
			),
			array(
				'00709077260149',
				0,
				'0709077260149',
			),
			array(
				'709077260149',
				0,
				'0709077260149',
			),
			array(
				'0709077260555',
				0,
				'',
			),
			array(
				'0709077260149',
				\Blobfolio\Blobfolio::PRETTY,
				'0-709077-260149',
			),
			array(
				'0051511500275',
				\Blobfolio\Blobfolio::PRETTY,
				'0-051511-500275',
			),
		);
	}

	/**
	 * Data: niceIsbn
	 *
	 * @return array Values.
	 */
	function data_niceIsbn() {
		return array(
			array(
				'0',
				'',
			),
			array(
				'0939117606',
				'0939117606',
			),
			array(
				'939117606',
				'0939117606',
			),
			array(
				'9780939117604',
				'9780939117604',
			),
			array(
				'0-9752298-0-X',
				'097522980X',
			),
			array(
				'0975229800',
				'',
			),
		);
	}

	/**
	 * Data: niceCcBrand
	 *
	 * @return array Values.
	 */
	function data_niceCcBrand() {
		return array(
			array(
				'American Express',
				'AMEX',
			),
			array(
				'Discover card',
				'Discover',
			),
			array(
				'mc',
				'MC',
			),
			array(
				'Japanese Credit B',
				'JCB',
			),
			array(
				'Visa',
				'Visa',
			),
			array(
				'amex',
				'AMEX',
			),
		);
	}

	/**
	 * Data: niceCcLast4
	 *
	 * @return array Values.
	 */
	function data_niceCcLast4() {
		return array(
			array(
				123,
				'0123',
			),
			array(
				'',
				'',
			),
			array(
				12345,
				'2345',
			),
			array(
				'xxx1234',
				'1234',
			),
			array(
				'0000',
				'',
			),
		);
	}

	/**
	 * Data: niceName
	 *
	 * @return array Values.
	 */
	function data_niceName() {
		return array(
			array(
				"åsa-britt\nkjellén",
				'Åsa-Britt Kjellén',
			),
			array(
				'john   doe',
				'John Doe',
			),
		);
	}

	/**
	 * Data: splitName
	 *
	 * @return array Values.
	 */
	function data_splitName() {
		return array(
			array(
				"åsa-britt\nkjellén",
				array(
					'firstname'=>'Åsa-Britt',
					'lastname'=>'Kjellén',
				),
			),
			array(
				'john   doe',
				array(
					'firstname'=>'John',
					'lastname'=>'Doe',
				),
			),
			array(
				'heather',
				array(
					'firstname'=>'Heather',
					'lastname'=>'',
				),
			),
		);
	}

	/**
	 * Data: nicePassword
	 *
	 * @return array Values.
	 */
	function data_nicePassword() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				'ålén ☺',
			),
			array(
				"\t ålén\n" . chr(27) . "  ☺\0",
				'ålén ☺',
			),
		);
	}

	/**
	 * Data: niceShippingUrl
	 *
	 * @return array Values.
	 */
	function data_niceShippingUrl() {
		$urls = array(
			'abf'=>'https://arcb.com/tools/tracking.html#/%s',
			'fedex'=>'https://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=%s',
			'ups'=>'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=%s',
			'usps'=>'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%s',
		);

		return array(
			array(
				'ups',
				'1234',
				sprintf($urls['ups'], '1234'),
			),
			array(
				'post office',
				'1234',
				sprintf($urls['usps'], '1234'),
			),
			array(
				'ArcBest',
				'1234',
				sprintf($urls['abf'], '1234'),
			),
			array(
				'PayPal',
				'1234',
				'',
			),
		);
	}

	/**
	 * Data: niceUpc
	 *
	 * @return array Values.
	 */
	function data_niceUpc() {
		return array(
			array(
				'0',
				0,
				'',
			),
			array(
				'089218545992',
				0,
				'089218545992',
			),
			array(
				'0089218545992',
				0,
				'089218545992',
			),
			array(
				'89218545992',
				0,
				'089218545992',
			),
			array(
				'089218545555',
				0,
				'',
			),
			array(
				'075597996524',
				\Blobfolio\Blobfolio::PRETTY,
				'0-75597-99652-4',
			),
		);
	}

	/**
	 * Data: ccExpMonths
	 *
	 * @return array Values.
	 */
	function data_ccExpMonths() {
		return array(
			array(
				'F',
				array(
					1=>'January',
					2=>'February',
					3=>'March',
					4=>'April',
					5=>'May',
					6=>'June',
					7=>'July',
					8=>'August',
					9=>'September',
					10=>'October',
					11=>'November',
					12=>'December',
				),
			),
		);
	}

	/**
	 * Data: ccExpYears
	 *
	 * @return array Values.
	 */
	function data_ccExpYears() {
		$year = (int) date('Y');

		$arr1 = array();
		$arr2 = array();
		for ($x = 0; $x < 10; $x++) {
			$key = $year + $x;

			if ($x < 5) {
				$arr1[$key] = $key;
			}

			$arr2[$key] = $key;
		}

		return array(
			array(
				5,
				$arr1,
			),
			array(
				10,
				$arr2,
			),
		);
	}
}
