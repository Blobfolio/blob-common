<?php
//---------------------------------------------------------------------
// FUNCTIONS: DEBUGGING
//---------------------------------------------------------------------
// This file contains functions to help developers debug theme/plugin
// issues.

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//-------------------------------------------------
// Developer Debug Email
//
// ever need to quickly email yourself a print_r
// of a variable to see what happened?
//
// to send the result to someone other than the
// main site email, add the following to wp-config:
// define('WP_DEBUG_EMAIL', 'me@domain.com');
//
// @param variable
// @param subject
// @param use mail() instead of wp_mail()
// @param use var_dump instead of print_r
// @return true/false
if(!function_exists('common_debug_mail')){
	function common_debug_mail($variable, $subject=null, $mail=true, $var_dump=false){
		$mto = defined('WP_DEBUG_EMAIL') ? common_sanitize_email(WP_DEBUG_EMAIL) : get_bloginfo('admin_email');
		$msub = common_sanitize_whitespace('[' . get_bloginfo('name') . '] ' . (is_null($subject) ? 'Debug' : $subject));

		ob_start();
		if($var_dump)
			var_dump($variable);
		else
			print_r($variable);
		$mbody = ob_get_clean();

		try {
			if($mail)
				mail($mto, $msub, $mbody);
			else
				wp_mail($mto, $msub, $mbody);
		} catch(Exception $e){ return false; }

		return true;
	}
}

//-------------------------------------------------
// Database Logging - Query Errors
//
// this logs invalid queries to db-debug.log if
// the WP_DB_DEBUG_LOG constant is set to true.
// this can be used independently of WP_DEBUG
//
// @param n/a
// @return n/a
if(!function_exists('common_db_debug_log')){
	function common_db_debug_log(){
		//check for enablement here instead of prior to
		//enqueueing the action so we can catch very
		//early errors
		if(!defined('WP_DB_DEBUG_LOG') || !WP_DB_DEBUG_LOG)
			return;

		//WP already stores query errors in this obscure
		//global variable, so we can see what we've ended
		//up with just before shutdown
		global $EZSQL_ERROR;
		$log = ABSPATH . '/wp-content/db-debug.log';

		try {
			if(is_array($EZSQL_ERROR) && count($EZSQL_ERROR)){
				$xout = array();
				$xout[] = "DATE: " . date('r', current_time('timestamp'));
				$xout[] = "SITE: " . site_url();
				$xout[] = "IP: " . $_SERVER['REMOTE_ADDR'];
				$xout[] = "UA: " . $_SERVER['HTTP_USER_AGENT'];
				$xout[] = "SCRIPT: " . $_SERVER['SCRIPT_NAME'];
				$xout[] = "REQUEST: " . $_SERVER['REQUEST_URI'];
				foreach($EZSQL_ERROR AS $e)
					$xout[] = str_repeat('-', 50) . "\n" . implode("\n", $e) . "\n" . str_repeat('-', 50);
				$xout[] = "\n\n\n\n";

				@file_put_contents($log, implode("\n", $xout), FILE_APPEND);
			}
		} catch(Exception $e){ }

		return;
	}
	add_action('shutdown', 'common_db_debug_log');
}

?>