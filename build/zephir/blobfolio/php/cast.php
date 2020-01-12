<?php
/**
 * Blobfolio: Cast
 *
 * Type juggling is the best juggling.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;
use Throwable;



final class Cast {
	// -----------------------------------------------------------------
	// Properties
	// -----------------------------------------------------------------

	/**
	 * Boolish values.
	 *
	 * @var array $boolish
	 */
	private static $boolish = array(
		'0'=>false,
		'1'=>true,
		'false'=>false,
		'no'=>false,
		'off'=>false,
		'on'=>true,
		'true'=>true,
		'yes'=>true,
	);



	// -----------------------------------------------------------------
	// Conversion
	// -----------------------------------------------------------------

	/**
	 * To Array
	 *
	 * @param mixed $value Value.
	 * @return array Value.
	 */
	public static function toArray($value) : array {
		// Short circuit.
		if ('array' === \gettype($value)) {
			return $value;
		}

		try {
			// Zephir doesn't support (array) hinting in this one place.
			\settype($value, 'array');
		} catch (Throwable $e) {
			$value = array();
		}

		return $value;
	}

	/**
	 * To Bool
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @return bool Bool.
	 */
	public static function toBool($value, int $flags=0) {
		$flatten = !! ($flags & Shim::FLATTEN);

		// Recurse.
		if (! $flatten && ('array' === \gettype($value))) {
			foreach ($value as $k=>$v) {
				$value[$k] = (bool) self::toBool($v);
			}
			return $value;
		}
		else {
			switch (\gettype($value)) {
				// Short circuit.
				case 'boolean':
					return $value;
				case 'string':
					$value = \strtolower($value);

					// Special cases.
					if (isset(self::$boolish[$value])) {
						return self::$boolish[$value];
					}

					return !! $value;
				case 'array':
					return !! \count($value);
			}

			try {
				$value = (bool) $value;
			} catch (Throwable $e) {
				$value = false;
			}
		}

		return $value;
	}

	/**
	 * To Float
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @return float Float.
	 */
	public static function toFloat($value, int $flags=0) {
		$flatten = !! ($flags & Shim::FLATTEN);

		// Recurse.
		if (! $flatten && ('array' === \gettype($value))) {
			foreach ($value as $k=>$v) {
				$value[$k] = self::toFloat($v);
			}
			return $value;
		}
		// Short circuit.
		elseif ('double' === \gettype($value)) {
			return $value;
		}
		else {
			$value = self::toNumber($value, Shim::FLATTEN);
		}

		return $value;
	}

	/**
	 * To Integer
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @return int Integer.
	 */
	public static function toInt($value, int $flags=0) {
		$flatten = !! ($flags & Shim::FLATTEN);

		// Recurse.
		if (! $flatten && ('array' === \gettype($value))) {
			foreach ($value as $k=>$v) {
				$value[$k] = self::toInt($v);
			}
			return $value;
		}
		else {
			switch (\gettype($value)) {
				case 'array':
					if (1 === \count($value)) {
						\reset($value);
						return self::toInt($value[\key($value)], Shim::FLATTEN);
					}

					return 0;
				case 'int':
				case 'integer':
				case 'long':
					return $value;
				case 'string':
					$value = \strtolower($value);

					// Special cases.
					if (isset(self::$boolish[$value])) {
						return self::$boolish[$value] ? 1 : 0;
					}
			}
		}

		$value = (int) self::toNumber($value, Shim::FLATTEN);
		return $value;
	}

	/**
	 * Sanitize Number
	 *
	 * This ultimately returns a float, but does a lot of string
	 * manipulation along the way to try to get the sanest result.
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @return float Number.
	 */
	public static function toNumber($value, int $flags=0) {
		$flatten = !! ($flags & Shim::FLATTEN);

		// Recurse.
		if (! $flatten && ('array' === \gettype($value))) {
			foreach ($value as $k=>$v) {
				$value[$k] = self::toNumber($v);
			}
			return $value;
		}
		else {
			switch (\gettype($value)) {
				case 'array':
					if (1 === \count($value)) {
						\reset($value);
						return self::toNumber($value[\key($value)], Shim::FLATTEN);
					}

					return 0.0;
				case 'double':
				case 'float':
				case 'number':
					return $value;
				case 'int':
				case 'integer':
				case 'long':
					return (float) $value;
				case 'string':
					// Weird Unicode numbers.
					$number_char_keys = array(
						"\xef\xbc\x90", "\xef\xbc\x91", "\xef\xbc\x92",
						"\xef\xbc\x93", "\xef\xbc\x94", "\xef\xbc\x95",
						"\xef\xbc\x96", "\xef\xbc\x97", "\xef\xbc\x98",
						"\xef\xbc\x99", "\xd9\xa0", "\xd9\xa1", "\xd9\xa2",
						"\xd9\xa3", "\xd9\xa4", "\xd9\xa5", "\xd9\xa6",
						"\xd9\xa7", "\xd9\xa8", "\xd9\xa9", "\xdb\xb0",
						"\xdb\xb1", "\xdb\xb2", "\xdb\xb3", "\xdb\xb4",
						"\xdb\xb5", "\xdb\xb6", "\xdb\xb7", "\xdb\xb8",
						"\xdb\xb9", "\xe1\xa0\x90", "\xe1\xa0\x91",
						"\xe1\xa0\x92", "\xe1\xa0\x93", "\xe1\xa0\x94",
						"\xe1\xa0\x95", "\xe1\xa0\x96", "\xe1\xa0\x97",
						"\xe1\xa0\x98", "\xe1\xa0\x99",
					);

					// The equivalent as actual numbers.
					$number_char_values = array(
						0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6,
						7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3,
						4, 5, 6, 7, 8, 9,
					);

					// Fix weird Unicode numbers.
					$value = \str_replace(
						$number_char_keys,
						$number_char_values,
						$value
					);

					// Convert from cents.
					if (\preg_match('/^\-?[\d,]*\.?\d+(¢|%)$/', $value)) {
						return self::toNumber(
							\preg_replace('/[^\-\d\.]/', '', $value)
						) / 100;
					}
			}
		}

		try {
			$value = (float) \filter_var(
				$value,
				\FILTER_SANITIZE_NUMBER_FLOAT,
				\FILTER_FLAG_ALLOW_FRACTION
			);
		} catch (Throwable $e) {
			$value = 0.0;
		}

		return $value;
	}

	/**
	 * To String
	 *
	 * @param mixed $value Value.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function toString($value, int $flags=0) {
		$flatten = !! ($flags & Shim::FLATTEN);

		// Recurse.
		if (! $flatten && ('array' === \gettype($value))) {
			foreach ($value as $k=>$v) {
				$value[$k] = self::toString($v);
			}
			return $value;
		}
		else {
			// If a single-entry array is passed, use that value.
			if (('array' === \gettype($value))) {
				if (1 === \count($value)) {
					\reset($value);
					return self::toString($value[\key($value)], Shim::FLATTEN);
				}

				return '';
			}

			try {
				$value = (string) $value;
			} catch (Throwable $e) {
				return '';
			}

			// Fix up UTF-8 maybe.
			if ($value && ! \mb_check_encoding($value, 'ASCII')) {
				$value = \Blobfolio\Strings::utf8($value);
			}
		}

		return $value;
	}

	/**
	 * To X Type
	 *
	 * @param mixed $value Variable.
	 * @param string $type Type.
	 * @param int $flags Flags.
	 * @return mixed Value.
	 */
	public static function toType($value, string $type, int $flags=0) {
		switch (\strtolower($type)) {
			case 'string':
				return self::toString($value, $flags);
			case 'int':
			case 'integer':
			case 'long':
				return self::toInt($value, $flags);
			case 'double':
			case 'float':
			case 'number':
				return self::toFloat($value, $flags);
			case 'bool':
			case 'boolean':
				return self::toBool($value, $flags);
			case 'array':
				return self::toArray($value);
		}

		return $value;
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Parse Arguments
	 *
	 * Make sure user arguments follow a default
	 * format. Unlike `wp_parse_args()`-type functions,
	 * only keys from the template are allowed.
	 *
	 * @param mixed $args User arguments.
	 * @param mixed $defaults Default values/format.
	 * @param int $flags Flags.
	 * @return array Parsed arguments.
	 */
	public static function parseArgs($args, $defaults, int $flags = 3) : array {
		// Nothing to crunch if the template isn't set.
		if ('array' !== \gettype($defaults) || empty($defaults)) {
			return array();
		}

		// If there are no arguments to crunch, return the template.
		$args = self::toArray($args);
		if (empty($args)) {
			return $defaults;
		}

		$strict = !! ($flags & Shim::PARSE_STRICT);
		$recursive = !! ($flags & Shim::PARSE_STRICT);

		// Rebuild with user args!
		foreach ($defaults as $k=>$v) {
			if (\array_key_exists($k, $args)) {
				// Recurse if the default is a populated associative
				// array.
				if (
					$recursive &&
					('array' === \gettype($defaults[$k])) &&
					('associative' === \Blobfolio\Arrays::getType($defaults[$k]))
				) {
					$defaults[$k] = self::parseArgs(
						$args[$k],
						$defaults[$k],
						$flags
					);
				}
				// Otherwise just replace.
				else {
					$defaults[$k] = $args[$k];
					if ($strict && (null !== $v)) {
						$d_type = \gettype($v);
						$a_type = \gettype($defaults[$k]);

						if ($a_type !== $d_type) {
							$defaults[$k] = self::toType(
								$defaults[$k],
								$d_type,
								Shim::FLATTEN
							);
						}
					}
				}
			}
		}

		return $defaults;
	}
}
