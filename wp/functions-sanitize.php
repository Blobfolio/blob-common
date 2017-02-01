<?php
//---------------------------------------------------------------------
// FUNCTIONS: SANITIZE/VALIDATE
//---------------------------------------------------------------------
// This file contains functions related to sanitizing, validating,
// and formatting data

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



//---------------------------------------------------------------------
// Case Conversion
//---------------------------------------------------------------------

//-------------------------------------------------
// Lower Case
//
// will return multi-byte lowercase if capabale,
// otherwise regular lowercase
//
// @param str
// @return str
if (!function_exists('common_strtolower')) {
	function common_strtolower($str='') {
		return \blobfolio\common\mb::strtolower($str);
	}
}

//-------------------------------------------------
// Upper Case
//
// will return multi-byte uppercase if capabale,
// otherwise regular uppercase
//
// @param str
// @return str
if (!function_exists('common_strtoupper')) {
	function common_strtoupper($str='') {
		return \blobfolio\common\mb::strtoupper($str);
	}
}

//-------------------------------------------------
// Title Case
//
// will return multi-byte title case if capabale,
// otherwise regular title case
//
// @param str
// @return str
if (!function_exists('common_ucwords')) {
	function common_ucwords($str='') {
		return \blobfolio\common\mb::ucwords($str);
	}
}

//-------------------------------------------------
// Sentence Case
//
// will return multi-byte sentence case if capabale,
// otherwise regular sentence case
//
// @param str
// @return str
if (!function_exists('common_ucfirst')) {
	function common_ucfirst($str='') {
		return \blobfolio\common\mb::ucfirst($str);
	}
}

//--------------------------------------------------------------------- end case

//---------------------------------------------------------------------
// Misc Formatting
//---------------------------------------------------------------------

//-------------------------------------------------
// Format money
//
// @param amount
// @param cents (if under $1, use ¢ sign)
// @return money
if (!function_exists('common_format_money')) {
	function common_format_money($amount, $cents=false) {
		return \blobfolio\common\format::money($amount, $cents);
	}
	add_filter('common_format_money', 'common_format_money', 5, 2);
}

//-------------------------------------------------
// Format phone
//
// again, this assumes north american formatting
//
// @param n/a
// @return phone (pretty)
if (!function_exists('common_format_phone')) {
	function common_format_phone($value='') {
		$value = common_sanitize_phone($value);

		if (common_strlen($value) >= 10) {
			$first10 = common_substr($value, 0, 10);
			return preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{4})/i', "(\\1) \\2-\\3", $first10) . (common_strlen($value) > 10 ? ' x' . common_substr($value, 10) : '');
		}

		return $value;
	}
}

//-------------------------------------------------
// Singular/Plural inflection based on number
//
// @param number
// @param single
// @param plural
// @return string
if (!function_exists('common_inflect')) {
	function common_inflect($num, $single='', $plural='') {
		return \blobfolio\common\format::inflect($num, $single, $plural);
	}
}

//-------------------------------------------------
// Make excerpt (character length)
//
// @param string
// @param length
// @param append
// @param chop method (chars or words)
// @return excerpt
if (!function_exists('common_get_excerpt')) {
	function common_get_excerpt($str, $length=200, $append='...', $method='chars') {
		\blobfolio\common\mb::strtolower($method);

		return \blobfolio\common\format::excerpt(
			$str,
			array(
				'unit'=>$method,
				'suffix'=>$append,
				'length'=>$length
			)
		);
	}
}

//-------------------------------------------------
// Unix slashes
//
// fix backward Windows slashes, and also get
// rid of double slashes and dot paths
//
// @param path
// @return path
if (!function_exists('common_unixslashit')) {
	function common_unixslashit($path='') {
		return \blobfolio\common\file::unixslash($path);
	}
}

//-------------------------------------------------
// Unleading Slash
//
// WP doesn't have leading slash functions for
// some reason
//
// @param path
// @return path
if (!function_exists('common_unleadingslashit')) {
	function common_unleadingslashit($path='') {
		return \blobfolio\common\file::unleadingslash($path);
	}
}

