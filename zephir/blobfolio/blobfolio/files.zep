//<?php
/**
 * Blobfolio: Files
 *
 * Filepath helpers.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Files {
	/**
	 * Add Leading Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function leadingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::leadingSlash(v);
			}
			return str;
		}

		return "/" . self::unleadingSlash(str);
	}

	/**
	 * Add Trailing Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function trailingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::trailingSlash(v);
			}
			return str;
		}

		return self::untrailingSlash(str) . "/";
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function unixSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::unixSlash(v);
			}
			return str;
		}

		let str = \Blobfolio\Cast::toString(str, true);
		let str = str_replace("\\", "/", str);
		let str = str_replace("/./", "/", str);
		return preg_replace("#/{2,}#u", "/", str);
	}

	/**
	 * Strip Leading Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function unleadingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::unleadingSlash(v);
			}
			return str;
		}

		let str = self::unixSlash(str);
		return ltrim(str, "/");
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function untrailingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::untrailingSlash(v);
			}
			return str;
		}

		let str = self::unixSlash(str);
		return rtrim(str, "/");
	}
}
