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

	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Sanitize IP Address Formatting
	 *
	 * @param string $str IP.
	 * @param bool $restricted Allow private/restricted values.
	 * @param bool $condense Condense IPv6.
	 * @return void Nothing.
	 */
	public static function niceIp(string str, const bool restricted=false, const bool condense=true) -> string {

		// Start by getting rid of obviously bad data.
		let str = (string) preg_replace("/[^\d\.\:a-f]/", "", strtolower(str));

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
				let str = (string) inet_ntop(inet_pton(str));
			}
			// Expand.
			else {
				array hex = (array) unpack("H*hex", inet_pton(str));
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

	/**
	 * CIDR to IP Range
	 *
	 * Find the minimum and maximum IPs in a given CIDR range.
	 *
	 * @param string $cidr CIDR.
	 * @return array|bool Range or false.
	 */
	public static function cidrToRange(string cidr) -> array | bool {
		array parts = (array) array_pad(explode("/", cidr), 2, 0);

		// The subnet should always be a number.
		let parts[1] = Cast::toInt(parts[1], true);

		// IPv4?
		if (filter_var(parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return self::cidrToRange4(parts);
		}

		// IPv6? Of course a little more complicated.
		if (filter_var(parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return self::cidrToRange6(parts);
		}

		return false;
	}

	/**
	 * CIDR to Range (IPv4)
	 *
	 * @param array $parts Parts.
	 * @return array|bool Range or false.
	 */
	private static function cidrToRange4(array parts) -> array | bool {
		array range = ["min": 0, "max": 0];

		// IPv4 is only 32-bit.
		if (parts[1] < 0) {
			let parts[1] = 0;
		}
		elseif (parts[1] > 32) {
			let parts[1] = 32;
		}

		if (0 === parts[1]) {
			let range["min"] = self::niceIp(parts[0], true);
			let range["max"] = range["min"];
			return range;
		}

		// Work from binary.
		let parts[1] = bindec(str_pad(str_repeat("1", parts[1]), 32, "0"));

		// Calculate the range.
		long ip = (long) ip2long(parts[0]);
		int netmask = (int) parts[1];
		long first = (ip & netmask);
		long bc = (first | ~netmask);

		let range["min"] = long2ip(first);
		let range["max"] = long2ip(bc);

		return range;
	}

	/**
	 * CIDR to Range (IPv6)
	 *
	 * @param array $parts Parts.
	 * @return array|bool Range or false.
	 */
	private static function cidrToRange6(array parts) -> array | bool {
		array range = ["min": 0, "max": 0];

		// IPv6 is only 128-bit.
		if (parts[1] < 0) {
			let parts[1] = 0;
		}
		elseif (parts[1] > 128) {
			let parts[1] = 128;
		}

		if (0 === parts[1]) {
			let range["min"] = self::niceIp(parts[0], true);
			let range["max"] = range["min"];
			return range;
		}

		// Work from binary.
		var bin = str_pad(str_repeat("1", parts[1]), 128, "0");
		var netmask = gmp_strval(gmp_init(bin, 2), 10);

		// Calculate the range.
		var ip = self::toNumber(parts[0]);

		var first;
		let first = gmp_and(ip, netmask);

		// GMP doesn't have the kind of ~ we're looking for. But
		// that's fine; binary is easy.
		let bin = gmp_strval(gmp_init(netmask, 10), 2);
		let bin = sprintf("%0128s", bin);
		let bin = strtr(bin, ["0": "1", "1": "0"]);

		var bc;
		let bc = gmp_or(first, gmp_strval(gmp_init(bin, 2), 10));

		// Make sure they're strings.
		let first = gmp_strval(first);
		let bc = gmp_strval(bc);

		let range["min"] = self::fromNumber(first);
		let range["max"] = self::fromNumber(bc);

		return range;
	}

	/**
	 * Number to IP
	 *
	 * @param string $ip Decimal.
	 * @return bool True/false.
	 */
	public static function fromNumber(var ip) -> string | bool {
		// IPv6 "numbers" are almost always strings, but IPv4 could be
		// a true number. For consistency, let's make it strings all
		// the way down.
		if ("string" !== typeof ip) {
			if (is_numeric(ip)) {
				let ip = (string) ip;
			}
			else {
				return false;
			}
		}

		// Ignore obviously bad values.
		if (empty ip || ("0" === ip)) {
			return false;
		}

		string bin = (string) gmp_strval(gmp_init(ip, 10), 2);
		let bin = sprintf("%0128s", bin);

		array chunk = [];
		int bit = 0;
		while (bit <= 7) {
			string bin_part = substr(bin, bit * 16, 16);
			let chunk[] = dechex(bindec(bin_part));
			let bit++;
		}

		let ip = (string) implode(":", chunk);
		let ip = (string) inet_ntop(inet_pton(ip));

		// Make sure IPv4 is normal.
		if (empty ip || "::" === ip) {
			return "0.0.0.0";
		}

		return self::niceIp(ip, true);
	}

	/**
	 * IP to Number
	 *
	 * @param string $ip IP.
	 * @return bool True/false.
	 */
	public static function toNumber(string ip) -> long | string | bool {
		// Ignore the bullshit.
		if (empty ip || !filter_var(ip, FILTER_VALIDATE_IP)) {
			return false;
		}

		// IPv4 is easy.
		if (filter_var(ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return ip2long(ip);
		}

		// IPv6 is a little more roundabout.
		if (filter_var(ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			try {
				string ip_n = (string) inet_pton(ip);
				string bin = "";
				int length = strlen(ip_n) - 1;

				int x = length;
				while (x >= 0) {
					let bin = sprintf("%08b", ord(ip_n[x])) . bin;
					let x--;
				}

				return gmp_strval(gmp_init(bin, 2), 10);
			} catch Throwable {
				return false;
			}
		}

		return false;
	}

	/**
	 * IP to Subnet
	 *
	 * This assumes the standard ranges of 24 for IPv4 and 64 for IPv6.
	 *
	 * @param string $ip IP.
	 * @return bool True/false.
	 */
	public static function toSubnet(string ip) -> string | bool {
		let ip = self::niceIp(ip, true, false);
		if (empty ip) {
			return false;
		}

		array bits;

		// IPv4, as always, easy.
		if (filter_var(ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			// Find the minimum IP (simply last chunk to 0).
			let bits = (array) explode(".", ip);
			let bits[3] = 0;
			return implode(".", bits) . "/24";
		}

		// IPv6, more annoying.
		// Find the minimum IP (last 64 bytes to 0).
		let bits = (array) explode(":", ip);
		int x = 4;
		while (x <= 7) {
			let bits[x] = 0;
			let x++;
		}

		let ip = (string) self::niceIp(implode(":", bits), true);
		return ip . "/64";
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * IP in Range?
	 *
	 * Check to see if an IP is in range. This
	 * either accepts a minimum and maximum IP,
	 * or a CIDR.
	 *
	 * @param string $ip String.
	 * @param string $min Min or CIDR.
	 * @param string $max Max.
	 * @return bool True/false.
	 */
	public static function inRange(var ip, var min, var max=null) -> bool {
		let ip = (string) self::niceIp(ip, true);
		if (empty ip || empty min || ("string" !== typeof min)) {
			return false;
		}

		// Is $min a range?
		if (false !== strpos(min, "/")) {
			var range = self::cidrToRange(min);
			if (false === range) {
				return false;
			}

			let min = range["min"];
			let max = range["max"];
		}
		// Max is required otherwise.
		elseif (empty max) {
			return false;
		}

		let ip = self::toNumber(ip);
		let min = self::toNumber(min);
		let max = self::toNumber(max);

		return (
			(false !== ip) &&
			(false !== min) &&
			(false !== max) &&
			ip >= min &&
			ip <= max
		);
	}
}
