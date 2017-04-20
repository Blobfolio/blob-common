<?php
/**
 * Data Helpers.
 *
 * Miscellaneous functions for processing and manipulating data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class data {

	/**
	 * Compare Two Arrays
	 *
	 * @param array $arr1 Array.
	 * @param array $arr2 Array.
	 * @return bool True/false.
	 */
	public static function array_compare(&$arr1, &$arr2) {
		// Obviously bad data.
		if (!is_array($arr1) || !is_array($arr2) || count($arr1) !== count($arr2)) {
			return false;
		}

		// Different keys, we don't need to check further.
		if (count(array_intersect_key($arr1, $arr2)) !== count($arr1)) {
			return false;
		}

		// We will ignore keys for non-associative arrays.
		if (cast::array_type($arr1) !== 'associative' && cast::array_type($arr2) !== 'associative') {
			return count(array_intersect($arr1, $arr2)) === count($arr1);
		}

		// Check each item.
		foreach ($arr1 as $k=>$v) {
			if (!isset($arr2[$k])) {
				return false;
			}

			// Recursive?
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

	/**
	 * Recursive Array Map
	 *
	 * @param callable $func Callback function.
	 * @param array $arr Array.
	 * @return bool True/false.
	 */
	public static function array_map_recursive(callable $func, array $arr) {
		return filter_var($arr, FILTER_CALLBACK, array('options'=>$func));
	}

	/**
	 * Otherize Values
	 *
	 * Convert an indefinitely large array of totals to
	 * a fixed length with the chopped bits grouped under
	 * "other".
	 *
	 * @param array $arr Array.
	 * @param int $length Length.
	 * @param string $other Label for others.
	 * @return array|bool Array or false.
	 */
	public static function array_otherize($arr=null, $length=5, $other='Other') {
		if ('associative' !== cast::array_type($arr)) {
			return false;
		}

		// Make sure everything is numeric.
		foreach ($arr as $k=>$v) {
			if (!is_numeric($arr[$k])) {
				ref\cast::to_float($arr[$k], true);
			}
		}

		arsort($arr);

		ref\cast::to_int($length, true);
		ref\sanitize::to_range($length, 1);

		// Nothing to do.
		if (count($arr) <= $length) {
			return $arr;
		}

		ref\cast::to_string($other, true);
		if (!$other) {
			$other = 'Other';
		}

		// Just sum it.
		if (1 === $length) {
			return array($other=>array_sum($arr));
		}

		$out = array_slice($arr, 0, $length - 1);
		$out[$other] = array_sum(array_slice($arr, $length - 1));

		return $out;
	}

	/**
	 * Return the last value of an array.
	 *
	 * This is like array_pop() but non-destructive.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function array_pop(array &$arr) {
		if (!count($arr)) {
			return false;
		}

		$reversed = array_reverse($arr);
		return static::array_pop_top($reversed);
	}

	/**
	 * Return the first value of an array.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function array_pop_top(array &$arr) {
		if (!count($arr)) {
			return false;
		}

		reset($arr);
		return $arr[key($arr)];
	}

	/**
	 * Generate Credit Card Expiration Months
	 *
	 * @param string $format Date format.
	 * @return array Months.
	 */
	public static function cc_exp_months($format='m - M') {
		ref\cast::to_string($format, true);
		$months = array();
		for ($x = 1; $x <= 12; $x++) {
			$months[$x] = date($format, strtotime('2000-' . sprintf('%02d', $x) . '-01'));
		}
		return $months;
	}

	/**
	 * Generate Credit Card Expiration Years
	 *
	 * @param int $length Number of years.
	 * @return array Years.
	 */
	public static function cc_exp_years($length=10) {
		ref\cast::to_int($length, true);
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

	/**
	 * Days Between Dates
	 *
	 * @param string $date1 Date.
	 * @param string $date2 Date.
	 * @return int Difference in Days.
	 */
	public static function datediff($date1, $date2) {
		ref\sanitize::date($date1);
		ref\sanitize::date($date2);

		// Same or bad dates.
		if (
			!is_string($date1) ||
			!is_string($date2) ||
			$date1 === $date2 ||
			'0000-00-00' === $date1 ||
			'0000-00-00' === $date2
		) {
			return 0;
		}

		// Prefer DateTime.
		try {
			$date1 = new \DateTime($date1);
			$date2 = new \DateTime($date2);
			$diff = $date1->diff($date2);

			return abs($diff->days);
		} catch (\Throwable $e) {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		} catch (\Exception $e) {
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		}
	}

	/**
	 * Is Value In Range?
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return bool True/false.
	 */
	public static function in_range($value, $min=null, $max=null) {
		return sanitize::to_range($value, $min, $max) === $value;
	}

	/**
	 * Is JSON?
	 *
	 * @param string $str String.
	 * @param bool $empty Allow empty.
	 * @return bool True/false.
	 */
	public static function is_json($str, $empty=false) {
		if (!is_string($str) || (!$empty && !strlen($str))) {
			return false;
		}

		if ($empty && !strlen($str)) {
			return true;
		}

		try {
			$json = json_decode($str);
			return !is_null($json);
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Is Value Valid UTF-8?
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function is_utf8($str) {
		try {
			$str = (string) $str;
			return (bool) preg_match('//u', $str);
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * JSON Decode As Array
	 *
	 * This ensures a JSON string is always decoded to
	 * an array, optionally matching the structure of
	 * a template object.
	 *
	 * @param string $json JSON.
	 * @param mixed $defaults Template.
	 * @param bool $strict Enforce types if templating.
	 * @param bool $recursive Recursive templating.
	 * @return array Data.
	 */
	public static function json_decode_array($json, $defaults=null, $strict=true, $recursive=true) {
		ref\format::json_decode($json);

		if (is_null($json) || (is_string($json) && !$json)) {
			$json = array();
		}
		else {
			ref\cast::to_array($json);
		}

		if (is_array($defaults)) {
			return static::parse_args($json, $defaults, $strict, $recursive);
		}
		else {
			return $json;
		}
	}

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
	public static function length_in_range($str, $min=null, $max=null) {
		ref\cast::to_string($str, true);
		if (!is_null($min) && !is_int($min)) {
			ref\cast::to_int($min, true);
		}
		if (!is_null($max) && !is_int($max)) {
			ref\cast::to_int($max, true);
		}

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
	public static function parse_args($args, $defaults, $strict=true, $recursive=true) {
		ref\cast::to_array($args);
		ref\cast::to_array($defaults);
		ref\cast::to_bool($strict, true);
		ref\cast::to_bool($recursive, true);

		if (!count($defaults)) {
			return array();
		}

		foreach ($defaults as $k=>$v) {
			if (array_key_exists($k, $args)) {
				// Recurse if the default is a populated associative array.
				if (
					$recursive &&
					is_array($defaults[$k]) &&
					cast::array_type($defaults[$k]) === 'associative'
				) {
					$defaults[$k] = static::parse_args($args[$k], $defaults[$k], $strict, $recursive);
				}
				// Otherwise just replace.
				else {
					$defaults[$k] = $args[$k];
					if ($strict && !is_null($v)) {
						ref\cast::to_type($defaults[$k], gettype($v), true);
					}
				}
			}
		}

		return $defaults;
	}

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
	public static function random_int($min=0, $max=1) {
		ref\cast::to_int($min, true);
		ref\cast::to_int($max, true);

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
	public static function random_string($length=10, $soup=null) {
		ref\cast::to_int($length, true);

		if (is_array($soup) && count($soup)) {
			$soup = implode('', array_map('\blobfolio\common\cast::to_string', $soup));
			ref\sanitize::printable($soup);				// Strip non-printable.
			$soup = preg_replace('/\s/u', '', $soup);	// Strip whitespace.
			$soup = array_unique(mb::str_split($soup));
			$soup = array_values($soup);
			if (!count($soup)) {
				return '';
			}
		}

		// Use default soup.
		if (!is_array($soup) || !count($soup)) {
			$soup = constants::RANDOM_CHARS;
		}

		if ($length < 1) {
			return '';
		}

		// Pick nine entries at random.
		$salt = '';
		$max = count($soup) - 1;
		for ($x = 0; $x < $length; $x++) {
			$salt .= $soup[static::random_int(0, $max)];
		}

		return $salt;
	}

	/**
	 * Switch Two Variables
	 *
	 * @param mixed $var1 Variable.
	 * @param mixed $var2 Variable.
	 * @return bool True.
	 */
	public static function switcheroo(&$var1, &$var2) {
		$tmp = $var1;
		$var1 = $var2;
		$var2 = $tmp;

		return true;
	}

	/**
	 * Delete a Cookie
	 *
	 * A companion to PHP's `setcookie()` function. It
	 * attempts to remove the cookie. The same path, etc.,
	 * values should be passed as were used to first set it.
	 *
	 * @param string $name Name.
	 * @param string $path Path.
	 * @param string $domain Domain.
	 * @param bool $secure SSL only.
	 * @param bool $httponly HTTP only.
	 * @return bool True/false.
	 */
	public static function unsetcookie($name, $path='', $domain='', $secure=false, $httponly=false) {
		ref\cast::to_string($name, true);
		ref\cast::to_string($path, true);
		ref\cast::to_string($domain, true);
		ref\cast::to_bool($secure, true);
		ref\cast::to_bool($httponly, true);

		try {
			setcookie($name, false, -1, $path, $domain, $secure, $httponly);
			if (isset($_COOKIE[$name])) {
				unset($_COOKIE[$name]);
			}
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}
}


