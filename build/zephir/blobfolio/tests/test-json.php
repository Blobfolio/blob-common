<?php
/**
 * Blobfolio\Json
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class json_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: decode
	 *
	 * @dataProvider data_decode
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected.
	 */
	function test_decode($value, $expected) {
		$result = \Blobfolio\Json::decode($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: decodeArray
	 *
	 * @dataProvider data_decodeArray
	 *
	 * @param mixed $value Value.
	 * @param mixed $default Default.
	 * @param int $flags Flags.
	 * @param mixed $expected.
	 */
	function test_decodeArray($value, $default, int $flags, $expected) {
		$result = \Blobfolio\Json::decodeArray($value, $default, $flags);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: encode
	 *
	 * @dataProvider data_encode
	 *
	 * @param mixed $value Value.
	 * @param int $options Options.
	 * @param int $depth Depth.
	 * @param mixed $expected.
	 */
	function test_encode($value, int $options, int $depth, $expected) {
		$result = \Blobfolio\Json::encode($value, $options, $depth);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: fix
	 *
	 * @dataProvider data_fix
	 *
	 * @param mixed $value Value.
	 * @param bool $pretty Pretty.
	 * @param mixed $expected.
	 */
	function test_fix($value, bool $pretty, $expected) {
		$result = \Blobfolio\Json::fix($value, $pretty);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: isJson
	 *
	 * @dataProvider data_isJson
	 *
	 * @param mixed $value Value.
	 * @param bool $loose Loose.
	 * @param mixed $expected.
	 */
	function test_isJson($value, bool $loose, $expected) {
		$result = \Blobfolio\Json::isJson($value, $loose);

		$this->assertSame($expected, $result);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: decode
	 *
	 * @return array Values.
	 */
	function data_decode() {
		return array(
			array(
				'{"hello": ["world", "and", "Bj\\u00f6rk"]}',
				array(
					'hello'=>array(
						'world',
						'and',
						'Björk',
					),
				),
			),
			array(
				"{hello: [\"world\", 'and', \"Bj\\u00f6rk\"]}",
				array(
					'hello'=>array(
						'world',
						'and',
						'Björk',
					),
				),
			),
			array(
				'hey',
				null,
			),
			array(
				'false',
				false,
			),
			array(
				'"hey"',
				'hey',
			),
			array(
				'12',
				12,
			),
			array(
				'1.1',
				1.1,
			),
			array(
				"[[['hello']]]",
				array(array(array('hello'))),
			),
		);
	}

	/**
	 * Data: decodeArray
	 *
	 * @return array Values.
	 */
	function data_decodeArray() {
		return array(
			array(
				'',
				null,
				0,
				array(),
			),
			array(
				'{"animal":"dog"}',
				null,
				0,
				array('animal'=>'dog'),
			),
			array(
				'{animal:"dog"}',
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				\Blobfolio\Json::DECODE_STRICT | \Blobfolio\Json::DECODE_RECURSIVE,
				array(
					'animal'=>'dog',
					'fruit'=>'banana',
				),
			),
			array(
				'{animal:{"dog":"wolf"}}',
				array(
					'animal'=>'bear',
					'fruit'=>'banana',
				),
				\Blobfolio\Json::DECODE_RECURSIVE,
				array(
					'animal'=>array('dog'=>'wolf'),
					'fruit'=>'banana',
				),
			),
			array(
				'{animal:"dog"}',
				array(
					'animal'=>array('bear'),
					'fruit'=>'banana',
				),
				\Blobfolio\Json::DECODE_STRICT | \Blobfolio\Json::DECODE_RECURSIVE,
				array(
					'animal'=>array('dog'),
					'fruit'=>'banana',
				),
			),
			array(
				'{price:{animal:2}}',
				array(
					'price'=>array(
						'animal'=>12.0,
						'fruit'=>15.0,
					),
				),
				\Blobfolio\Json::DECODE_STRICT | \Blobfolio\Json::DECODE_RECURSIVE,
				array(
					'price'=>array(
						'animal'=>2.0,
						'fruit'=>15.0,
					),
				),
			),
		);
	}

	/**
	 * Data: encode
	 *
	 * @return array Values.
	 */
	function data_encode() {
		return array(
			array(
				array('foo'=>'bar'),
				JSON_PRETTY_PRINT,
				512,
				"{\n    \"foo\": \"bar\"\n}",
			),
			array(
				array(
					'text'=>trim(file_get_contents(self::ASSETS . 'text-latin.txt')),
				),
				0,
				512,
				'{"text":"H\u00edrek"}',
			),
		);
	}

	/**
	 * Data: fix
	 *
	 * @return array Values.
	 */
	function data_fix() {
		return array(
			array(
				"{'hello': 'world'}",
				false,
				'{"hello":"world"}',
			),
		);
		return array(
			array(
				"{hello: 'world'}",
				true,
				"{\n    \"hello\": \"world\"\n}",
			),
		);
	}

	/**
	 * Data: isJson
	 *
	 * @return array Values.
	 */
	function data_isJson() {
		return array(
			array(
				1,
				false,
				false,
			),
			array(
				'yes',
				false,
				false,
			),
			array(
				'',
				false,
				false,
			),
			array(
				'{"happy":"days"}',
				false,
				true,
			),
			array(
				'[]',
				false,
				true,
			),
			array(
				'[1,2,3]',
				false,
				true,
			),
			array(
				'{"happy":"',
				false,
				false,
			),
			array(
				'',
				true,
				true,
			),
		);
	}
}
