<?php
//---------------------------------------------------------------------
// DATA HELPERS
//---------------------------------------------------------------------
// various functions for managing and parsing data



namespace blobfolio\common;

class data {

	//-------------------------------------------------
	// Compare Arrays
	//
	// @param arr1
	// @param arr2
	// @return true/false
	public static function array_compare(&$arr1, &$arr2) {
		//obviously bad data
		if (!is_array($arr1) || !is_array($arr2) || count($arr1) !== count($arr2)) {
			return false;
		}

		//different keys, we don't need to check further
		if (count(array_intersect_key($arr1, $arr2)) !== count($arr1)) {
			return false;
		}

		//we will ignore keys for non-associative arrays
		if (cast::array_type($arr1) !== 'associative' && cast::array_type($arr2) !== 'associative') {
			return count(array_intersect($arr1, $arr2)) === count($arr1);
		}

		//check each item
		foreach ($arr1 as $k=>$v) {
			if (!isset($arr2[$k])) {
				return false;
			}

			//recursive?
			if (is_array($arr1[$k]) && is_array($arr2[$k])) {
				if (!static::array_compare($arr1[$k], $arr2[$k])) {
					return false;
				}
			}
			elseif ($arr1[$k] !== $arr2[$k]) {
				return false;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Recursive Array Map
	//
	// @param callback
	// @param var
	public static function array_map_recursive(callable $func, array $array) {
		return filter_var($array, FILTER_CALLBACK, array('options'=>$func));
	}

	//-------------------------------------------------
	// Return the last index of an array
	//
	// this is like array_pop but doesn't alter the
	// array
	//
	// @param array
	// @return mixed or false
	public static function array_pop(array &$arr) {
		if (!count($arr)) {
			return false;
		}

		$reversed = array_reverse($arr);
		return static::array_pop_top($reversed);
	}

	//-------------------------------------------------
	// Return the first index of an array
	//
	// this is like array_pop for the first entry
	//
	// @param array
	// @return mixed or false
	public static function array_pop_top(array &$arr) {
		if (!count($arr)) {
			return false;
		}

		reset($arr);
		return $arr[key($arr)];
	}

	//-------------------------------------------------
	// CC Expiration Months
	//
	// @param format
	// @return months
	public static function cc_exp_months(string $format='m - M') {
		$months = array();
		for ($x = 1; $x <= 12; $x++) {
			$months[$x] = date($format, strtotime('2000-' . sprintf('%02d', $x) . '-01'));
		}
		return $months;
	}

	//-------------------------------------------------
	// CC Expiration Years
	//
	// @param format
	// @return months
	public static function cc_exp_years(int $length=10) {
		if ($length < 1) {
			$length = 10;
		}

		$years = array();
		for ($x = 0; $x < $length; $x++) {
			$year = (int) (date('Y') + $x);
			$years[$year] = $year;
		}

		return $years;
	}

	//-------------------------------------------------
	// Date Diff
	//
	// @param date1
	// @param date2
	// @return days
	public static function datediff($date1, $date2) {
		ref\sanitize::date($date1);
		ref\sanitize::date($date2);

		//same or bad dates
		if (
			$date1 === $date2 ||
			$date1 === '0000-00-00' ||
			$date2 === '0000-00-00'
		) {
			return 0;
		}

		//prefer DateTime
		try {
			$date1 = new \DateTime($date1);
			$date2 = new \DateTime($date2);
			$diff = $date1->diff($date2);

			return abs($diff->days);
		} //fallback to counting seconds
		catch (Throwable $e) {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		}
	}

	//-------------------------------------------------
	// In Range?
	//
	// @param value
	// @param min
	// @param max
	public static function in_range($value, $min=null, $max=null) {
		return $value === sanitize::to_range($value, $min, $max);
	}

	//-------------------------------------------------
	// Check for UTF-8
	//
	// @param string
	// @return true/false
	public static function is_utf8(string $str) {
		try {
			return (bool) preg_match('//u', $str);
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// JSON decode array
	//
	// @param json
	// @param defaults
	// @param strict
	// @param recursive
	// @return array
	public static function json_decode_array(string $json, $defaults=null, bool $strict=true, bool $recursive=true) {
		if (!mb::strlen($json)) {
			$json = array();
		}
		else {
			$json = json_decode($json, true);
			if (is_null($json)) {
				$json = array();
			}
			elseif (!is_array($json)) {
				ref\cast::array($json);
			}
		}

		if (is_array($defaults)) {
			return static::parse_args($json, $defaults);
		}
		else {
			return $json;
		}
	}

	//-------------------------------------------------
	// Length Range
	//
	// @param var
	// @return true/false
	public static function length_in_range(string $str, int $min=null, int $max=null) {
		$length = mb::strlen($str);
		if (!is_null($min) && !is_null($max) && $min > $max) {
			static::switcheroo($min, $max);
		}

		if (!is_null($min) && $min > $length) {
			return false;
		}

		if (!is_null($max) && $max < $length) {
			return false;
		}

		return true;
	}

	//-------------------------------------------------
	// Parse Args
	//
	// @param args
	// @param defaults
	// @param strict enforce type (be careful!)
	// @param recursive
	// @return args
	public static function parse_args($args, $defaults, bool $strict=true, bool $recursive=true) {
		ref\cast::array($args);
		ref\cast::array($defaults);

		if (!count($defaults)) {
			return array();
		}

		foreach ($defaults as $k=>$v) {
			if (array_key_exists($k, $args)) {
				//recurse if the default is a populated associative array
				if (
					$recursive &&
					is_array($defaults[$k]) &&
					cast::array_type($defaults[$k]) === 'associative'
				) {
					$defaults[$k] = static::parse_args($args[$k], $defaults[$k], $strict, $recursive);
				}
				//otherwise just replace
				else {
					$defaults[$k] = $args[$k];
					if ($strict && !is_null($v)) {
						ref\cast::to_type($defaults[$k], gettype($v));
					}
				}
			}
		}

		return $defaults;
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
	public static function random_int(int $min=0, int $max=1) {
		if ($min > $max) {
			static::switcheroo($min, $max);
		}

		if (function_exists('random_int')) {
			return random_int($min, $max);
		}
		else {
			return mt_rand($min, $max);
		}
	}

	//-------------------------------------------------
	// Generate random string
	//
	// @param length
	// @return string
	public static function random_string(int $length=10, $soup=null) {
		if (is_array($soup) && count($soup)) {
			$soup = implode('', array_map('\blobfolio\common\cast::string', $soup));
			ref\sanitize::printable($soup);				//strip non-printable
			$soup = preg_replace('/\s/u', '', $soup);	//strip whitespace
			$soup = array_unique(mb::str_split($soup));
			$soup = array_values($soup);
			if (!count($soup)) {
				return '';
			}
		}

		//use default soup
		if (!is_array($soup) || !count($soup)) {
			$soup = constants::RANDOM_CHARS;
		}

		if ($length < 1) {
			return '';
		}

		//pick nine entries at random
		$salt = '';
		$max = count($soup) - 1;
		for ($x = 0; $x < $length; $x++) {
			$salt .= $soup[static::random_int(0, $max)];
		}

		return $salt;
	}

	//-------------------------------------------------
	// Switch two variables
	//
	// @param var1
	// @param var2
	// @return n/a
	public static function switcheroo(&$var1, &$var2) {
		$tmp = $var1;
		$var1 = $var2;
		$var2 = $tmp;
	}

	//-------------------------------------------------
	// Delete Cookie
	//
	// @param key
	// @return true
	public static function unsetcookie(string $name, string $path='', string $domain='', bool $secure=false, bool $httponly=false) {
		try {
			setcookie($name, false, -1, $path, $domain, $secure, $httponly);
			if (isset($_COOKIE[$name])) {
				unset($_COOKIE[$name]);
			}
		} catch (\Throwable $e) {
			return false;
		}

		return true;
	}
}

?>