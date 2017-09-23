<?php
/**
 * Type Handling - By Reference
 *
 * Functions for typecasting and type detection. Magic fails
 * us here as we can't magically handle by-reference.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

use \blobfolio\common\cast as v_cast;
use \blobfolio\common\constants;
use \blobfolio\common\mb as v_mb;

abstract class cast_base {

	/**
	 * To Array
	 *
	 * @param mixed $value Variable.
	 * @return bool True.
	 */
	public static function to_array(&$value=null) {
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
	public static function to_bool(&$value=false, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_bool($value[$k]);
			}
		}
		else {
			// Evaluate special cases.
			if (is_string($value)) {
				$value = strtolower($value);
				if (in_array($value, constants::TRUE_BOOLS, true)) {
					$value = true;
				}
				elseif (in_array($value, constants::FALSE_BOOLS, true)) {
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
	public static function to_float(&$value=0, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_float($value[$k]);
			}
		}
		else {
			static::to_number($value, true);
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
	public static function to_int(&$value=0, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_int($value[$k]);
			}
		}
		else {
			// Evaluate special cases.
			if (is_string($value)) {
				$value = strtolower($value);
				if (in_array($value, constants::TRUE_BOOLS, true)) {
					$value = 1;
				}
				elseif (in_array($value, constants::FALSE_BOOLS, true)) {
					$value = 0;
				}
			}

			static::to_number($value, true);
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
	public static function to_number(&$value=0, $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_number($value[$k]);
			}
		}
		else {
			if (is_string($value)) {
				static::to_string($value);

				// Replace number chars.
				$from = array_keys(constants::NUMBER_CHARS);
				$to = array_values(constants::NUMBER_CHARS);
				$value = str_replace($from, $to, $value);

				// Convert from cents.
				if (preg_match('/^\-?[\d,]*\.?\d+¢$/', $value)) {
					$value = v_cast::to_number(preg_replace('/[^\-\d\.]/', '', $value)) / 100;
				}
				// Convert from percent.
				elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $value)) {
					$value = v_cast::to_number(preg_replace('/[^\-\d\.]/', '', $value)) / 100;
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
	public static function to_string(&$value='', $flatten=false) {
		if (!$flatten && is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_string($value[$k]);
			}
		}
		else {
			try {
				$value = (string) $value;
				if ($value && !mb_check_encoding($value, 'ASCII')) {
					sanitize::utf8($value);
				}
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
		static::to_string($type, true);
		if (!$type) {
			return true;
		}

		switch (strtolower($type)) {
			case 'string':
				static::to_string($value, $flatten);
				break;
			case 'integer':
				static::to_int($value, $flatten);
				break;
			case 'double':
				static::to_float($value, $flatten);
				break;
			case 'boolean':
				static::to_bool($value, $flatten);
				break;
			case 'array':
				static::to_array($value);
				break;
			case 'int':
				static::to_int($value, $flatten);
				break;
			case 'float':
				static::to_float($value, $flatten);
				break;
			case 'bool':
				static::to_bool($value, $flatten);
				break;
		}

		return true;
	}
}
