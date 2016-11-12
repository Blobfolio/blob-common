<?php
//---------------------------------------------------------------------
// FUNCTIONS: COMMON/MISC
//---------------------------------------------------------------------
// This file contains functions that don't particularly fit anywhere
// else.

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//---------------------------------------------------------------------
// Comparison/Eval Tools
//---------------------------------------------------------------------

//-------------------------------------------------
// Compare Arrays
//
// @param arr1
// @param arr2
// @return true/false
if(!function_exists('common_array_compare')){
	function common_array_compare(&$arr1, &$arr2){
		//obviously bad data
		if(!is_array($arr1) || !is_array($arr2) || count($arr1) !== count($arr2))
			return false;

		//different keys, we don't need to check further
		if(count(array_intersect_key($arr1, $arr2)) !== count($arr1))
			return false;

		//check each item
		foreach($arr1 AS $k=>$v){
			if(!isset($arr2[$k]))
				return false;

			//recursive?
			if(is_array($arr1[$k]) && is_array($arr2[$k])){
				if(!common_array_compare($arr1[$k], $arr2[$k]))
					return false;
			}
			elseif($arr1[$k] !== $arr2[$k])
				return false;
		}

		return true;
	}
}

//-------------------------------------------------
// Case-insensitive in_array()
//
// @param needle
// @param haystack
// @return true/false
if(!function_exists('common_iin_array()')){
	function common_iin_array($needle, $haystack){
		$needle = strtolower($needle);
		$haystack = array_map('strtolower', $haystack);
		return in_array($needle, $haystack);
	}
}

//-------------------------------------------------
// Case-insensitive array_key_exists()
//
// @param needle
// @param haystack
// @return true/false
if(!function_exists('common_iarray_key_exists()')){
	function common_iarray_key_exists($needle, $haystack){
		$needle = strtolower($needle);
		$haystack = array_map('strtolower', array_keys($haystack));
		return in_array($needle, $haystack);
	}
}

//-------------------------------------------------
// Case-insensitive substr_count
//
// @param haystack
// @param needle
// @return true/false
if(!function_exists('common_isubstr_count()')){
	function common_isubstr_count($haystack, $needle){
		$needle = strtolower($needle);
		$haystack = strtolower($haystack);
		return substr_count($haystack, $needle);
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
if(!function_exists('common_strlen')){
	function common_strlen($str){
		//prefer mb_strlen
		if(function_exists('mb_strlen'))
			$length = mb_strlen($str);
		else
			$length = strlen($str);

		if(false === $length)
			$length = 0;

		return $length;
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
if(!function_exists('common_to_char_array')){
	function common_to_char_array($input){ return str_split($input); }
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
if(!function_exists('common_array_map_recursive')){
	function common_array_map_recursive(callable $func, $value){
		if(is_array($value)){
			foreach($value AS $k=>$v)
				$value[$k] = common_array_map_recursive($func, $value[$k]);
		}
		elseif(is_object($value)){
			try {
				foreach($value AS $k=>$v){
					$value->{$k} = common_array_map_recursive($func, $value->{$k});
				}
			} catch(Exception $e){ return $value; }
		}
		else
			$value = call_user_func($func, $value);

		return $value;
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
if(!function_exists('common_random_int')){
	function common_random_int($min=0, $max=1){
		static $random_int;

		if(is_null($random_int))
			$random_int = function_exists('random_int');

		$min = (int) $min;
		$max = (int) $max;
		if($min > $max)
			common_switcheroo($min, $max);

		if($random_int)
			return random_int($min, $max);
		else
			return mt_rand($min, $max);
	}
}

//-------------------------------------------------
// Return the first index of an array
//
// this is like array_pop for the first entry
//
// @param array
// @return mixed or false
if(!function_exists('common_array_pop_top')){
	function common_array_pop_top(&$arr){
		if(!is_array($arr) || !count($arr))
			return false;

		reset($arr);
		return $arr[key($arr)];
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
if(!function_exists('common_array_pop')){
	function common_array_pop(&$arr){
		if(!is_array($arr) || !count($arr))
			return false;

		$reversed = array_reverse($arr);
		return common_array_pop_top($reversed);
	}
}

//-------------------------------------------------
// Switch two variables
//
// @param var1
// @param var2
// @return true
if(!function_exists('common_switcheroo')){
	function common_switcheroo(&$var1, &$var2){
		$tmp = $var2;
		$var2 = $var1;
		$var1 = $tmp;

		return true;
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
if(!function_exists('common_parse_args')){
	function common_parse_args($args=null, $defaults=null, $strict=false, $recursive=false){
		$defaults = (array) $defaults;
		$args = (array) $args;

		if(!count($defaults))
			return array();

		foreach($defaults AS $k=>$v){
			if(array_key_exists($k, $args)){
				if($recursive && is_array($defaults[$k]) && count($defaults[$k]))
					$defaults[$k] = common_parse_args($args[$k], $defaults[$k], $strict, $recursive);
				elseif($strict && !is_null($v) && gettype($args[$k]) !== gettype($v))
					$defaults[$k] = common_sanitize_by_type($args[$k], gettype($v));
				else
					$defaults[$k] = $args[$k];
			}
		}

		return $defaults;
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
if(!function_exists('common_parse_json_args')){
	function common_parse_json_args($json='', $defaults=null, $strict=true, $recursive=true){
		$json = trim(common_sanitize_string($json));
		if(common_strlen($json))
			$json = (array) json_decode($json, true);
		else
			$json = array();

		//if there are no defaults just return the result
		if(!is_array($defaults) || !count($defaults))
			return $json;

		//otherwise parse and return
		return common_parse_args($json, $defaults, $strict, $recursive);
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
if(!function_exists('common_generate_random_string')){
	function common_generate_random_string($length=10, $soup=null){
		if(is_array($soup) && count($soup)){
			$soup = implode('', array_map('strval', $soup));
			$soup = preg_replace('/[^[:print:]]/u', '', $soup);	//strip non-printable
			$soup = preg_replace('/\s/u', '', $soup);			//strip whitespace
			$soup = array_unique(str_split($soup));
			$soup = array_values($soup);
			if(!count($soup))
				return '';
		}

		//use default soup
		if(!is_array($soup) || !count($soup))
			$soup = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','8','9');

		$length = (int) $length;
		if($length <= 0)
			return '';

		//pick nine entries at random
		$salt = '';
		$max = count($soup) - 1;
		for($x=0; $x<$length; $x++)
			$salt .= $soup[common_random_int(0, $max)];

		return $salt;
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
if(!function_exists('common_get_cc_exp_years')){
	function common_get_cc_exp_years($length=10){
		$length = (int) $length;
		if($length <= 0)
			$length = 10;

		$years = array();
		for($x=0; $x<$length; $x++)
			$years[(intval(current_time('Y')) + $x)] = intval(current_time('Y')) + $x;

		return $years;
	}
}

//-------------------------------------------------
// Get Expiration Months
//
// @param format
// @return months
if(!function_exists('common_get_cc_exp_months')){
	function common_get_cc_exp_months($format='m - M'){
		$months = array();
		for($x=1; $x<=12; $x++)
			$months[$x] = date($format, strtotime("2000-" . sprintf('%02d', $x) . "-01"));

		return $months;
	}
}

//--------------------------------------------------------------------- end misc

?>