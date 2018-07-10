//<?php
/**
 * Blobfolio: Domain Suffixes
 *
 * Make Domain Validation Great Again.
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class IPs {
	/**
	 * Sanitize IP Address Formatting
	 *
	 * @param string $str IP.
	 * @param bool $restricted Allow private/restricted values.
	 * @param bool $condense Condense IPv6.
	 * @return void Nothing.
	 */
	public static function niceIp(var str, const bool restricted=false, const bool condense=true) -> string | array {
		// Recurse.
		if (unlikely "array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::niceIp(v, restricted, condense);
			}
			return str;
		}

		// Don't need to fancy cast.
		if ("string" !== typeof str) {
			return "";
		}

		// Start by getting rid of obviously bad data.
		let str = preg_replace("/[^\d\.\:a-f]/", "", strtolower(str));

		// IPv6 might be encased in brackets.
		if (preg_match("/^\[[\d\.\:a-f]+\]$/", str)) {
			let str = substr(str, 1, -1);
		}

		// Turn IPv6-ized 4s back into IPv4.
		if ((0 === strpos(str, "::")) && (false !== strpos(str, "."))) {
			let str = substr(str, 2);
		}

		// IPv6.
		if (filter_var(str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			// Condense it?
			if (condense) {
				let str = inet_ntop(inet_pton(str));
			}
			// Expand.
			else {
				var hex = unpack("H*hex", inet_pton(str));
				let str = substr(preg_replace(
					"/([a-f\d]{4})/",
					"$1:",
					hex["hex"]
				), 0, -1);
			}
		}
		elseif (!filter_var(str, FILTER_VALIDATE_IP)) {
			return "";
		}

		if (
			!restricted &&
			str &&
			!filter_var(
				str,
				FILTER_VALIDATE_IP,
				FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
			)
		) {
			return "";
		}

		return str;
	}
}
