<?php
/**
 * Compile Blob-Common Phar
 *
 * @package blobfolio/common
 * @author  Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\dev;

use \blobfolio\common\file as v_file;
use \blobfolio\common\mb as v_mb;
use \blobfolio\common\ref\sanitize as r_sanitize;
use \blobfolio\bob\utility;
use \Phar;

class common extends \blobfolio\bob\base\build {
	const NAME = 'blob-common';

	const REQUIRED_CLASSES = array('Phar');

	// We aren't using binaries or build steps.
	const SKIP_BINARY_DEPENDENCIES = false;
	const SKIP_BUILD = false;
	const SKIP_FILE_DEPENDENCIES = true;
	const SKIP_PACKAGE = true;

	const BINARIES = array('composer');

	// In/Out paths.
	const COMMON_OUT = BOB_ROOT_DIR . 'bin/blob-common.phar';
	const COMMON_REMOTE = 'https://github.com/Blobfolio/blob-common/raw/7.0_Next/bin/blob-common.phar';
	const COMMON_TEMPLATE = BOB_BUILD_DIR . 'skel/common_index.template';
	const COMPOSER_CONFIG = BOB_BUILD_DIR . 'skel/composer.json';
	const LIB_LOCAL_DIR = BOB_ROOT_DIR . 'lib/blobfolio/';
	const LIB_WORKING_DIR = '%TMP%vendor/blobfolio/blob-common/lib/blobfolio/';
	const TEST_OUT = BOB_ROOT_DIR . 'bin/test.phar';
	const TEST_REMOTE = 'https://github.com/Blobfolio/blob-common/raw/master/bin/test.phar';
	const TEST_TEMPLATE = BOB_BUILD_DIR . 'skel/test_index.template';

	protected static $stub_common;
	protected static $stub_test;



	// -----------------------------------------------------------------
	// Build
	// -----------------------------------------------------------------

	/**
	 * Pre-Build Tasks
	 *
	 * @return void Nothing.
	 */
	protected static function pre_build_tasks() {
		// We need a working directory early.
		static::make_working();

		// Install composer.
		static::$deps['composer']->install(static::$working_dir, static::COMPOSER_CONFIG, true);

		$result = utility::prompt('Use local copy of blob-common?', false, 'Yn');
		if (0 === strpos(strtolower($result), 'y')) {
			utility::log('Replacing remote with local lib…');
			$working = str_replace('%TMP%', static::$working_dir, static::LIB_WORKING_DIR);
			v_file::rmdir($working);
			v_file::copy(static::LIB_LOCAL_DIR, $working);
		}

		utility::log('Parsing classmap…');

		// We don't want all the Composer stuff, so we'll be copying
		// bits over as needed.
		$vendor_old = static::$working_dir . 'vendor/';
		$vendor_new = static::$working_dir . 'lib/';
		v_file::mkdir($vendor_new, 0755);

		// Parse the map.
		$raw = file_get_contents("{$vendor_old}composer/autoload_static.php");
		if (
			(false === ($start = strpos($raw, '$classMap ='))) ||
			(false === ($end = strpos($raw, ');', $start)))
		) {
			utility::log('Could not parse classmap.', 'error');
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

		utility::log('Cleaning up…');

		// Don't need the old vendor directory any more.
		v_file::rmdir($vendor_old);

		// We also don't need any shitlist files.
		$files = v_file::scandir(static::$working_dir);
		foreach ($files as $v) {
			if (utility::is_shitlist($v, array('#/LICENSE$#'))) {
				if (is_dir($v)) {
					v_file::rmdir($v);
				}
				else {
					unlink($v);
				}
			}
		}

		utility::log('Compressing PHP…');

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

		utility::log('Compiling stubs…');

		// The main one first.
		static::$stub_common = file_get_contents(static::COMMON_TEMPLATE);
		static::$stub_common = str_replace(
			'CLASSMAP',
			"\n\t\t\t" . implode(",\n\t\t\t", $out) . "\n\t\t",
			static::$stub_common
		);

		// Our test file.
		static::$stub_test = file_get_contents(static::TEST_TEMPLATE);
	}

	/**
	 * Build Tasks
	 *
	 * @return void Nothing.
	 */
	protected static function build_tasks() {
		utility::log('Building blob-common.phar…');

		// Remove original.
		if (is_file(static::COMMON_OUT)) {
			unlink(static::COMMON_OUT);
		}

		clearstatcache();

		// Make new.
		$phar = new Phar(static::COMMON_OUT, 0);
		$phar->startBuffering();
		$phar->buildFromDirectory(static::$working_dir);
		$phar->setStub(static::$stub_common);
		$phar->compressFiles(Phar::GZ);
		$phar->stopBuffering();

		// The "version.json" file is deprecated, but for now we will
		// keep building it.
		$out = array(
			'date'=>date('c'),
			'checksum'=>md5_file(static::COMMON_OUT),
		);
		file_put_contents(BOB_ROOT_DIR . 'bin/version.json', json_encode($out));

		// Going forward, we'll switch to "blob-common.json".
		$out['date_created'] = $out['date'];
		unset($out['date']);
		$out['size'] = (int) filesize(static::COMMON_OUT);
		$out['url'] = static::COMMON_REMOTE;
		ksort($out);
		file_put_contents(BOB_ROOT_DIR . 'bin/blob-common.json', json_encode($out, JSON_PRETTY_PRINT));

		utility::log('Building test.phar…');

		$phar = new Phar(static::TEST_OUT, 0);
		$phar->startBuffering();
		$phar->addFromString('dummy.php', "<?php\n// Comment.");
		$phar->setStub(static::$stub_test);
		$phar->compressFiles(Phar::GZ);
		$phar->stopBuffering();
	}

	// ----------------------------------------------------------------- end build
}
