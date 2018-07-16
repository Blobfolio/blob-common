//<?php
/**
 * Blobfolio: Arrays
 *
 * Array helpers.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Retail {
	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * USD
	 *
	 * @param float $value Value.
	 * @param string $separator Separator.
	 * @param bool $cents Cents.
	 * @param bool $trim Trim.
	 * @return string Value.
	 */
	public static function usd(var value, const string separator=",", const bool cents=false, const bool trim=false) -> string {
		float money = (float) Cast::toFloat(value);
		let money = round(money, 2);
		string out = "";
		if (money < 0) {
			let out .= "-";
			let money = (float) abs(money);
		}

		// Fancy cents.
		if (unlikely cents && money < 1) {
			let out .= strval(100 * money) . "¢";
		}
		else {
			let out .= "$" . number_format(money, 2, ".", separator);
			if (
				trim &&
				(".00" === substr(out, -3))
			) {
				let out = substr(out, 0, -3);
			}
		}

		return out;
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Generate Nice Card Exp Months
	 *
	 * @param string $format Date Format.
	 * @return array Months.
	 */
	public static function ccExpMonths(string format="m - M") -> array {
		array out = [];
		int x = 1;

		while x < 13 {
			let out[x] = date(
				format,
				strtotime("2000-" . sprintf("%02d", x) . "-01")
			);
			let x++;
		}

		return out;
	}

	/**
	 * Generate Nice Card Exp Years
	 *
	 * @param int $length Length.
	 * @return array Months.
	 */
	public static function ccExpYears(int length=10) -> array {
		if (length < 1) {
			let length = 10;
		}

		array out = [];
		int x = 0;
		int currentYear = (int) date("Y");
		int tmpYear;

		while x < length {
			let tmpYear = currentYear + x;
			let out[tmpYear] = tmpYear;
			let x++;
		}

		return out;
	}
}
