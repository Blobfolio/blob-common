<?php
/**
 * Blobfolio: Retail
 *
 * E-commerce, retail, etc.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;



final class Retail {
	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * USD
	 *
	 * @param float $value Value.
	 * @param int $flags Flags.
	 * @return string Value.
	 */
	public static function usd($value, int $flags=1) : string {
		$money = (float) \Blobfolio\Cast::toFloat($value, Shim::FLATTEN);
		$money = \round($money, 2);

		$out = '';
		if ($money < 0) {
			$out .= '-';
			$money = (float) \abs($money);
		}

		// Parse flags.
		$flagsThousands = !! ($flags & Shim::USD_THOUSANDS);
		$flagsCents = !! ($flags & Shim::USD_CENTS);
		$flagsTrim = !! ($flags & Shim::USD_TRIM);

		// Fancy cents.
		if ($flagsCents && $money < 1) {
			$out .= \strval(100 * $money) . '¢';
		}
		else {
			$separator = ',';
			if (! $flagsThousands) {
				$separator = '';
			}

			$out .= '$' . \number_format($money, 2, '.', $separator);

			if (
				$flagsTrim &&
				('.00' === \substr($out, -3))
			) {
				$out = \substr($out, 0, -3);
			}
		}

		return $out;
	}

	/**
	 * Shipping Carrier
	 *
	 * Use consistent, appropriate casing for shipping carriers.
	 *
	 * @param string $carrier Carrier.
	 * @return string Carrier.
	 */
	public static function niceCarrier(string $carrier) : string {
		switch (\preg_replace('/[^a-z]/', '', \strtolower($carrier))) {
			case 'abf':
			case 'arcbest':
			case 'arcbestfreight':
				return 'ABF';
			case 'dhl':
			case 'dalseyhillblomandlynn':
			case 'dalseyhillblomlynn':
			case 'dhlexpress':
			case 'dhlworldwide':
				return 'DHL';
			case 'fedex':
			case 'federalexpress':
				return 'FedEx';
			case 'ups':
			case 'unitedparcelservice':
				return 'UPS';
			case 'postoffice':
			case 'unitedstatespostalservice':
			case 'usps':
				return 'USPS';
		}

		return 'Other';
	}

	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return string Card.
	 */
	public static function niceCc(string $ccnum) : string {
		// Digits only.
		$ccnum = \preg_replace('/[^\d]/', '', $ccnum);
		$ccLength = (int) \strlen($ccnum);
		if ($ccLength < 13 || $ccLength > 16) {
			return '';
		}

		// Different cards have different length requirements.
		switch (\substr($ccnum, 0, 1)) {
			// Amex.
			case '3':
				if ((15 !== $ccLength) || ! \preg_match('/^3[47]/', $ccnum)) {
					return '';
				}
				break;
			// Visa.
			case '4':
				if (13 !== $ccLength && 16 !== $ccLength) {
					return '';
				}
				break;
			// MC.
			case '2':
				if (16 !== $ccLength) {
					return '';
				}
				break;
			case '5':
				if ((16 !== $ccLength) || ! \preg_match('/^5[1-5]/', $ccnum)) {
					return '';
				}
				break;
			// Disc.
			case '6':
				if (
					(16 !== $ccLength) ||
					(0 !== \strpos($ccnum, '6011'))
				) {
					return '';
				}
				break;
		}

		$dbl = array();
		$digits = (array) \str_split($ccnum);
		$i = $ccLength - 2;
		$j = 0;
		$validate = 0;

		// Convert digits to digits.
		foreach ($digits as $k=>$v) {
			$digits[$k] = (int) $v;
		}

		// MOD10 checks.
		while ($i >= 0) {
			$dbl[$j] = $digits[$i] * 2;
			$i -= 2;
			$j++;
		}

		$dblLength = \count($dbl);

		$i = 0;
		while ($i < $dblLength) {
			$add = (array) \str_split($dbl[$i]);
			$addLength = (int) \count($add);
			$j = 0;
			while ($j < $addLength) {
				$validate += (int) $add[$j];
				$j++;
			}
			$i++;
		}

		$i = $ccLength - 1;
		while ($i >= 0) {
			$validate += (int) $digits[$i];
			$i -= 2;
		}

		if (\intval(\substr($validate, -1)) === 0) {
			return $ccnum;
		}

		return '';
	}

	/**
	 * EAN13
	 *
	 * Almost exactly like UPC, but not quite.
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string EAN.
	 */
	public static function niceEan(string $str, int $flags=0) : string {
		// Numbers only.
		$str = \preg_replace('/[^\d]/', '', $str);
		$str = \str_pad($str, 13, '0', \STR_PAD_LEFT);

		// Trim leading zeroes if it is too long.
		while (\strlen($str) > 13 && (0 === \strpos($str, '0'))) {
			$str = \substr($str, 1);
		}

		if (\strlen($str) !== 13 || ('0000000000000' === $str)) {
			return '';
		}

		// Try to pad it.
		while (! self::checkGtin($str) && \strlen($str) <= 18) {
			$str = '0' . $str;
		}

		if (! self::checkGtin($str)) {
			return '';
		}

		// Last thing, format?
		$formatted = !! ($flags & Shim::PRETTY);
		if ($formatted) {
			$str = \preg_replace('/^(\d{1})(\d{6})(\d{6})$/', '$1-$2-$3', $str);
		}

		return $str;
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
	public static function niceIsbn(string $str) : string {
		$str = \strtoupper($str);
		$str = \preg_replace('/[^\dX]/', '', $str);

		$length = \strlen($str);

		// Zero-pad.
		if ($length < 11) {
			$str = \str_pad($str, 10, '0', \STR_PAD_LEFT);
			$length = 10;
		}
		elseif ($length < 13) {
			$str = \preg_replace('/[^\d]/', '', $str);
			$str = \str_pad($str, 13, '0', \STR_PAD_LEFT);
			$length = 13;
		}

		if (
			('0000000000' === $str) ||
			('0000000000000' === $str) ||
			$length >= 14
		) {
			return '';
		}

		// Validate a 10.
		if (10 === $length) {
			$checksum = 0;
			$x = 0;
			while ($x < 9) {
				$current = \substr($str, $x, 1);
				if ('X' === $current) {
					$checksum += 10 * (10 - $x);
				}
				else {
					$checksum += \intval($current) * (10 - $x);
				}

				$x++;
			}

			$checksum = 11 - $checksum % 11;

			if (10 === $checksum) {
				$check1 = 'X';
			}
			elseif (11 === $checksum) {
				$check1 = 0;
			}
			else {
				$check1 = (int) $checksum;
			}

			$last = \substr($str, -1);
			if ('X' === $last) {
				$check2 = 'X';
			}
			else {
				$check2 = (int) $last;
			}

			if ($check2 !== $check1) {
				return '';
			}
		}
		// Validate a 13.
		elseif (! self::checkGtin($str)) {
			return '';
		}

		return $str;
	}

	/**
	 * Nice ccBrand
	 *
	 * @param string $brand Brand.
	 * @return string Brand.
	 */
	public static function niceCcBrand(string $brand) : string {
		// Do it the hard way. Haha.
		switch (\substr(\trim(\strtolower($brand)), 0, 4)) {
			case 'visa':
				return 'Visa';
			case 'mast':
			case 'mc':
				return 'MC';
			case 'amer':
			case 'amex':
				return 'AMEX';
			case 'disc':
				return 'Discover';
			case 'japa':
			case 'jcb':
				return 'JCB';
		}

		return 'Other';
	}

	/**
	 * Nice ccLast4
	 *
	 * @param string $last4 Last4.
	 * @return string Last4.
	 */
	public static function niceCcLast4(string $last4) : string {
		$last4 = \preg_replace('/[^\d]/', '', $last4);
		$length = \strlen($last4);
		if ($length > 4) {
			$last4 = (string) \substr($last4, -4);
		}
		elseif ($length < 4) {
			$last4 = \str_pad($last4, 4, '0', \STR_PAD_LEFT);
		}

		if ('0000' === $last4) {
			return '';
		}

		return $last4;
	}

	/**
	 * Nice Name
	 *
	 * This helps sanely format a human name.
	 *
	 * @param string $str Name.
	 * @param int $flags Flags.
	 * @return string Name.
	 */
	public static function niceName(string $str, int $flags=0) : string {
		$str = \Blobfolio\Strings::niceText($str, 0, ($flags & Shim::TRUSTED));
		$str = \preg_replace("/[^\p{L}\p{Zs}\p{Pd}\d'\"\,\.]/u", '', $str);
		$str = \Blobfolio\Strings::whitespace($str, 0, Shim::TRUSTED);
		return \Blobfolio\Strings::toTitle($str, Shim::TRUSTED);
	}

	/**
	 * Split Name
	 *
	 * Splits a single name into first/last components.
	 *
	 * @param string $str Name.
	 * @param int $flags Flags.
	 * @return array Parts.
	 */
	public static function splitName(string $str, int $flags=0) : array {
		$str = self::niceName($str, ($flags & Shim::TRUSTED));

		$out = array(
			'firstname'=>$str,
			'lastname'=>'',
		);

		// Split on space.
		$start = \mb_strpos($str, ' ', 0, 'UTF-8');
		if (false !== $start) {
			$out['firstname'] = \trim(\mb_substr($str, 0, $start, 'UTF-8'));
			$out['lastname'] = \trim(\mb_substr($str, $start, null, 'UTF-8'));
		}

		return $out;
	}

	/**
	 * Nice Password
	 *
	 * This helps ensure password characters make a kind of sense.
	 *
	 * @param string $str Password.
	 * @param int $flags Flags.
	 * @return string Password.
	 */
	public static function nicePassword(string $str, int $flags=0) : string {
		$str = \Blobfolio\Strings::printable($str, ($flags & Shim::TRUSTED));
		$str = \Blobfolio\Strings::controlChars($str, Shim::TRUSTED);
		return \Blobfolio\Strings::whitespace($str, 0, Shim::TRUSTED);
	}

	/**
	 * Shipping URL
	 *
	 * @param string $carrier Carrier.
	 * @param string $shipping_id Shipping ID.
	 * @return string URL.
	 */
	public static function niceShippingUrl(string $carrier, string $shipping_id) : string {
		$shipping_id = \preg_replace('/\s/u', '', $shipping_id);
		$shipping_id = \urlencode($shipping_id);

		switch (self::niceCarrier($carrier)) {
			case 'ABF':
				return 'https://arcb.com/tools/tracking.html#/' . $shipping_id;
			case 'FedEx':
				return 'https://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=' . $shipping_id;
			case 'UPS':
				return 'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $shipping_id;
			case 'USPS':
				return 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=' . $shipping_id;
		}

		return '';
	}

	/**
	 * UPC-A
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string UPC.
	 */
	public static function niceUpc(string $str, int $flags=0) : string {
		$str = \preg_replace('/[^\d]/', '', $str);
		$str = \str_pad($str, 12, '0', \STR_PAD_LEFT);

		// Trim leading zeroes if it is too long.
		while (\strlen($str) > 12 && (0 === \strpos($str, '0'))) {
			$str = \substr($str, 1);
		}

		if ((\strlen($str) !== 12) || ('000000000000' === $str)) {
			return '';
		}

		// Temporarily add an extra 0 to validate the GTIN.
		$str = '0' . $str;
		if (self::checkGtin($str)) {
			$str = \substr($str, 1);
		}
		else {
			return '';
		}

		// Last thing, format?
		$formatted = !! ($flags & Shim::PRETTY);
		if ($formatted) {
			$str = \preg_replace(
				'/^(\d)(\d{5})(\d{5})(\d)$/',
				'$1-$2-$3-$4',
				$str
			);
		}

		return $str;
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
	public static function ccExpMonths(string $format='m - M') : array {
		$out = array();
		$x = 1;

		while ($x < 13) {
			$out[$x] = \date(
				$format,
				\strtotime('2000-' . \sprintf('%02d', $x) . '-01')
			);
			$x++;
		}

		return $out;
	}

	/**
	 * Generate Nice Card Exp Years
	 *
	 * @param int $length Length.
	 * @return array Months.
	 */
	public static function ccExpYears(int $length=10) : array {
		if ($length < 1) {
			$length = 10;
		}

		$out = array();
		$x = 0;
		$currentYear = (int) \date('Y');

		while ($x < $length) {
			$tmpYear = $currentYear + $x;
			$out[$tmpYear] = $tmpYear;
			$x++;
		}

		return $out;
	}

	/**
	 * Validate GTIN
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function checkGtin(string $str) : bool {
		$str = \preg_replace('/[^\d]/', '', $str);
		$code = (array) \str_split(\substr($str, 0, -1));
		$check = (int) \substr($str, -1);

		$total = 0;
		$x = \count($code) - 1;
		while ($x >= 0) {
			$total += (($x % 2) * 2 + 1) * $code[$x];
			$x--;
		}

		$checksum = (10 - ($total % 10));

		return $checksum === $check;
	}
}
