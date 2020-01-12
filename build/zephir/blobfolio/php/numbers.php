<?php
/**
 * Blobfolio: Numbers
 *
 * Number manipulation.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Numbers {
	// -----------------------------------------------------------------
	// Rounding
	// -----------------------------------------------------------------

	/**
	 * Ceil
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @return float Number.
	 */
	public static function ceil($num, int $precision=0) : float {
		$num = (float) \Blobfolio\Cast::toFloat($num, globals_get("flag_flatten"));
		if ($precision < 0) {
			$precision = 0;
		}

		$precision = (int) pow(10, $precision);

		// We have to split the operations up to prevent premature int
		// truncation.
		$num = (int) ceil((float) $num * $precision);
		return $num / $precision;
	}

	/**
	 * Floor
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @return float Number.
	 */
	public static function floor($num, int $precision=0) : float {
		$num = (float) \Blobfolio\Cast::toFloat($num, globals_get("flag_flatten"));
		if ($precision < 0) {
			$precision = 0;
		}

		$precision = (int) pow(10, $precision);

		$num = (int) floor((float) $num * $precision);
		return $num / $precision;
	}

	/**
	 * Fraction
	 *
	 * @param mixed $num Number.
	 * @param float $precision Precision.
	 * @return string Fraction.
	 */
	public static function fraction($num, float $precision=0.0001) : string {
		$num = (float) \Blobfolio\Cast::toFloat($num, globals_get("flag_flatten"));

		// We need a tolerable tolerance.
		if ($precision <= 0 || $precision >= 1) {
			return "";
		}

		// We don't have to work hard for non-numbers.
		if (0.0 === $num) {
			return "0";
		}

		$negative = ($num < 0);
		$numFloat = (float) abs($num);
		$b = 1.0 / $numFloat;
		$denominator = 0.0;
		$h2 = 0.0;
		$k2 = 1.0;
		$numerator = 1.0;
		$max = (float) $numFloat * $precision;

		while (true) {
			$b = 1.0 / $b;
			$a = floor($b);
			$aux = $numerator;
			$numerator = $a * $numerator + $h2;
			$h2 = $aux;
			$aux = $denominator;
			$denominator = $a * $denominator + $k2;
			$k2 = $aux;
			$b -= $a;

			if (abs($numFloat - $numerator / $denominator) <= $max) {
				break;
			}
		}

		// Denominator is one.
		if (1.0 === $denominator) {
			$num = strval($numerator);
		}
		else {
			$num = strval($numerator) . "/" . strval($denominator);
		}

		if ($negative) {
			return "-" . $num;
		}

		return $num;
	}

	/**
	 * In Range
	 *
	 * @param number $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Num.
	 */
	public static function inRange($value, $min=null, $max=null) : bool {
		return ($value === self::toRange($value, $min, $max));
	}

	/**
	 * To Range
	 *
	 * @param number $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Num.
	 */
	public static function toRange($value, $min=null, $max=null) {
		$type = "integer";
		$minNum = !! (!empty($min) && is_numeric($min));
		$maxNum = !! (!empty($max) && is_numeric($max));

		// Make sure we have the same type all around.
		if (
			($minNum && ("integer" !== gettype($min))) ||
			($maxNum && ("integer" !== gettype($max)))
		) {
			$type = "double";
		}

		// Typecast the trio.
		if ($minNum && ($type !== gettype($min))) {
			$min = \Blobfolio\Cast::toType($min, $type, globals_get("flag_flatten"));
		}
		if ($maxNum && ($type !== gettype($max))) {
			$max = \Blobfolio\Cast::toType($max, $type, globals_get("flag_flatten"));
		}
		if ($type !== gettype($value)) {
			$value = \Blobfolio\Cast::toType($value, $type, globals_get("flag_flatten"));
		}

		// Make sure they're in the right order.
		if (
			$minNum &&
			$maxNum &&
			$min > $max
		) {
			if ("integer" === $type) {
				$tmp = $min;
				$min = (int) $max;
				$max = (int) $tmp;
			}
			else {
				$tmp = $min;
				$min = (float) $max;
				$max = (float) $tmp;
			}
		}

		if ($minNum && $value < $min) {
			$value = $min;
		}

		if ($maxNum && $value > $max) {
			$value = $max;
		}

		return $value;
	}
}
