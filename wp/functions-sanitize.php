<?php
/**
 * Sanitizing Functions
 *
 * This file contains functions for sanitizing and
 * formatting various kinds of data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (! \defined('ABSPATH')) {
	exit;
}

use blobfolio\common\cast as v_cast;
use blobfolio\common\data;
use blobfolio\common\file as v_file;
use blobfolio\common\format as v_format;
use blobfolio\common\mb as v_mb;
use blobfolio\common\ref\cast as r_cast;
use blobfolio\common\sanitize as v_sanitize;
use blobfolio\domain\domain;

// ---------------------------------------------------------------------
// Case Conversion
// ---------------------------------------------------------------------

if (! \function_exists('common_strtolower')) {
	/**
	 * Wrapper For strtolower()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_strtolower($str='') {
		return v_mb::strtolower($str);
	}
}

if (! \function_exists('common_strtoupper')) {
	/**
	 * Wrapper For strtoupper()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_strtoupper($str='') {
		return v_mb::strtoupper($str);
	}
}

if (! \function_exists('common_ucwords')) {
	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_ucwords($str='') {
		return v_mb::ucwords($str);
	}
}

if (! \function_exists('common_ucfirst')) {
	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_ucfirst($str='') {
		return v_mb::ucfirst($str);
	}
}

// --------------------------------------------------------------------- end case



// ---------------------------------------------------------------------
// Misc Formatting
// ---------------------------------------------------------------------

if (! \function_exists('common_format_money')) {
	/**
	 * Money (USD)
	 *
	 * @param float $value Value.
	 * @param bool $cents Return sub-$1 values with ¢.
	 * @return string Value.
	 */
	function common_format_money($value, $cents=false) {
		return v_format::money($value, $cents);
	}
	\add_filter('common_format_money', 'common_format_money', 5, 2);
}

if (! \function_exists('common_format_phone')) {
	/**
	 * Phone
	 *
	 * Format a North American phone number
	 * like (123) 456-7890.
	 *
	 * @param string $value Phone number.
	 * @return string Phone number.
	 */
	function common_format_phone($value='') {
		$value = \common_sanitize_phone($value);

		if (\strlen($value) >= 10) {
			$first10 = \substr($value, 0, 10);
			return \preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{4})/i', "(\\1) \\2-\\3", $first10) . (\strlen($value) > 10 ? ' x' . \substr($value, 10) : '');
		}

		return $value;
	}
}

if (! \function_exists('common_inflect')) {
	/**
	 * Inflect
	 *
	 * Inflect a phrase given a count. `sprintf` formatting
	 * is supported. If an array is passed as $count, its
	 * size will be used for inflection.
	 *
	 * @param int|array $count Count.
	 * @param string $single Singular.
	 * @param string $plural Plural.
	 * @return string Inflected string.
	 */
	function common_inflect($count, $single='', $plural='') {
		return v_format::inflect($count, $single, $plural);
	}
}

if (! \function_exists('common_get_excerpt')) {
	/**
	 * Generate Text Except
	 *
	 * @param string $str String.
	 * @param int $length Length limit.
	 * @param string $append Suffix.
	 * @param string $method Method (chars or words).
	 * @return string Excerpt.
	 */
	function common_get_excerpt($str, $length=200, $append='...', $method='chars') {
		v_mb::strtolower($method);

		return v_format::excerpt(
			\strip_shortcodes($str),
			array(
				'unit'=>$method,
				'suffix'=>$append,
				'length'=>$length,
			)
		);
	}
}

if (! \function_exists('common_unixslashit')) {
	/**
	 * Fix Path Slashes
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	function common_unixslashit($path='') {
		return v_file::unixslash($path);
	}
}

if (! \function_exists('common_unleadingslashit')) {
	/**
	 * Strip Leading Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	function common_unleadingslashit($path='') {
		return v_file::unleadingslash($path);
	}
}

if (! \function_exists('common_leadingslashit')) {
	/**
	 * Add Leading Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	function common_leadingslashit($path='') {
		return v_file::leadingslash($path);
	}
}

if (! \function_exists('common_array_to_indexed')) {
	/**
	 * Create Index Array
	 *
	 * This will convert a {k:v} associative array
	 * into an indexed array with {key: k, value: v}
	 * as the values. Useful when exporting sorted
	 * data to Javascript, which doesn't preserve
	 * object key ordering.
	 *
	 * @param array $arr Array.
	 * @return array Array.
	 */
	function common_array_to_indexed($arr) {
		return v_format::array_to_indexed($arr);
	}
}

