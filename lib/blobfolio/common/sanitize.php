<?php
//---------------------------------------------------------------------
// SANITIZE
//---------------------------------------------------------------------
// sanitize data



namespace blobfolio\common;

class sanitize {

	//-------------------------------------------------
	// Strip Accents
	//
	// @param str
	// @return str
	public static function accents($str) {
		ref\sanitize::accents($str);
		return $str;
	}

	//-------------------------------------------------
	// Credit Card
	//
	// @param str
	// @return str
	public static function cc($ccnum='') {
		ref\sanitize::cc($ccnum);
		return $ccnum;
	}

	//-------------------------------------------------
	// Sanitize Country
	//
	// @param str
	// @return str
	public static function country($str='') {
		ref\sanitize::country($str);
		return $str;
	}

	//-------------------------------------------------
	// Sanitize CSV
	//
	// @param str
	// @return str
	public static function csv($str='') {
		ref\sanitize::csv($str);
		return $str;
	}

	//-------------------------------------------------
	// Datetime
	//
	// @param date
	// @return date
	public static function datetime($str='') {
		ref\sanitize::datetime($str);
		return $str;
	}

	//-------------------------------------------------
	// Date
	//
	// @param str
	// @return str
	public static function date($str='') {
		ref\sanitize::date($str);
		return $str;
	}

	//-------------------------------------------------
	// Domain Name
	//
	// @param str
	// @return str
	public static function domain($str='') {
		ref\sanitize::domain($str);
		return $str;
	}

	//-------------------------------------------------
	// Email
	//
	// @param str
	// @return str
	public static function email($str=null) {
		ref\sanitize::email($str);
		return $str;
	}

	//-------------------------------------------------
	// Sanitize File Extension
	//
	// @param str
	// @return str
	public static function file_extension($str='') {
		ref\sanitize::file_extension($str);
		return $str;
	}

	//-------------------------------------------------
	// HTML
	//
	// @param str
	// @return str
	public static function html($str=null) {
		ref\sanitize::html($str);
		return $str;
	}

	//-------------------------------------------------
	// Get Domain Host
	//
	// @param domain
	// @param keep www
	// @return host or false
	public static function hostname(string $domain, bool $www=false) {
		ref\sanitize::hostname($domain, $www);
		return $domain;
	}

	//-------------------------------------------------
	// IP
	//
	// @param str
	// @param allow restricted range?
	// @return str
	public static function ip($str='', bool $restricted=false) {
		ref\sanitize::ip($str, $restricted);
		return $str;
	}

	//-------------------------------------------------
	// JS Variable
	//
	// @param str
	// @param quote type
	// @return str
	public static function js($str='', $quote="'") {
		ref\sanitize::js($str, $quote);
		return $str;
	}

	//-------------------------------------------------
	// Sanitize MIME type
	//
	// @param str
	// @return str
	public static function mime($str='') {
		ref\sanitize::mime($str);
		return $str;
	}

	//-------------------------------------------------
	// Sanitize name (like a person's name)
	//
	// @param name
	// @return name
	public static function name($str='') {
		ref\sanitize::name($str);
		return $str;
	}

	//-------------------------------------------------
	// Password
	//
	// @param str
	// @return str
	public static function password($str='') {
		ref\sanitize::password($str);
		return $str;
	}

	//-------------------------------------------------
	// Printable
	//
	// @param str
	// @return str
	public static function printable($str='') {
		ref\sanitize::printable($str);
		return $str;
	}

	//-------------------------------------------------
	// Canadian Province
	//
	// @param str
	// @return str
	public static function province($str='') {
		ref\sanitize::province($str);
		return $str;
	}

	//-------------------------------------------------------
	// Quotes
	//
	// @param str
	// @return str
	public static function quotes($str='') {
		ref\sanitize::quotes($str);
		return $str;
	}

	//-------------------------------------------------
	// State
	//
	// @param str
	// @return str
	public static function state($str='') {
		ref\sanitize::state($str);
		return $str;
	}

	//-------------------------------------------------
	// SVG
	//
	// @param str
	// @param whitelisted tags
	// @param whitelisted attributes
	// @return str
	public static function svg($str='', $tags=null, $attr=null) {
		ref\sanitize::svg($str, $tags, $attr);
		return $str;
	}

	//-------------------------------------------------
	// Timezone
	//
	// @param timezone
	// @return timezone
	public static function timezone($str='') {
		ref\sanitize::timezone($str);
		return $str;
	}

	//-------------------------------------------------
	// Force a value to fall within a range
	//
	// @param value
	// @param min
	// @param max
	// @return value
	public static function to_range($value, $min=null, $max=null) {
		ref\sanitize::to_range($value, $min, $max);
		return $value;
	}

	//-------------------------------------------------
	// URL
	//
	// @param str
	// @return str
	public static function url($str='') {
		ref\sanitize::url($str);
		return $str;
	}

	//-------------------------------------------------
	// UTF-8
	//
	// @param str
	// @return str
	public static function utf8($str='') {
		ref\sanitize::utf8($str);
		return $str;
	}

	//-------------------------------------------------------
	// Whitespace
	//
	// @param str
	// @param number consecutive linebreaks allowed
	// @return str
	public static function whitespace($str='', int $newlines=0) {
		ref\sanitize::whitespace($str, $newlines);
		return $str;
	}

	//-------------------------------------------------------
	// Whitespace (allow new lines)
	//
	// @param str
	// @param consecutive linebreaks allowed
	// @return str
	public static function whitespace_multiline($str='', int $newlines=1) {
		ref\sanitize::whitespace_multiline($str, $newlines);
		return $str;
	}

	//-------------------------------------------------
	// Zip5
	//
	// @param str
	// @return str
	public static function zip5($str='') {
		ref\sanitize::zip5($str);
		return $str;
	}
}

?>