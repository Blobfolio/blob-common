//<?php
/**
 * Blobfolio: Internal/Global Stuff
 *
 * Bitwise flags:
 * 1
 * 2
 * 4
 * 8
 * 16
 * 32
 * 64
 * 128
 * 256
 * 512
 * 1024
 * 2048
 * 4096
 * 8192
 * 16384
 * 32768
 * 65536
 * 131072
 * 262144
 * 524288
 * 1048576
 * 2097152
 * 4194304
 * 8388608
 * 16777216
 * 33554432
 * 67108864
 * 134217728
 * 268435456
 * 536870912
 * 1073741824
 * 2147483648
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 * @license WTFPL <http://www.wtfpl.net>
 */

namespace Blobfolio;

final class Blobfolio {
	/**
	 * Flags: General
	 */
	const PRETTY = 2147483648;
	const TRUSTED = 1073741824;
	const FLATTEN = 536870912;
	const REFRESH = 268435456;
	const UNICODE = 134217728;

	/**
	 * Flags: Address::niceAddress
	 */
	const ADDRESS_FIELD_ALL = 7;
	const ADDRESS_FIELD_EMAIL = 1;
	const ADDRESS_FIELD_PHONE = 2;
	const ADDRESS_FIELD_COMPANY = 4;

	/**
	 * Flags: Cast::parseArgs
	 * Flags: Json::decodeArray
	 */
	const PARSE_STRICT = 1;
	const PARSE_RECURSIVE = 2;

	/**
	 * Flags: Dom::niceJs
	 */
	const JS_FOR_QUOTES = 1;
	const JS_FOR_APOSTROPHES = 2;

	/**
	 * Flags: Domains::niceHost
	 */
	const HOST_STRIP_WWW = 1;
	const HOST_UNICODE = 134217728;

	/**
	 * Flags: Files::path
	 */
	const PATH_VALIDATE = 1;

	/**
	 * Flags: Geo::getUsStates
	 */
	const US_TERRITORIES = 1;

	/**
	 * Flags: Images::cleanSvg
	 */
	const SVG_CLEAN_STYLES = 1;
	const SVG_FIX_DIMENSIONS = 2;
	const SVG_NAMESPACE = 4;
	const SVG_RANDOM_ID = 8;
	const SVG_REWRITE_STYLES = 16;
	const SVG_SANITIZE = 32;
	const SVG_SAVE = 64;
	const SVG_STRIP_DATA = 128;
	const SVG_STRIP_ID = 256;
	const SVG_STRIP_STYLE = 512;
	const SVG_STRIP_TITLE = 1024;

	/**
	 * Flags: Ips::niceIp
	 */
	const IP_RESTRICTED = 1;
	const IP_CONDENSE = 2;

	/**
	 * Flags: Retail::usd
	 */
	const USD_THOUSANDS = 1;
	const USD_CENTS = 2;
	const USD_TRIM = 4;

	/**
	 * Flags: Strings::excerpt
	 */
	const EXCERPT_BREAK_CHARACTER = 1;
	const EXCERPT_BREAK_WORD = 2;
	const EXCERPT_ELLIPSIS = 4;
	const EXCERPT_INCLUSIVE = 8;



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

			let self::_data_dir = (string) \Blobfolio\Files::path(self::_data_dir, globals_get("flag_trusted"));
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
