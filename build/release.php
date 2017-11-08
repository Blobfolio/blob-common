<?php
/**
 * Package WordPress Plugin
 *
 * We want to get rid of source files and whatnot, and since they're
 * kinda all over the place, it is better to let a robot handle it.
 *
 * Dirty, dirty work.
 *
 * @package blobfolio/common
 * @author  Blobfolio, LLC <hello@blobfolio.com>
 */

define('BUILD_DIR', dirname(__FILE__) . '/');
define('BIN_DIR', dirname(BUILD_DIR) . '/bin/');
define('PLUGIN_BASE', dirname(BUILD_DIR) . '/wp/');
define('RELEASE_BASE', dirname(BUILD_DIR) . '/blob-common/');



echo "\n";
echo "+ Copying the source.\n";

// Delete the release base if it already exists.
if (file_exists(RELEASE_BASE)) {
	// @codingStandardsIgnoreStart
	shell_exec('rm -rf ' . escapeshellarg(RELEASE_BASE));
	// @codingStandardsIgnoreEnd
}

// Rebuild the test phar.
// @codingStandardsIgnoreStart
shell_exec('php -d phar.readonly=0 ' . escapeshellarg(BUILD_DIR . 'skel/build-test.php'));
// @codingStandardsIgnoreEnd

// Copy the latest bin.
// @codingStandardsIgnoreStart
shell_exec('cp -a ' . escapeshellarg(BIN_DIR . 'blob-common.phar') . ' ' . escapeshellarg(PLUGIN_BASE . 'lib/blob-common.phar'));
// @codingStandardsIgnoreEnd

// Copy the trunk.
// @codingStandardsIgnoreStart
shell_exec('cp -aR ' . escapeshellarg(PLUGIN_BASE) . ' ' . escapeshellarg(RELEASE_BASE));
// @codingStandardsIgnoreEnd



echo "+ Cleaning the source.\n";

// Files.
$tmp = array(
	'.travis.yml',
	'Gruntfile.js',
	'package.json',
	'phpcs.ruleset.xml',
	'phpunit.xml.dist',
	'README.md',
);
foreach ($tmp as $v) {
	unlink(RELEASE_BASE . $v);
}

// Directories.
$tmp = array(
	'bin',
	'docs',
	'img/assets',
	'node_modules',
	'tests',
);
foreach ($tmp as $v) {
	// @codingStandardsIgnoreStart
	shell_exec('rm -rf ' . escapeshellarg(RELEASE_BASE . $v));
	// @codingStandardsIgnoreEnd
}

// Miscellaneous.
// @codingStandardsIgnoreStart
shell_exec('find ' . escapeshellarg(RELEASE_BASE) . ' -name ".gitignore" -type f -delete');
// @codingStandardsIgnoreEnd



echo "+ Fixing permissions.\n";
// @codingStandardsIgnoreStart
shell_exec('find ' . escapeshellarg(RELEASE_BASE) . ' -type d -print0 | xargs -0 chmod 755');
shell_exec('find ' . escapeshellarg(RELEASE_BASE) . ' -type f -print0 | xargs -0 chmod 644');
// @codingStandardsIgnoreEnd



echo "\nDone!.\n";
