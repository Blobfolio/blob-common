<?php
//---------------------------------------------------------------------
// TYPECAST & TYPE HANDLING
//---------------------------------------------------------------------
// functions for typecasting and detecting type



namespace blobfolio\common;

class cast {

	//-------------------------------------------------
	// Array
	//
	// @param value
	// @return value
	public static function array($value=null) {
		ref\cast::array($value);
		return $value;
	}

	//-------------------------------------------------
	// Array Type
	//
	// this will return "associative" if there are any
	// string keys, "sequential" if the keys are
	// sequential numbers, "index" if all keys are
	// numeric, or false if empty or not an array
	//
	// @param array
	// @return sequential/indexed/associative/false
	public static function array_type(&$arr=null) {
		if (!is_array($arr) || !count($arr)) {
			return false;
		}

		$keys = array_keys($arr);
		if ($keys === range(0, count($keys) - 1)) {
			return 'sequential';
		}
		elseif (count($keys) === count(array_filter($keys, 'is_numeric'))) {
			return 'indexed';
		}
		else {
			return 'associative';
		}
	}

	//-------------------------------------------------
	// Bool
	//
	// @param value
	// @return true/false
	public static function bool($value=false) {
		ref\cast::bool($value);
		return $value;
	}

	//-------------------------------------------------
	// Float
	//
	// @param value
	// @return true/false
	public static function float($value=0) {
		ref\cast::float($value);
		return $value;
	}

	//-------------------------------------------------
	// Int
	//
	// @param value
	// @return true/false
	public static function int($value=0) {
		ref\cast::int($value);
		return $value;
	}

	//-------------------------------------------------
	// Number
	//
	// @param value
	// @return value
	public static function number($value=0) {
		ref\cast::number($value);
		return $value;
	}

	//-------------------------------------------------
	// String
	//
	// @param value
	// @return value
	public static function string($value='') {
		ref\cast::string($value);
		return $value;
	}

	//-------------------------------------------------
	// Sanitize by Type
	//
	// @param value
	// @param type
	// @return value
	public static function to_type($value, string $type=null) {
		ref\cast::to_type($value, $type);
		return $value;
	}
}

?>