//-------------------------------------------------
// Leading Slash
//
// WP doesn't have leading slash functions for
// some reason
//
// @param path
// @return path
if (!function_exists('common_leadingslashit')) {
	function common_leadingslashit($path='') {
		return \blobfolio\common\file::leadingslash($path);
	}
}

//-------------------------------------------------
// Convert a k=>v associative array to an indexed
// array
//
// @param arr
// @return arr
if (!function_exists('common_array_to_indexed')) {
	function common_array_to_indexed($arr) {
		return \blobfolio\common\format::array_to_indexed($arr);
	}
}

//-------------------------------------------------
// CSV
//
// @param data
// @param headers
// @param delimiter
// @param EOL
// @return CSV
if (!function_exists('common_to_csv')) {
	function common_to_csv($data=null, $headers=null, $delimiter=',', $eol="\n") {
		return \blobfolio\common\format::to_csv($data, $headers, $delimiter, $eol);
	}
}

//-------------------------------------------------
// XLS
//
// use Microsoft's XML format
//
// @param data
// @param headers
// @return XLS
if (!function_exists('common_to_xls')) {
	function common_to_xls($data=null, $headers=null) {
		return \blobfolio\common\format::to_xls($data, $headers);
	}
}

//--------------------------------------------------------------------- end formatting



//---------------------------------------------------------------------
// Sanitization
//---------------------------------------------------------------------

//-------------------------------------------------
// Force a value to fall within a range
//
// @param value
// @param min
// @param max
// @return value
if (!function_exists('common_to_range')) {
	function common_to_range($value, $min=null, $max=null) {
		return \blobfolio\common\sanitize::to_range($value, $min, $max);
	}
}

//-------------------------------------------------
// Check if a value is within range
//
// @param value
// @param min
// @param max
// @return true/false
if (!function_exists('common_in_range')) {
	function common_in_range($value, $min=null, $max=null) {
		return \blobfolio\common\data::in_range($value, $min, $max);
	}
}

//-------------------------------------------------
// Check if a string's length is within range
//
// @param str
// @param min
// @param max
// @return true/false
if (!function_exists('common_length_in_range')) {
	function common_length_in_range($str, $min=null, $max=null) {
		return \blobfolio\common\data::length_in_range($str, $min, $max);
	}
}

//-------------------------------------------------
// Convert to UTF-8
//
// @param string
// @return string or false
if (!function_exists('common_utf8')) {
	function common_utf8($str) {
		return \blobfolio\common\sanitize::utf8($str);
	}
}
//alias
if (!function_exists('common_sanitize_utf8')) {
	function common_sanitize_utf8($str) { return common_utf8($str);
	}
}

//-------------------------------------------------
// Sanitize name (like a person's name)
//
// @param name
// @return name
if (!function_exists('common_sanitize_name')) {
	function common_sanitize_name($str='') {
		return \blobfolio\common\sanitize::name($str);
	}
}

//-------------------------------------------------
// Sanitize printable
//
// @param str
// @return str
if (!function_exists('common_sanitize_printable')) {
	function common_sanitize_printable($str='') {
		return \blobfolio\common\sanitize::printable($str);
	}
}

//-------------------------------------------------
// Sanitize CSV
//
// @param field
// @param allow newlines
// @return field
if (!function_exists('common_sanitize_csv')) {
	function common_sanitize_csv($str='', $newlines=false) {
		return \blobfolio\common\sanitize::csv($str);
	}
}

//-------------------------------------------------
// Consistent new lines (\n)
//
// @param str
// @return str
if (!function_exists('common_sanitize_newlines')) {
	function common_sanitize_newlines($str='', $newlines=2) {
		return \blobfolio\common\sanitize::whitespace($str, $newlines);
	}
}

//-------------------------------------------------
// Single spaces
//
// @param str
// @return str
if (!function_exists('common_sanitize_spaces')) {
	function common_sanitize_spaces($str='') {
		$str = common_utf8($str);
		return trim(preg_replace('/\h{1,}/u', ' ', $str));
	}
}

