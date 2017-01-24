<?php
//---------------------------------------------------------------------
// MULTIBYTE WRAPPERS
//---------------------------------------------------------------------
// prefer multibyte functionality but fallback to dumb functions as
// needed



namespace blobfolio\common;

class mb {

	//-------------------------------------------------
	// Parse (query) String
	//
	// @param string
	// @param result
	// @return true/false
	public static function parse_str($str, &$result) {
		if (function_exists('mb_parse_str')) {
			return mb_parse_str($str, $result);
		}
		else {
			return parse_str($str, $result);
		}
	}

	//-------------------------------------------------
	// Split String
	//
	// @param string
	// @param length
	// @return split
	public static function str_split($str, int $split_length=1) {
		ref\cast::string($str);
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

	//-------------------------------------------------
	// Proper Length Detection
	//
	// @param var
	// @return length
	public static function strlen($str) {
		ref\cast::string($str);

		if (function_exists('mb_strlen')) {
			return (int) mb_strlen($str, 'UTF-8');
		}
		else {
			return strlen($str);
		}
	}

	//-------------------------------------------------
	// Str_Pad
	//
	// @param string
	// @param pad length
	// @param pad string
	// @param pad type
	public static function str_pad($str='', int $pad_length, string $pad_string=' ', $pad_type=null) {
		ref\cast::string($string);
		ref\cast::string($pad_string);

		$current_length = static::strlen($str);
		$pad_string_length = static::strlen($pad_string);

		if ($pad_length <= $current_length || !$pad_string_length) {
			return $str;
		}

		//pad left
		if ($pad_type === STR_PAD_LEFT) {
			$str = str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length)) . $str;
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, $new_length - $pad_length);
			}
		}
		//pad both
		elseif ($pad_type === STR_PAD_BOTH) {
			$leftright = 'right';
			while (static::strlen($str) < $pad_length) {
				$leftright = $leftright === 'left' ? 'right' : 'left';
				if ($leftright === 'left') {
					$str = "{$pad_string}{$str}";
				}
				else {
					$str .= $pad_string;
				}
			}

			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				if ($leftright === 'left') {
					$str = static::substr($str, $new_length - $pad_length);
				}
				else {
					$str = static::substr($str, 0, $pad_length);
				}
			}
		}
		//pad right
		else {
			$str .= str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length));
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, 0, $pad_length);
			}
		}

		return $str;
	}

	//-------------------------------------------------
	// Strpos
	//
	// @param haystack
	// @param needle
	// @param offset
	// @return count
	public static function strpos($haystack, $needle, $offset=0) {
		ref\cast::string($haystack);
		ref\cast::string($needle);

		if (function_exists('mb_strpos')) {
			return mb_strpos($haystack, $needle, $offset, 'UTF-8');
		}
		else {
			return strpos($haystack, $needle, $offset);
		}
	}

	//-------------------------------------------------
	// Lowercase
	//
	// @param str
	// @return str
	public static function strtolower($str='') {
		ref\mb::strtolower($str);
		return $str;
	}

	//-------------------------------------------------
	// Uppercase
	//
	// @param str
	// @return str
	public static function strtoupper($str='') {
		ref\mb::strtoupper($str);
		return $str;
	}

	//-------------------------------------------------
	// Substring
	//
	// @param var
	// @param start
	// @param length
	// @return substring
	public static function substr($str, $start=0, $length=null) {
		ref\cast::string($str);

		if (function_exists('mb_substr')) {
			return mb_substr($str, $start, $length, 'UTF-8');
		}
		else {
			return substr($str, $start, $length);
		}
	}

	//-------------------------------------------------
	// Substring Count
	//
	// @param haystack
	// @param needle
	// @return count
	public static function substr_count($haystack, $needle) {
		ref\cast::string($haystack);
		ref\cast::string($needle);

		if (function_exists('mb_substr_count')) {
			return mb_substr_count($haystack, $needle, 'UTF-8');
		}
		else {
			return substr_count($haystack, $needle);
		}
	}

	//-------------------------------------------------
	// Sentence Case
	//
	// @param str
	// @return str
	public static function ucfirst($str='') {
		ref\mb::ucfirst($str);
		return $str;
	}

	//-------------------------------------------------
	// Title Case
	//
	// @param str
	// @return str
	public static function ucwords($str='') {
		ref\mb::ucwords($str);
		return $str;
	}
}

?>