if (! \function_exists('common_to_csv')) {
	/**
	 * Generate CSV from Data
	 *
	 * @param array $data Data (row=>cells).
	 * @param array $headers Headers.
	 * @param string $delimiter Delimiter.
	 * @param string $eol Line ending type.
	 * @return string CSV content.
	 */
	function common_to_csv($data=null, $headers=null, $delimiter=',', $eol="\n") {
		return v_format::to_csv($data, $headers, $delimiter, $eol);
	}
}

if (! \function_exists('common_to_xls')) {
	/**
	 * Generate XLS from Data
	 *
	 * This uses Microsoft's XML spreadsheet format.
	 *
	 * @param array $data Data (row=>cells).
	 * @param array $headers Headers.
	 * @return string XLS content.
	 */
	function common_to_xls($data=null, $headers=null) {
		return v_format::to_xls($data, $headers);
	}
}

// --------------------------------------------------------------------- end formatting



// ---------------------------------------------------------------------
// Sanitization
// ---------------------------------------------------------------------

if (! \function_exists('common_to_range')) {
	/**
	 * Confine a Value to a Range
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Value.
	 */
	function common_to_range($value, $min=null, $max=null) {
		return v_sanitize::to_range($value, $min, $max);
	}
}

if (! \function_exists('common_in_range')) {
	/**
	 * Is Value In Range?
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return bool True/false.
	 */
	function common_in_range($value, $min=null, $max=null) {
		return data::in_range($value, $min, $max);
	}
}

if (! \function_exists('common_length_in_range')) {
	/**
	 * Length in Range
	 *
	 * See if the length of a string is between two extremes.
	 *
	 * @param string $str String.
	 * @param int $min Min length.
	 * @param int $max Max length.
	 * @return bool True/false.
	 */
	function common_length_in_range($str, $min=null, $max=null) {
		return data::length_in_range($str, $min, $max);
	}
}

if (! \function_exists('common_utf8')) {
	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_utf8($str) {
		return v_sanitize::utf8($str);
	}
}

// Alias.
if (! \function_exists('common_sanitize_utf8')) {
	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_sanitize_utf8($str) {
		return \common_utf8($str);
	}
}

if (! \function_exists('common_sanitize_name')) {
	/**
	 * (Person's) Name
	 *
	 * A bit of a fool's goal, but this will attempt to
	 * strip out obviously bad data and convert to title
	 * casing.
	 *
	 * @param string $str Name.
	 * @return string Name.
	 */
	function common_sanitize_name($str='') {
		return v_sanitize::name($str);
	}
}

if (! \function_exists('common_sanitize_printable')) {
	/**
	 * Printable
	 *
	 * Remove non-printable characters (except spaces).
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_sanitize_printable($str='') {
		return v_sanitize::printable($str);
	}
}

if (! \function_exists('common_sanitize_csv')) {
	/**
	 * CSV Cell Data
	 *
	 * @param string $str String.
	 * @param bool $newlines Deprecated.
	 * @return string String.
	 */
	function common_sanitize_csv($str='', $newlines=false) {
		return v_sanitize::csv($str);
	}
}

if (! \function_exists('common_sanitize_newlines')) {
	/**
	 * Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space, and constrict consecutive newlines.
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	function common_sanitize_newlines($str='', $newlines=2) {
		return v_sanitize::whitespace($str, $newlines);
	}
}

if (! \function_exists('common_sanitize_spaces')) {
	/**
	 * Horizontal Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_sanitize_spaces($str='') {
		$str = \common_utf8($str);
		return \trim(\preg_replace('/\h{1,}/u', ' ', $str));
	}
}

if (! \function_exists('common_sanitize_whitespace')) {
	/**
	 * Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space, and constrict consecutive newlines.
	 *
	 * @param string $str String.
	 * @param bool $multiline Allow linebreaks.
	 * @return string String.
	 */
	function common_sanitize_whitespace($str='', $multiline=false) {
		$newlines = $multiline ? 2 : 0;
		return v_sanitize::whitespace($str, $newlines);
	}
}

