<?php
/**
 * Blobfolio: IP Addresses
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;
use Throwable;



final class IPs {
	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Sanitize IP Address Formatting
	 *
	 * @param string $str IP.
	 * @param int $flags Flags.
	 * @return string IP.
	 */
	public static function niceIp(string $str, int $flags=2) : string {
		// Start by getting rid of obviously bad data.
		$str = (string) \preg_replace('/[^\d\.\:a-f]/', '', \strtolower($str));

		// IPv6 might be encased in brackets.
		if (\preg_match('/^\[[\d\.\:a-f]+\]$/', $str)) {
			$str = \substr($str, 1, -1);
		}

		// Turn IPv6-ized 4s back into IPv4.
		if ((0 === \strpos($str, '::')) && (false !== \strpos($str, '.'))) {
			$str = \substr($str, 2);
		}

		$condense = !! ($flags & Shim::IP_CONDENSE);
		$restricted = !! ($flags & Shim::IP_RESTRICTED);

		// IPv6.
		if (\filter_var($str, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
			// Condense it?
			if ($condense) {
				$str = (string) \inet_ntop(\inet_pton($str));
			}
			// Expand.
			else {
				$hex = (array) \unpack('H*hex', \inet_pton($str));
				$str = \substr(\preg_replace(
					'/([a-f\d]{4})/',
					'$1:',
					$hex['hex']
				), 0, -1);
			}
		}
		elseif (! \filter_var($str, \FILTER_VALIDATE_IP)) {
			return '';
		}

		if (
			! $restricted &&
			! empty($str) &&
			! \filter_var(
				$str,
				\FILTER_VALIDATE_IP,
				\FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE
			)
		) {
			return '';
		}

		return $str;
	}

	/**
	 * CIDR to IP Range
	 *
	 * Find the minimum and maximum IPs in a given CIDR range.
	 *
	 * @param string $cidr CIDR.
	 * @return array|bool Range or false.
	 */
	public static function cidrToRange(string $cidr) {
		$parts = (array) \array_pad(\explode('/', $cidr), 2, 0);

		// The subnet should always be a number.
		$parts[1] = \Blobfolio\Cast::toInt($parts[1], Shim::FLATTEN);

		// IPv4?
		if (\filter_var($parts[0], \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
			return self::cidrToRange4($parts);
		}

		// IPv6? Of course a little more complicated.
		if (\filter_var($parts[0], \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
			return self::cidrToRange6($parts);
		}

		return false;
	}

	/**
	 * CIDR to Range (IPv4)
	 *
	 * @param array $parts Parts.
	 * @return array|bool Range or false.
	 */
	private static function cidrToRange4(array $parts) {
		$range = array('min'=>0, 'max'=>0);

		// IPv4 is only 32-bit.
		if ($parts[1] < 0) {
			$parts[1] = 0;
		}
		elseif ($parts[1] > 32) {
			$parts[1] = 32;
		}

		if (0 === $parts[1]) {
			$range['min'] = self::niceIp($parts[0], Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
			$range['max'] = $range['min'];
			return $range;
		}

		// Work from binary.
		$parts[1] = \bindec(\str_pad(\str_repeat('1', $parts[1]), 32, '0'));

		// Calculate the range.
		$ip = (int) \ip2long($parts[0]);
		$netmask = (int) $parts[1];
		$first = ($ip & $netmask);
		$bc = ($first | ~$netmask);

		$range['min'] = \long2ip($first);
		$range['max'] = \long2ip($bc);

		return $range;
	}

	/**
	 * CIDR to Range (IPv6)
	 *
	 * @param array $parts Parts.
	 * @return array|bool Range or false.
	 */
	private static function cidrToRange6(array $parts) {
		$range = array('min'=>0, 'max'=>0);

		// IPv6 is only 128-bit.
		if ($parts[1] < 0) {
			$parts[1] = 0;
		}
		elseif ($parts[1] > 128) {
			$parts[1] = 128;
		}

		if (0 === $parts[1]) {
			$range['min'] = self::niceIp($parts[0], Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
			$range['max'] = $range['min'];
			return $range;
		}

		// Work from binary.
		$bin = \str_pad(\str_repeat('1', $parts[1]), 128, '0');
		$netmask = \gmp_strval(\gmp_init($bin, 2), 10);

		// Calculate the range.
		$ip = self::toNumber($parts[0]);

		$first = \gmp_and($ip, $netmask);

		// GMP doesn't have the kind of ~ we're looking for. But
		// that's fine; binary is easy.
		$bin = \gmp_strval(\gmp_init($netmask, 10), 2);
		$bin = \sprintf('%0128s', $bin);
		$bin = \strtr($bin, array('0'=>'1', '1'=>'0'));

		$bc = \gmp_or($first, \gmp_strval(\gmp_init($bin, 2), 10));

		// Make sure they're strings.
		$first = \gmp_strval($first);
		$bc = \gmp_strval($bc);

		$range['min'] = self::fromNumber($first);
		$range['max'] = self::fromNumber($bc);

		return $range;
	}

	/**
	 * Number to IP
	 *
	 * @param string $ip Decimal.
	 * @return bool True/false.
	 */
	public static function fromNumber($ip) {
		// IPv6 "numbers" are almost always strings, but IPv4 could be
		// a true number. For consistency, let's make it strings all
		// the way down.
		if ('string' !== \gettype($ip)) {
			if (\is_numeric($ip)) {
				$ip = (string) $ip;
			}
			else {
				return false;
			}
		}

		// Ignore obviously bad values.
		if (empty($ip) || ('0' === $ip)) {
			return false;
		}

		// Try the native PHP function first. This will work if the
		// source was an IPv4 address, and is a lot faster than
		// rebuliding manually with math extensions.
		if ($ip <= 2147483647) {
			try {
				$tmp = \long2ip($ip);
				if (\filter_var($tmp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
					return $tmp;
				}
			} catch (Throwable $e) {
				return '';
			}
		}

		$bin = (string) \gmp_strval(\gmp_init($ip, 10), 2);
		$bin = \sprintf('%0128s', $bin);

		$chunk = array();
		$bit = 0;
		while ($bit <= 7) {
			$bin_part = \substr($bin, $bit * 16, 16);
			$chunk[] = \dechex(\bindec($bin_part));
			$bit++;
		}

		$ip = (string) \implode(':', $chunk);
		$ip = (string) \inet_ntop(\inet_pton($ip));

		// Make sure IPv4 is normal.
		if (empty($ip) || '::' === $ip) {
			return '0.0.0.0';
		}

		return self::niceIp($ip, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
	}

	/**
	 * IP to Number
	 *
	 * @param string $ip IP.
	 * @return bool True/false.
	 */
	public static function toNumber(string $ip) {
		// Ignore the bullshit.
		if (empty($ip) || ! \filter_var($ip, \FILTER_VALIDATE_IP)) {
			return false;
		}

		// IPv4 is easy.
		if (\filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
			return \ip2long($ip);
		}

		// IPv6 is a little more roundabout.
		if (\filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
			try {
				$ip_n = (string) \inet_pton($ip);
				$bin = '';
				$length = \strlen($ip_n) - 1;

				$x = $length;
				while ($x >= 0) {
					$bin = \sprintf('%08b', \ord($ip_n[$x])) . $bin;
					$x--;
				}

				return \gmp_strval(\gmp_init($bin, 2), 10);
			} catch (Throwable $e) {
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
	public static function toSubnet(string $ip) {
		$ip = self::niceIp($ip, Shim::IP_RESTRICTED);
		if (empty($ip)) {
			return false;
		}

		$bits = array();

		// IPv4, as always, easy.
		if (\filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
			// Find the minimum IP (simply last chunk to 0).
			$bits = (array) \explode('.', $ip);
			$bits[3] = 0;
			return \implode('.', $bits) . '/24';
		}

		// IPv6, more annoying.
		// Find the minimum IP (last 64 bytes to 0).
		$bits = (array) \explode(':', $ip);
		$x = 4;
		while ($x <= 7) {
			$bits[$x] = 0;
			$x++;
		}

		$ip = (string) self::niceIp(\implode(':', $bits), Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
		return $ip . '/64';
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
	public static function inRange($ip, $min, $max=null) : bool {
		$ip = (string) self::niceIp($ip, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
		if (empty($ip) || empty($min) || ('string' !== \gettype($min))) {
			return false;
		}

		// Is $min a range?
		if (false !== \strpos($min, '/')) {
			$range = self::cidrToRange($min);
			if (false === $range) {
				return false;
			}

			$min = $range['min'];
			$max = $range['max'];
		}
		// Max is required otherwise.
		elseif (empty($max)) {
			return false;
		}

		$ip = self::toNumber($ip);
		$min = self::toNumber($min);
		$max = self::toNumber($max);

		return (
			(false !== $ip) &&
			(false !== $min) &&
			(false !== $max) &&
			$ip >= $min &&
			$ip <= $max
		);
	}
}
