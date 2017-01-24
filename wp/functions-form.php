<?php
//---------------------------------------------------------------------
// FUNCTIONS: FORMS
//---------------------------------------------------------------------
// This file includes functions related to web forms.

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



//-------------------------------------------------
// Generate Form Timestamp
//
// this field can be used to prevent overly rapid
// form submissions by robots
//
// @param n/a
// @return hash
if (!function_exists('common_get_form_timestamp')) {
	function common_get_form_timestamp() {
		$time = time();
		return "$time," . md5($time . NONCE_KEY);
	}
}
//alias
if (!function_exists('common_generate_form_timestamp')) {
	function common_generate_form_timestamp() {
		return common_get_form_timestamp();
	}
}

//-------------------------------------------------
// Validate Form Timestamp
//
// @param hash
// @param time elapsed (must be >= this value)
// @return true/false
if (!function_exists('common_check_form_timestamp')) {
	function common_check_form_timestamp($hash='', $elapsed=5) {
		if (!preg_match('/^\d+,([\da-f]{32})$/i', $hash)) {
			return false;
		}
		list($t,$h) = explode(',', $hash);
		return ($h === md5($t . NONCE_KEY) && time() - $t >= $elapsed);
	}
}
//alias
if (!function_exists('common_check_form_timestamp')) {
	function common_verify_form_timestamp($hash='', $elapsed=5) {
		return common_check_form_timestamp($hash, $elapsed);
	}
}
?>