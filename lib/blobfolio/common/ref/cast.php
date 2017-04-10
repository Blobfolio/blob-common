<?php
// @codingStandardsIgnoreFile
/**
 * Type Handling - By Reference
 *
 * Functions for typecasting and type detection. This extends
 * the cast_base class. For PHP7 users, additional functions
 * named after the types are added.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

// Unfortunately PHP < 7 prohibits the use of reserved words
// in method names, and we can't just use the Magic overloader
// like we did with our by-value version since Magic/Reference
// is a no-go.
if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
	class cast extends cast_base {
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
}
else {
	class cast extends cast_base {

	}
}

