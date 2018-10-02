<?php
/**
 * Blobfolio\Ascii85
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class ascii85_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: encode/decode
	 *
	 * @dataProvider data_encode_decode
	 *
	 * @covers encode
	 * @covers decode
	 *
	 * @param mixed $value Value.
	 * @param string $expected Expected.
	 */
	function test_encode_decode($value, string $expected) {
		$encoded = \Blobfolio\Ascii85::encode($value);
		$decoded = \Blobfolio\Ascii85::decode($encoded);

		$this->assertSame($encoded, $expected);
		$this->assertSame($decoded, $value);
	}

	/**
	 * Test: hash
	 *
	 * @dataProvider data_hash
	 *
	 * @param string $algo Algorithm.
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_hash(string $algo, string $value, string $expected) {
		$result = \Blobfolio\Ascii85::hash($algo, $value);

		$this->assertSame($result, $expected);
	}

	/**
	 * Test: hash_file
	 *
	 * @dataProvider data_hash_file
	 *
	 * @param string $algo Algorithm.
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_hash_file(string $algo, string $value, string $expected) {
		$result = \Blobfolio\Ascii85::hash_file($algo, $value);

		$this->assertSame($result, $expected);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: encode/decode
	 *
	 * @return array Values.
	 */
	function data_encode_decode() {
		return array(
			array(
				'Hello World',
				'nm=QNzY&b1A+]n',
			),
			array(
				'Björk Guðmundsdóttir is a swan.',
				'ltBADA+=^/B&gC2B-7-hww+-}Bzk+rx(+z9aAI:nzz2',
			),
		);
	}

	/**
	 * Data: hash
	 *
	 * @return array Values.
	 */
	function data_hash() {
		return array(
			array(
				'md5',
				'Hello World',
				'U)MF}wz?O@1?gF2>pP0X',
			),
			array(
				'sha256',
				'Hello World',
				'RidU83?LMbn=Y.:=:Qfv!*$yE3^CB%sf^56T!cqB',
			),
			array(
				'sha512',
				'Hello World',
				'eoH?b)xtdLGH-n@xf>@hjasUk^IebSVahb#VJC*IEUx}sG(JW]xuP0rS/H1&5m/iPwlN#s+n4^%m1mT{',
			),
		);
	}

	/**
	 * Data: hash_file
	 *
	 * @return array Values.
	 */
	function data_hash_file() {
		return array(
			array(
				'md5',
				self::ASSETS . 'dark01.jpg',
				'>]3&(S4[KQ*O]8)}WNi1',
			),
			array(
				'sha256',
				self::ASSETS . 'dark01.jpg',
				'(Z?x<4)3(W(74{Kdg^-Rb!?U)cB]fZ4oZaCskL4Y',
			),
			array(
				'sha512',
				self::ASSETS . 'dark01.jpg',
				'voMs!yLeZ]-lr5DZS.at=K7#jYXm.#VqJ4bPc3O$Z!A2tD>iOx8gq*^Ns^Nk@Gor?aDX:Qf*15}[u!sJ',
			),
		);
	}
}
