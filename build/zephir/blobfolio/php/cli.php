<?php
/**
 * Blobfolio: CLI
 *
 * Command line helpers.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Cli {
	/**
	 * Is CLI?
	 *
	 * @return bool True/false.
	 */
	public static function isCli() : bool {
		return ("cli" === php_sapi_name());
	}

	/**
	 * Is Root?
	 *
	 * @return bool True/false.
	 */
	public static function isRoot() : bool {
		if (function_exists("posix_getuid")) {
			return (0 === posix_getuid());
		}

		return false;
	}

	/**
	 * Colorize String
	 *
	 * Apply sexy BASH formatting to a string. This accepts a variable
	 * number of arguments, each one a substring and any style codes to
	 * apply to it. If an argument is a string, no styles will be
	 * applied. Otherwise if it is an array, the first value should be
	 * the string, and all subsequent values are considered codes.
	 *
	 * Example: "hello"
	 * Example: ["hello", 1]
	 * Example: ["hello", [1, 32]]
	 * Example: ["hello", 1, 32]
	 *
	 * @see {https://misc.flogisoft.com/bash/tip_colors_and_formatting}
	 *
	 * @param mixed $args Arguments.
	 * @return string Colorized string.
	 */
	public static function colorize() : string {
		$args = (array) func_get_args();
		$line = chr(27) . "[%sm%s" . chr(27) . "[0m";
		$out = "";

		foreach ($args as $v) {
			$v = \Blobfolio\Cast::toArray($v);
			if (!count($v)) {
				continue;
			}

			// The string comes first.
			$tmp = array_shift($v);
			$str = (string) \Blobfolio\Cast::toString($tmp, globals_get("flag_flatten"));

			// Deal with codes.
			$v = \Blobfolio\Arrays::flatten($v);
			$v = array_filter($v, "is_int");
			if (count($v)) {
				$out .= sprintf(
					$line,
					implode(";", $v),
					$str
				);
			}
			else {
				$out .= $str;
			}
		}

		return $out;
	}
}
