<?php
/**
 * Blobfolio\Arrays
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class arrays_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: flatten
	 *
	 * @dataProvider data_flatten
	 *
	 * @param mixed $value Value.
	 * @param mixed $flags Flags.
	 * @param mixed $expected Expected.
	 */
	function test_flatten($value, int $flags, $expected) {
		$result = \Blobfolio\Arrays::flatten($value, $flags);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: flattenAssoc
	 *
	 * @dataProvider data_flattenAssoc
	 *
	 * @param mixed $value Value.
	 * @param string $stub Stub.
	 * @param mixed $expected Expected.
	 */
	function test_flattenAssoc($value, string $stub, $expected) {
		$result = \Blobfolio\Arrays::flattenAssoc($value, $stub);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: fromList
	 *
	 * @dataProvider data_fromList
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_fromList($value, $args, $expected) {
		$result = \Blobfolio\Arrays::fromList($value, $args);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: otherize
	 *
	 * @dataProvider data_otherize
	 *
	 * @param mixed $value Value.
	 * @param int $length Length.
	 * @param string $label Label.
	 * @param mixed $expected Expected.
	 */
	function test_otherize($value, int $length, string $label, $expected) {
		$result = \Blobfolio\Arrays::otherize($value, $length, $label);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: toCsv
	 *
	 * @dataProvider data_toCsv
	 *
	 * @param mixed $value Value.
	 * @param mixed $headers Headers.
	 * @param string $delimiter Delimiter.
	 * @param string $eol Eol.
	 * @param mixed $expected Expected.
	 */
	function test_toCsv($value, $headers, string $delimiter, string $eol, $expected) {
		$result = \Blobfolio\Arrays::toCsv($value, $headers, $delimiter, $eol);
		$this->assertSame($expected, $result);
		$this->assertSame('string', gettype($result));
	}

	/**
	 * Test: toIndexed
	 *
	 * @dataProvider data_toIndexed
	 *
	 * @param mixed $value Value.
	 * @param string $key_label Key label.
	 * @param string $value_label Value label.
	 * @param mixed $expected Expected.
	 */
	function test_toIndexed($value, string $key_label, string $value_label, $expected) {
		$result = \Blobfolio\Arrays::toIndexed($value, $key_label, $value_label);
		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: iDiff
	 *
	 * @dataProvider data_iDiff
	 */
	function test_iDiff() {
		$args = func_get_args();
		$expected = array_pop($args);

		$result = call_user_func_array(
			array('\\Blobfolio\\Arrays', 'iDiff'),
			$args
		);

		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: iInArray
	 *
	 * @dataProvider data_iInArray
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @param bool $expected Expected.
	 */
	function test_iInArray($needle, array $haystack, bool $strict, bool $expected) {
		$result = \Blobfolio\Arrays::iInArray($needle, $haystack, $strict);

		$this->assertSame($expected, $result);
		$this->assertSame('boolean', gettype($result));
	}

	/**
	 * Test: iIntersect
	 *
	 * @dataProvider data_iIntersect
	 */
	function test_iIntersect() {
		$args = func_get_args();
		$expected = array_pop($args);

		$result = call_user_func_array(
			array('\\Blobfolio\\Arrays', 'iIntersect'),
			$args
		);

		$this->assertSame($expected, $result);
		$this->assertSame('array', gettype($result));
	}

	/**
	 * Test: iKeyExists
	 *
	 * @dataProvider data_iKeyExists
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $expected Expected.
	 */
	function test_iKeyExists($needle, array $haystack, bool $expected) {
		$result = \Blobfolio\Arrays::iKeyExists($needle, $haystack);

		$this->assertSame($expected, $result);
		$this->assertSame('boolean', gettype($result));
	}

	/**
	 * Test: iSearch
	 *
	 * @dataProvider data_iSearch
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @param mixed $expected Expected.
	 */
	function test_iSearch($needle, array $haystack, bool $strict, $expected) {
		$result = \Blobfolio\Arrays::iSearch($needle, $haystack, $strict);

		$this->assertSame($expected, $result);
		$this->assertSame(gettype($expected), gettype($result));
	}

	/**
	 * Test: compare
	 *
	 * @dataProvider data_compare
	 *
	 * @param array $arr1 Array 1.
	 * @param array $arr2 Array 2.
	 * @param bool $expected Expected.
	 */
	function test_compare($arr1, $arr2, bool $expected) {
		$result = \Blobfolio\Arrays::compare($arr1, $arr2);

		$this->assertSame($expected, $result);
		$this->assertSame('boolean', gettype($result));
	}

	/**
	 * Test: getType
	 *
	 * @dataProvider data_getType
	 *
	 * @param array $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_getType($value, $expected) {
		$result = \Blobfolio\Arrays::getType($value);

		$this->assertSame($expected, $result);
		$this->assertSame(getType($expected), gettype($result));
	}

	/**
	 * Test: pop
	 *
	 * @dataProvider data_pop
	 *
	 * @param array $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_pop($value, $expected) {
		$result = \Blobfolio\Arrays::pop($value);

		$this->assertSame($expected, $result);
		$this->assertSame(getType($expected), gettype($result));
	}

	/**
	 * Test: popRand
	 *
	 * @dataProvider data_popRand
	 *
	 * @param array $value Value.
	 */
	function test_popRand($value) {
		// No value, no pop.
		if (!count($value)) {
			$this->assertSame(false, \Blobfolio\Arrays::popRand($value));
		}
		// One value should always return the same result.
		elseif (1 === count($value)) {
			$this->assertSame(
				\Blobfolio\Arrays::popRand($value),
				\Blobfolio\Arrays::popRand($value)
			);
		}
		else {
			// Give the function 25 tries to produce a randomly
			// different result.
			$different = false;
			$last = \Blobfolio\Arrays::popRand($value);

			for ($x = 0; $x < 25; ++$x) {
				$result = \Blobfolio\Arrays::popRand($value);

				if ($last !== $result) {
					$different = true;
					break;
				}
			}

			$this->assertSame(true, $different);
		}
	}

	/**
	 * Test: popTop
	 *
	 * @dataProvider data_popTop
	 *
	 * @param array $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_popTop($value, $expected) {
		$result = \Blobfolio\Arrays::popTop($value);

		$this->assertSame($expected, $result);
		$this->assertSame(getType($expected), gettype($result));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: flatten
	 *
	 * @return array Values.
	 */
	function data_flatten() {
		return array(
			array(
				array(1, 2, 3, 4),
				0,
				array(1, 2, 3, 4),
			),
			array(
				array(
					1,
					array(
						2,
						array(3, 4),
					),
				),
				0,
				array(1, 2, 3, 4),
			),
			array(
				array(
					array('hi'),
					array('there'),
					array('hello', 'world'),
				),
				0,
				array('hi', 'there', 'hello', 'world'),
			),
			array(
				json_decode(
					file_get_contents(self::ASSETS . 'flatten.json'),
					true
				),
				\Blobfolio\Blobfolio::UNIQUE | \Blobfolio\Blobfolio::SORT,
				array(
					'absinthe', 'agave', 'aged', 'ale', 'amantillado',
					'amaro', 'american', 'aperitif', 'apple', 'aromatic',
					'beer', 'bitters', 'black lemon', 'boubon', 'bourbon',
					'brandy', 'calvados', 'cardamom', 'celery',
					'cherry bark vanilla', 'cider', 'citrate', 'cocktail',
					'cognac', 'cream', 'creole', 'dry', 'eau de vie',
					'elderflower', 'fino', 'firewater', 'flavored', 'french',
					'gin', 'grapefruit', 'grappa', 'herbal', 'ipa', 'irish',
					'jamaican #2', 'kit', 'lager', 'lime', 'liqueur',
					'london dry', 'manzanilla', 'mezcal', 'mixer',
					'moonshine', 'neutral grain spirit', 'orange',
					'orange cream', 'peychaud\'s', 'pisco', 'plymouth',
					'product', 'red', 'red - sparkling', 'red clover',
					'rhum agricole', 'rosé', 'rosé - sparkling', 'rum',
					'rum - spiced', 'rye', 'scotch', 'sherry', 'sloe',
					'sparkling', 'spiced', 'stout', 'sweet', 'syrup',
					'tennessee', 'tequila', 'umeshu', 'vermouth',
					'vodka', 'whiskey', 'whisky', 'white', 'wine',
				),
			),
		);
	}

	/**
	 * Data: flattenAssoc
	 *
	 * @return array Values.
	 */
	function data_flattenAssoc() {
		return array(
			array(
				array(
					'name'=>'Barney',
					'contact'=>array(
						'email'=>'barney@hello.com',
						'phone'=>'+1 123-456-7890',
						'preferences'=>array(
							'dnc'=>true,
							'mode'=>'email',
						),
					),
				),
				'',
				array(
					'name'=>'Barney',
					'contact_email'=>'barney@hello.com',
					'contact_phone'=>'+1 123-456-7890',
					'contact_preferences_dnc'=>true,
					'contact_preferences_mode'=>'email',
				),
			),
			array(
				array(
					'name'=>'Barney',
					'contact'=>array(
						'email'=>'barney@hello.com',
						'phone'=>'+1 123-456-7890',
						'preferences'=>array(
							'dnc'=>true,
							'mode'=>'email',
						),
					),
				),
				'stub',
				array(
					'stub_name'=>'Barney',
					'stub_contact_email'=>'barney@hello.com',
					'stub_contact_phone'=>'+1 123-456-7890',
					'stub_contact_preferences_dnc'=>true,
					'stub_contact_preferences_mode'=>'email',
				),
			),
		);
	}

	/**
	 * Data: fromList
	 *
	 * @return array Values.
	 */
	function data_fromList() {
		return array(
			array(
				'1,2,3',
				null,
				array('1', '2', '3'),
			),
			array(
				array(0, '1,2,3'),
				null,
				array('0', '1', '2', '3'),
			),
			array(
				array('1,2;3,4'),
				';',
				array('1,2', '3,4'),
			),
			array(
				array('1, 2, 3,, 3, 4, 4'),
				array(
					'delimiter'=>',',
					'trim'=>true,
					'unique'=>true,
					'sort'=>false,
					'cast'=>'int',
					'min'=>2,
					'max'=>3,
				),
				array(2, 3),
			),
			array(
				array('1, 2, 3,, 3, 4, 4'),
				array(
					'unique'=>false,
					'cast'=>'int',
					'min'=>2,
					'max'=>3,
				),
				array(2, 3, 3),
			),
			array(
				array('2015-01-01', array(array('2010-01-01,2014-06-01'))),
				array(
					'unique'=>true,
					'min'=>'2011',
					'sort'=>true,
				),
				array('2014-06-01', '2015-01-01'),
			),
		);
	}

	/**
	 * Data: otherize
	 *
	 * @return array Values.
	 */
	function data_otherize() {
		$arr = array(
			'US'=>100,
			'CA'=>200,
			'CN'=>5,
			'GB'=>10,
			'MX'=>30,
		);

		return array(
			array(
				$arr,
				3,
				'Other',
				array(
					'CA'=>200,
					'US'=>100,
					'Other'=>45,
				),
			),
			array(
				$arr,
				6,
				'Other',
				array(
					'CA'=>200,
					'US'=>100,
					'MX'=>30,
					'GB'=>10,
					'CN'=>5,
				),
			),
			array(
				$arr,
				1,
				'Other',
				array(
					'Other'=>345,
				),
			),
			array(
				array(
					'US'=>'5%',
					'CA'=>'10¢',
					'MX'=>'$1.32',
					'TX'=>'hotdogs',
				),
				4,
				'Other',
				array(
					'MX'=>1.32,
					'CA'=>.1,
					'US'=>.05,
					'TX'=>0.0,
				),
			),
		);
	}

	/**
	 * Data: toCsv
	 *
	 * @return array Values.
	 */
	function data_toCsv() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		return array(
			array(
				$data,
				null,
				",",
				"\n",
				"\"NAME\",\"PHONE\"\n\"John\",\"+1 201-555-0123\"",
			),
			array(
				$data,
				$headers,
				",",
				"\n",
				"\"FIRST NAME\",\"PHONE NUMBER\"\n\"John\",\"+1 201-555-0123\"",
			),
			array(
				$data,
				null,
				"\t",
				"\r\n",
				"\"NAME\"\t\"PHONE\"\r\n\"John\"\t\"+1 201-555-0123\"",
			),
			array(
				array(array('NAME'=>'John "Cool" Dude')),
				$headers,
				",",
				"\n",
				"\"FIRST NAME\",\"PHONE NUMBER\"\n\"John \"\"Cool\"\" Dude\"",
			),
			array(
				array(
					array(
						'Name'=>'Josh',
						'Phone'=>'+1 201-555-0123',
					),
					array(
						'Phone'=>'+1 201-555-0123',
						'Name'=>'Josh',
					),
					array(
						'Name'=>'Josh',
						'Phone'=>'+1 201-555-0123',
						'State'=>'Nevada',
					),
					array(
						'Name'=>'Josh',
					),
				),
				null,
				",",
				"\n",
				"\"Name\",\"Phone\"\n\"Josh\",\"+1 201-555-0123\"\n\"Josh\",\"+1 201-555-0123\"\n\"Josh\",\"+1 201-555-0123\"\n\"Josh\",\"\""
			),
		);
	}

	/**
	 * Data: toIndexed
	 *
	 * @return array Values.
	 */
	function data_toIndexed() {
		return array(
			array(
				array(1),
				'key',
				'value',
				array(
					array(
						'key'=>0,
						'value'=>1,
					),
				),
			),
			array(
				array(),
				'key',
				'value',
				array(),
			),
			array(
				array('Foo'=>'Bar'),
				'value',
				'label',
				array(
					array(
						'value'=>'Foo',
						'label'=>'Bar',
					),
				),
			),
		);
	}

	/**
	 * Data: iDiff
	 *
	 * @return array Values.
	 */
	function data_iDiff() {
		// The last value in each set is the $expected.
		return array(
			array(
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat'),
				array('BAT', '800'),
				array(
					3=>'Sat',
					4=>'Mat',
					5=>'Matt',
					6=>'Tat',
					7=>800,
				),
			),
			array(
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat', 'bat', 'sat'),
				array('mat', 'matt', 'tat', 800),
				array(),
			),
		);
	}

	/**
	 * Data: iInArray
	 *
	 * @return array Values.
	 */
	function data_iInArray() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
				false,
			),
			array(
				'BAR',
				array('Foo'=>'Bar'),
				true,
				true,
			),
			array(
				2,
				array(2, 3, 4),
				true,
				true,
			),
			array(
				'2',
				array(2, 3, 4),
				false,
				true,
			),
			array(
				'2',
				array(2, 3, 4),
				true,
				false,
			),
		);
	}

	/**
	 * Data: iIntersect
	 *
	 * @return array Values.
	 */
	function data_iIntersect() {
		// The last value in each set is the $expected.
		return array(
			array(
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat'),
				array('BAT', '800'),
				array(),
			),
			array(
				array('Rat', 'Cat', 'Bat', 'Sat', 'Mat', 'Matt', 'Tat', 800),
				array('rat', 'cat', 'sat'),
				array('BAT', 'CAT', 'SAT'),
				array(
					1=>'Cat',
					3=>'Sat',
				),
			),
		);
	}

	/**
	 * Data: iKeyExists
	 *
	 * @return array Values.
	 */
	function data_iKeyExists() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
			),
			array(
				'food',
				array('Foo'=>'Bar'),
				false,
			),
			array(
				1,
				array(2, 3, 4),
				true,
			),
			array(
				18,
				array(2, 3, 4),
				false,
			),
		);
	}

	/**
	 * Data: iSearch
	 *
	 * @return array Values.
	 */
	function data_iSearch() {
		return array(
			array(
				'foo',
				array('Foo'=>'Bar'),
				true,
				false,
			),
			array(
				'BJÖRK',
				array('Foo'=>'Björk'),
				true,
				'Foo',
			),
			array(
				2,
				array(2, 3, 4),
				true,
				0,
			),
			array(
				'2',
				array(2, 3, 4),
				false,
				0,
			),
			array(
				'2',
				array(2, 3, 4),
				true,
				false,
			),
		);
	}

	/**
	 * Data: compare
	 *
	 * @return array Values.
	 */
	function data_compare() {
		$arr1 = array(1, 2, 3);
		$arr2 = array(2, 3, 1);
		$arr3 = array(
			'Foo'=>'Bar',
			'Bar'=>array(1, 2, 3),
		);
		$arr4 = array();

		return array(
			array($arr1, $arr1, true),
			array($arr1, $arr2, true),
			array($arr1, $arr3, false),
			array($arr3, $arr3, true),
			array($arr4, $arr4, true),
		);
	}

	/**
	 * Data: getType
	 *
	 * @return array Values.
	 */
	function data_getType() {
		return array(
			array(
				array(),
				false,
			),
			array(
				array(1, 2, 3),
				'sequential',
			),
			array(
				array(
					0=>1,
					1=>2,
					2=>3,
				),
				'sequential',
			),
			array(
				array(
					2=>3,
					0=>1,
				),
				'indexed',
			),
			array(
				array(
					0=>1,
					'bat'=>2,
				),
				'associative',
			),
			array(
				array(
					'foo'=>'bar',
				),
				'associative',
			),
		);
	}

	/**
	 * Data: pop
	 *
	 * @return array Values.
	 */
	function data_pop() {
		return array(
			array(
				array(1, 2, 3),
				3,
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
				),
				'Foo',
			),
			array(
				array(),
				false,
			),
		);
	}

	/**
	 * Data: popRand
	 *
	 * @return array Values.
	 */
	function data_popRand() {
		return array(
			array(
				array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0),
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
					'You'=>'Hoo',
					'apples',
					'carrots',
					'oranges',
					8,
					9,
					0,
					'hello',
				),
			),
			array(
				array(),
			),
			array(
				array('foobar'),
			),
		);
	}

	/**
	 * Data: popTop
	 *
	 * @return array Values.
	 */
	function data_popTop() {
		return array(
			array(
				array(1, 2, 3),
				1,
			),
			array(
				array(
					'Foo'=>'Bar',
					'Bar'=>'Foo',
				),
				'Bar',
			),
			array(
				array(),
				false,
			),
		);
	}
}