if (! \function_exists('common_sanitize_quotes')) {
	/**
	 * Quotes
	 *
	 * Replace those damn curly quotes with the straight
	 * ones Athena intended!
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	function common_sanitize_quotes($str='') {
		return v_sanitize::quotes($str);
	}
}

if (! \function_exists('common_sanitize_js_variable')) {
	/**
	 * JS Variable
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @return string String.
	 */
	function common_sanitize_js_variable($str='', $quote="'") {
		return v_sanitize::js($str, $quote);
	}
}

if (! \function_exists('common_sanitize_email')) {
	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @return string Email.
	 */
	function common_sanitize_email($str='') {
		return v_sanitize::email($str);
	}
}

if (! \function_exists('common_sanitize_zip5')) {
	/**
	 * US ZIP5
	 *
	 * @param string $str ZIP Code.
	 * @return string ZIP Code.
	 */
	function common_sanitize_zip5($str) {
		return v_sanitize::zip5($str);
	}
}

if (! \function_exists('common_sanitize_ip')) {
	/**
	 * IP Address
	 *
	 * @param string $str IP.
	 * @return string IP.
	 */
	function common_sanitize_ip($str) {
		return v_sanitize::ip($str, true);
	}
}

if (! \function_exists('common_sanitize_number')) {
	/**
	 * To Number
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return float Number.
	 */
	function common_sanitize_number($value, $flatten=false) {
		return v_cast::to_number($value, $flatten);
	}
}

if (! \function_exists('common_sanitize_bool')) {
	/**
	 * To Bool
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool Bool.
	 */
	function common_sanitize_bool($value=false, $flatten=false) {
		return v_cast::to_bool($value, $flatten);
	}
}

// Alias.
if (! \function_exists('common_sanitize_boolean')) {
	/**
	 * To Bool
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool Bool.
	 */
	function common_sanitize_boolean($value=false, $flatten=false) {
		return \common_sanitize_bool($value, $flatten);
	}
}

if (! \function_exists('common_sanitize_float')) {
	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return float Float.
	 */
	function common_sanitize_float($value=0, $flatten=false) {
		return v_cast::to_float($value, $flatten);
	}
}

// Alias.
if (! \function_exists('common_doubleval')) {
	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return float Float.
	 */
	function common_doubleval($value=0, $flatten=false) {
		return \common_sanitize_float($value, $flatten);
	}
}

// Alias.
if (! \function_exists('common_floatval')) {
	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return float Float.
	 */
	function common_floatval($value=0, $flatten=false) {
		return \common_sanitize_float($value, $flatten);
	}
}

if (! \function_exists('common_sanitize_by_type')) {
	/**
	 * To X Type
	 *
	 * @param mixed $value Variable.
	 * @param string $type Type.
	 * @param bool $flatten Do not recurse.
	 * @return mixed Cast value.
	 */
	function common_sanitize_by_type($value, $type=null, $flatten=false) {
		return v_cast::to_type($value, $type, $flatten);
	}
}

if (! \function_exists('common_sanitize_int')) {
	/**
	 * To Int
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return int Int.
	 */
	function common_sanitize_int($value=0, $flatten=false) {
		return v_cast::to_int($value, $flatten);
	}
}
// Alias.
if (! \function_exists('common_intval')) {
	/**
	 * To Int
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return int Int.
	 */
	function common_intval($value=0, $flatten=false) {
		return \common_sanitize_int($value, $flatten);
	}
}

if (! \function_exists('common_sanitize_string')) {
	/**
	 * To String
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return string String.
	 */
	function common_sanitize_string($value='', $flatten=false) {
		return v_cast::to_string($value, $flatten);
	}
}
// Alias.
if (! \function_exists('common_strval')) {
	/**
	 * To String
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return string String.
	 */
	function common_strval($value='', $flatten=false) {
		return \common_sanitize_string($value, $flatten);
	}
}

