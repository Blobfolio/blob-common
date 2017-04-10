<?php
/**
 * Type Handling - By Reference
 *
 * This class can only exist on PHP7+ installs.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

abstract class cast_full extends cast_base {
	/**
	 * To Array
	 *
	 * @param mixed $value Variable.
	 * @return array Array.
	 */
	public static function array(&$value=null) {
		return static::to_array($value);
	}
	/**
	 * To Bool
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return bool True/false.
	 */
	public static function bool(&$value=null, $flatten=false) {
		return static::to_bool($value, $flatten);
	}
	/**
	 * To Bool
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return bool True/false.
	 */
	public static function boolean(&$value=null, $flatten=false) {
		return static::to_bool($value, $flatten);
	}
	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return float Value.
	 */
	public static function double(&$value=null, $flatten=false) {
		return static::to_bool($value, $flatten);
	}
	/**
	 * To Float
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return float Value.
	 */
	public static function float(&$value=null, $flatten=false) {
		return static::to_float($value, $flatten);
	}
	/**
	 * To Integer
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return int Value.
	 */
	public static function int(&$value=null, $flatten=false) {
		return static::to_int($value, $flatten);
	}
	/**
	 * To Integer
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return int Value.
	 */
	public static function integer(&$value=null, $flatten=false) {
		return static::to_bool($value, $flatten);
	}
	/**
	 * To Number
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return float Value.
	 */
	public static function number(&$value=null, $flatten=false) {
		return static::to_number($value, $flatten);
	}
	/**
	 * To String
	 *
	 * @param mixed $value Variable.
	 * @param bool $flatten Flatten.
	 * @return string String.
	 */
	public static function string(&$value=null, $flatten=false) {
		return static::to_string($value, $flatten);
	}
}
