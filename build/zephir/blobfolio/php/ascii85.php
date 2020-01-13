<?php
/**
 * Blobfolio: Z85
 *
 * Base85 encode and decode using the Z85 character set. Among other
 * things, this can result in reduced string length for hash-type data.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 * @see {https://en.wikipedia.org/wiki/Ascii85#ZeroMQ_Version_.28Z85.29}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Ascii85 {
	/**
	 * Z85 ASCII-85 character set.
	 *
	 * @var array $map
	 */
	private static $map = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-:+=^!/*?&<>()[]{}@%$#';

	/**
	 * Encode
	 *
	 * @param mixed $data Variable.
	 * @return string Base85-encoded value.
	 */
	public static function encode($data) : string {
		// Convert integers to a string.
		if (\is_integer($data)) {
			$data = (string) \pack('N', $data);
		}

		if (empty($data)) {
			return '';
		}

		$padding = 0;
		if ($mod = \strlen($data) % 4) {
			$padding = 4 - $mod;
		}
		$data .= \str_repeat("\0", $padding);

		$out = array();
		$pow = array(52200625, 614125, 7225, 85, 1);
		$soup = (array) \unpack('N*', $data);

		// Loop and convert.
		foreach ($soup as $v) {
			$stub = '';
			$quotient = (int) $v;

			foreach ($pow as $v2) {
				$remainder = $quotient % $v2;
				$quotient = (int) ($quotient / $v2);
				$stub .= (string) self::$map[$quotient];
				$quotient = $remainder;
			}

			$out[] = (string) $stub;
		}

		// Remove padding.
		if ($padding) {
			$last = (int) (\count($out) - 1);
			$out[$last] = (string) \substr($out[$last], 0, 5 - $padding);
		}

		return \implode('', $out);
	}

	/**
	 * Decode
	 *
	 * @param string $data Data.
	 * @return mixed Decoded.
	 */
	public static function decode(string $data) : string {
		// Bad data or no data.
		if (! $data || \strlen($data) !== \strspn($data, self::$map)) {
			return '';
		}

		$padding = 0;
		$length = \strlen($data);
		if ($mod = $length % 5) {
			$padding = 5 - $mod;
			$data .= \str_repeat('#', $padding);
			$length += $padding;
		}

		// Remap.
		$fifths = \str_split($data, 5);
		$bytes = array();
		foreach ($fifths as $byte=>$chars) {
			$char = 0;
			$values = \unpack('C*', $chars);
			foreach ($values as $key) {
				$index = (int) \strpos(self::$map, \chr($key));
				$char = $char * 85 + $index;
			}
			$bytes[$byte] = \pack('N', $char);
		}

		// Kill any padding.
		if ($padding) {
			$last = \count($bytes) - 1;
			$bytes[$last] = \substr($bytes[$last], 0, 4 - $padding);
		}

		return \implode('', $bytes);
	}

	/**
	 * Hash
	 *
	 * Return an ASCII85-encoded hash.
	 *
	 * @param string $algo Algo.
	 * @param string $data Data.
	 * @return string Hash.
	 */
	public static function hash(string $algo, string $data) : string {
		// MD5 operations are optimized.
		if ('md5' === $algo) {
			$hash = (string) \md5($data, true);
		}
		else {
			$hash = (string) \hash($algo, $data, true);
		}

		return self::encode($hash);
	}

	/**
	 * Hash File
	 *
	 * Return an ASCII85-encoded file hash.
	 *
	 * @param string $algo Algo.
	 * @param string $file File path.
	 * @return string Hash.
	 */
	public static function hash_file(string $algo, string $file) : string {
		// MD5 operations are optimized.
		if ('md5' === $algo) {
			$hash = (string) \md5_file($file, true);
		}
		else {
			$hash = (string) \hash_file($algo, $file, true);
		}

		return self::encode($hash);
	}
}