if (! \function_exists('common_sanitize_array')) {
	/**
	 * To Array
	 *
	 * @param mixed $value Variable.
	 * @return array Array.
	 */
	function common_sanitize_array($value=null) {
		return v_cast::to_array($value);
	}
}

if (! \function_exists('common_sanitize_datetime')) {
	/**
	 * Datetime
	 *
	 * @param string|int $date Date or timestamp.
	 * @return string Date.
	 */
	function common_sanitize_datetime($date) {
		return v_sanitize::datetime($date);
	}
}

if (! \function_exists('common_sanitize_date')) {
	/**
	 * Date
	 *
	 * @param string|int $date Date or timestamp.
	 * @return string Date.
	 */
	function common_sanitize_date($date) {
		return v_sanitize::date($date);
	}
}

if (! \function_exists('common_sanitize_phone')) {
	/**
	 * Phone Number
	 *
	 * Sanitize a North American telephone number.
	 *
	 * @param string $value Phone number.
	 * @return string Phone number.
	 */
	function common_sanitize_phone($value='') {
		$value = \common_sanitize_string($value);
		$value = \preg_replace('/[^\d]/', '', $value);

		// If this looks like a 10-digit number with the +1 on it, chop it off.
		if ((\strlen($value) === 11) && (\intval(\substr($value, 0, 1)) === 1)) {
			$value = \substr($value, 1);
		}

		return $value;
	}
}

if (! \function_exists('common_sanitize_domain_name')) {
	/**
	 * Domain Name.
	 *
	 * This locates the domain name portion of a URL,
	 * removes leading "www." subdomains, and ignores
	 * IP addresses.
	 *
	 * @param string $str Domain.
	 * @return string Domain.
	 */
	function common_sanitize_domain_name($str) {
		return v_sanitize::domain($str);
	}
}

// --------------------------------------------------------------------- end sanitize



// ---------------------------------------------------------------------
// Validate
// ---------------------------------------------------------------------

if (! \function_exists('common_is_utf8')) {
	/**
	 * Is Value Valid UTF-8?
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	function common_is_utf8($str) {
		return data::is_utf8($str);
	}
}

if (! \function_exists('common_validate_email')) {
	/**
	 * Is Email Valid
	 *
	 * @param string $email String.
	 * @return bool True/false.
	 */
	function common_validate_email($email='') {
		return \filter_var($email, \FILTER_VALIDATE_EMAIL) && \preg_match('/^.+\@.+\..+$/', $email);
	}
}

if (! \function_exists('common_validate_phone')) {
	/**
	 * Is Phone Valid
	 *
	 * This only applies to North American numbers.
	 *
	 * @param string $value Phone number.
	 * @return bool True/false.
	 */
	function common_validate_phone($value='') {
		// Match the first 10.
		$value = \common_sanitize_string($value);
		$first10 = \substr($value, 0, 10);
		return \preg_match('/^[2-9][0-8][0-9][2-9][0-9]{2}[0-9]{4}$/i', $first10);
	}
}

if (! \function_exists('common_validate_cc')) {
	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return bool True/false.
	 */
	function common_validate_cc($ccnum='') {
		return false !== v_sanitize::cc($ccnum);
	}
}

if (! \function_exists('common_sanitize_url')) {
	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $url URL.
	 * @return string URL.
	 */
	function common_sanitize_url($url='') {
		return v_sanitize::url($url);
	}
}

if (! \function_exists('common_validate_domain_name')) {
	/**
	 * Validate Domain Name
	 *
	 * Check if domain name appears valid.
	 *
	 * @param string $domain Domain.
	 * @param bool $live Check DNS.
	 * @return bool True/false.
	 */
	function common_validate_domain_name($domain, $live=true) {
		r_cast::to_bool($live, true);

		$host = new domain($domain);
		if (! $host->is_valid() || $host->is_ip()) {
			return false;
		}

		if ($live) {
			return $host->has_dns();
		}

		return $host->is_fqdn();
	}
}

// --------------------------------------------------------------------- end validate

