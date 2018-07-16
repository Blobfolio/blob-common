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

	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return string Card.
	 */
	public static function niceCc(string ccnum) -> string {
		// Digits only.
		let ccnum = preg_replace("/[^\d]/", "", ccnum);
		int ccLength = (int) strlen(ccnum);
		if (ccLength < 13 || ccLength > 16) {
			return "";
		}

		// Different cards have different length requirements.
		switch (substr(ccnum, 0, 1)) {
			// Amex.
			case "3":
				if ((ccLength !== 15) || !preg_match("/^3[47]/", ccnum)) {
					return "";
				}
				break;
			// Visa.
			case "4":
				if (ccLength !== 13 && ccLength !== 16) {
					return "";
				}
				break;
			// MC.
			case "5":
				if ((ccLength !== 16) || !preg_match("/^5[1-5]/", ccnum)) {
					return "";
				}
				break;
			// Disc.
			case "6":
				if (
					(ccLength !== 16) ||
					(0 !== strpos(ccnum, "6011"))
				) {
					return "";
				}
				break;
			// There is nothing else...
			default:
				return "";
		}

		array add;
		array dbl = [];
		array digits = (array) str_split(ccnum);
		int addLength;
		int dblLength;
		int i = ccLength - 2;
		int j = 0;
		int validate = 0;
		var k;
		var v;

		// Convert digits to digits.
		for k, v in digits {
			let digits[k] = (int) v;
		}

		// MOD10 checks.
		while i >= 0 {
			let dbl[j] = digits[i] * 2;
			let i -= 2;
			let j++;
		}

		let dblLength = count(dbl);

		let i = 0;
		while i < dblLength {
			let add = (array) str_split(dbl[i]);
			let addLength = (int) count(add);
			let j = 0;
			while j < addLength {
				let validate += (int) add[j];
				let j++;
			}
			let i++;
		}

		let i = ccLength - 1;
		while i >= 0 {
			let validate += (int) digits[i];
			let i -= 2;
		}

		if (intval(substr(validate, -1)) === 0) {
			return ccnum;
		}

		return "";
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
