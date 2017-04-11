<?php
/**
 * Functions to assist common theme operations.
 *
 * @package blobfolio/common
 * @version 7.1.7
 *
 * @wordpress-plugin
 * Plugin Name: Tutan Common
 * Plugin URI: https://github.com/Blobfolio/blob-common
 * Description: Functions to assist common theme operations.
 * Author: Blobfolio, LLC
 * Author URI: https://blobfolio.com/
 * Version: 7.1.7
 * License: WTFPL
 * License URI: http://www.wtfpl.net/
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}



// The root path to the plugin.
define('BLOB_COMMON_ROOT', dirname(__FILE__));
@require_once(BLOB_COMMON_ROOT . '/lib/vendor/autoload.php');	// Autoload.
@require_once(BLOB_COMMON_ROOT . '/functions-behavior.php');
@require_once(BLOB_COMMON_ROOT . '/functions-debug.php');
@require_once(BLOB_COMMON_ROOT . '/functions-email.php');
@require_once(BLOB_COMMON_ROOT . '/functions-form.php');
@require_once(BLOB_COMMON_ROOT . '/functions-image.php');
@require_once(BLOB_COMMON_ROOT . '/functions-spacetime.php');
@require_once(BLOB_COMMON_ROOT . '/functions-sanitize.php');
@require_once(BLOB_COMMON_ROOT . '/functions-tool.php');




// ---------------------------------------------------------------------
// SELF AWARENESS/UPDATES
// ---------------------------------------------------------------------
// The plugin is not hosted in the WP plugin repository, so we need
// some helpers to help it help itself. :)
//
// To reduce overhead, update checks are throttled to once per hour.

/**
 * Activation Checks
 *
 * @return bool True/false.
 * @throws Exception Missing requirements.
 */
function blobcommon_activation_requirements() {
	if (version_compare(PHP_VERSION, '5.6.0') < 0) {
		throw new Exception('PHP 5.6.0 or newer is required.');
	}

	if (function_exists('is_multisite') && is_multisite()) {
		throw new Exception('This plugin cannot be used on Multi-Site.');
	}

	try {
		include_once(BLOB_COMMON_ROOT . '/lib/test.phar');
	} catch(Throwable $e){
		throw new Exception('PHAR/Gzip support is required.');
	} catch(Exception $e) {
		throw new Exception('PHAR/Gzip support is required.');
	}

	if(!function_exists('blobfolio_phar_test')){
		throw new Exception('PHAR/Gzip support is required.');
	}

	return true;
}
register_activation_hook(__FILE__, 'blobcommon_activation_requirements');

/**
 * Get Plugin Info (Local)
 *
 * @param string $key Key.
 * @return mixed Info.
 */
function blobcommon_get_info($key = null) {
	static $info;

	if (is_null($info)) {
		require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');
		$info = get_plugin_data(__FILE__);
	}

	// Return what corresponds to $key.
	if (!is_null($key)) {
		return array_key_exists($key, $info) ? $info[$key] : false;
	}

	// Return the whole thing.
	return $info;
}

/**
 * Get Remote Branch
 *
 * Updates are no longer provided for old versions.
 *
 * @return string Branch URL.
 */
function blobcommon_get_release_branch() {
	try {
		blobcommon_activation_requirements();
	} catch(Throwable $e) {
		return false;
	} catch(Exception $e) {
		return false;
	}

	return 'https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/wp.json';
}

/**
 * Get Plugin Info (Remote)
 *
 * @param string $key Key.
 * @return mixed Info.
 */
function blobcommon_get_remote_info($key = null) {
	static $info;
	$transient_key = 'blobcommon_remote_info';

	if (is_null($info) && false === $info = get_transient($transient_key)) {
		$info = array();
		if(false === ($branch = blobcommon_get_release_branch())){
			$info = array();
		}
		else {
			$data = wp_remote_get($branch);
			if (is_array($data) && array_key_exists('body', $data)) {
				try {
					$response = json_decode($data['body'], true);
					if (is_array($response)) {
						foreach ($response as $k=>$v) {
							$info[$k] = $v;
						}

						set_transient($transient_key, $info, 3600);
					}
				} catch (Exception $e) {
					$info = array();
				}
			}
		}
	}

	// Return what corresponds to $key.
	if (!is_null($key)) {
		return array_key_exists($key, $info) ? $info[$key] : false;
	}

	// Return the whole thing.
	return $info;
}

/**
 * Get Installed Version
 *
 * @return string Version.
 */
function blobcommon_get_installed_version() {
	static $version;

	if (is_null($version)) {
		$version = (string) trim(blobcommon_get_info('Version'));
	}

	return $version;
}

/**
 * Get Latest Version
 *
 * @return string Version.
 */
function blobcommon_get_latest_version() {
	static $version;

	if (is_null($version)) {
		$version = (string) trim(blobcommon_get_remote_info('Version'));
	}

	return $version;
}

/**
 * Check for Updates
 *
 * @param mixed $option Option.
 * @return mixed Option.
 */
function blobcommon_check_update($option) {

	// Make sure arguments make sense.
	if (!is_object($option)) {
		return $option;
	}

	// Local and remote versions.
	$installed = blobcommon_get_installed_version();
	$remote = blobcommon_get_latest_version();

	// Bad data and/or match, nothing to do!
	if (false === $remote || false === $installed || $remote <= $installed) {
		return $option;
	}

	// Set up the entry.
	$path = 'blob-common/index.php';
	if (!array_key_exists($path, $option->response)) {
		$option->response[$path] = new stdClass();
	}

	$option->response[$path]->url = blobcommon_get_info('PluginURI');
	$option->response[$path]->slug = 'blob-common';
	$option->response[$path]->plugin = $path;
	$option->response[$path]->package = blobcommon_get_remote_info('DownloadURI');
	$option->response[$path]->new_version = $remote;
	$option->response[$path]->id = 0;

	// Done.
	return $option;
}
add_filter('transient_update_plugins', 'blobcommon_check_update');
add_filter('site_transient_update_plugins', 'blobcommon_check_update');

// --------------------------------------------------------------------- end updates


