<?php
//---------------------------------------------------------------------
// MULTIBYTE WRAPPERS
//---------------------------------------------------------------------
// prefer multibyte functionality but fallback to dumb functions as
// needed



namespace blobfolio\common\ref;

class mb {

	//-------------------------------------------------
	// Lowercase
	//
	// @param str
	// @return str
	public static function strtolower(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::strtolower($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (function_exists('mb_strtolower')) {
				$str = mb_strtolower($str, 'UTF-8');

				//replace some extra characters
				$from = array_keys(\blobfolio\common\constants::CASE_CHARS);
				$to = array_values(\blobfolio\common\constants::CASE_CHARS);
				$str = str_replace($from, $to, $str);
			}
			else {
				$str = strtolower($str);
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Uppercase
	//
	// @param str
	// @return str
	public static function strtoupper(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::strtoupper($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (function_exists('mb_strtoupper')) {
				$str = mb_strtoupper($str, 'UTF-8');

				//replace some extra characters
				$to = array_keys(\blobfolio\common\constants::CASE_CHARS);
				$from = array_values(\blobfolio\common\constants::CASE_CHARS);
				$str = str_replace($from, $to, $str);
			}
			else {
				$str = strtoupper($str);
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Sentence Case
	//
	// @param str
	// @return str
	public static function ucfirst(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ucfirst($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (function_exists('mb_substr')) {
				$first = \blobfolio\common\mb::substr($str, 0, 1);
				static::strtoupper($first);
				$str = $first . \blobfolio\common\mb::substr($str, 1, null);
			}
			else {
				$str = ucfirst($str);
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Title Case
	//
	// @param str
	// @return str
	public static function ucwords(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ucwords($str[$k]);
			}
		}
		else {
			cast::string($str);

			//don't use the built-in case functions as those
			//kinda suck. instead let's adjust manually
			$extra = array();

			//the first letter
			preg_match_all('/^(\p{L})/u', $str, $matches);
			if (count($matches[0])) {
				static::strtoupper($matches[1][0]);
				if ($matches[0][0] !== $matches[1][0]) {
					$extra[$matches[0][0]] = $matches[1][0];
				}
			}

			//any letter following a dash, space, or forward slash
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

			//make replacement(s)
			if (count($extra)) {
				$extra = array_unique($extra);
				$str = str_replace(array_keys($extra), array_values($extra), $str);
			}
		}

		return true;
	}
}

?>