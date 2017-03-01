<?php
/**
 * Miscellaneous Functions
 *
 * This file contains functionality that didn't much
 * fit anywhere else.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

// ---------------------------------------------------------------------
// Comparison/Eval Tools
// ---------------------------------------------------------------------

if (!function_exists('common_array_type')) {
	/**
	 * Array Type
	 *
	 * "associative": If there are string keys.
	 * "sequential": If the keys are sequential numbers.
	 * "indexed": If the keys are at least numeric.
	 * FALSE: Any other condition.
	 *
	 * @param array $arr Array.
	 * @return string|bool Type. False on failure.
	 */
	function common_array_type(&$arr=null) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\cast::array_type($arr);
	}
}

if (!function_exists('common_array_compare')) {
	/**
	 * Compare Two Arrays
	 *
	 * @param array $arr1 Array.
	 * @param array $arr2 Array.
	 * @return bool True/false.
	 */
	function common_array_compare(&$arr1, &$arr2) {
		if (!is_array($arr1) || !is_array($arr2)) {
			return false;
		}
		return \blobfolio\common\data::array_compare($arr1, $arr2);
	}
}

if (!function_exists('common_iin_array()')) {
	/**
	 * Case-Insensitive in_array()
	 *
	 * @param string $needle Needle.
	 * @param array $haystack Haystack.
	 * @return bool True/false.
	 */
	function common_iin_array($needle, $haystack) {
		$needle = common_strtolower($needle);
		$haystack = array_map('common_strtolower', $haystack);
		return in_array($needle, $haystack, true);
	}
}

if (!function_exists('common_iarray_key_exists()')) {
	/**
	 * Case-Insensitive array_key_exists()
	 *
	 * @param string $needle Needle.
	 * @param array $haystack Haystack.
	 * @return bool True/false.
	 */
	function common_iarray_key_exists($needle, $haystack) {
		if (!is_array($haystack)) {
			return false;
		}
		$needle = common_strtolower($needle);
		$haystack = array_map('common_strtolower', array_keys($haystack));
		return in_array($needle, $haystack, true);
	}
}

if (!function_exists('common_isubstr_count()')) {
	/**
	 * Case-Insensitive substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @return bool True/false.
	 */
	function common_isubstr_count($haystack, $needle) {
		$needle = common_strtolower($needle);
		$haystack = common_strtolower($haystack);
		return common_substr_count($haystack, $needle);
	}
}

// --------------------------------------------------------------------- end comparisons



// ---------------------------------------------------------------------
// Other Tools
// ---------------------------------------------------------------------

if (!function_exists('common_strlen')) {
	/**
	 * Wrapper For strlen()
	 *
	 * @param string $str String.
	 * @return int String length.
	 */
	function common_strlen($str) {
		return \blobfolio\common\mb::strlen($str);
	}
}

if (!function_exists('common_strpos')) {
	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool First occurrence or false.
	 */
	function common_strpos($haystack, $needle, $offset=0) {
		return \blobfolio\common\mb::strpos($haystack, $needle, $offset);
	}
}

if (!function_exists('common_substr')) {
	/**
	 * Wrapper For substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @return string String.
	 */
	function common_substr($str, $start=0, $length=null) {
		return \blobfolio\common\mb::substr($str, $start, $length);
	}
}

if (!function_exists('common_substr_count')) {
	/**
	 * Wrapper For substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @return int Count.
	 */
	function common_substr_count($haystack, $needle) {
		return \blobfolio\common\mb::substr_count($haystack, $needle);
	}
}

if (!function_exists('common_to_char_array')) {
	/**
	 * Wrapper For str_split()
	 *
	 * @param string $str String.
	 * @return array|bool Split string or false.
	 */
	function common_to_char_array($str) {
		return \blobfolio\common\mb::str_split($str);
	}
}

if (!function_exists('common_array_map_recursive')) {
	/**
	 * Recursive Array Map
	 *
	 * @param callable $func Callback function.
	 * @param array $arr Array.
	 * @return bool True/false.
	 */
	function common_array_map_recursive(callable $func, $arr) {
		return \blobfolio\common\data::array_map_recursive($func, $arr);
	}
}

