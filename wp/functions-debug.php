<?php
/**
 * Debugging Functions
 *
 * This file contains functions to assist developers with common
 * debugging tasks.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

use \blobfolio\common\sanitize as v_sanitize;
use \blobfolio\common\ref\sanitize as r_sanitize;

if (!function_exists('common_debug_mail')) {
	/**
	 * Debug Email
	 *
	 * Send a quick `print_r()` dump of a variable via
	 * email when direct output isn't possible.
	 *
	 * The default email address is the site admin. To
	 * override, set the following in wp-config:
	 * define('WP_DEBUG_EMAIL', 'me@domain.com');
	 *
	 * @param mixed $variable Variable.
	 * @param string $subject Subject.
	 * @param bool $mail Use mail() instead of wp_mail().
	 * @param bool $var_dump Use var_dump() instead of print_r().
	 * @return bool True.
	 */
	function common_debug_mail($variable, $subject=null, $mail=true, $var_dump=false) {
		$mto = defined('WP_DEBUG_EMAIL') ? v_sanitize::email(WP_DEBUG_EMAIL) : get_bloginfo('admin_email');
		$msub = v_sanitize::whitespace('[' . get_bloginfo('name') . '] ' . (is_null($subject) ? 'Debug' : $subject));

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

if (!function_exists('common_db_debug_log')) {
	/**
	 * Database Logging - Query Errors
	 *
	 * This logs invalid queries to db-debug.log if
	 * the WP_DB_DEBUG_LOG constant is set to true.
	 *
	 * Note: This is independent of WP_DEBUG.
	 *
	 * @return bool True.
	 */
	function common_db_debug_log() {
		global $EZSQL_ERROR;

		// Not needed?
		if (
			!defined('WP_DB_DEBUG_LOG') ||
			!WP_DB_DEBUG_LOG ||
			!is_array($EZSQL_ERROR) ||
			!count($EZSQL_ERROR)
		) {
			return true;
		}

		// Allow sites to filter the list of log-worthy errors.
		$errors = apply_filters('common_db_debug', $EZSQL_ERROR);
		if (!is_array($errors) || !count($errors)) {
			return true;
		}

		// Where we putting the errors?
		$log = trailingslashit(WP_CONTENT_DIR) . 'db-debug.log';

		// Headers.
		$headers = array(
			'IP'=>'REMOTE_ADDR',
			'UA'=>'HTTP_USER_AGENT',
			'SCRIPT'=>'SCRIPT_NAME',
			'REQUEST'=>'REQUEST_URI',
		);
		$xout = array(
			'DATE: ' . date('r', current_time('timestamp')),
			'SITE: ' . site_url(),
		);
		foreach ($headers as $k=>$v) {
			if (isset($_SERVER[$v])) {
				$xout[] = "$k: {$_SERVER[$v]}";
			}
		}

		$divider = str_repeat('-', 50);
		foreach ($errors as $e) {
			$xout[] = "$divider\n" . implode("\n", $e) . "\n$divider";
		}
		$xout[] = "\n\n\n\n";

		try {
			@file_put_contents($log, implode("\n", $xout), FILE_APPEND);
			if (!defined('FS_CHMOD_FILE')) {
				define('FS_CHMOD_FILE', (@fileperms(ABSPATH . 'index.php') & 0777 | 0644));
			}
			@chmod($log, FS_CHMOD_FILE);
		} catch (Throwable $e) {
			return true;
		} catch (Exception $e) {
			return true;
		}

		return true;
	}
	add_action('shutdown', 'common_db_debug_log');
}

if (!function_exists('common_debug_log_menu')) {
	/**
	 * Menu: debug.log Viewer
	 *
	 * By default this page requires manage_options
	 * capabilities. To change this, set the constant
	 * WP_DEBUG_LOG_CAP.
	 *
	 * @return void Nothing.
	 */
	function common_debug_log_menu() {
		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		add_submenu_page(
			'tools.php',
			'Debug Log',
			'Debug Log',
			$requires,
			'common-debug-log',
			'common_debug_log_page'
		);
	}
	add_action('admin_menu', 'common_debug_log_menu');
}

if (!function_exists('common_debug_log_page')) {
	/**
	 * Page: debug.log Viewer
	 *
	 * @return bool True.
	 */
	function common_debug_log_page() {
		require_once(dirname(__FILE__) . '/admin-debug-log.php');
		return true;
	}
}

if (!function_exists('_common_debug_log_highlight')) {
	/**
	 * Log Highlighting
	 *
	 * @param string $log Log contents.
	 * @return string Log contents.
	 */
	function _common_debug_log_highlight($log) {
		try {
			r_sanitize::whitespace($log, 1);
			$log = array_filter(explode("\n", $log), 'strlen');
		} catch (Throwable $e) {
			$log = array();
		} catch (Exception $e) {
			$log = array();
		}

		$num = 0;
		foreach ($log as $k=>$l) {
			$num++;

			r_sanitize::quotes($l);
			$l = esc_html($l);

			// Date.
			$l = preg_replace('/^(\[[^\]]+\])/', '<span class="log-date">$1</span>', $l);

			// Line number.
			$l = preg_replace('/(line \d+)/', '<span class="log-line">$1</span>', $l);

			// Path.
			$l = preg_replace('/(' . preg_quote(ABSPATH, '/') . '[^\s\:]+)(:\d+)?/', '<span class="log-path">$1</span><span class="log-line">$2</span>', $l);

			// Error type.
			$l = preg_replace('/(PHP [^:]+:)/', '<span class="log-type">$1</span>', $l);
			$l = str_replace('WordPress database error', '<span class="log-type">WordPress database error</span>', $l);

			// Wrap and finish.
			$classes = array('log-entry');
			if (
				(substr($l, 0, 1) === '#') ||
				(substr($l, 0, 12) === 'Stack trace:') ||
				(substr($l, 0, 10) === 'thrown in ')
			) {
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

if (!function_exists('common_ajax_debug_log')) {
	/**
	 * Load Log: debug.log Viewer
	 *
	 * @return void Nothing.
	 */
	function common_ajax_debug_log() {
		$xout = array('log'=>'', 'errors'=>array());

		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		if (!current_user_can($requires)) {
			$xout['errors'][] = 'You do not have sufficient permissions to access this page.';
		}

		$n = isset($_POST['n']) ? $_POST['n'] : 'n';
		if (!wp_verify_nonce($n, 'debug-log')) {
			$xout['errors'][] = 'This page has expired. Please reload and try again.';
		}

		if (!count($xout['errors'])) {
			$logpath = trailingslashit(WP_CONTENT_DIR) . 'debug.log';
			$log = file_exists($logpath) ? @file_get_contents($logpath) : '';

			$today = isset($_POST['today']) && (intval($_POST['today']) === 1) ? true : false;
			$tail = isset($_POST['tail']) ? intval($_POST['tail']) : 0;
			r_sanitize::to_range($tail, 0);

			try {
				r_sanitize::whitespace($log, 1);
				$log = array_filter(explode("\n", $log), 'strlen');
			} catch (Throwable $e) {
				$log = array();
			} catch (Exception $e) {
				$log = array();
			}

			// Match today?
			if ($today) {
				$date = preg_quote('[' . current_time('d-M-Y'), '/');
				foreach ($log as $k=>$l) {
					if (!preg_match("/$date/", $l)) {
						unset($log[$k]);
						continue;
					}
					// Not all log entries will be prefixed with a date, but
					// since they're linear we can assume anything appearing
					// after the first instance of today is fine.
					else {
						break;
					}
				}
			}

			// Tailing?
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

if (!function_exists('common_ajax_debug_log_delete')) {
	/**
	 * Delete Log: debug.log Viewer
	 *
	 * @return void Nothing.
	 */
	function common_ajax_debug_log_delete() {
		$xout = array('success'=>false, 'errors'=>array());

		$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
		if (!current_user_can($requires)) {
			$xout['errors'][] = 'You do not have sufficient permissions to access this page.';
		}

		$n = isset($_POST['n']) ? $_POST['n'] : 'n';
		if (!wp_verify_nonce($n, 'debug-log')) {
			$xout['errors'][] = 'This page has expired. Please reload and try again.';
		}

		if (!count($xout['errors'])) {
			$logpath = trailingslashit(WP_CONTENT_DIR) . 'debug.log';
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


