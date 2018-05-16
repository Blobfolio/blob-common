<?php
/**
 * Compile Plugin
 *
 * This will update dependencies, optimize the autoloader, and
 * optionally generate a new release zip.
 *
 * @package blobfolio/mimes
 * @author  Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\dev;

use \blobfolio\bob\io;
use \blobfolio\bob\log;
use \blobfolio\common\file as v_file;

class plugin extends \blobfolio\bob\base\mike_wp {
	// Project Name.
	const NAME = 'Tutan Common';
	const DESCRIPTION = 'Tutan Common is a (deprecated) WordPress plugin providing blob-common and WP-specific features.';
	const CONFIRMATION = '';
	const SLUG = 'blob-common';

	const USE_PHPAB = false;
	const USE_COMPOSER = false;
	const USE_GRUNT = '';

	const RELEASE_TYPE = 'zip';



	/**
	 * Overload: Build Source
	 *
	 * @return void Nothing.
	 */
	protected static function build_update_source() {
		// We need to copy the latest version of our Phar files.
		$bin_dir = dirname(BOB_ROOT_DIR) . '/bin/';
		$out_dir = static::get_plugin_dir() . 'lib/';

		log::print('Copying Phar archives…');

		foreach (array('blob-common.phar', 'test.phar') as $file) {
			if (is_file("{$out_dir}{$file}")) {
				unlink("{$out_dir}{$file}");
			}

			copy("{$bin_dir}{$file}", "{$out_dir}{$file}");
			chmod("{$out_dir}{$file}", 0644);
		}
	}

	/**
	 * Overload: Patch Version
	 *
	 * @return void Nothing.
	 */
	protected static function patch_version() {
		// Until PHP 5.6 support is fully dropped, we are keeping a
		// redundant release file at "wp.json".
		$file = dirname(BOB_ROOT_DIR) . '/release/wp.json';
		$content = trim(file_get_contents($file));
		$content = json_decode($content, true);

		if (isset($content['Version'])) {
			$content['Version'] = static::$_version;
			file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));
		}
	}

	/**
	 * Get Shitlist
	 *
	 * @return array Shitlist.
	 */
	protected static function get_shitlist() {
		$shitlist = io::SHITLIST;
		$shitlist [] = '#/bin$#';
		$shitlist [] = '#/docs$#';
		$shitlist [] = '#/img/assets$#';
		$shitlist [] = '#/tests$#';
		return $shitlist;
	}

	/**
	 * Get Source Directory
	 *
	 * This should be a path to the main plugin root.
	 *
	 * @return string Source.
	 */
	protected static function get_plugin_dir() {
		return dirname(BOB_ROOT_DIR) . '/wp/';
	}

	/**
	 * Get Release Path
	 *
	 * When building a zip, the path should end in .zip. When copying,
	 * it should be an empty directory.
	 *
	 * @return string Source.
	 */
	protected static function get_release_path() {
		return dirname(BOB_ROOT_DIR) . '/release/' . static::SLUG . '.zip';
	}
}
