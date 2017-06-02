<?php
/**
 * Class SanitizeTests
 *
 * @package blob-common
 */

/**
 * Test functions-sanitize.php.
 */
class SanitizeTests extends WP_UnitTestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * Lowercase
	 *
	 * @return void Nothing.
	 */
	function test_common_strtolower() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('queen björk ⅷ loves 3 apples.', common_strtolower($thing));
	}

	/**
	 * Uppercase
	 *
	 * @return void Nothing.
	 */
	function test_common_strtoupper() {
		$thing = 'THE lazY Rex ⅸ eAtS f00d.';

		$this->assertEquals('THE LAZY REX Ⅸ EATS F00D.', common_strtoupper($thing));
	}

	/**
	 * Sentence Case
	 *
	 * @return void Nothing.
	 */
	function test_common_ucfirst() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ loVes 3 aPplEs.', common_ucfirst($thing));
	}

	/**
	 * Title Case
	 *
	 * @return void Nothing.
	 */
	function test_common_ucwords() {
		$thing = 'quEen BjöRk Ⅷ loVes 3 aPplEs.';

		$this->assertEquals('QuEen BjöRk Ⅷ LoVes 3 APplEs.', common_ucwords($thing));
	}

	/**
	 * Money
	 *
	 * @return void Nothing.
	 */
	function test_common_format_money() {
		$thing = 2.5;
		$this->assertEquals('$2.50', common_format_money($thing));

		$thing = '1';
		$this->assertEquals('$1.00', common_format_money($thing));

		$thing = 2500;
		$this->assertEquals('$2500.00', common_format_money($thing));

		$thing = .23;
		$this->assertEquals('$0.23', common_format_money($thing, false));
		$this->assertEquals('23¢', common_format_money($thing, true));
	}

	/**
	 * Phone
	 *
	 * @return void Nothing.
	 */
	function test_common_format_phone() {
		$thing = 2342342345;
		$this->assertEquals('(234) 234-2345', common_format_phone($thing));
	}

	/**
	 * Inflect
	 *
	 * @return void Nothing.
	 */
	function test_common_inflect() {
		$count = 1;
		$single = '%d book';
		$plural = '%d books';

		$this->assertEquals('1 book', common_inflect($count, $single, $plural));

		$count = 2;
		$this->assertEquals('2 books', common_inflect($count, $single, $plural));

		$count = 0;
		$this->assertEquals('0 books', common_inflect($count, $single, $plural));
	}

	/**
	 * Excerpt
	 *
	 * @return void Nothing.
	 */
	function test_common_get_excerpt() {
		$thing = 'It ẉẩṩ a dark and stormy night.';

		$this->assertEquals('It ẉẩṩ a!', common_get_excerpt($thing, 3, '!', 'word'));
		$this->assertEquals('It ẉẩṩ a...', common_get_excerpt($thing, 3, '...', 'word'));
		$this->assertEquals('It ẉẩṩ…', common_get_excerpt($thing, 6, '…', 'char'));
	}

	/**
	 * Unix Slashit
	 *
	 * @return void Nothing.
	 */
	function test_common_unixslashit() {
		$thing = 'C:\Windows\Fonts';
		$this->assertEquals('C:/Windows/Fonts', common_unixslashit($thing));

		$thing = '/path/./to/foobar';
		$this->assertEquals('/path/to/foobar', common_unixslashit($thing));
	}

	/**
	 * Leading Slashit
	 *
	 * @return void Nothing.
	 */
	function test_common_leadingslashit() {
		$thing = '/hello/there';
		$this->assertEquals($thing, common_leadingslashit($thing));

		$thing = 'hello/there';
		$this->assertEquals('/hello/there', common_leadingslashit($thing));
	}

	/**
	 * Array to Indexed
	 *
	 * @return void Nothing.
	 */
	function test_common_array_to_indexed() {
		$thing = array(1);
		$this->assertEquals(array(array('key'=>0, 'value'=>1)), common_array_to_indexed($thing));
	}

	/**
	 * To CSV
	 *
	 * @return void Nothing.
	 */
	function test_common_to_csv() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = common_to_csv($data);
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = common_to_csv($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));

		$csv = common_to_csv($data, $headers, "\t");
		$this->assertEquals(true, false !== strpos($csv, "\t"));
	}

	/**
	 * To XLS
	 *
	 * @return void Nothing.
	 */
	function test_common_to_xls() {
		$data = array(array('NAME'=>'John', 'PHONE'=>'+1 201-555-0123'));
		$headers = array('FIRST NAME', 'PHONE NUMBER');

		$csv = common_to_xls($data);
		$this->assertEquals(true, false !== strpos($csv, 'NAME'));

		$csv = common_to_xls($data, $headers);
		$this->assertEquals(true, false !== strpos($csv, 'FIRST NAME'));
	}

	/**
	 * To Range
	 *
	 * @return void Nothing.
	 */
	function test_common_to_range() {
		$this->assertEquals(3, common_to_range(3, 1, 5));
		$this->assertEquals(3, common_to_range(3, 1));
		$this->assertEquals(3, common_to_range(3, null, 5));

		$this->assertEquals('2015-01-15', common_to_range('2015-01-01', '2015-01-15', '2015-02-01'));
	}

	/**
	 * In Range
	 *
	 * @return void Nothing.
	 */
	function test_common_in_range() {
		$thing = 1;

		$this->assertEquals(true, common_in_range($thing, -1, 5));
		$this->assertEquals(false, common_in_range($thing, 2, 5));
		$this->assertEquals(false, common_in_range($thing, -2, 0));

		$thing = '2015-01-02';
		$this->assertEquals(true, common_in_range($thing, '2015-01-01', '2015-01-15'));
		$this->assertEquals(false, common_in_range($thing, '2015-01-15', '2015-01-20'));
	}

	/**
	 * Length in Range
	 *
	 * @return void Nothing.
	 */
	function test_common_length_in_range() {
		$thing = 'cat';
		$this->assertEquals(true, common_length_in_range($thing, 1, 5));
		$this->assertEquals(true, common_length_in_range($thing, 3, 3));

		$thing = 'Ḉẩt';
		$this->assertEquals(true, common_length_in_range($thing, 3, 3));
	}

	/**
	 * UTF-8
	 *
	 * @return void Nothing.
	 */
	function test_common_utf8() {
		$thing = 'Björk Guðmundsdóttir';

		$str = common_utf8($thing);
		$this->assertEquals('UTF-8', mb_detect_encoding($str));

		$str = common_sanitize_utf8($thing);
		$this->assertEquals('UTF-8', mb_detect_encoding($str));
	}

	/**
	 * Sanitize Name
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_name() {
		$thing = "åsa-britt\nkjellén";
		$this->assertEquals('Åsa-Britt Kjellén', common_sanitize_name($thing));
	}

	/**
	 * Sanitize Printable
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_printable() {
		$thing = " test\t ing\n";
		$this->assertEquals(" test\t ing\n", common_sanitize_printable($thing));
	}

	/**
	 * Sanitize CSV
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_csv() {
		$thing = 'Hello"';
		$this->assertEquals('Hello""', common_sanitize_csv($thing));

		$thing = "New\nLine";
		$this->assertEquals('New Line', common_sanitize_csv($thing));
	}

	/**
	 * Sanitize Newlines
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_newlines() {
		$thing = "Björk  Guðmundsdóttir\n";

		$this->assertEquals('Björk Guðmundsdóttir', common_sanitize_newlines($thing));
		$this->assertEquals('Björk Guðmundsdóttir', common_sanitize_newlines($thing, 1));

		$thing = "New\n\n\nLine!";
		$this->assertEquals("New\n\nLine!", common_sanitize_newlines($thing, 2));
	}

	/**
	 * Sanitize Spaces
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_spaces() {
		$thing = " Björk  Guðmundsdóttir\t ";
		$this->assertEquals('Björk Guðmundsdóttir', common_sanitize_spaces($thing));
	}

	/**
	 * Sanitize Whitespace
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_whitespace() {
		$thing = " Björk\n\n\nGuðmundsdóttir\t ";
		$this->assertEquals('Björk Guðmundsdóttir', common_sanitize_whitespace($thing));
	}

	/**
	 * Sanitize Quotes
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_quotes() {
		$thing = '“T’was the night before Christmas...”';
		$this->assertEquals('"T\'was the night before Christmas..."', common_sanitize_quotes($thing));
	}

	/**
	 * Sanitize JS Variable
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_js_variable() {
		$thing = "What's up?";
		$this->assertEquals("What\'s up?", common_sanitize_js_variable($thing));

		$thing = "What's up?";
		$this->assertEquals("What's up?", common_sanitize_js_variable($thing, '"'));
	}

	/**
	 * Sanitize Email
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_email() {
		$thing = 'Hello@Blo"bfolio.Com';
		$this->assertEquals('hello@blobfolio.com', common_sanitize_email($thing));

		$thing = 'Hello@Blobfolio';
		$this->assertEquals(false, common_sanitize_email($thing));
	}

	/**
	 * Sanitize ZIP5
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_zip5() {
		$this->assertEquals('00123', common_sanitize_zip5(123));
		$this->assertEquals('12345', common_sanitize_zip5(12345));
		$this->assertEquals('', common_sanitize_zip5('no'));
		$this->assertEquals('', common_sanitize_zip5(0));
	}

	/**
	 * Sanitize IP
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_ip() {
		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals('2600:3c00::f03c:91ff:feae:ff2', common_sanitize_ip($thing));

		$thing = '127.00.0.1';
		$this->assertEquals('', common_sanitize_ip($thing));
	}

	/**
	 * Sanitize Number
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_number() {
		$thing = 'string';
		$this->assertEquals(0.0, common_sanitize_number($thing));
		$this->assertEquals('double', gettype(common_sanitize_number($thing)));
	}

	/**
	 * Sanitize Bool
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_bool() {
		$thing = 'string';
		$this->assertEquals(true, common_sanitize_bool($thing));
		$this->assertEquals(true, common_sanitize_boolean($thing));

		$this->assertEquals('boolean', gettype(common_sanitize_bool($thing)));

		$thing = 'off';
		$this->assertEquals(false, common_sanitize_bool($thing));

		$thing = 'FALSE';
		$this->assertEquals(false, common_sanitize_bool($thing));

		$thing = 1;
		$this->assertEquals(true, common_sanitize_bool($thing));

		$thing = array(1, 'Off', false);
		$this->assertEquals(array(true, false, false), common_sanitize_bool($thing));

		$this->assertEquals(true, common_sanitize_bool($thing, true));
	}

	/**
	 * Sanitize Float
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_float() {
		$thing = 'string';
		$this->assertEquals(0.0, common_sanitize_float($thing));

		$this->assertEquals('double', gettype(common_sanitize_float($thing)));

		$thing = '$2.50';
		$this->assertEquals(2.5, common_sanitize_float($thing));
		$this->assertEquals(2.5, common_doubleval($thing));
		$this->assertEquals(2.5, common_floatval($thing));

		$thing = 1;
		$this->assertEquals(1.0, common_sanitize_float($thing));

		$thing = '50%';
		$this->assertEquals(.5, common_sanitize_float($thing));

		$thing = '67¢';
		$this->assertEquals(.67, common_sanitize_float($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1.0, 2.5, 0.0), common_sanitize_float($thing));

		$this->assertEquals(0, common_sanitize_float($thing, true));
	}

	/**
	 * Sanitize By Type
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_by_type() {
		$thing = array('false', 2.5, true, 1);

		$this->assertEquals(array('false', 2.5, true, 1), common_sanitize_by_type($thing, 'array'));

		$this->assertEquals(array(false, true, true, true), common_sanitize_by_type($thing, 'bool'));
		$this->assertEquals(array(false, true, true, true), common_sanitize_by_type($thing, 'boolean'));

		$this->assertEquals(array(0, 2.5, 1.0, 1.0), common_sanitize_by_type($thing, 'float'));
		$this->assertEquals(array(0, 2.5, 1.0, 1.0), common_sanitize_by_type($thing, 'double'));

		$this->assertEquals(array(0, 2, 1, 1), common_sanitize_by_type($thing, 'integer'));
		$this->assertEquals(array(0, 2, 1, 1), common_sanitize_by_type($thing, 'int'));

		$this->assertEquals(array('false', '2.5', '1', '1'), common_sanitize_by_type($thing, 'string'));

		$this->assertEquals(true, common_sanitize_by_type($thing, 'bool', true));
		$this->assertEquals(0.0, common_sanitize_by_type($thing, 'float', true));
		$this->assertEquals(0, common_sanitize_by_type($thing, 'integer', true));
		$this->assertEquals('', common_sanitize_by_type($thing, 'string', true));
	}

	/**
	 * Sanitize Int
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_int() {
		$thing = 'string';
		$this->assertEquals(0, common_sanitize_int($thing));

		$this->assertEquals('integer', gettype(common_sanitize_int($thing)));

		$thing = 2.5;
		$this->assertEquals(2, common_sanitize_int($thing));
		$this->assertEquals(2, common_intval($thing));

		$thing = '33';
		$this->assertEquals(33, common_sanitize_int($thing));

		$thing = 'on';
		$this->assertEquals(1, common_sanitize_int($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array(1, 2, 0), common_sanitize_int($thing));

		$this->assertEquals(0, common_sanitize_int($thing, true));
	}

	/**
	 * Sanitize String
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_string() {
		$thing = 'string';
		$this->assertEquals('string', common_sanitize_string($thing));

		$this->assertEquals('string', gettype(common_sanitize_string($thing)));

		$thing = 2.5;
		$this->assertEquals('2.5', common_sanitize_string($thing));
		$this->assertEquals('2.5', common_strval($thing));

		$thing = false;
		$this->assertEquals('', common_sanitize_string($thing));

		$thing = array(1, '2.5', false);
		$this->assertEquals(array('1', '2.5', ''), common_sanitize_string($thing));

		$this->assertEquals('', common_sanitize_string($thing, true));
	}

	/**
	 * Sanitize Array
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_array() {
		$thing = 'string';
		$this->assertEquals(array('string'), common_sanitize_array($thing));

		$this->assertEquals('array', gettype(common_sanitize_array($thing)));

		$thing = array('string');
		$this->assertEquals(array('string'), common_sanitize_array($thing));

		$thing = null;
		$this->assertEquals(array(), common_sanitize_array($thing));
	}

	/**
	 * Sanitize Datetime
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_datetime() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02 00:00:00', common_sanitize_datetime($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02 13:23:11', common_sanitize_datetime($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02 13:23:11', common_sanitize_datetime($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00 00:00:00', common_sanitize_datetime($thing));
	}

	/**
	 * Sanitize Date
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_date() {
		$thing = '2015-01-02';
		$this->assertEquals('2015-01-02', common_sanitize_date($thing));

		$thing = '2015-01-02 13:23:11';
		$this->assertEquals('2015-01-02', common_sanitize_date($thing));

		$thing = strtotime($thing);
		$this->assertEquals('2015-01-02', common_sanitize_date($thing));

		$thing = 'Not Time';
		$this->assertEquals('0000-00-00', common_sanitize_date($thing));
	}

	/**
	 * Sanitize Phone
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_phone() {
		$this->assertEquals('2342342345', common_sanitize_phone('(234) 234-2345'));
	}

	/**
	 * Sanitize Domain Name
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_domain_name() {
		$thing = 'https://www.Google.com';
		$this->assertEquals('google.com', common_sanitize_domain_name($thing));

		$thing = 'www.Google.com';
		$this->assertEquals('google.com', common_sanitize_domain_name($thing));

		$thing = '50.116.18.174';
		$this->assertEquals('', common_sanitize_domain_name($thing));
	}

	/**
	 * Is UTF-8
	 *
	 * @return void Nothing.
	 */
	function test_common_is_utf8() {
		$thing = 'hello';
		$this->assertEquals(true, common_is_utf8($thing));

		$thing = 50;
		$this->assertEquals(true, common_is_utf8($thing));

		$thing = "\xc3\x28";
		$this->assertEquals(false, common_is_utf8($thing));
	}

	/**
	 * Validate Email
	 *
	 * @return void Nothing.
	 */
	function test_common_validate_email() {
		$thing = 'josh';
		$this->assertEquals(false, common_validate_email($thing));

		$thing = 'josh@localhost';
		$this->assertEquals(false, common_validate_email($thing));

		$thing = 'josh@domain.com';
		$this->assertEquals(true, common_validate_email($thing));
	}

	/**
	 * Validate Phone
	 *
	 * @return void Nothing.
	 */
	function test_common_validate_phone() {
		$thing = '2342342345';
		$this->assertEquals(true, common_validate_phone($thing));

		$thing = '1231231234';
		$this->assertEquals(false, common_validate_phone($thing));
	}

	/**
	 * Validate Credit Card
	 *
	 * @return void Nothing.
	 */
	function test_common_validate_cc() {
		$thing = '4242424242424242';
		$this->assertEquals(true, common_validate_cc($thing));

		$thing = '4242424242424241';
		$this->assertEquals(false, common_validate_cc($thing));
	}

	/**
	 * Sanitize URL
	 *
	 * @return void Nothing.
	 */
	function test_common_sanitize_url() {
		$this->assertEquals('', common_sanitize_url('google.com'));
		$this->assertEquals('https://google.com', common_sanitize_url('//google.com'));
		$this->assertEquals('http://google.com', common_sanitize_url('http://google.com'));
	}

	/**
	 * Validate Domain Name
	 *
	 * @return void Nothing.
	 */
	function test_common_validate_domain_name() {
		$thing = 'localhost';
		$this->assertEquals(false, common_validate_domain_name($thing));

		$thing = 'localhost.com';
		$this->assertEquals(true, common_validate_domain_name($thing));
	}
}
