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
	// @param flatten
	// @return true/false
	public static function bool($value=false, bool $flatten=false) {
		ref\cast::bool($value, $flatten);
		return $value;
	}

	//-------------------------------------------------
	// Float
	//
	// @param value
	// @param flatten
	// @return true/false
	public static function float($value=0, bool $flatten=false) {
		ref\cast::float($value, $flatten);
		return $value;
	}

	//-------------------------------------------------
	// Int
	//
	// @param value
	// @param flatten
	// @return true/false
	public static function int($value=0, bool $flatten=false) {
		ref\cast::int($value, $flatten);
		return $value;
	}

	//-------------------------------------------------
	// Number
	//
	// @param value
	// @param flatten
	// @return value
	public static function number($value=0, bool $flatten=false) {
		ref\cast::number($value, $flatten);
		return $value;
	}

	//-------------------------------------------------
	// String
	//
	// @param value
	// @param flatten
	// @return value
	public static function string($value='', bool $flatten=false) {
		ref\cast::string($value, $flatten);
		return $value;
	}

	//-------------------------------------------------
	// Sanitize by Type
	//
	// @param value
	// @param type
	// @param flatten
	// @return value
	public static function to_type($value, string $type=null, bool $flatten=false) {
		ref\cast::to_type($value, $type, $flatten);
		return $value;
	}
}

?>