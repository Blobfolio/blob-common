//<?php
/**
 * Blobfolio: Retail
 *
 * E-commerce, retail, etc.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

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
		float money = (float) \Blobfolio\Cast::toFloat(value);
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
	 * Shipping Carrier
	 *
	 * Use consistent, appropriate casing for shipping carriers.
	 *
	 * @param string $carrier Carrier.
	 * @return void Nothing.
	 */
	public static function niceCarrier(string carrier) -> string {
		switch (preg_replace("/[^a-z]/", "", strtolower(carrier))) {
			case "abf":
			case "arcbest":
			case "arcbestfreight":
				return "ABF";
			case "dhl":
			case "dalseyhillblomandlynn":
			case "dalseyhillblomlynn":
			case "dhlexpress":
			case "dhlworldwide":
				return "DHL";
			case "fedex":
			case "federalexpress":
				return "FedEx";
			case "ups":
			case "unitedparcelservice":
				return "UPS";
			case "postoffice":
			case "unitedstatespostalservice":
			case "usps":
				return "USPS";
		}

		return "Other";
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

	/**
	 * EAN13
	 *
	 * Almost exactly like UPC, but not quite.
	 *
	 * @param string $str String.
	 * @param bool $formatted Formatted.
	 * @return string EAN.
	 */
	public static function niceEan(string str, const bool formatted=false) -> string {
		// Numbers only.
		let str = preg_replace("/[^\d]/", "", str);
		let str = str_pad(str, 13, "0", STR_PAD_LEFT);

		// Trim leading zeroes if it is too long.
		while (strlen(str) > 13 && (0 === strpos(str, "0"))) {
			let str = substr(str, 1);
		}

		if (strlen(str) !== 13 || ("0000000000000" === str)) {
			return "";
		}

		// Try to pad it.
		while (!self::checkGtin(str) && strlen(str) <= 18) {
			let str = "0" . str;
		}

		if (!self::checkGtin(str)) {
			return "";
		}

		// Last thing, format?
		if (formatted) {
			let str = preg_replace("/^(\d{1})(\d{6})(\d{6})$/", "$1-$2-$3", str);
		}

		return str;
	}

	/**
	 * ISBN
	 *
	 * Validate an ISBN 10 or 13.
	 *
	 * @see {https://www.isbn-international.org/export_rangemessage.xml}
	 *
	 * @param string $str String.
	 * @return string ISBN.
	 */
	public static function niceIsbn(string str) -> string {
		let str = strtoupper(str);
		let str = preg_replace("/[^\dX]/", "", str);

		int length = strlen(str);

		// Zero-pad.
		if (length < 11) {
			let str = str_pad(str, 10, "0", STR_PAD_LEFT);
			let length = 10;
		}
		elseif (length < 13) {
			let str = preg_replace("/[^\d]/", "", str);
			let str = str_pad(str, 13, "0", STR_PAD_LEFT);
			let length = 13;
		}

		if (
			("0000000000" === str) ||
			("0000000000000" === str) ||
			length >= 14
		) {
			return "";
		}

		// Validate a 10.
		if (length === 10) {
			int checksum = 0;
			int x = 0;
			string current;
			while x < 9 {
				let current = substr(str, x, 1);
				if ("X" === current) {
					let checksum += 10 * (10 - x);
				}
				else {
					let checksum += intval(current) * (10 - x);
				}

				let x++;
			}

			let checksum = 11 - checksum % 11;

			var check1;
			var check2;

			if (10 === checksum) {
				let check1 = "X";
			}
			elseif (11 === checksum) {
				let check1 = 0;
			}
			else {
				let check1 = (int) checksum;
			}

			string last = substr(str, -1);
			if ("X" === last) {
				let check2 = "X";
			}
			else {
				let check2 = (int) last;
			}

			if (check2 !== check1) {
				return "";
			}
		}
		// Validate a 13.
		elseif (!self::checkGtin(str)) {
			return "";
		}

		return str;
	}

	/**
	 * Nice ccBrand
	 *
	 * @param string $brand Brand.
	 * @return string Brand.
	 */
	public static function niceCcBrand(string brand) -> string {
		string test = trim(strtolower(brand));
		array nice = [
			"amex": "AMEX",
			"discover": "Discover",
			"jcb": "JCB",
			"mc": "MC",
			"other": "Other",
			"visa": "Visa"
		];

		// Maybe it is already looking good.
		if (isset(nice[test])) {
			return nice[test];
		}

		// Do it the hard way. Haha.
		if (0 === strpos(test, "american")) {
			return "AMEX";
		}
		elseif (0 === strpos(test, "master")) {
			return "MC";
		}
		elseif (0 === strpos(test, "disc")) {
			return "Discover";
		}
		elseif (0 === strpos(test, "japan")) {
			return "JCB";
		}

		return "Other";
	}

	/**
	 * Nice ccLast4
	 *
	 * @param string $last4 Last4.
	 * @return string Last4.
	 */
	public static function niceCcLast4(string last4) -> string {
		let last4 = preg_replace("/[^\d]/", "", last4);
		int length = strlen(last4);
		if (length > 4) {
			return "";
		}
		elseif (length < 4) {
			let last4 = str_pad(last4, 4, "0", STR_PAD_LEFT);
		}

		if ("0000" === last4) {
			return "";
		}

		return last4;
	}

	/**
	 * Nice Name
	 *
	 * This helps sanely format a human name.
	 *
	 * @param string $str Name.
	 * @param bool $trusted Trusted.
	 * @return string Name.
	 */
	public static function niceName(string str, const bool trusted=false) -> string {
		let str = \Blobfolio\Strings::niceText(str, trusted);
		let str = preg_replace("/[^\p{L}\p{Zs}\p{Pd}\d'\"\,\.]/u", "", str);
		let str = \Blobfolio\Strings::whitespace(str, 0, true);
		return \Blobfolio\Strings::toTitle(str, true);
	}

	/**
	 * Nice Password
	 *
	 * This helps ensure password characters make a kind of sense.
	 *
	 * @param string $str Password.
	 * @param bool $trusted Trusted.
	 * @return string Password.
	 */
	public static function nicePassword(string str, const bool trusted=false) -> string {
		let str = \Blobfolio\Strings::printable(str, trusted);
		let str = \Blobfolio\Strings::controlChars(str, true);
		return \Blobfolio\Strings::whitespace(str, 0, true);
	}

	/**
	 * Shipping URL
	 *
	 * @param string $carrier Carrier.
	 * @param string $shipping_id Shipping ID.
	 * @return string|bool URL or false.
	 */
	public static function niceShippingUrl(string carrier, string shipping_id) -> string {
		let carrier = self::niceCarrier(carrier);
		let shipping_id = preg_replace("/\s/u", "", shipping_id);
		let shipping_id = urlencode(shipping_id);

		switch (carrier) {
			case "ABF":
				return "https://arcb.com/tools/tracking.html#/" . shipping_id;
			case "FedEx":
				return "https://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=" . shipping_id;
			case "UPS":
				return "https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=" . shipping_id;
			case "USPS":
				return "https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=" . shipping_id;
		}

		return "";
	}

	/**
	 * UPC-A
	 *
	 * @param string $str String.
	 * @param bool $formatted Formatted.
	 * @return string UPC.
	 */
	public static function niceUpc(string str, const bool formatted=false) -> string {
		let str = preg_replace("/[^\d]/", "", str);
		let str = str_pad(str, 12, "0", STR_PAD_LEFT);

		// Trim leading zeroes if it is too long.
		while (strlen(str) > 12 && (0 === strpos(str, "0"))) {
			let str = substr(str, 1);
		}

		if ((strlen(str) !== 12) || ("000000000000" === str)) {
			return "";
		}

		// Temporarily add an extra 0 to validate the GTIN.
		let str = "0" . str;
		if (self::checkGtin(str)) {
			let str = substr(str, 1);
		}
		else {
			return "";
		}

		// Last thing, format?
		if (formatted) {
			let str = preg_replace(
				"/^(\d)(\d{5})(\d{5})(\d)$/",
				"$1-$2-$3-$4",
				str
			);
		}

		return str;
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

	/**
	 * Validate GTIN
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function checkGtin(string str) -> bool {
		let str = preg_replace("/[^\d]/", "", str);
		array code = (array) str_split(substr(str, 0, -1));
		int check = (int) substr(str, -1);

		int total = 0;
		int x = count(code) - 1;
		while x >= 0 {
			let total += ((x % 2) * 2 + 1) * code[x];
			let x--;
		}

		int checksum = (10 - (total % 10));

		return checksum === check;
	}
}
