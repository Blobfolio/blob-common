<?php
/**
 * Form Functions
 *
 * This file contains functions to assist developers with
 * HTML forms.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (! \defined('ABSPATH')) {
	exit;
}



// -------------------------------------------------
// Generate Form Timestamp
//
// this field can be used to prevent overly rapid
// form submissions by robots
//
// @param n/a
// @return hash
if (! \function_exists('common_get_form_timestamp')) {
	/**
	 * Generate Form Timestamp
	 *
	 * Like a nonce, but measuring time-to-submission.
	 *
	 * @return string Hash.
	 */
	function common_get_form_timestamp() {
		$salt = \defined('NONCE_KEY') ? \NONCE_KEY : 'no_nonce_' . \site_url();
		$time = \time();
		return "$time," . \md5($time . $salt);
	}
}
// Alias.
if (! \function_exists('common_generate_form_timestamp')) {
	/**
	 * Generate Form Timestamp
	 *
	 * Like a nonce, but measuring time-to-submission.
	 *
	 * @return string Hash.
	 */
	function common_generate_form_timestamp() {
		return \common_get_form_timestamp();
	}
}

// -------------------------------------------------
// Validate Form Timestamp
//
// @param hash
// @param time elapsed (must be >= this value)
// @return true/false
if (! \function_exists('common_check_form_timestamp')) {
	/**
	 * Validate Form Timestamp
	 *
	 * @param string $hash Hash.
	 * @param int $elapsed Minimum seconds allowed.
	 * @return bool True/false.
	 */
	function common_check_form_timestamp($hash='', $elapsed=5) {
		$salt = \defined('NONCE_KEY') ? \NONCE_KEY : 'no_nonce_' . \site_url();
		if (! \preg_match('/^\d+,([\da-f]{32})$/i', $hash)) {
			return false;
		}
		list($t,$h) = \explode(',', $hash);
		return ((\md5($t . $salt) === $h) && (\time() - $t >= $elapsed));
	}
}
// Alias.
if (! \function_exists('common_verify_form_timestamp')) {
	/**
	 * Validate Form Timestamp
	 *
	 * @param string $hash Hash.
	 * @param int $elapsed Minimum seconds allowed.
	 * @return bool True/false.
	 */
	function common_verify_form_timestamp($hash='', $elapsed=5) {
		return \common_check_form_timestamp($hash, $elapsed);
	}
}

