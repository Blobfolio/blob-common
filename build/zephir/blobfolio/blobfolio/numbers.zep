//<?php
/**
 * Blobfolio: Numbers
 *
 * Number manipulation.
 *
 * @see {blobfolio\common\mb}
 * @see {blobfolio\common\ref\mb}
 * @see {blobfolio\common\ref\sanitize}
 * @see {blobfolio\common\sanitize}
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
	public static function ceil(var num, int precision=0) -> float {
		let num = (float) \Blobfolio\Cast::toFloat(num, true);
		if (precision < 0) {
			let precision = 0;
		}

		let precision = (int) pow(10, precision);

		// We have to split the operations up to prevent premature int
		// truncation.
		let num = (int) ceil((float) num * precision);
		return num / precision;
	}

	/**
	 * Floor
	 *
	 * @param mixed $num Number.
	 * @param int $precision Precision.
	 * @return float Number.
	 */
	public static function floor(var num, int precision=0) -> float {
		let num = (float) \Blobfolio\Cast::toFloat(num, true);
		if (precision < 0) {
			let precision = 0;
		}

		let precision = (int) pow(10, precision);

		let num = (int) floor(num * precision);
		return num / precision;
	}

	/**
	 * Fraction
	 *
	 * @param mixed $num Number.
	 * @param float $precision Precision.
	 * @return string Fraction.
	 */
	public static function fraction(var num, float precision=0.0001) -> string {
		let num = (float) \Blobfolio\Cast::toFloat(num, true);

		// We need a tolerable tolerance.
		if (precision <= 0 || precision >= 1) {
			return "";
		}

		// We don't have to work hard for non-numbers.
		if (0.0 === num) {
			return "0";
		}

		bool negative = (num < 0);
		float numFloat = (float) abs(num);
		float a;
		float aux;
		float b = 1.0 / numFloat;
		float denominator = 0.0;
		float h2 = 0.0;
		float k2 = 1.0;
		float numerator = 1.0;
		float max = (float) numFloat * precision;

		loop {
			let b = 1.0 / b;
			let a = floor(b);
			let aux = numerator;
			let numerator = a * numerator + h2;
			let h2 = aux;
			let aux = denominator;
			let denominator = a * denominator + k2;
			let k2 = aux;
			let b -= a;

			if (abs(numFloat - numerator / denominator) <= max) {
				break;
			}
		}

		// Denominator is one.
		if (1.0 === denominator) {
			let num = strval(numerator);
		}
		else {
			let num = strval(numerator) . "/" . strval(denominator);
		}

		if (negative) {
			return "-" . num;
		}

		return num;
	}
}
