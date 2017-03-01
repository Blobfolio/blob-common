<?php
/**
 * Multi-Byte.
 *
 * Functions for multi-byte string handling.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class mb {

	/**
	 * Wrapper For parse_str()
	 *
	 * @param string $str String.
	 * @param mixed $result Result.
	 * @return bool True/false.
	 */
	public static function parse_str($str, &$result) {
		if (function_exists('mb_parse_str')) {
			return mb_parse_str($str, $result);
		}
		else {
			return parse_str($str, $result);
		}
	}

	/**
	 * Wrapper For str_split()
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @return array|bool Split string or false.
	 */
	public static function str_split($str, int $split_length=1) {
		ref\cast::string($str, true);
		if ($split_length < 1) {
			return false;
		}

		$str_length = static::strlen($str);
		$out = array();

		for ($i = 0; $i < $str_length; $i += $split_length) {
			$out[] = static::substr($str, $i, $split_length);
		}

		return $out;
	}

	/**
	 * Wrapper For strlen()
	 *
	 * @param string $str String.
	 * @return int String length.
	 */
	public static function strlen($str) {
		ref\cast::string($str, true);

		if (function_exists('mb_strlen')) {
			return (int) mb_strlen($str, 'UTF-8');
		}
		else {
			return strlen($str);
		}
	}

	/**
	 * Wrapper For str_pad()
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @return string Padded string.
	 */
	public static function str_pad($str='', int $pad_length, $pad_string=' ', $pad_type=null) {
		ref\cast::string($string, true);
		ref\cast::string($pad_string, true);

		$current_length = static::strlen($str);
		$pad_string_length = static::strlen($pad_string);

		if ($pad_length <= $current_length || !$pad_string_length) {
			return $str;
		}

		// Pad left.
		if (STR_PAD_LEFT === $pad_type) {
			$str = str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length)) . $str;
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, $new_length - $pad_length);
			}
		}
		// Pad both.
		elseif (STR_PAD_BOTH === $pad_type) {
			$leftright = 'right';
			while (static::strlen($str) < $pad_length) {
				$leftright = 'left' === $leftright ? 'right' : 'left';
				if ('left' === $leftright) {
					$str = "{$pad_string}{$str}";
				}
				else {
					$str .= $pad_string;
				}
			}

			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				if ('left' === $leftright) {
					$str = static::substr($str, $new_length - $pad_length);
				}
				else {
					$str = static::substr($str, 0, $pad_length);
				}
			}
		}
		// Pad right.
		else {
			$str .= str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length));
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, 0, $pad_length);
			}
		}

		return $str;
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool First occurrence or false.
	 */
	public static function strpos($haystack, $needle, $offset=0) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_strpos')) {
			return mb_strpos($haystack, $needle, $offset, 'UTF-8');
		}
		else {
			return strpos($haystack, $needle, $offset);
		}
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool Last occurrence or false.
	 */
	public static function strrpos($haystack, $needle, $offset=0) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_strrpos')) {
			return mb_strrpos($haystack, $needle, $offset, 'UTF-8');
		}
		else {
			return strrpos($haystack, $needle, $offset);
		}
	}

	/**
	 * Wrapper For strtolower()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function strtolower($str='') {
		ref\mb::strtolower($str);
		return $str;
	}

	/**
	 * Wrapper For strtoupper()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function strtoupper($str='') {
		ref\mb::strtoupper($str);
		return $str;
	}

	/**
	 * Wrapper For substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @return string String.
	 */
	public static function substr($str, $start=0, $length=null) {
		ref\cast::string($str, true);

		if (function_exists('mb_substr')) {
			return mb_substr($str, $start, $length, 'UTF-8');
		}
		else {
			return substr($str, $start, $length);
		}
	}

	/**
	 * Wrapper For substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @return int Count.
	 */
	public static function substr_count($haystack, $needle) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_substr_count')) {
			return mb_substr_count($haystack, $needle, 'UTF-8');
		}
		else {
			return substr_count($haystack, $needle);
		}
	}

	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function ucfirst($str='') {
		ref\mb::ucfirst($str);
		return $str;
	}

	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function ucwords($str='') {
		ref\mb::ucwords($str);
		return $str;
	}
}