//-------------------------------------------------
// Sanitize all white space
//
// @param str
// @param multiline
// @return str
if (!function_exists('common_sanitize_whitespace')) {
	function common_sanitize_whitespace($str='', $multiline=false) {
		$newlines = $multiline ? 2 : 0;
		return \blobfolio\common\sanitize::whitespace($str, $newlines);
	}
}

//-------------------------------------------------
// Make consistent quotes
//
// @param str
// @return str
if (!function_exists('common_sanitize_quotes')) {
	function common_sanitize_quotes($str='') {
		return \blobfolio\common\sanitize::quotes($str);
	}
}

//-------------------------------------------------
// Sanitize JS variable
//
// this should be used for var = 'variable';
//
// @param str
// @return str
if (!function_exists('common_sanitize_js_variable')) {
	function common_sanitize_js_variable($str='') {
		return \blobfolio\common\sanitize::js($str);
	}
}

//-------------------------------------------------
// Better email sanitizing
//
// @param email
// @return email
if (!function_exists('common_sanitize_email')) {
	function common_sanitize_email($email='') {
		return \blobfolio\common\sanitize::email($email);
	}
}

//-------------------------------------------------
// Sanitize a US zip5 code
//
// @param zip
// @return zip
if (!function_exists('common_sanitize_zip5')) {
	function common_sanitize_zip5($zip) {
		return \blobfolio\common\sanitize::zip5($zip);
	}
}

//-------------------------------------------------
// Sanitize IP
//
// IPv6 addresses are compacted for consistency
//
// @param IP
// @return IP
if (!function_exists('common_sanitize_ip')) {
	function common_sanitize_ip($ip) {
		return \blobfolio\common\sanitize::ip($ip, true);
	}
}

//-------------------------------------------------
// Remove non-numeric chars from str
//
// @param num
// @return num (float)
if (!function_exists('common_sanitize_number')) {
	function common_sanitize_number($num) {
		return \blobfolio\common\cast::number($num);
	}
}

//-------------------------------------------------
// Bool
//
// @param value
// @return true/false
if (!function_exists('common_sanitize_bool')) {
	function common_sanitize_bool($value=false) {
		return \blobfolio\common\cast::bool($value);
	}
}
//alias
if (!function_exists('common_sanitize_boolean')) {
	function common_sanitize_boolean($value=false) {
		return common_sanitize_bool($value);
	}
}

//-------------------------------------------------
// Float
//
// @param value
// @return true/false
if (!function_exists('common_sanitize_float')) {
	function common_sanitize_float($num=0) {
		return \blobfolio\common\cast::float($num);
	}
}
//alias
if (!function_exists('common_doubleval')) {
	function common_doubleval($num=0) {
		return common_sanitize_float($num);
	}
}
if (!function_exists('common_floatval')) {
	function common_floatval($num=0) {
		return common_sanitize_float($num);
	}
}

//-------------------------------------------------
// Sanitize by Type
//
// @param value
// @param type
// @return value
if (!function_exists('common_sanitize_by_type')) {
	function common_sanitize_by_type($value, $type=null) {
		return \blobfolio\common\cast::to_type($value, $type);
	}
}

//-------------------------------------------------
// Int
//
// @param value
// @return true/false
if (!function_exists('common_sanitize_int')) {
	function common_sanitize_int($num=0) {
		return \blobfolio\common\cast::int($num);
	}
}
//another wrapper
if (!function_exists('common_intval')) {
	function common_intval($num=0) { return common_sanitize_int($num);
	}
}

//-------------------------------------------------
// String
//
// @param value
// @return value
if (!function_exists('common_sanitize_string')) {
	function common_sanitize_string($value='') {
		return \blobfolio\common\cast::string($value);
	}
}
//alias
if (!function_exists('common_strval')) {
	function common_strval($value='') {
		return common_sanitize_string($value);
	}
}

