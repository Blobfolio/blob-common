<?php
/**
 * Functions to assist common theme operations.
 *
 * @package blobfolio/common
 * @version 8.0.4
 *
 * @wordpress-plugin
 * Plugin Name: Tutan Common
 * Version: 8.0.4
 * Plugin URI: https://github.com/Blobfolio/blob-common
 * Description: Functions to assist common theme operations.
 * Author: Blobfolio, LLC
 * Author URI: https://blobfolio.com/
 * Info URI: https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/blob-common.json
 * Phar URI: https://raw.githubusercontent.com/Blobfolio/blob-common/master/bin/blob-common.json
 * Text Domain: blob-common
 * License: WTFPL
 * License URI: http://www.wtfpl.net/
 */

// This must be called through WordPress.
if (! \defined('ABSPATH')) {
	exit;
}

use blobfolio\wp\common\blobcommon;



// The root path to the plugin.
\define('BLOBCOMMON_PLUGIN_DIR', \dirname(__FILE__));
\define('BLOBCOMMON_INDEX', \BLOBCOMMON_PLUGIN_DIR . '/index.php');
\define('BLOBCOMMON_CHMOD_DIR', (@\fileperms(\ABSPATH) & 0777 | 0755));
\define('BLOBCOMMON_CHMOD_FILE', (@\fileperms(\ABSPATH . 'index.php') & 0777 | 0644));

// Phar path, in case this is somewhere else.
if (! \defined('BLOBCOMMON_PHAR_PATH')) {
	\define('BLOBCOMMON_PHAR_PATH', \BLOBCOMMON_PLUGIN_DIR . '/lib/blob-common.phar');
}

// Whether or not we're doing library updates.
if (! \defined('BLOBCOMMON_PHAR_UPDATES')) {
	\define('BLOBCOMMON_PHAR_UPDATES', false);
}

// Is this installed as a Must-Use plugin?
$blobcommon_must_use = (
	\defined('WPMU_PLUGIN_DIR') &&
	@\is_dir(\WPMU_PLUGIN_DIR) &&
	(0 === \strpos(\BLOBCOMMON_PLUGIN_DIR, \WPMU_PLUGIN_DIR))
);
\define('BLOBCOMMON_MUST_USE', $blobcommon_must_use);



/**
 * Compatibility Checks
 *
 * If any of these checks fail, the plugin cannot be run.
 *
 * @return bool True (OK) / false (bad).
 */
function blobcommon_compatibility() {
	$errors = array();

	// Really we require 7.0, but for this one release, we'll let the
	// plugin load with shitty PHP, we just won't allow updates.
	if (\version_compare(\PHP_VERSION, '7.2.0') < 0) {
		$errors[] = \__('PHP 7.2+ is required.', 'blob-common');
	}

	if (\function_exists('is_multisite') && \is_multisite()) {
		$errors[] = \__('This plugin is not compatible with WordPress multi-site.', 'blob-common');
	}

	if (! \class_exists('Phar')) {
		$errors[] = \__('The Phar PHP extension must be installed.', 'blob-common');
	}

	// We can check for Gzip by looking for a basic function.
	if (! \function_exists('gzopen')) {
		$errors[] = \__('Gzip support is required.', 'blob-common');
	}
	// But to know if it worked for sure, we'll need to try to open a
	// simple example archive.
	elseif (! \get_transient('blob-common_phar_works')) {
		try {
			@include \BLOBCOMMON_PLUGIN_DIR . '/lib/test.phar';
			if (! \function_exists('blobfolio_phar_test')) {
				$errors[] = \sprintf(
					\__('The server must support %s files.', 'blob-common'),
					'<code>phar.gz</code>'
				);
			}
			else {
				// Record the success to the transient table so we can
				// avoid repeating this particular check for a while.
				\set_transient('blob-common_phar_works', 1, \WEEK_IN_SECONDS);
			}
		} catch (Throwable $e) {
			$errors[] = \sprintf(
				\__('The server must support %s files.', 'blob-common'),
				'<code>phar.gz</code>'
			);
		} catch (Exception $e) {
			$errors[] = \sprintf(
				\__('The server must support %s files.', 'blob-common'),
				'<code>phar.gz</code>'
			);
		}
	}

	// Bail if we're okay.
	if (! \count($errors)) {
		return true;
	}

	// Force deactivation.
	if (! \BLOBCOMMON_MUST_USE) {
		$errors[] = \__('The plugin has been deactivated automatically.', 'blob-common');
		\add_action('admin_init', function() {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
			\deactivate_plugins(\BLOBCOMMON_INDEX);
		});
	}
	else {
		$errors[] = \__('Because the plugin was installed to the Must Use folder, it must be manually removed.');
	}

	// Explain what went wrong.
	\add_action('admin_notices', function() use($errors) {
		?>
		<div class="notice notice-error">
			<p>
				<strong><?=\__('Error', 'blob-common')?>:</strong>
				<?php echo \sprintf(
					\__('This server does not meet the requirements for running %s.', 'blob-common'),
					'<code>Tutan Common</code>'
				);
				?>
			</p>
			<?=\wpautop(\implode("\n\n", $errors))?>
		</div>
		<?php
	});

	return false;
}
if (! \blobcommon_compatibility()) {
	return;
}



// The blob-common library.
require \BLOBCOMMON_PLUGIN_DIR . '/lib/blobcommon.php';
blobcommon::init();

// And everything else.
require \BLOBCOMMON_PLUGIN_DIR . '/functions-behavior.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-debug.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-email.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-form.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-image.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-spacetime.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-sanitize.php';
require \BLOBCOMMON_PLUGIN_DIR . '/functions-tool.php';
