<?php
/**
 * Blobfolio\Strings
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

/**
 * Test Suite
 */
class strings_tests extends \PHPUnit\Framework\TestCase {

	const ASSETS = __DIR__ . '/assets/';



	// -----------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------

	/**
	 * Test: accents
	 *
	 * @dataProvider data_accents
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_accents(string $value, string $expected) {
		$result = \Blobfolio\Strings::accents($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: controlChars
	 *
	 * @dataProvider data_controlChars
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_controlChars(string $value, string $expected) {
		$result = \Blobfolio\Strings::controlChars($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: excerpt
	 *
	 * @dataProvider data_excerpt
	 *
	 * @param string $value Value.
	 * @param int $length Length.
	 * @param int $flags Flags.
	 * @param string $expected Expected.
	 */
	function test_excerpt(string $value, int $length, int $flags, string $expected) {
		$result = \Blobfolio\Strings::excerpt($value, $length, $flags);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: inflect
	 *
	 * @dataProvider data_inflect
	 *
	 * @param mixed $value Value.
	 * @param string $single Single.
	 * @param string $plural Plural.
	 * @param string $expected Expected.
	 */
	function test_inflect($value, string $single, string $plural, string $expected) {
		$result = \Blobfolio\Strings::inflect($value, $single, $plural);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: niceText
	 *
	 * @dataProvider data_niceText
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	function test_niceText(string $value, int $newlines, string $expected) {
		$result = \Blobfolio\Strings::niceText($value, $newlines);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: printable
	 *
	 * @dataProvider data_printable
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_printable(string $value, string $expected) {
		$result = \Blobfolio\Strings::printable($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: quotes
	 *
	 * @dataProvider data_quotes
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_quotes(string $value, string $expected) {
		$result = \Blobfolio\Strings::quotes($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: length
	 *
	 * @dataProvider data_length
	 *
	 * @param string $value Value.
	 * @param int $expected Expected.
	 */
	function test_length(string $value, int $expected) {
		$result = \Blobfolio\Strings::length($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: pad
	 *
	 * @dataProvider data_pad
	 *
	 * @param string $value Value.
	 * @param int $pad_length Pad Length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Type.
	 * @param string $expected Expected.
	 */
	function test_pad(string $value, int $pad_length, string $pad_string, int $pad_type, string $expected) {
		$result = \Blobfolio\Strings::pad($value, $pad_length, $pad_string, $pad_type);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: split
	 *
	 * @dataProvider data_split
	 *
	 * @param string $value Value.
	 * @param int $length Length.
	 * @param mixed $expected Expected.
	 */
	function test_split(string $value, int $length, $expected) {
		$result = \Blobfolio\Strings::split($value, $length);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: strpos
	 *
	 * @dataProvider data_strpos
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	function test_strpos(string $haystack, string $needle, int $offset, $expected) {
		$result = \Blobfolio\Strings::strpos($haystack, $needle, $offset);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: strrev
	 *
	 * @dataProvider data_strrev
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_strrev(string $value, string $expected) {
		$result = \Blobfolio\Strings::strrev($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: strrpos
	 *
	 * @dataProvider data_strrpos
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @param mixed $expected Expected.
	 */
	function test_strrpos(string $haystack, string $needle, int $offset, $expected) {
		$result = \Blobfolio\Strings::strrpos($haystack, $needle, $offset);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toLower
	 *
	 * @dataProvider data_toLower
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_toLower(string $value, string $expected) {
		$result = \Blobfolio\Strings::toLower($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toUpper
	 *
	 * @dataProvider data_toUpper
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_toUpper(string $value, string $expected) {
		$result = \Blobfolio\Strings::toUpper($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: substr
	 *
	 * @dataProvider data_substr
	 *
	 * @param string $value Value.
	 * @param int $start Start.
	 * @param mixed $length Length.
	 * @param string $expected Expected.
	 */
	function test_substr(string $value, int $start, $length, string $expected) {
		$result = \Blobfolio\Strings::substr($value, $start, $length);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: substrCount
	 *
	 * @dataProvider data_substrCount
	 *
	 * @param string $haystack Value.
	 * @param string $needle Needle.
	 * @param int $expected Expected.
	 */
	function test_substrCount(string $value, string $needle, int $expected) {
		$result = \Blobfolio\Strings::substrCount($value, $needle);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: trim
	 *
	 * @dataProvider data_trim
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_trim(string $value, string $expected) {
		$result = \Blobfolio\Strings::trim($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toSentence
	 *
	 * @dataProvider data_toSentence
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_toSentence($value, $expected) {
		$result = \Blobfolio\Strings::toSentence($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toTitle
	 *
	 * @dataProvider data_toTitle
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_toTitle(string $value, string $expected) {
		$result = \Blobfolio\Strings::toTitle($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toYesNo
	 *
	 * @dataProvider data_toYesNo
	 *
	 * @param mixed $value Value.
	 * @param string $yes Yes.
	 * @param string $no No.
	 * @param string $expected Expected.
	 */
	function test_toYesNo($value, string $yes, string $no, string $expected) {
		$result = \Blobfolio\Strings::toYesNo($value, $yes, $no);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: utf8
	 *
	 * @dataProvider data_utf8
	 *
	 * @param string $value Value.
	 * @param string $expected Expected.
	 */
	function test_utf8(string $value, string $expected) {
		$result = \Blobfolio\Strings::utf8($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: utf8Recursive
	 *
	 * @dataProvider data_utf8Recursive
	 *
	 * @param mixed $value Value.
	 * @param mixed $expected Expected.
	 */
	function test_utf8Recursive($value, $expected) {
		$result = \Blobfolio\Strings::utf8Recursive($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: whitespace
	 *
	 * @dataProvider data_whitespace
	 *
	 * @param string $value Value.
	 * @param int $newlines Newlines.
	 * @param string $expected Expected.
	 */
	function test_whitespace(string $value, int $newlines, string $expected) {
		$result = \Blobfolio\Strings::whitespace($value, $newlines);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: wordwrap
	 *
	 * @dataProvider data_wordwrap
	 *
	 * @param string $value Value.
	 * @param int $width Width.
	 * @param string $eol Eol.
	 * @param bool $cut Cut.
	 * @param string $expected Expected.
	 */
	function test_wordwrap(string $value, int $width, string $eol, bool $cut, string $expected) {
		$result = \Blobfolio\Strings::wordwrap($value, $width, $eol, $cut);

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
	 * @param bool $expected Expected.
	 */
	function test_inRange(string $value, $min, $max, bool $expected) {
		$result = \Blobfolio\Strings::inRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: lengthInRange
	 *
	 * @dataProvider data_lengthInRange
	 *
	 * @param string $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param bool $expected Expected.
	 */
	function test_lengthInRange(string $value, $min, $max, bool $expected) {
		$result = \Blobfolio\Strings::lengthInRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: toRange
	 *
	 * @dataProvider data_toRange
	 *
	 * @param string $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @param string $expected Expected.
	 */
	function test_toRange(string $value, $min, $max, string $expected) {
		$result = \Blobfolio\Strings::toRange($value, $min, $max);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: isUtf8
	 *
	 * @dataProvider data_isUtf8
	 *
	 * @param string $value Value.
	 * @param bool $expected Expected.
	 */
	function test_isUtf8($value, bool $expected) {
		$result = \Blobfolio\Strings::isUtf8($value);

		$this->assertSame($expected, $result);
	}

	/**
	 * Test: random
	 */
	function test_random() {
		// Let's make sure we get a different result.
		$last = \Blobfolio\Strings::random(10);
		$result = false;
		for ($x = 0; $x < 25; ++$x) {
			$result = \Blobfolio\Strings::random(10);
			if ($result !== $last) {
				break;
			}
		}

		$this->assertNotSame($last, $result);
		$this->assertSame('string', gettype($last));
		$this->assertSame('string', gettype($result));

		// Try an alternate alphabet.
		$result = \Blobfolio\Strings::random(50, 'Bö');
		$this->assertSame(50, mb_strlen($result, 'UTF-8'));
		$this->assertTrue(false !== mb_strpos($result, 'ö', 0, 'UTF-8'));
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Data: accents
	 *
	 * @return array Values.
	 */
	function data_accents() {
		return array(
			array(
				'Björk Guðmundsdóttir, best þekkt sem Björk (fædd 21. nóvember 1965 í Reykjavík) er íslenskur popptónlistarmaður, sem hefur náð alþjóðlegri hylli.',
				'Bjork Gudmundsdottir, best thekkt sem Bjork (faedd 21. november 1965 i Reykjavik) er islenskur popptonlistarmadur, sem hefur nad althjodlegri hylli.',
			),
			array(
				'Nabokov explore plusieurs thèmes, dont certains déjà présents dans ses ouvrages précédents.',
				'Nabokov explore plusieurs themes, dont certains deja presents dans ses ouvrages precedents.',
			),
			array(
				'Rosé the Day Away',
				'Rose the Day Away',
			),
		);
	}

	/**
	 * Data: controlChars
	 *
	 * @return array Values.
	 */
	function data_controlChars() {
		return array(
			array(
				'\0Björk',
				'Björk',
			),
			array(
				chr(27) . '\\Björk',
				'\\Björk',
			),
		);
	}

	/**
	 * Data: excerpt
	 *
	 * @return array Values.
	 */
	function data_excerpt() {
		return array(
			array(
				'It ẉẩṩ a dark and stormy night.',
				3,
				\Blobfolio\Blobfolio::EXCERPT_BREAK_WORD |
				\Blobfolio\Blobfolio::EXCERPT_ELLIPSIS,
				'It ẉẩṩ a…',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				30,
				\Blobfolio\Blobfolio::EXCERPT_BREAK_WORD,
				'It ẉẩṩ a dark and stormy night.',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				3,
				\Blobfolio\Blobfolio::EXCERPT_BREAK_WORD,
				'It ẉẩṩ a',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				6,
				\Blobfolio\Blobfolio::EXCERPT_BREAK_CHARACTER |
				\Blobfolio\Blobfolio::EXCERPT_ELLIPSIS,
				'It ẉẩṩ…',
			),
			array(
				'It ẉẩṩ a dark and stormy night.',
				6,
				\Blobfolio\Blobfolio::EXCERPT_BREAK_CHARACTER |
				\Blobfolio\Blobfolio::EXCERPT_ELLIPSIS |
				\Blobfolio\Blobfolio::EXCERPT_INCLUSIVE,
				'It ẉẩ…',
			),
		);
	}

	/**
	 * Data: inflect
	 *
	 * @return array Values.
	 */
	function data_inflect() {
		return array(
			array(
				1,
				'%d book',
				'%d books',
				'1 book',
			),
			array(
				0,
				'%d book',
				'%d books',
				'0 books',
			),
			array(
				1.5,
				'%.01f book',
				'%.01f books',
				'1.5 books',
			),
			array(
				array(1, 2, 3),
				'%d book',
				'%d books',
				'3 books',
			),
		);
	}

	/**
	 * Data: niceText
	 *
	 * @return array Values.
	 */
	function data_niceText() {
		return array(
			array(
				"Once upon \r\na “time”, \0there was a Björk.",
				0,
				'Once upon a "time", there was a Björk.',
			),
			array(
				"Once upon \r\na “time”, \0there was a Björk.",
				1,
				'Once upon' . "\n" . 'a "time", there was a Björk.',
			),
		);
	}

	/**
	 * Data: printable
	 *
	 * @return array Values.
	 */
	function data_printable() {
		return array(
			array(
				"\t ålén\n  ☺\0",
				"\t ålén\n  ☺",
			),
			array(
				// This starter text has some zero-width characters
				// buried in it. Depending on the code viewer, that
				// might not be obvious.
				"Confidential Announcement: ‌﻿​﻿‌﻿​﻿​﻿‌﻿​﻿‌﻿‍﻿‌﻿​﻿​﻿‌﻿‌﻿​﻿‌﻿​﻿‍﻿‌﻿​﻿​﻿​﻿‌﻿‌﻿​﻿‌﻿‍﻿‌﻿​﻿​﻿‌﻿​﻿​﻿​﻿​This is some confidential text that you really shouldn't be sharing anywhere else.",
				// This one is clean.
				"Confidential Announcement: This is some confidential text that you really shouldn't be sharing anywhere else.",
			),
		);
	}

	/**
	 * Data: quotes
	 *
	 * @return array Values.
	 */
	function data_quotes() {
		return array(
			array(
				'“T’was the night before Christmas...”',
				'"T\'was the night before Christmas..."',
			),
		);
	}

	/**
	 * Data: length
	 *
	 * @return array Values.
	 */
	function data_length() {
		return array(
			array(
				'Björk',
				5,
			),
			array(
				'Happy Days',
				10,
			),
		);
	}

	/**
	 * Data: pad
	 *
	 * @return array Values.
	 */
	function data_pad() {
		return array(
			array(
				'Björk',
				10,
				'~',
				STR_PAD_LEFT,
				'~~~~~Björk',
			),
			array(
				'Björk',
				10,
				'~',
				STR_PAD_BOTH,
				'~~~Björk~~',
			),
			array(
				'Björk',
				10,
				'~',
				STR_PAD_RIGHT,
				'Björk~~~~~',
			),
			array(
				'Björk',
				3,
				'~',
				STR_PAD_RIGHT,
				'Björk',
			),
			array(
				'Björk',
				10,
				'',
				STR_PAD_RIGHT,
				'Björk',
			),
		);
	}

	/**
	 * Data: split
	 *
	 * @return array Values.
	 */
	function data_split() {
		return array(
			array(
				'Björk',
				1,
				array('B', 'j', 'ö', 'r', 'k'),
			),
			array(
				'Björk',
				3,
				array('Bjö', 'rk'),
			),
		);
	}

	/**
	 * Data: strpos
	 *
	 * @return array Values.
	 */
	function data_strpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				3,
			),
			array(
				'Björk Björk',
				'ö',
				0,
				2,
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8,
			),
			array(
				'Björk Björk',
				'E',
				0,
				false,
			),
		);
	}

	/**
	 * Data: strrev
	 *
	 * @return array Values.
	 */
	function data_strrev() {
		return array(
			array(
				'Björk',
				'kröjB',
			),
			array(
				"Hello-World\n",
				"\ndlroW-olleH",
			),
		);
	}

	/**
	 * Data: strrpos
	 *
	 * @return array Values.
	 */
	function data_strrpos() {
		return array(
			array(
				'Björk Björk',
				'r',
				0,
				9,
			),
			array(
				'Björk Björk',
				'ö',
				0,
				8,
			),
			array(
				'Björk Björk',
				'ö',
				4,
				8,
			),
			array(
				'Björk Björk',
				'E',
				0,
				false,
			),
		);
	}

	/**
	 * Data: toLower
	 *
	 * @return array Values.
	 */
	function data_toLower() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'queen björk ⅷ loves 3 apples.',
			),
			array(
				"Hello-world\n",
				"hello-world\n",
			),
			array(
				'HAPPY',
				'happy',
			),
		);
	}

	/**
	 * Data: toYesNo
	 *
	 * @return array Values.
	 */
	function data_toYesNo() {
		return array(
			array(
				array(),
				'Yes',
				'No',
				'No',
			),
			array(
				array(1),
				'Yes',
				'No',
				'Yes',
			),
			array(
				0.0,
				'Yes',
				'No',
				'No',
			),
			array(
				123,
				'Y',
				'N',
				'Y',
			),
		);
	}

	/**
	 * Data: toUpper
	 *
	 * @return array Values.
	 */
	function data_toUpper() {
		return array(
			array(
				'THE lazY Rex ⅸ eAtS f00d.',
				'THE LAZY REX Ⅸ EATS F00D.',
			),
			array(
				"Hello-world\n",
				"HELLO-WORLD\n",
			),
			array(
				'happy',
				'HAPPY',
			),
		);
	}

	/**
	 * Data: substr
	 *
	 * @return array Values.
	 */
	function data_substr() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				0,
				11,
				'quEen BjöRk',
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				6,
				null,
				'BjöRk Ⅷ loVes 3 aPplEs.',
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				-7,
				null,
				'aPplEs.',
			),
		);
	}

	/**
	 * Data: substrCount
	 *
	 * @return array Values.
	 */
	function data_substrCount() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'BjöRk',
				1,
			),
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'töast',
				0,
			),
			array(
				"Hello\nWorld\n",
				"\n",
				2,
			),
		);
	}

	/**
	 * Data: trim
	 *
	 * @return array Values.
	 */
	function data_trim() {
		return array(
			array(
				' 	test ',
				'test',
			),
			array(
				"\ntöast",
				'töast',
			),
			array(
				chr(0xA0) . ' test' . chr(0xA0),
				'test',
			),
		);
	}

	/**
	 * Data: toSentence
	 *
	 * @return array Values.
	 */
	function data_toSentence() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'QuEen BjöRk Ⅷ loVes 3 aPplEs.',
			),
			array(
				'hello-world',
				'Hello-world',
			),
			array(
				13,
				'13',
			),
			array(
				13,
				'13',
			),
			array(
				'happy times are here again.',
				'Happy times are here again.',
			),
		);
	}

	/**
	 * Data: toTitle
	 *
	 * @return array Values.
	 */
	function data_toTitle() {
		return array(
			array(
				'quEen BjöRk Ⅷ loVes 3 aPplEs.',
				'QuEen BjöRk Ⅷ LoVes 3 APplEs.',
			),
			array(
				'hello-world',
				'Hello-World',
			),
			array(
				'happy times are here again.',
				'Happy Times Are Here Again.',
			),
		);
	}

	/**
	 * Data: utf8
	 *
	 * @return array Values.
	 */
	function data_utf8() {
		return array(
			array(
				'Björk Guðmundsdóttir',
				'Björk Guðmundsdóttir',
			),
			array(
				"Hello\nWorld",
				"Hello\nWorld",
			),
			array(
				file_get_contents(self::ASSETS . 'text-utf8.txt'),
				"Hírek\n",
			),
			array(
				file_get_contents(self::ASSETS . 'text-latin.txt'),
				"Hírek\n",
			),
		);
	}

	/**
	 * Data: utf8Recursive
	 *
	 * @return array Values.
	 */
	function data_utf8Recursive() {
		return array(
			array(
				'Björk Guðmundsdóttir',
				'Björk Guðmundsdóttir',
			),
			array(
				array(
					"Hello\nWorld",
					123,
					array(
						file_get_contents(self::ASSETS . 'text-latin.txt'),
					),
				),
				array(
					"Hello\nWorld",
					123,
					array(
						"Hírek\n",
					),
				),
			),
			array(
				true,
				true,
			),
		);
	}

	/**
	 * Data: whitespace
	 *
	 * @return array Values.
	 */
	function data_whitespace() {
		return array(
			array(
				" Björk\n\n",
				0,
				'Björk',
			),
			array(
				" Björk\n\n",
				2,
				'Björk',
			),
			array(
				"Happy\n\n\nSpaces",
				2,
				"Happy\n\nSpaces",
			),
			array(
				"Happy\n\n\nSpaces\t&\tPlaces",
				0,
				'Happy Spaces & Places',
			),
		);
	}

	/**
	 * Data: wordwrap
	 *
	 * @return array Values.
	 */
	function data_wordwrap() {
		return array(
			array(
				'Björk',
				2,
				"\n",
				false,
				'Björk',
			),
			array(
				'Björk',
				2,
				"\n",
				true,
				"Bj\nör\nk",
			),
			array(
				"Björk's new album is an action-packed thrill-ride—to those with taste.",
				35,
				"\n",
				false,
				"Björk's new album is an action-\npacked thrill-ride—to those with\ntaste.",
			),
			array(
				"Björk's new album is an action-packed thrill-ride—to those with taste.",
				35,
				"\n",
				true,
				"Björk's new album is an action-\npacked thrill-ride—to those with\ntaste.",
			),
			array(
				"Björk's dress is attention-getting.",
				15,
				"\n",
				true,
				"Björk's dress\nis attention-\ngetting.",
			),
			array(
				'Visit https://blobfolio.com for more information.',
				10,
				"\n",
				false,
				"Visit\nhttps://blobfolio.com\nfor more\ninformation.",
			),
			array(
				'Visit https://blobfolio.com for more information.',
				10,
				"\n",
				true,
				"Visit\nhttps://bl\nobfolio.co\nm for more\ninformatio\nn.",
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
				'cat',
				'aardvark',
				'dog',
				true,
			),
			array(
				'2010-01-01',
				'2011-01-01',
				null,
				false,
			),
			array(
				'2010-01-01',
				null,
				'2011-01-01',
				true,
			),
		);
	}

	/**
	 * Data: lengthInRange
	 *
	 * @return array Values.
	 */
	function data_lengthInRange() {
		return array(
			array(
				'Ḉẩt',
				1,
				3,
				true,
			),
			array(
				'Ḉẩt',
				4,
				null,
				false,
			),
			array(
				'Cat',
				1,
				3,
				true,
			),
			array(
				'Cat',
				null,
				4,
				true,
			),
		);
	}

	/**
	 * Data: toRange
	 *
	 * @return array Values.
	 */
	function data_toRange() {
		return array(
			array(
				'cat',
				'aardvark',
				'dog',
				'cat',
			),
			array(
				'2010-01-01',
				'2011-01-01',
				null,
				'2011-01-01',
			),
			array(
				'2010-01-01',
				null,
				'2011-01-01',
				'2010-01-01',
			),
		);
	}

	/**
	 * Data: isUtf8
	 *
	 * @return array Values.
	 */
	function data_isUtf8() {
		return array(
			array(
				1,
				true,
			),
			array(
				'Hello World',
				true,
			),
			array(
				"\xc3\x28",
				false,
			),
			array(
				file_get_contents(self::ASSETS . 'text-latin.txt'),
				false,
			),
		);
	}
}
