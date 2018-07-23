//<?php
/**
 * Blobfolio: Internal/Global Stuff
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Blobfolio {
	private static _data_dir = null;

	/**
	 * Get Data Directory
	 *
	 * Some particularly heavy datasets are stored externally for
	 * performance reasons. That location is set via the PHP
	 * "blobfolio.data_dir" INI directive, which we need to clean up
	 * and validate before trying to use.
	 *
	 * @param string $file File.
	 * @return string Directory or file contents.
	 */
	public static function getDataDir(string file="") -> string {
		// We need to pull it.
		if (null === self::_data_dir) {
			let self::_data_dir = (string) ini_get("blobfolio.data_dir");

			// Default path.
			if (empty self::_data_dir) {
				let self::_data_dir = "/usr/share/php/Blobfolio/";
			}

			let self::_data_dir = (string) \Blobfolio\Files::path(self::_data_dir, true);
		}

		// Check a specific file?
		if (!empty file) {
			if (!empty self::_data_dir) {
				let file = self::_data_dir . file;
				if (is_file(file)) {
					return (string) file_get_contents(file);
				}
			}

			return "";
		}

		// Just return the base directory.
		return self::_data_dir;
	}
}
