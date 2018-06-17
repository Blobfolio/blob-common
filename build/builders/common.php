<?php
/**
 * Compile Blob-Common Phar
 *
 * @package blobfolio/common
 * @author  Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\dev;

use \blobfolio\bob\format;
use \blobfolio\bob\io;
use \blobfolio\bob\log;
use \blobfolio\common\file as v_file;
use \Phar;

class common extends \blobfolio\bob\base\mike {
	// Project Name.
	const NAME = 'blob-common';
	const DESCRIPTION = 'blob-common is a PHP library with handy, reusable functions for sanitizing, formatting, and manipulating data.';
	const SLUG = 'blob-common';

	// Runtime requirements.
	const REQUIRED_CLASSES = array('Phar');

	// Automatic setup.
	const CLEAN_ON_SUCCESS = false;			// Delete tmp/bob when done.

	const COMMON_REMOTE = 'https://github.com/Blobfolio/blob-common/raw/master/bin/blob-common.phar';
	const TEST_REMOTE = 'https://github.com/Blobfolio/blob-common/raw/master/bin/test.phar';

	// Functions to run to complete the build, in order, grouped by
	// heading.
	const ACTIONS = array(
		'Updating Data'=>array(
			'build',
			'release',
		),
	);

	protected static $stub_common;
	protected static $stub_test;



	// -----------------------------------------------------------------
	// Build
	// -----------------------------------------------------------------

	/**
	 * Build
	 *
	 * @return void Nothing.
	 */
	public static function build() {
		// We need a working directory early.
		static::make_working_dir();
		$skel_dir = BOB_ROOT_DIR . 'skel/';

		// Install composer.
		io::composer_install(static::$_working_dir, "{$skel_dir}composer.json", true);

		// Use local blob-common instead of pulling from Git?
		if (log::confirm('Use local copy of blob-common?')) {
			log::print('Replacing remote with local lib…');
			$working = static::$_working_dir . 'vendor/blobfolio/blob-common/lib/blobfolio/';
			v_file::rmdir($working);
			v_file::copy(dirname(BOB_ROOT_DIR) . '/lib/blobfolio/', $working);
		}

		log::print('Parsing classmap…');

		// We don't want all the Composer stuff, so we'll be copying
		// bits over as needed.
		$vendor_old = static::$_working_dir . 'vendor/';
		$vendor_new = static::$_working_dir . 'lib/';
		v_file::mkdir($vendor_new, 0755);

		// Parse the map.
		$raw = file_get_contents("{$vendor_old}composer/autoload_static.php");
		if (
			(false === ($start = strpos($raw, '$classMap ='))) ||
			(false === ($end = strpos($raw, ');', $start)))
		) {
			log::error('Could not parse classmap.');
		}

		$map = substr($raw, $start, ($end - $start + 2));
		$map = str_replace("__DIR__ . '/..' . ", '', $map);
		// phpcs:disable
		eval($map);
		//phpcs:enable

		// Run through each entry, moving the source and generating a
		// new map entry.
		$out = array();
		foreach ($classMap as $k=>$v) {
			// Move the files out of the original location.
			$subdir = explode('/', ltrim($v, '/'));
			$subdir = array_shift($subdir);
			if (file_exists("{$vendor_old}{$subdir}")) {
				rename("{$vendor_old}{$subdir}", "{$vendor_new}{$subdir}");
			}

			$classMap[$k] = '/lib/' . ltrim($v, '/');
			$out[] = "'" . addslashes($k) . "'=>'phar://' . \$blobcommon_phar . '/" . addslashes(ltrim($classMap[$k], '/')) . "'";
		}

		log::print('Cleaning up…');

		// Don't need the old vendor directory any more.
		v_file::rmdir($vendor_old);

		// We also don't need any shitlist files.
		$files = v_file::scandir(static::$_working_dir);
		$shitlist = io::SHITLIST;
		$shitlist[] = '#/LICENSE$#';
		foreach ($files as $v) {
			if (io::is_shitlist($v, $shitlist)) {
				if (is_dir($v)) {
					v_file::rmdir($v);
				}
				else {
					unlink($v);
				}
			}
		}

		log::print('Compressing PHP…');

		$files = v_file::scandir($vendor_new);
		foreach ($files as $v) {
			if (
				('.php' === substr($v, -4)) ||
				// Remember blob-phone stores its classes with a .txt
				// extension to avoid bloating an optimized classmap
				// file.
				preg_match('#/data[A-Z]{2}.txt$#', $v)
			) {
				file_put_contents($v, php_strip_whitespace($v));
			}
		}

		log::print('Compiling stubs…');

		// The main one first.
		static::$stub_common = file_get_contents("{$skel_dir}common_index.template");
		static::$stub_common = str_replace(
			'CLASSMAP',
			"\n\t" . implode(",\n\t", $out) . "\n",
			static::$stub_common
		);

		// Our test file.
		static::$stub_test = file_get_contents("{$skel_dir}test_index.template");
	}

	/**
	 * Release
	 *
	 * @return void Nothing.
	 */
	public static function release() {
		log::print('Building blob-common.phar…');

		// Define some paths.
		$bin_dir = dirname(BOB_ROOT_DIR) . '/bin/';

		// Remove original.
		if (is_file("{$bin_dir}blob-common.phar")) {
			unlink("{$bin_dir}blob-common.phar");
		}

		clearstatcache();

		// Make new.
		$phar = new Phar("{$bin_dir}blob-common.phar", 0);
		$phar->startBuffering();
		$phar->buildFromDirectory(static::$_working_dir);
		$phar->setStub(static::$stub_common);
		$phar->compressFiles(Phar::GZ);
		$phar->stopBuffering();

		// Compile a version release file.
		$out = array(
			'checksum'=>md5_file("{$bin_dir}blob-common.phar"),
			'date_created'=>date('c'),
			'size'=>intval(filesize("{$bin_dir}blob-common.phar")),
			'url'=>static::COMMON_REMOTE,
		);
		file_put_contents("{$bin_dir}blob-common.json", json_encode($out, JSON_PRETTY_PRINT));

		log::print('Building test.phar…');

		$phar = new Phar("{$bin_dir}test.phar", 0);
		$phar->startBuffering();
		$phar->addFromString('dummy.php', "<?php\n// Comment.");
		$phar->setStub(static::$stub_test);
		$phar->compressFiles(Phar::GZ);
		$phar->stopBuffering();
	}

	// ----------------------------------------------------------------- end build
}
