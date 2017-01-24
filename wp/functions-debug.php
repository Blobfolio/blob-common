<?php
//---------------------------------------------------------------------
// FUNCTIONS: DEBUGGING
//---------------------------------------------------------------------
// This file contains functions to help developers debug theme/plugin
// issues.

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



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
if (!function_exists('common_debug_mail')) {
	function common_debug_mail($variable, $subject=null, $mail=true, $var_dump=false) {
		$mto = defined('WP_DEBUG_EMAIL') ? common_sanitize_email(WP_DEBUG_EMAIL) : get_bloginfo('admin_email');
		$msub = common_sanitize_whitespace('[' . get_bloginfo('name') . '] ' . (is_null($subject) ? 'Debug' : $subject));

		ob_start();
		if ($var_dump) {
			var_dump($variable);
		}
		else {
			print_r($variable);
		}
		$mbody = ob_get_clean();

		try {
			if ($mail) {
				mail($mto, $msub, $mbody);
			}
			else {
				wp_mail($mto, $msub, $mbody);
			}
		} catch (Exception $e) {
			return false;
		}

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
if (!function_exists('common_db_debug_log')) {
	function common_db_debug_log() {
		//check for enablement here instead of prior to
		//enqueueing the action so we can catch very
		//early errors
		if (!defined('WP_DB_DEBUG_LOG') || !WP_DB_DEBUG_LOG) {
			return;
		}

		//WP already stores query errors in this obscure
		//global variable, so we can see what we've ended
		//up with just before shutdown
		global $EZSQL_ERROR;
		$log = ABSPATH . '/wp-content/db-debug.log';

		try {
			if (is_array($EZSQL_ERROR) && count($EZSQL_ERROR)) {
				$xout = array();
				$xout[] = 'DATE: ' . date('r', current_time('timestamp'));
				$xout[] = 'SITE: ' . site_url();
				$xout[] = 'IP: ' . $_SERVER['REMOTE_ADDR'];
				$xout[] = 'UA: ' . $_SERVER['HTTP_USER_AGENT'];
				$xout[] = 'SCRIPT: ' . $_SERVER['SCRIPT_NAME'];
				$xout[] = 'REQUEST: ' . $_SERVER['REQUEST_URI'];
				foreach ($EZSQL_ERROR as $e) {
					$xout[] = str_repeat('-', 50) . "\n" . implode("\n", $e) . "\n" . str_repeat('-', 50);
				}
				$xout[] = "\n\n\n\n";

				@file_put_contents($log, implode("\n", $xout), FILE_APPEND);
			}
		} catch (Exception $e) {
			return;
		}

		return;
	}
	add_action('shutdown', 'common_db_debug_log');
}

//-------------------------------------------------
// debug.log viewer (menu entry)
//
// this allows developers to view the debug.log
// contents in cases where a server rule forbids
// access (which is generally recommended)
//
// by default this requires "manage_options"
// privilege; to change this set the constant
// WP_DEBUG_LOG_CAP
//
// @param n/a
// @return n/a
if (!function_exists('common_debug_log_menu')) {
	function common_debug_log_menu() {
		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		add_submenu_page('tools.php', 'Debug Log', 'Debug Log', $requires, 'common-debug-log', 'common_debug_log_page');
	}
	add_action('admin_menu', 'common_debug_log_menu');
}

//-------------------------------------------------
// debug.log viewer (page)
//
// @param n/a
// @return page
if (!function_exists('common_debug_log_page')) {
	function common_debug_log_page() {
		require_once(dirname(__FILE__) . '/admin-debug-log.php');
		return true;
	}
}

//-------------------------------------------------
// debug.log formatting
//
// this wraps log keywords in <span>s so they can
// be color-coded
//
// @param log
// @return log
if (!function_exists('_common_debug_log_highlight')) {
	function _common_debug_log_highlight($log) {
		try {
			$log = common_sanitize_newlines($log, 1);
			$log = array_filter(explode("\n", $log), 'strlen');
		} catch (Throwable $e) {
			$log = array();
		} catch (Exception $e) {
			$log = array();
		}

		$num = 0;
		foreach ($log as $k=>$l) {
			$num++;

			$l = common_sanitize_quotes($l);
			$l = esc_html($l);

			//date
			$l = preg_replace('/^(\[[^\]]+\])/', '<span class="log-date">$1</span>', $l);

			//line number
			$l = preg_replace('/(line \d+)/', '<span class="log-line">$1</span>', $l);

			//path
			$l = preg_replace('/(' . preg_quote(ABSPATH, '/') . '[^\s\:]+)(:\d+)?/', '<span class="log-path">$1</span><span class="log-line">$2</span>', $l);

			//error type
			$l = preg_replace('/(PHP [^:]+:)/', '<span class="log-type">$1</span>', $l);
			$l = str_replace('WordPress database error', '<span class="log-type">WordPress database error</span>', $l);

			//wrap and finish
			$classes = array('log-entry');
			if (substr($l, 0, 1) === '#' || substr($l, 0, 12) === 'Stack trace:' || substr($l, 0, 10) === 'thrown in ') {
				$classes[] = 'log-comment';
			}
			$l = '<div class="' . implode(' ', $classes) . '" data-number="' . $num . '">' . $l . '</div>';

			$log[$k] = $l;
		}

		if (!count($log)) {
			$log[] = '<div class="log-entry log-happy" data-number="1">Nothing to report! ☻</div>';
		}

		return implode('', $log);
	}
	add_filter('common_debug_log_highlight', '_common_debug_log_highlight');
}

//-------------------------------------------------
// AJAX: debug.log (load)
//
// @param n/a
// @return JSON
if (!function_exists('common_ajax_debug_log')) {
	function common_ajax_debug_log() {
		$xout = array('log'=>'', 'errors'=>array());

		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		if (!current_user_can($requires)) {
			$xout['errors'][] = 'You do not have sufficient permissions to access this page.';
		}

		$n = isset($_POST['n']) ? $_POST['n'] : 'n';
		if (!wp_verify_nonce($_POST['n'], 'debug-log')) {
			$xout['errors'][] = 'This page has expired. Please reload and try again.';
		}

		if (!count($xout['errors'])) {
			$logpath = trailingslashit(ABSPATH) . 'wp-content/debug.log';
			$log = file_exists($logpath) ? @file_get_contents($logpath) : '';

			$today = isset($_POST['today']) && intval($_POST['today']) === 1 ? true : false;
			$tail = isset($_POST['tail']) ? intval($_POST['tail']) : 0;
			$tail = common_to_range($tail, 0);

			try {
				$log = common_sanitize_newlines($log, 1);
				$log = array_filter(explode("\n", $log), 'strlen');
			} catch (Throwable $e) {
				$log = array();
			} catch (Exception $e) {
				$log = array();
			}

			//match today?
			if ($today) {
				$date = preg_quote('[' . current_time('d-M-Y'), '/');
				foreach ($log as $k=>$l) {
					if (!preg_match("/$date/", $l)) {
						unset($log[$k]);
						continue;
					}
					//not all log entries will be prefixed with a date, but
					//since they're linear we can assume anything appearing
					//after the first instance of today is fine
					else {
						break;
					}
				}
			}

			//tailing?
			if ($tail > 0 && count($log) > $tail) {
				$log = array_splice($log, 0 - $tail);
			}

			$xout['log'] = apply_filters('common_debug_log_highlight', implode("\n", $log));
		}

		wp_send_json($xout);
		exit;
	}
	add_action('wp_ajax_common_ajax_debug_log', 'common_ajax_debug_log');
}

//-------------------------------------------------
// AJAX: debug.log (load)
//
// @param n/a
// @return JSON
if (!function_exists('common_ajax_debug_log_delete')) {
	function common_ajax_debug_log_delete() {
		$xout = array('success'=>false, 'errors'=>array());

		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		if (!current_user_can($requires)) {
			$xout['errors'][] = 'You do not have sufficient permissions to access this page.';
		}

		$n = isset($_POST['n']) ? $_POST['n'] : 'n';
		if (!wp_verify_nonce($_POST['n'], 'debug-log')) {
			$xout['errors'][] = 'This page has expired. Please reload and try again.';
		}

		if (!count($xout['errors'])) {
			$logpath = trailingslashit(ABSPATH) . 'wp-content/debug.log';
			if (file_exists($logpath)) {
				if (false === @unlink($logpath)) {
					$xout['errors'][] = 'The log file could not be deleted.';
				}
				else {
					$xout['success'] = true;
				}
			}
		}

		wp_send_json($xout);
		exit;
	}
	add_action('wp_ajax_common_ajax_debug_log_delete', 'common_ajax_debug_log_delete');
}

?>