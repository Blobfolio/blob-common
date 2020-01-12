<?php
/**
 * Blobfolio: JSON
 *
 * Improved JSON support for PHP.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;



final class Json {
	/**
	 * JSON Decode
	 *
	 * A more robust version of JSON decode that can somewhat handle
	 * general Javascript objects. This always returns objecty things as
	 * associative arrays.
	 *
	 * @param string $str String.
	 * @param bool $recursed Recursed.
	 * @return bool True/false.
	 */
	public static function decode($str, bool $recursed=false) {
		// Copy str over to our typed variable.
		if (! $recursed) {
			$encoded = (string) \Blobfolio\Cast::toString($str, Shim::FLATTEN);
		}
		else {
			$encoded = (string) $str;
		}

		// Remove comments.
		$str = \preg_replace(
			array(
				// Single line //.
				'#^\s*//(.+)$#m',
				// Multi-line /* */.
				'#^\s*/\*(.+)\*/#Us',
				'#/\*(.+)\*/\s*$#Us',
			),
			'',
			$str
		);

		// Trim it.
		$str = \Blobfolio\Strings::trim($str);

		// Is it empty?
		if (! $str || ("''" === $str) || ('""' === $str)) {
			return '';
		}

		// Maybe it just works?
		$tmp = \json_decode($str, true);
		if (null !== $tmp) {
			return $tmp;
		}

		$lower = \Blobfolio\Strings::toLower($str);
		// Bool.
		if ('true' === $lower || 'false' === $lower) {
			return \Blobfolio\Cast::toBool($str);
		}
		// Null.
		elseif ('null' === $lower) {
			return null;
		}
		// Number.
		elseif (\is_numeric($lower)) {
			if (false !== \strpos($lower, '.')) {
				return (float) $lower;
			}
			else {
				return (int) $lower;
			}
		}
		// String.
		elseif (\preg_match('/^("|\')(.+)(\1)$/s', $str, $match) && ($match[1] === $match[3])) {
			return \Blobfolio\Dom::decodeEntities($match[2]);
		}
		// Bail if we don't have an object at this point.
		elseif (
			! \preg_match('/^\[.*\]$/s', $str) &&
			! \preg_match('/^\{.*\}$/s', $str)
		) {
			return null;
		}

		// Start building an array.
		$slices = array(
			array(
				'type'=>'slice',
				'from'=>0,
				'delimiter'=>false,
			),
		);
		$out = array();
		if (0 === \strpos($str, '[')) {
			$type = 'array';
		}
		else {
			$type = 'object';
		}
		$chunk = \mb_substr($str, 1, -1, 'UTF-8');
		$length = (int) \mb_strlen($chunk, 'UTF-8');
		for ($x = 0; $x <= $length; ++$x) {
			$last = \end($slices);
			$subchunk = \mb_substr($chunk, $x, 2, 'UTF-8');

			// A comma or the end.
			if (
				($x === $length) ||
				((',' === $chunk[$x]) && 'slice' === $last['type'])
			) {
				$slice = \mb_substr($chunk, $last['from'], ($x - $last['from']), 'UTF-8');
				$slices[] = array(
					'type'=>'slice',
					'from'=>$x + 1,
					'delimiter'=>false,
				);

				// Arrays are straightforward, just pop it in.
				if ('array' === $type) {
					$out[] = self::decode($slice, true);
				}
				// Objects need key/value separation.
				else {
					// Key is quoted.
					if (\preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
						$key = self::decode($parts[1], true);
						$val = self::decode($parts[2], true);
						$out[$key] = $val;
					}
					// Key is unquoted.
					elseif (\preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
						$key = $parts[1];
						\Blobfolio\Dom::decodeJsEntities($key);
						$val = self::decode($parts[2], true);
						$out[$key] = $val;
					}
				}
			}
			// A new quote.
			elseif (
				(('"' === $chunk[$x]) || ("'" === $chunk[$x])) &&
				('string' !== $last['type'])
			) {
				$slices[] = array(
					'type'=>'string',
					'from'=>$x,
					'delimiter'=>$chunk[$x],
				);
			}
			// An end quote.
			elseif (
				($chunk[$x] === $last['delimiter']) &&
				('string' === $last['type']) &&
				(
					('\\' !== $chunk[$x - 1]) ||
					(('\\' === $chunk[$x - 1]) && ('\\' === $chunk[$x - 2]))
				)
			) {
				\array_pop($slices);
			}
			// Opening bracket (and we're in a slice/objectish thing.
			elseif (
				('[' === $chunk[$x]) &&
				\in_array($last['type'], array('slice', 'array', 'object'), true)
			) {
				$slices[] = array(
					'type'=>'array',
					'from'=>$x,
					'delimiter'=>false,
				);
			}
			// Closing bracket.
			elseif (
				(']' === $chunk[$x]) &&
				('array' === $last['type'])
			) {
				\array_pop($slices);
			}
			// Opening brace (and we're in a slice/objectish thing.
			elseif (
				('{' === $chunk[$x]) &&
				\in_array($last['type'], array('slice', 'array', 'object'), true)
			) {
				$slices[] = array(
					'type'=>'object',
					'from'=>$x,
					'delimiter'=>false,
				);
			}
			// Closing brace.
			elseif (
				('}' === $chunk[$x]) &&
				('object' === $last['type'])
			) {
				\array_pop($slices);
			}
			// Opening comment.
			elseif (
				('/*' === $subchunk) &&
				\in_array($last['type'], array('slice', 'array', 'object'), true)
			) {
				$slices[] = array(
					'type'=>'comment',
					'from'=>$x,
					'delimiter'=>false,
				);
				++$x;
			}
			// Closing comment.
			elseif (
				('*/' === $subchunk) &&
				('comment' === $last['type'])
			) {
				\array_pop($slices);
				++$x;
				for ($y = $last['from']; $y <= $x; ++$y) {
					$chunk[$y] = ' ';
				}
			}
		}// End each char.

		return $out;
	}

	/**
	 * JSON Decode Array
	 *
	 * This ensures a JSON string is always returned as an array with
	 * optional argument parsing.
	 *
	 * @param string $json JSON.
	 * @param array $defaults Defaults.
	 * @param int $flags Flags.
	 * @return array Data.
	 */
	public static function decodeArray($json, $defaults=null, int $flags = 3) : array {
		$json = self::decode($json);

		if ((null === $json) || (('string' === \gettype($json) && empty($json)))) {
			$json = array();
		}
		elseif ('array' !== \gettype($json)) {
			$json = \Blobfolio\Cast::toArray($json);
		}

		// Parse args?
		if ('array' === \gettype($defaults)) {
			return \Blobfolio\Cast::parseArgs($json, $defaults, $flags);
		}

		return $json;
	}

	/**
	 * JSON Encode
	 *
	 * This is a wrapper for json_encode, but will try to fix common
	 * issues.
	 *
	 * @param mixed $value Value.
	 * @param int $options Options.
	 * @param int $depth Depth.
	 * @return ?string Encoded or null.
	 */
	public static function encode($value, int $options=0, int $depth=512) : ?string {
		// Simple values don't require a lot of thought.
		if (empty($value) || \is_numeric($value) || \is_bool($value)) {
			return \json_encode($value, $options, $depth);
		}

		// Make a copy, try PHP's version, and revert if necessary.
		$original = $value;
		$value = \json_encode($value, $options, $depth);

		// Try again with UTF-8 sanitizing if this failed.
		if (empty($value)) {
			$original = \Blobfolio\Strings::utf8Recursive($original);
			$value = \json_encode($original, $options, $depth);
		}

		return $value;
	}

	/**
	 * JSON
	 *
	 * Fix JSON formatting.
	 *
	 * @param string $str String.
	 * @param bool $pretty Pretty.
	 * @return bool True/false.
	 */
	public static function fix($str, bool $pretty=true) : ?string {
		if ('string' !== \gettype($str)) {
			if ($pretty) {
				return self::encode($str, $pretty);
			}

			return self::encode($str);
		}

		$decoded = self::decode($str);
		if ((false === $decoded) || (null === $decoded)) {
			return null;
		}

		// Regular PHP can handle the rest.
		if ($pretty) {
			return \json_encode($decoded, \JSON_PRETTY_PRINT);
		}

		return \json_encode($decoded);
	}

	/**
	 * Is JSON
	 *
	 * @param mixed $str String.
	 * @param bool $loose Allow empty.
	 * @return bool True/false.
	 */
	public static function isJson($str, bool $loose=false) : bool {
		if (('string' !== \gettype($str)) || (! $loose && empty($str))) {
			return false;
		}

		if ($loose && empty($str)) {
			return true;
		}

		$json = \json_decode($str);
		return (null !== $json);
	}
}
