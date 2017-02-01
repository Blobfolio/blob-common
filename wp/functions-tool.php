<?php
//---------------------------------------------------------------------
// FUNCTIONS: COMMON/MISC
//---------------------------------------------------------------------
// This file contains functions that don't particularly fit anywhere
// else.

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



//---------------------------------------------------------------------
// Comparison/Eval Tools
//---------------------------------------------------------------------

//-------------------------------------------------
// Array Type
//
// PHP arrays are a bit slutty which can cause
// issues when encoding for different platforms,
// etc.
//
// @param arr
// @return true/false
if (!function_exists('common_array_type')) {
	function common_array_type(&$arr=null) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\cast::array_type($arr);
	}
}

//-------------------------------------------------
// Compare Arrays
//
// @param arr1
// @param arr2
// @return true/false
if (!function_exists('common_array_compare')) {
	function common_array_compare(&$arr1, &$arr2) {
		if (!is_array($arr1) || !is_array($arr2)) {
			return false;
		}
		return \blobfolio\common\data::array_compare($arr1, $arr2);
	}
}

//-------------------------------------------------
// Case-insensitive in_array()
//
// @param needle
// @param haystack
// @return true/false
if (!function_exists('common_iin_array()')) {
	function common_iin_array($needle, $haystack) {
		$needle = common_strtolower($needle);
		$haystack = array_map('common_strtolower', $haystack);
		return in_array($needle, $haystack);
	}
}

//-------------------------------------------------
// Case-insensitive array_key_exists()
//
// @param needle
// @param haystack
// @return true/false
if (!function_exists('common_iarray_key_exists()')) {
	function common_iarray_key_exists($needle, $haystack) {
		if (!is_array($haystack)) {
			return false;
		}
		$needle = common_strtolower($needle);
		$haystack = array_map('common_strtolower', array_keys($haystack));
		return in_array($needle, $haystack);
	}
}

//-------------------------------------------------
// Case-insensitive substr_count
//
// @param haystack
// @param needle
// @return true/false
if (!function_exists('common_isubstr_count()')) {
	function common_isubstr_count($haystack, $needle) {
		$needle = common_strtolower($needle);
		$haystack = common_strtolower($haystack);
		return common_substr_count($haystack, $needle);
	}
}

//--------------------------------------------------------------------- end comparisons



//---------------------------------------------------------------------
// Other Tools
//---------------------------------------------------------------------

//-------------------------------------------------
// String Length
//
// will return multi-byte string length if capable
// or fall back to strlen()
//
// @param str
// @return length
if (!function_exists('common_strlen')) {
	function common_strlen($str) {
		return \blobfolio\common\mb::strlen($str);
	}
}

//-------------------------------------------------
// Strpos
//
// will return multi-byte substring position if
// capable or fall back to strpos()
//
// @param haystack
// @param needle
// @param offset
// @return position
if (!function_exists('common_strpos')) {
	function common_strpos($haystack, $needle, $offset=0) {
		return \blobfolio\common\mb::strpos($haystack, $needle, $offset);
	}
}

//-------------------------------------------------
// Substring
//
// will return multi-byte substring if capable or
// fall back to substr()
//
// @param str
// @param start
// @param length
// @return substring
if (!function_exists('common_substr')) {
	function common_substr($str, $start=0, $length=null) {
		return \blobfolio\common\mb::substr($str, $start, $length);
	}
}

//-------------------------------------------------
// Substring Count
//
// will return multi-byte substring count if
// capable or fall back to substr_count()
//
// @param haystack
// @param needle
// @return count
if (!function_exists('common_substr_count')) {
	function common_substr_count($haystack, $needle) {
		return \blobfolio\common\mb::substr_count($haystack, $needle);
	}
}

//-------------------------------------------------
// Turn a string into an array of chars
//
// this is now just a wrapper function for
// str_split but is preserved for backward
// compatibility.
//
// @param string
// @return array
if (!function_exists('common_to_char_array')) {
	function common_to_char_array($input) { return \blobfolio\common\mb::str_split($input);
	}
}

//-------------------------------------------------
// Recursive Array Map
//
// this should work on single values, arrays, and
// loopable objects
//
// @param callback
// @param var
// @return filtered
if (!function_exists('common_array_map_recursive')) {
	function common_array_map_recursive(callable $func, $value) {
		return \blobfolio\common\data::array_map_recursive($func, $value);
	}
}

//-------------------------------------------------
// Get a Random Integer
//
// this will shoot for several implementations of
// randomness in order of preference until a
// supported one is found
//
// @param min
// @param max
// @return random
if (!function_exists('common_random_int')) {
	function common_random_int($min=0, $max=1) {
		return \blobfolio\common\data::random_int($min, $max);
	}
}

//-------------------------------------------------
// Return the first index of an array
//
// this is like array_pop for the first entry
//
// @param array
// @return mixed or false
if (!function_exists('common_array_pop_top')) {
	function common_array_pop_top(&$arr) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\data::array_pop_top($arr);
	}
}

//-------------------------------------------------
// Return the last index of an array
//
// this is like array_pop but doesn't destroy the
// array
//
// @param array
// @return mixed or false
if (!function_exists('common_array_pop')) {
	function common_array_pop(&$arr) {
		if (!is_array($arr)) {
			return false;
		}
		return \blobfolio\common\data::array_pop($arr);
	}
}

//-------------------------------------------------
// Switch two variables
//
// @param var1
// @param var2
// @return true
if (!function_exists('common_switcheroo')) {
	function common_switcheroo(&$var1, &$var2) {
		return \blobfolio\common\data::switcheroo($var1, $var2);
	}
}

//-------------------------------------------------
// WP Parse Args wrapper
//
// remove extra keys from $args if they are not in
// $defaults
//
// @param args
// @param defaults
// @param strict (force same type, one level deep)
// @param recursive
// @return parsed
if (!function_exists('common_parse_args')) {
	function common_parse_args($args=null, $defaults=null, $strict=false, $recursive=false) {
		return \blobfolio\common\data::parse_args($args, $defaults, $strict, $recursive);
	}
}

//-------------------------------------------------
// Parse Args from JSON source
//
// defaults are optional
//
// @param args
// @param defaults
// @param strict (force same type, one level deep)
// @param recursive
// @return parsed
if (!function_exists('common_parse_json_args')) {
	function common_parse_json_args($json='', $defaults=null, $strict=true, $recursive=true) {
		return \blobfolio\common\data::json_decode_array($json, $defaults, $strict, $recursive);
	}
}

//-------------------------------------------------
// Generate a random string
//
// using only unambiguous letters
//
// @param length
// @param character set (optional)
// @return string
if (!function_exists('common_generate_random_string')) {
	function common_generate_random_string($length=10, $soup=null) {
		return \blobfolio\common\data::random_string($length, $soup);
	}
}

//--------------------------------------------------------------------- end tools




//---------------------------------------------------------------------
// Misc
//---------------------------------------------------------------------

//-------------------------------------------------
// Get Expiration Years
//
// @param length
// @return years
if (!function_exists('common_get_cc_exp_years')) {
	function common_get_cc_exp_years($length=10) {
		return \blobfolio\common\data::cc_exp_years($length);
	}
}

//-------------------------------------------------
// Get Expiration Months
//
// @param format
// @return months
if (!function_exists('common_get_cc_exp_months')) {
	function common_get_cc_exp_months($format='m - M') {
		return \blobfolio\common\data::cc_exp_months($format);
	}
}

//--------------------------------------------------------------------- end misc

?>