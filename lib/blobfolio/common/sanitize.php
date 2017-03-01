<?php
/**
 * Sanitizing.
 *
 * Functions for sanitizing data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class sanitize {

	/**
	 * Strip Accents
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function accents($str) {
		ref\sanitize::accents($str);
		return $str;
	}

	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return string|bool Card number or false.
	 */
	public static function cc($ccnum='') {
		ref\sanitize::cc($ccnum);
		return $ccnum;
	}

	/**
	 * Country
	 *
	 * @param string $str Country.
	 * @return string ISO country code.
	 */
	public static function country($str='') {
		ref\sanitize::country($str);
		return $str;
	}

	/**
	 * CSV Cell Data
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function csv($str='') {
		ref\sanitize::csv($str);
		return $str;
	}

	/**
	 * Datetime
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function datetime($str='') {
		ref\sanitize::datetime($str);
		return $str;
	}

	/**
	 * Date
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function date($str='') {
		ref\sanitize::date($str);
		return $str;
	}

	/**
	 * Domain Name.
	 *
	 * This locates the domain name portion of a URL,
	 * removes leading "www." subdomains, and ignores
	 * IP addresses.
	 *
	 * @param string $str Domain.
	 * @return string Domain.
	 */
	public static function domain($str='') {
		ref\sanitize::domain($str);
		return $str;
	}

	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @return string Email.
	 */
	public static function email($str=null) {
		ref\sanitize::email($str);
		return $str;
	}

	/**
	 * File Extension
	 *
	 * @param string $str Extension.
	 * @return string Extension.
	 */
	public static function file_extension($str='') {
		ref\sanitize::file_extension($str);
		return $str;
	}

	/**
	 * HTML
	 *
	 * @param string $str HTML.
	 * @return string HTML.
	 */
	public static function html($str=null) {
		ref\sanitize::html($str);
		return $str;
	}

	/**
	 * Hostname
	 *
	 * @param string $domain Hostname.
	 * @param bool $www Keep leading www.
	 * @return string|bool Hostname or false.
	 */
	public static function hostname(string $domain, bool $www=false) {
		ref\sanitize::hostname($domain, $www);
		return $domain;
	}

	/**
	 * IP Address
	 *
	 * @param string $str IP.
	 * @param bool $restricted Allow private/restricted values.
	 * @return string IP.
	 */
	public static function ip($str='', bool $restricted=false) {
		ref\sanitize::ip($str, $restricted);
		return $str;
	}

	/**
	 * JS Variable
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @return string String.
	 */
	public static function js($str='', $quote="'") {
		ref\sanitize::js($str, $quote);
		return $str;
	}

	/**
	 * IANA MIME Type
	 *
	 * @param string $str MIME.
	 * @return string MIME.
	 */
	public static function mime($str='') {
		ref\sanitize::mime($str);
		return $str;
	}

	/**
	 * (Person's) Name
	 *
	 * A bit of a fool's goal, but this will attempt to
	 * strip out obviously bad data and convert to title
	 * casing.
	 *
	 * @param string $str Name.
	 * @return string Name.
	 */
	public static function name($str='') {
		ref\sanitize::name($str);
		return $str;
	}

	/**
	 * Password
	 *
	 * This simply removes excess whitespace and
	 * non-printable characters, which are likely
	 * only present because of user error.
	 *
	 * @param string $str Password.
	 * @return string Password.
	 */
	public static function password($str='') {
		ref\sanitize::password($str);
		return $str;
	}

	/**
	 * Printable
	 *
	 * Remove non-printable characters (except spaces).
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function printable($str='') {
		ref\sanitize::printable($str);
		return $str;
	}

	/**
	 * Canadian Province
	 *
	 * @param string $str Province.
	 * @return string Province.
	 */
	public static function province($str='') {
		ref\sanitize::province($str);
		return $str;
	}

	/**
	 * Quotes
	 *
	 * Replace those damn curly quotes with the straight
	 * ones Athena intended!
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function quotes($str='') {
		ref\sanitize::quotes($str);
		return $str;
	}

	/**
	 * US State/Territory
	 *
	 * @param string $str State.
	 * @return string State.
	 */
	public static function state($str='') {
		ref\sanitize::state($str);
		return $str;
	}

	/**
	 * SVG
	 *
	 * @param string $str SVG code.
	 * @param array $tags Additional whitelist tags.
	 * @param array $attr Additional whitelist attributes.
	 * @return string SVG code.
	 */
	public static function svg($str='', $tags=null, $attr=null) {
		ref\sanitize::svg($str, $tags, $attr);
		return $str;
	}

	/**
	 * Timezone
	 *
	 * @param string $str Timezone.
	 * @return string Timezone or UTC on failure.
	 */
	public static function timezone($str='') {
		ref\sanitize::timezone($str);
		return $str;
	}

	/**
	 * Confine a Value to a Range
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Value.
	 */
	public static function to_range($value, $min=null, $max=null) {
		ref\sanitize::to_range($value, $min, $max);
		return $value;
	}

	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $str URL.
	 * @return string URL.
	 */
	public static function url($str='') {
		ref\sanitize::url($str);
		return $str;
	}

	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function utf8($str='') {
		ref\sanitize::utf8($str);
		return $str;
	}

	/**
	 * Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space, and constrict consecutive newlines.
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace($str='', int $newlines=0) {
		ref\sanitize::whitespace($str, $newlines);
		return $str;
	}

	/**
	 * Whitespace Multiline
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace_multiline($str='', int $newlines=1) {
		ref\sanitize::whitespace_multiline($str, $newlines);
		return $str;
	}

	/**
	 * US ZIP5
	 *
	 * @param string $str ZIP Code.
	 * @return string ZIP Code.
	 */
	public static function zip5($str='') {
		ref\sanitize::zip5($str);
		return $str;
	}
}