//-------------------------------------------------
// Array
//
// @param value
// @return value
if (!function_exists('common_sanitize_array')) {
	function common_sanitize_array($value=null) {
		return \blobfolio\common\cast::array($value);
	}
}

//-------------------------------------------------
// Datetime
//
// @param date
// @return date
if (!function_exists('common_sanitize_datetime')) {
	function common_sanitize_datetime($date) {
		return \blobfolio\common\sanitize::datetime($date);
	}
}
//wrapper for just the date half
if (!function_exists('common_sanitize_date')) {
	function common_sanitize_date($date) {
		return \blobfolio\common\sanitize::date($date);
	}
}

//-------------------------------------------------
// Sanitize phone number
//
// this function should only be used on north
// american numbers, like: (123) 456-7890 x12345
//
// @param phone
// @return phone
if (!function_exists('common_sanitize_phone')) {
	function common_sanitize_phone($value='') {
		$value = common_sanitize_string($value);
		$value = preg_replace('/[^\d]/', '', $value);

		//if this looks like a 10-digit number with the +1 on it, chop it off
		if (strlen($value) === 11 && intval(substr($value, 0, 1)) === 1) {
			$value = substr($value, 1);
		}

		return $value;
	}
}

//-------------------------------------------------
// Sanitize domain name
//
// this does not strip invalid characters; it
// merely attempts to extract the hostname portion
// of a URL-like string
//
// @param domain
// @return domain or false
if (!function_exists('common_sanitize_domain_name')) {
	function common_sanitize_domain_name($domain) {
		return \blobfolio\common\sanitize::domain($domain);
	}
}

//--------------------------------------------------------------------- end sanitize



//---------------------------------------------------------------------
// Validate
//---------------------------------------------------------------------

//-------------------------------------------------
// Check for UTF-8
//
// @param string
// @return true/false
if (!function_exists('common_is_utf8')) {
	function common_is_utf8($str) {
		return \blobfolio\common\data::is_utf8($str);
	}
}

//-------------------------------------------------
// Validate an email (FQDN)
//
// @param email
// @return true/false
if (!function_exists('common_validate_email')) {
	function common_validate_email($email='') {
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^.+\@.+\..+$/', $email);
	}
}

//-------------------------------------------------
// Validate north american phone number
//
// the first 10 digits must match standards
//
// @param phone
// @return true/false
if (!function_exists('common_validate_phone')) {
	function common_validate_phone($value='') {
		//match the first 10
		$value = common_sanitize_string($value);
		$first10 = common_substr($value, 0, 10);
		return preg_match('/^[2-9][0-8][0-9][2-9][0-9]{2}[0-9]{4}$/i', $first10);
	}
}

//-------------------------------------------------
// Validate credit card
//
// @param card
// @return true/false
if (!function_exists('common_validate_cc')) {
	function common_validate_cc($ccnum='') {
		return false !== \blobfolio\common\sanitize::cc($ccnum);
	}
}

//-------------------------------------------------
// Sanitize a URL
//
// @param url
// @return url
if (!function_exists('common_sanitize_url')) {
	function common_sanitize_url($url='') {
		\blobfolio\common\ref\sanitize::url($url);
		return $url;
	}
}

//-------------------------------------------------
// Validate domain name
//
// @param domain
// @param live (does it have an IP?)
// @return true/false
if (!function_exists('common_validate_domain_name')) {
	function common_validate_domain_name($domain, $live=true) {
		if (false === $host = common_sanitize_domain_name($domain)) {
			return false;
		}

		//we only want ASCII domains
		if ($host !== filter_var($host, FILTER_SANITIZE_URL)) {
			return false;
		}

		//does our host kinda match domain standards?
		if (!preg_match('/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})$/', $host)) {
			return false;
		}

		//does it have an A record?
		if ($live && !filter_var(gethostbyname($host), FILTER_VALIDATE_IP)) {
			return false;
		}

		return true;
	}
}

//--------------------------------------------------------------------- end validate
?>