if (!function_exists('common_random_int')) {
	/**
	 * Generate Random Integer
	 *
	 * This will use the most secure function
	 * available for the environment.
	 *
	 * @param int $min Min.
	 * @param int $max Max.
	 * @return int Random number.
	 */
	function common_random_int($min=0, $max=1) {
		return \blobfolio\common\data::random_int($min, $max);
	}
}

if (!function_exists('common_array_pop_top')) {
	/**
	 * Return the last value of an array.
	 *
	 * This is like array_pop() but non-destructive.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	function common_array_pop_top(&$arr) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\data::array_pop_top($arr);
	}
}

if (!function_exists('common_array_pop')) {
	/**
	 * Return the first value of an array.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	function common_array_pop(&$arr) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\data::array_pop($arr);
	}
}

if (!function_exists('common_switcheroo')) {
	/**
	 * Switch Two Variables
	 *
	 * @param mixed $var1 Variable.
	 * @param mixed $var2 Variable.
	 * @return bool True.
	 */
	function common_switcheroo(&$var1, &$var2) {
		return \blobfolio\common\data::switcheroo($var1, $var2);
	}
}

if (!function_exists('common_parse_args')) {
	/**
	 * Parse Arguments
	 *
	 * Make sure user arguments follow a default
	 * format. Unlike `wp_parse_args()`-type functions,
	 * only keys from the template are allowed.
	 *
	 * @param mixed $args User arguments.
	 * @param mixed $defaults Default values/format.
	 * @param bool $strict Strict type enforcement.
	 * @param bool $recursive Recursively apply formatting if inner values are also arrays.
	 * @return array Parsed arguments.
	 */
	function common_parse_args($args=null, $defaults=null, $strict=false, $recursive=false) {
		return \blobfolio\common\data::parse_args($args, $defaults, $strict, $recursive);
	}
}

if (!function_exists('common_parse_json_args')) {
	/**
	 * Parse Arguments (JSON)
	 *
	 * Make sure user arguments follow a default
	 * format. Unlike `wp_parse_args()`-type functions,
	 * only keys from the template are allowed.
	 *
	 * @param string $json User arguments.
	 * @param mixed $defaults Default values/format.
	 * @param bool $strict Strict type enforcement.
	 * @param bool $recursive Recursively apply formatting if inner values are also arrays.
	 * @return array Parsed arguments.
	 */
	function common_parse_json_args($json='', $defaults=null, $strict=true, $recursive=true) {
		return \blobfolio\common\data::json_decode_array($json, $defaults, $strict, $recursive);
	}
}

if (!function_exists('common_generate_random_string')) {
	/**
	 * Generate Random String
	 *
	 * By default the string will only contain
	 * unambiguous uppercase letters and numbers.
	 * Alternate alphabets can be passed instead.
	 *
	 * @param int $length Length.
	 * @param array $soup Alternate alphabet.
	 * @return string Random string.
	 */
	function common_generate_random_string($length=10, $soup=null) {
		return \blobfolio\common\data::random_string($length, $soup);
	}
}

// --------------------------------------------------------------------- end tools




// ---------------------------------------------------------------------
// Misc
// ---------------------------------------------------------------------

if (!function_exists('common_get_cc_exp_years')) {
	/**
	 * Generate Credit Card Expiration Years
	 *
	 * @param int $length Number of years.
	 * @return array Years.
	 */
	function common_get_cc_exp_years($length=10) {
		return \blobfolio\common\data::cc_exp_years($length);
	}
}

if (!function_exists('common_get_cc_exp_months')) {
	/**
	 * Generate Credit Card Expiration Months
	 *
	 * @param string $format Date format.
	 * @return array Months.
	 */
	function common_get_cc_exp_months($format='m - M') {
		return \blobfolio\common\data::cc_exp_months($format);
	}
}

// --------------------------------------------------------------------- end misc


