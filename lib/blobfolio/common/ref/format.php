<?php
/**
 * Formatting - By Reference
 *
 * Functions for formatting data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

class format {

	/**
	 * Create Index Array
	 *
	 * This will convert a {k:v} associative array
	 * into an indexed array with {key: k, value: v}
	 * as the values. Useful when exporting sorted
	 * data to Javascript, which doesn't preserve
	 * object key ordering.
	 *
	 * @param array $arr Array.
	 * @return bool True.
	 */
	public static function array_to_indexed(&$arr) {
		cast::array($arr);
		if (count($arr)) {
			$out = array();
			foreach ($arr as $k=>$v) {
				$out[] = array(
					'key'=>$k,
					'value'=>$v
				);
			}
			$arr = $out;
		}

		return true;
	}

	/**
	 * Decode HTML Entities
	 *
	 * Decode all HTML entities back into their char
	 * counterparts, recursively until every last one
	 * is captured.
	 *
	 * @param string $str String.
	 * @return bool True.
	 */
	public static function decode_entities(&$str='') {
		cast::string($str, true);

		$last = '';
		while ($str !== $last) {
			$last = $str;

			$str = preg_replace_callback('/&#([0-9]+);/', array(get_called_class(), 'decode_entities_chr'), $str);
			$str = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', array(get_called_class(), 'decode_entities_hex'), $str);
			$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
		}

		return true;
	}

	/**
	 * Decode HTML Entities Callback - Chr
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	protected static function decode_entities_chr($matches) {
		return chr($matches[1]);
	}

	/**
	 * Decode HTML Entities Callback - Hex
	 *
	 * @param array $matches Matches.
	 * @return string ASCII.
	 */
	protected static function decode_entities_hex($matches) {
		return chr(hexdec($matches[1]));
	}

	/**
	 * IP to Number
	 *
	 * @param string $ip IP.
	 * @return bool True.
	 */
	public static function ip_to_number(&$ip) {
		cast::string($ip, true);

		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			$ip = false;
			return true;
		}

		// IPv4 is easy.
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$ip = ip2long($ip);
			return true;
		}

		// IPv6 is a little more roundabout.
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			try {
				$ip_n = inet_pton($ip);
				$bin = '';
				for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
					$bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
				}
				$dec = '0';
				for ($i = 0; $i < strlen($bin); $i++) {
					$dec = bcmul($dec, '2', 0);
					$dec = bcadd($dec, $bin[$i], 0);
				}
				$ip = $dec;
				return true;
			} catch (\Throwable $e) {
				$ip = false;
				return false;
			}
		}

		$ip = false;
		return true;
	}

	/**
	 * Money (USD)
	 *
	 * @param float $value Value.
	 * @param bool $cents Return sub-$1 values with ¢.
	 * @param string $separator Separator.
	 * @return bool True.
	 */
	public static function money(&$value=0, bool $cents=false, string $separator='') {
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::money($value[$k], $cents, $separator);
			}
		}
		else {
			cast::float($value);
			$value = round($value, 2);
			$negative = $value < 0;
			if ($negative) {
				$value = abs($value);
			}

			if ($value >= 1 || false === $cents) {
				$value = ($negative ? '-' : '') . '$' . number_format($value, 2, '.', $separator);
			}
			else {
				$value = ($negative ? '-' : '') . (100 * $value) . '¢';
			}
		}

		return true;
	}

	/**
	 * Phone
	 *
	 * @param string $str Phone.
	 * @param string $country Country.
	 * @param array $types Types, e.g. Mobile.
	 * @return bool True.
	 */
	public static function phone(&$str='', $country='', $types=array()) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::phone($str[$k], $country, $types);
			}
		}
		else {
			cast::string($str);
			sanitize::whitespace($str);

			if (!\blobfolio\common\mb::strlen($str)) {
				$str = '';
				return false;
			}

			cast::string($country);
			cast::array($types);

			$str = new \blobfolio\phone\phone($str, $country);
			if (!$str->is_phone($types)) {
				$str = '';
				return false;
			}

			$str = (string) $str;
		}

		return true;
	}

	/**
	 * Convert Timezones
	 *
	 * @param string $date Date.
	 * @param string $from Original Timezone.
	 * @param string $to New Timezone.
	 * @return bool True.
	 */
	public static function to_timezone(&$date, $from='UTC', $to='UTC') {
		cast::string($date, true);
		cast::string($from, true);
		cast::string($to, true);

		sanitize::datetime($date);
		sanitize::timezone($from);
		sanitize::timezone($to);

		if ('0000-00-00 00:00:00' === $date || $from === $to) {
			return true;
		}

		$original = $date;
		try {
			$date_new = new \DateTime($date, new \DateTimeZone($from));
			$date_new->setTimezone(new \DateTimeZone($to));
			$date = $date_new->format('Y-m-d H:i:s');
		} catch (\Throwable $e) {
			$date = $original;
		}

		return true;
	}

}


