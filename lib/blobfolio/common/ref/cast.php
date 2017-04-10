<?php
/**
 * Type Handling - By Reference
 *
 * Functions for typecasting and type detection.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

class cast {

	/**
	 * To Array
	 *
	 * @param mixed $value Variable.
	 * @return bool True.
	 */
	public static function array(&$value=null) {
		try {
			$value = (array) $value;
		} catch (\Throwable $e) {
			$value = array();
		} catch (\Exception $e) {
			$value = array();
		}

		return true;
	}

	/**
	 * To Bool
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function bool(&$value=false, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::bool($value[$k]);
			}
		}
		else {
			// Evaluate special cases.
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
				} catch (\Exception $e) {
					$value = false;
				}
			}
		}

		return true;
	}

	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function float(&$value=0, $flatten=false) {
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
			} catch (\Exception $e) {
				$value = 0.0;
			}
		}

		return true;
	}

	/**
	 * To Int
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function int(&$value=0, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::int($value[$k]);
			}
		}
		else {
			// Evaluate special cases.
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

	/**
	 * To Number
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function number(&$value=0, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::number($value[$k]);
			}
		}
		else {
			if (is_string($value)) {
				static::string($value);

				// Replace number chars.
				$from = array_keys(\blobfolio\common\constants::NUMBER_CHARS);
				$to = array_values(\blobfolio\common\constants::NUMBER_CHARS);
				$value = str_replace($from, $to, $value);

				// Convert from cents.
				if (preg_match('/^\-?[\d,]*\.?\d+¢$/', $value)) {
					$value = preg_replace('/[^\-\d\.]/', '', $value) / 100;
				}
				// Convert from percent.
				elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $value)) {
					$value = preg_replace('/[^\-\d\.]/', '', $value) / 100;
				}
			}

			try {
				$value = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			} catch (\Throwable $e) {
				$value = 0.0;
			} catch (\Exception $e) {
				$value = 0.0;
			}
		}

		return true;
	}

	/**
	 * To String
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function string(&$value='', $flatten=false) {
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
			} catch (\Exception $e) {
				$value = '';
			}
		}

		return true;
	}

	/**
	 * To X Type
	 *
	 * @param mixed $value Variable.
	 * @param string $type Type.
	 * @param bool $flatten Do not recurse.
	 * @return bool True.
	 */
	public static function to_type(&$value, $type=null, $flatten=false) {
		static::string($type, true);
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


