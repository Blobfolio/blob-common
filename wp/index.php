<?php
/**
 * Functions to assist common theme operations.
 *
 * @package blobfolio/common
 * @version 7.3.4
 *
 * @wordpress-plugin
 * Plugin Name: Tutan Common
 * Version: 7.3.4
 * Plugin URI: https://github.com/Blobfolio/blob-common
 * Description: Functions to assist common theme operations.
 * Author: Blobfolio, LLC
 * Author URI: https://blobfolio.com/
 * Info URI: https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/wp.json
 * Text Domain: blob-common
 * License: WTFPL
 * License URI: http://www.wtfpl.net/
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}



// The root path to the plugin.
define('BLOBCOMMON_ROOT', dirname(__FILE__));
define('BLOBCOMMON_INDEX', BLOBCOMMON_ROOT . '/index.php');
define('BLOBCOMMON_CHMOD_DIR', (@fileperms(ABSPATH) & 0777 | 0755));
define('BLOBCOMMON_CHMOD_FILE', (@fileperms(ABSPATH . 'index.php') & 0777 | 0644));

// Is this installed as a Must-Use plugin?
$blobcommon_must_use = (
	defined('WPMU_PLUGIN_DIR') &&
	@is_dir(WPMU_PLUGIN_DIR) &&
	(0 === strpos(BLOBCOMMON_ROOT, WPMU_PLUGIN_DIR))
);
define('BLOBCOMMON_MUST_USE', $blobcommon_must_use);



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
		include_once(BLOBCOMMON_ROOT . '/lib/test.phar');
	} catch (Throwable $e) {
		throw new Exception('PHAR/Gzip support is required.');
	} catch (Exception $e) {
		throw new Exception('PHAR/Gzip support is required.');
	}

	if (!function_exists('blobfolio_phar_test')) {
		throw new Exception('PHAR/Gzip support is required.');
	}

	return true;
}
register_activation_hook(__FILE__, 'blobcommon_activation_requirements');



// Bail now if requirements aren't met.
try {
	blobcommon_activation_requirements();
} catch (Throwable $e) {
	return;
} catch (Exception $e) {
	return;
}



// The blob-common library.
require(BLOBCOMMON_ROOT . '/lib/blobcommon.php');
\blobfolio\wp\common\blobcommon::init();

// And everything else.
require(BLOBCOMMON_ROOT . '/functions-behavior.php');
require(BLOBCOMMON_ROOT . '/functions-debug.php');
require(BLOBCOMMON_ROOT . '/functions-email.php');
require(BLOBCOMMON_ROOT . '/functions-form.php');
require(BLOBCOMMON_ROOT . '/functions-image.php');
require(BLOBCOMMON_ROOT . '/functions-spacetime.php');
require(BLOBCOMMON_ROOT . '/functions-sanitize.php');
require(BLOBCOMMON_ROOT . '/functions-tool.php');
