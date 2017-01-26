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
	public static function bool(&$value=false) {
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::bool($value[$k]);
			}
		}
		else {
			//evaluate special cases
			if (is_string($value)) {
				mb::strtolower($value);
				if (in_array($value, \blobfolio\common\constants::TRUE_BOOLS)) {
					$value = true;
				}
				elseif (in_array($value, \blobfolio\common\constants::FALSE_BOOLS)) {
					$value = false;
				}
				else {
					$value = (bool) $value;
				}
			}
			else {
				$value = (bool) $value;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Float
	//
	// @param value
	// @return true/false
	public static function float(&$value=0) {
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::float($value[$k]);
			}
		}
		else {
			static::number($value);
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
	// @return true/false
	public static function int(&$value=0) {
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::int($value[$k]);
			}
		}
		else {
			static::number($value);
			$value = (int) $value;
		}

		return true;
	}

	//-------------------------------------------------
	// Number
	//
	// @param value
	// @return value
	public static function number(&$value=0) {
		if (is_array($value)) {
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
					$value = preg_replace('/[^\-\d\.]/', '', $value) * 100;
				}
				//convert from percent
				elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $value)) {
					$value = preg_replace('/[^\-\d\.]/', '', $value) / 100;
				}
			}

			$value = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		}

		return true;
	}

	//-------------------------------------------------
	// String
	//
	// @param value
	// @return value
	public static function string(&$value='') {
		if (is_array($value)) {
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
	// @return value
	public static function to_type(&$value, string $type=null) {
		if (!\blobfolio\common\mb::strlen($type)) {
			return true;
		}

		mb::strtolower($type);

		if ($type === 'boolean' || $type === 'bool') {
			static::bool($value);
		}
		elseif ($type === 'integer' || $type === 'int') {
			static::int($value);
		}
		elseif ($type === 'double' || $type === 'float') {
			static::float($value);
		}
		elseif ($type === 'string') {
			static::string($value);
		}
		elseif ($type === 'array') {
			static::array($value);
		}

		return true;
	}
}

?>