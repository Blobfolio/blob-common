<?php
//---------------------------------------------------------------------
// TYPECAST & TYPE HANDLING
//---------------------------------------------------------------------
// functions for typecasting and detecting type



namespace blobfolio\common\ref;

class cast {

	//-------------------------------------------------
	// Array
	//
	// @param value
	// @return value
	public static function array(&$value=null) {
		try {
			$value = (array) $value;
		} catch (\Throwable $e) {
			$value = array();
		}

		return true;
	}

	//-------------------------------------------------
	// Bool
	//
	// @param value
	// @return true/false
	public static function bool(&$value=false, bool $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::bool($value[$k]);
			}
		}
		else {
			//evaluate special cases
			if (is_string($value)) {
				mb::strtolower($value);
				if (in_array($value, \blobfolio\common\constants::TRUE_BOOLS, true)) {
					$value = true;
				}
				elseif (in_array($value, \blobfolio\common\constants::FALSE_BOOLS, true)) {
					$value = false;
				}
				else {
					$value = (bool) $value;
				}
			}
			else {
				try {
					$value = (bool) $value;
				} catch (\Throwable $e) {
					$value = false;
				}
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Float
	//
	// @param value
	// @param flatten
	// @return true/false
	public static function float(&$value=0, bool $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::float($value[$k]);
			}
		}
		else {
			static::number($value, true);
			try {
				$value = (float) $value;
			} catch (\Throwable $e) {
				$value = 0.0;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Int
	//
	// @param value
	// @param flatten
	// @return true/false
	public static function int(&$value=0, bool $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::int($value[$k]);
			}
		}
		else {
			//evaluate special cases
			if (is_string($value)) {
				mb::strtolower($value);
				if (in_array($value, \blobfolio\common\constants::TRUE_BOOLS, true)) {
					$value = 1;
				}
				elseif (in_array($value, \blobfolio\common\constants::FALSE_BOOLS, true)) {
					$value = 0;
				}
			}

			static::number($value, true);
			$value = (int) $value;
		}

		return true;
	}

	//-------------------------------------------------
	// Number
	//
	// @param value
	// @param flatten
	// @return value
	public static function number(&$value=0, bool $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::number($value[$k]);
			}
		}
		else {
			if (is_string($value)) {
				static::string($value);

				//replace number chars
				$from = array_keys(\blobfolio\common\constants::NUMBER_CHARS);
				$to = array_values(\blobfolio\common\constants::NUMBER_CHARS);
				$value = str_replace($from, $to, $value);

				//convert from cents
				if (preg_match('/^\-?[\d,]*\.?\d+¢$/', $value)) {
					$value = preg_replace('/[^\-\d\.]/', '', $value) / 100;
				}
				//convert from percent
				elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $value)) {
					$value = preg_replace('/[^\-\d\.]/', '', $value) / 100;
				}
			}

			try {
				$value = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			} catch (\Throwable $e) {
				$value = 0.0;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// String
	//
	// @param value
	// @param flatten
	// @return value
	public static function string(&$value='', bool $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::string($value[$k]);
			}
		}
		else {
			try {
				$value = (string) $value;
				sanitize::utf8($value);
			} catch (\Throwable $e) {
				$value = '';
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Sanitize by Type
	//
	// @param value
	// @param type
	// @param flatten
	// @return value
	public static function to_type(&$value, string $type=null, bool $flatten=false) {
		if (!\blobfolio\common\mb::strlen($type)) {
			return true;
		}

		mb::strtolower($type);

		if ('boolean' === $type || 'bool' === $type) {
			static::bool($value, $flatten);
		}
		elseif ('integer' === $type || 'int' === $type) {
			static::int($value, $flatten);
		}
		elseif ('double' === $type || 'float' === $type) {
			static::float($value, $flatten);
		}
		elseif ('string' === $type) {
			static::string($value, $flatten);
		}
		elseif ('array' === $type) {
			static::array($value);
		}

		return true;
	}
}

?>