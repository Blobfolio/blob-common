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
	 * @var array $map Z85 ASCII-85 character set.
	 */
	private static $map = [
		"0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d",
		"e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r",
		"s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F",
		"G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
		"U", "V", "W", "X", "Y", "Z", ".", "-", ":", "+", "=", "^", "!", "/",
		"*", "?", "&", "<", ">", "(", ")", "[", "]", "{", "}", "@", "%", "$",
		"#"
	];

	/**
	 * Encode
	 *
	 * @param mixed $data Variable.
	 * @return string Base85-encoded value.
	 */
	public static function encode($data) : string {
		// Convert integers to a string.
		if (is_integer($data)) {
			$data = (string) pack("N", $data);
		}

		if (empty($data)) {
			return "";
		}

		$padding = 0;
		$mod = strlen(data) % 4;
		if ($mod > 0) {
			$padding = 4 - $mod;
			$data .= str_repeat("\0", $padding);
		}

		$out = [];
		$pow = [52200625, 614125, 7225, 85, 1];
		$soup = (array) unpack("N*", $data);

		// Loop and convert.
		foreach ($soup as $v) {
			$stub = "";
			$quotient = (int) $v;

			foreach ($pow as $v2) {
				$remainder = (int) ($quotient % intval($v2));
				$quotient = (int) floor($quotient / intval($v2));
				$stub .= (string) self::$map[$quotient];
				$quotient = $remainder;
			}

			$out[] = (string) $stub;
		}

		// Remove padding.
		if ($padding) {
			$last = (int) (count($out) - 1);
			$out[$last] = (string) substr($out[$last], 0, 5 - $padding);
		}

		return implode("", $out);
	}

	/**
	 * Decode
	 *
	 * @param string $data Data.
	 * @return mixed Decoded.
	 */
	public static function decode(string $data) : string {
		$padding = 0;
		$mod = strlen($data) % 5;
		if ($mod > 0) {
			$padding = 5 - $mod;
			$data .= str_repeat("u", $padding);
		}

		$soup = (array) str_split($data, 5);
		$out = [];

		// Loop and decode.
		foreach ($soup as $v) {
			$stub = (array) unpack("C*", $v);
			$accumulator = 0;

			foreach ($stub as $v2) {
				$index = array_search(chr($v2), self::$map, true);
				if (false === $index) {
					$index = 0;
				}
				$accumulator = $accumulator * 85 + intval($index);
			}

			$out[] = pack("N", $accumulator);
		}

		// Remove padding.
		if ($padding) {
			$last = (int) (count($out) - 1);
			$out[$last] = (string) substr($out[$last], 0, 4 - $padding);
		}

		return implode("", $out);
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
		if ("md5" === $algo) {
			$hash = (string) md5($data, true);
		}
		else {
			$hash = (string) hash($algo, $data, true);
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
		if ("md5" === $algo) {
			$hash = (string) md5_file($file, true);
		}
		else {
			$hash = (string) hash_file($algo, $file, true);
		}

		return self::encode($hash);
	}
}
