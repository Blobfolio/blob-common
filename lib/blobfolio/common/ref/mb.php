<?php
/**
 * Multi-Byte - By Reference
 *
 * Functions for multi-byte string handling.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

use \blobfolio\common\constants;
use \blobfolio\common\mb as v_mb;

class mb {

	/**
	 * Wrapper For str_pad()
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @return bool True/false.
	 */
	public static function str_pad(&$str='', $pad_length, $pad_string=' ', $pad_type=null) {
		cast::to_string($string, true);
		cast::to_int($pad_length, true);
		cast::to_string($pad_string, true);

		$current_length = v_mb::strlen($str);
		$pad_string_length = v_mb::strlen($pad_string);

		if ($pad_length <= $current_length || !$pad_string_length) {
			return true;
		}

		// Pad left.
		if (STR_PAD_LEFT === $pad_type) {
			$str = str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length)) . $str;
			$new_length = v_mb::strlen($str);
			if ($new_length > $pad_length) {
				$str = v_mb::substr($str, $new_length - $pad_length);
			}
		}
		// Pad both.
		elseif (STR_PAD_BOTH === $pad_type) {
			$leftright = 'right';
			while (v_mb::strlen($str) < $pad_length) {
				$leftright = 'left' === $leftright ? 'right' : 'left';
				if ('left' === $leftright) {
					$str = "{$pad_string}{$str}";
				}
				else {
					$str .= $pad_string;
				}
			}

			$new_length = v_mb::strlen($str);
			if ($new_length > $pad_length) {
				if ('left' === $leftright) {
					$str = v_mb::substr($str, $new_length - $pad_length);
				}
				else {
					$str = v_mb::substr($str, 0, $pad_length);
				}
			}
		}
		// Pad right.
		else {
			$str .= str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length));
			$new_length = v_mb::strlen($str);
			if ($new_length > $pad_length) {
				$str = v_mb::substr($str, 0, $pad_length);
			}
		}

		return true;
	}

	/**
	 * Wrapper For str_split()
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @return bool True/false.
	 */
	public static function str_split(&$str, $split_length=1) {
		cast::to_int($split_length, true);
		if ($split_length < 1) {
			$str = false;
			return false;
		}

		cast::to_string($str, true);
		$str_length = v_mb::strlen($str);
		$out = array();

		for ($i = 0; $i < $str_length; $i += $split_length) {
			$out[] = v_mb::substr($str, $i, $split_length);
		}

		$str = $out;
		return true;
	}

	/**
	 * Wrapper For strrev()
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function strrev(&$str) {
		cast::to_string($str, true);

		if (!$str) {
			return false;
		}

		preg_match_all('/./us', $str, $arr);
		$str = implode('', array_reverse($arr[0]));

		return true;
	}

	/**
	 * Wrapper For strtolower()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function strtolower(&$str='', $strict=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::strtolower($str[$k], $strict);
			}
		}
		elseif (!$strict || is_string($str)) {
			cast::to_string($str);

			if (function_exists('mb_strtolower')) {
				$str = mb_strtolower($str, 'UTF-8');

				// Replace some extra characters.
				$from = array_keys(constants::CASE_CHARS);
				$to = array_values(constants::CASE_CHARS);
				$str = str_replace($from, $to, $str);
			}
			else {
				$str = strtolower($str);
			}
		}

		return true;
	}

	/**
	 * Wrapper For strtoupper()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function strtoupper(&$str='', $strict=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::strtoupper($str[$k], $strict);
			}
		}
		elseif (!$strict || is_string($str)) {
			cast::to_string($str);

			if (function_exists('mb_strtoupper')) {
				$str = mb_strtoupper($str, 'UTF-8');

				// Replace some extra characters.
				$to = array_keys(constants::CASE_CHARS);
				$from = array_values(constants::CASE_CHARS);
				$str = str_replace($from, $to, $str);
			}
			else {
				$str = strtoupper($str);
			}
		}

		return true;
	}

	/**
	 * Trim
	 *
	 * Trim all whitespacey bits from both ends.
	 *
	 * @param string $str String.
	 * @return bool True.
	 */
	public static function trim(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::trim($str[$k]);
			}
		}
		else {
			cast::to_string($str);

			$str = preg_replace('/^\s+/u', '', $str);
			$str = preg_replace('/\s+$/u', '', $str);
		}

		return true;
	}

	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function ucfirst(&$str='', $strict=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ucfirst($str[$k], $strict);
			}
		}
		elseif (!$strict || is_string($str)) {
			cast::to_string($str);

			if (function_exists('mb_substr')) {
				$first = v_mb::substr($str, 0, 1);
				static::strtoupper($first);
				$str = $first . v_mb::substr($str, 1, null);
			}
			else {
				$str = ucfirst($str);
			}
		}

		return true;
	}

	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @param bool $strict Strict.
	 * @return string String.
	 */
	public static function ucwords(&$str='', $strict=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ucwords($str[$k], $strict);
			}
		}
		elseif (!$strict || is_string($str)) {
			cast::to_string($str);

			// Don't use the built-in case functions as those
			// kinda suck. Instead let's adjust manually.
			$extra = array();

			// The first letter.
			preg_match_all('/^(\p{L})/u', $str, $matches);
			if (count($matches[0])) {
				static::strtoupper($matches[1][0]);
				if ($matches[0][0] !== $matches[1][0]) {
					$extra[$matches[0][0]] = $matches[1][0];
				}
			}

			// Any letter following a dash, space, or forward slash.
			preg_match_all('/(\s|\p{Pd}|\/)(.)/u', $str, $matches);
			if (count($matches[0])) {
				foreach ($matches[0] as $k=>$v) {
					static::strtoupper($matches[2][$k]);
					$new = $matches[1][$k] . $matches[2][$k];
					if ($v !== $new) {
						$extra[$v] = $new;
					}
				}
			}

			// Make replacement(s).
			if (count($extra)) {
				$extra = array_unique($extra);
				$str = str_replace(array_keys($extra), array_values($extra), $str);
			}
		}

		return true;
	}
}


