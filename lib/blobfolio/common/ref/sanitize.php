<?php
/**
 * Sanitizing - By Reference
 *
 * Functions for sanitizing data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

class sanitize {

	/**
	 * Strip Accents
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function accents(&$str) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::accents($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (preg_match('/[\x80-\xff]/', $str)) {
				$str = strtr($str, \blobfolio\common\constants::ACCENT_CHARS);
			}
		}

		return true;
	}

	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return string|bool Card number or false.
	 */
	public static function cc(&$ccnum='') {
		// Digits only.
		cast::string($ccnum, true);
		$ccnum = preg_replace('/[^\d]/', '', $ccnum);
		$str = $ccnum;

		// Different cards have different length requirements.
		switch (\blobfolio\common\mb::substr($ccnum, 0, 1)) {
			// Amex.
			case 3:
				if (\blobfolio\common\mb::strlen($ccnum) !== 15 || !preg_match('/3[47]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			// Visa.
			case 4:
				if (!in_array(\blobfolio\common\mb::strlen($ccnum), array(13,16), true)) {
					$ccnum = false;
					return false;
				}
				break;
			// MC.
			case 5:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || !preg_match('/5[1-5]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			// Disc.
			case 6:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || intval(\blobfolio\common\mb::substr($ccnum, 0, 4)) !== 6011) {
					$ccnum = false;
					return false;
				}
				break;
			// There is nothing else...
			default:
				$ccnum = false;
				return false;
		}

		// MOD10 checks.
		$dig = \blobfolio\common\mb::str_split($ccnum);
		$numdig = count($dig);
		$j = 0;
		for ($i = ($numdig - 2); $i >= 0; $i -= 2) {
			$dbl[$j] = $dig[$i] * 2;
			$j++;
		}
		$dblsz = count($dbl);
		$validate = 0;
		for ($i = 0; $i < $dblsz; $i++) {
			$add = \blobfolio\common\mb::str_split($dbl[$i]);
			for ($j = 0; $j < count($add); $j++) {
				$validate += $add[$j];
			}
			$add = '';
		}
		for ($i = ($numdig - 1); $i >= 0; $i -= 2) {
			$validate += $dig[$i];
		}

		if (intval(\blobfolio\common\mb::substr($validate, -1, 1)) === 0) {
			$ccnum = $str;
		}
		else {
			$ccnum = false;
		}

		return true;
	}

	/**
	 * Country
	 *
	 * @param string $str Country.
	 * @return string ISO country code.
	 */
	public static function country(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::country($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);
			if (!isset(\blobfolio\common\constants::COUNTRIES[$str])) {
				// Maybe a name?
				$found = false;
				foreach (\blobfolio\common\constants::COUNTRIES as $k=>$v) {
					if (\blobfolio\common\mb::strtoupper($v['name']) === $str) {
						$str = $k;
						$found = true;
						break;
					}
				}
				if (!$found) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * CSV Cell Data
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function csv(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::csv($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			static::whitespace($str);

			// Strip existing double quotes.
			while (false !== \blobfolio\common\mb::strpos($str, '""')) {
				$str = str_replace('""', '"', $str);
			}

			// Double quotes.
			$str = str_replace('"', '""', $str);
		}

		return true;
	}

	/**
	 * Datetime
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function datetime(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::datetime($str[$k]);
			}
		}
		else {
			if (is_int($str)) {
				$str = date('Y-m-d H:i:s', $str);
			}

			cast::string($str);

			if (
				!\blobfolio\common\mb::strlen($str) ||
				\blobfolio\common\mb::substr($str, 0, 10) === '0000-00-00' ||
				false === $str = strtotime($str)
			) {
				$str = '0000-00-00 00:00:00';
			}
			else {
				$str = date('Y-m-d H:i:s', $str);
			}
		}

		return true;
	}

	/**
	 * Date
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function date(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::date($str[$k]);
			}
		}
		else {
			static::datetime($str);
			$str = \blobfolio\common\mb::substr($str, 0, 10);
		}

		return true;
	}

	/**
	 * Domain Name.
	 *
	 * This locates the domain name portion of a URL,
	 * removes leading "www." subdomains, and ignores
	 * IP addresses.
	 *
	 * @param string $str Domain.
	 * @return string Domain.
	 */
	public static function domain(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::domain($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (false === $host = \blobfolio\common\sanitize::hostname($str)) {
				$str = '';
				return true;
			}

			// We only want ASCII domains.
			if (filter_var($host, FILTER_SANITIZE_URL) !== $host) {
				$str = '';
				return true;
			}

			// Does our host kinda match domain standards?
			// @codingStandardsIgnoreStart
			if (!preg_match('/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})$/', $host)) {
				$str = '';
				return true;
			}
			// @codingStandardsIgnoreEnd

			$str = $host;
		}

		return true;
	}

	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @return string Email.
	 */
	public static function email(&$str=null) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::email($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			mb::strtolower($str);
			$str = str_replace(array('"',"'"), '', filter_var($str, FILTER_SANITIZE_EMAIL));

			if (
				!filter_var($str, FILTER_VALIDATE_EMAIL) ||
				!preg_match('/^.+\@.+\..+$/', $str)
			) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * File Extension
	 *
	 * @param string $str Extension.
	 * @return string Extension.
	 */
	public static function file_extension(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::file_extension($str[$k]);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);
			static::whitespace($str);
			$str = ltrim($str, '*. ');
			$str = preg_replace( '/\s/u', '', $str );
		}

		return true;
	}

	/**
	 * HTML
	 *
	 * @param string $str HTML.
	 * @return string HTML.
	 */
	public static function html(&$str=null) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::html($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		return true;
	}

	/**
	 * Hostname
	 *
	 * @param string $domain Hostname.
	 * @param bool $www Keep leading www.
	 * @return string|bool Hostname or false.
	 */
	public static function hostname(string &$domain, bool $www=false) {
		cast::string($domain, true);
		static::whitespace($domain);
		mb::strtolower($domain);

		if (!\blobfolio\common\mb::strlen($domain)) {
			$domain = false;
			return false;
		}

		// Maybe it is a full URL?
		$host = parse_url($domain, PHP_URL_HOST);

		// Nope...
		if (!$host) {
			$host = $domain;
			// Maybe there's a path?
			if (false !== \blobfolio\common\mb::strpos($host, '/')) {
				$host = explode('/', $host);
				$host = \blobfolio\common\data::array_pop_top($host);
			}
			// And/or a query?
			if (false !== \blobfolio\common\mb::strpos($host, '?')) {
				$host = explode('?', $host);
				$host = \blobfolio\common\data::array_pop_top($host);
			}
		}

		// Remove leading www.
		if (\blobfolio\common\mb::strlen($host) && !$www) {
			$host = preg_replace('/^www\./', '', $host);
		}

		if (!$host) {
			$host = false;
		}

		$domain = $host;

		return true;
	}

	/**
	 * IP Address
	 *
	 * @param string $str IP.
	 * @param bool $restricted Allow private/restricted values.
	 * @return string IP.
	 */
	public static function ip(&$str='', bool $restricted=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ip($str[$k], $restricted);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);

			// Start by getting rid of obviously bad data.
			$str = preg_replace('/[^\d\.\:a-f]/', '', $str);

			// Try to compact IPv6.
			if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$str = inet_ntop(inet_pton($str));
			}
			elseif (!filter_var($str, FILTER_VALIDATE_IP)) {
				$str = '';
			}

			if (
				!$restricted &&
				\blobfolio\common\mb::strlen($str) &&
				!filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
			) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * JS Variable
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @return string String.
	 */
	public static function js(&$str='', $quote="'") {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::js($str[$k], $quote);
			}
		}
		else {
			cast::string($str);
			sanitize::quotes($str);
			sanitize::whitespace($str);

			if ("'" === $quote) {
				$str = str_replace("'", "\'", $str);
			}
			elseif ('"' === $quote) {
				$str = str_replace('"', '\"', $str);
			}
		}

		return true;
	}

	/**
	 * IANA MIME Type
	 *
	 * @param string $str MIME.
	 * @return string MIME.
	 */
	public static function mime(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::mime($str[$k]);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);
			$str = preg_replace('/[^-+*.a-z0-9\/]/', '', $str);
		}

		return true;
	}

	/**
	 * (Person's) Name
	 *
	 * A bit of a fool's goal, but this will attempt to
	 * strip out obviously bad data and convert to title
	 * casing.
	 *
	 * @param string $str Name.
	 * @return string Name.
	 */
	public static function name(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::name($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			static::whitespace($str);
			$str = preg_replace('/[^\p{L}\p{Zs}\p{Pd}\d\'\"\,\.]/u', '', $str);
			static::whitespace($str);
			mb::ucwords($str);
		}

		return true;
	}

	/**
	 * Password
	 *
	 * This simply removes excess whitespace and
	 * non-printable characters, which are likely
	 * only present because of user error.
	 *
	 * @param string $str Password.
	 * @return string Password.
	 */
	public static function password(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::password($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::printable($str);
			static::whitespace($str);
		}

		return true;
	}

	/**
	 * Printable
	 *
	 * Remove non-printable characters (except spaces).
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function printable(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::printable($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = preg_replace('/[^[:print:]]/u', '', $str);
		}

		return true;
	}

	/**
	 * Canadian Province
	 *
	 * @param string $str Province.
	 * @return string Province.
	 */
	public static function province(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::province($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);

			if (!isset(\blobfolio\common\constants::PROVINCES[$str])) {
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::PROVINCES), true)) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * Quotes
	 *
	 * Replace those damn curly quotes with the straight
	 * ones Athena intended!
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function quotes(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::quotes($str[$k]);
			}
		}
		else {
			cast::string($str);
			$from = array_keys(\blobfolio\common\constants::QUOTE_CHARS);
			$to = array_values(\blobfolio\common\constants::QUOTE_CHARS);
			$str = str_replace($from, $to, $str);
		}

		return true;
	}

	/**
	 * US State/Territory
	 *
	 * @param string $str State.
	 * @return string State.
	 */
	public static function state(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::state($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);

			if (!isset(\blobfolio\common\constants::STATES[$str])) {
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::STATES), true)) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * SVG
	 *
	 * @param string $str SVG code.
	 * @param array $tags Additional whitelist tags.
	 * @param array $attr Additional whitelist attributes.
	 * @return string SVG code.
	 */
	public static function svg(&$str='', $tags=null, $attr=null) {
		cast::string($str, true);
		cast::array($tags);
		cast::array($attr);

		$tags = array_merge(\blobfolio\common\constants::SVG_WHITELIST_TAGS, $tags);
		mb::strtolower($tags);
		$tags = array_filter($tags, 'strlen');
		$tags = array_unique($tags);
		sort($tags);

		$attr = array_merge(\blobfolio\common\constants::SVG_WHITELIST_ATTR, $attr);
		mb::strtolower($attr);
		$attr = array_filter($attr, 'strlen');
		$attr = array_unique($attr);
		sort($attr);

		$dom = \blobfolio\common\dom::load_svg($str);
		$svg = $dom->getElementsByTagName('svg');
		if (!$svg->length) {
			$str = '';
			return false;
		}

		// Remove invalid tags and attributes.
		$tmp = $dom->getElementsByTagName('*');
		for ($x = $tmp->length - 1; $x >= 0; $x--) {
			$tag = \blobfolio\common\mb::strtolower($tmp->item($x)->tagName);
			$tag2 = false;
			// Maybe it is namespaced?
			if (false !== $pos = \blobfolio\common\mb::strpos($tag, ':')) {
				$tag2 = \blobfolio\common\mb::substr($tag, $pos + 1);
			}
			if (
				!in_array($tag, $tags, true) &&
				(false === $tag2 || !in_array($tag2, $tags, true))
			) {
				\blobfolio\common\dom::remove_node($tmp->item($x));
				continue;
			}

			// Now go through attributes.
			for ($y = $tmp->item($x)->attributes->length - 1; $y >= 0; $y--) {
				$att = $tmp->item($x)->attributes->item($y);
				$attName = \blobfolio\common\mb::strtolower($att->name);

				// First check, does it match straight off?
				if (true === ($valid = (preg_match('/^(xmlns\:|data\-)/', $attName) || in_array($attName, $attr, true)))) {

					// Strip \0.
					$attValue = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $att->value);
					if ($attValue !== $att->value) {
						$tmp->item($x)->setAttribute($att->name, $attValue);
					}

					format::decode_entities($attValue);

					// Strip scripts.
					if (!strlen($attValue) || preg_match('/(?:\w+script):/xi', $attValue)) {
						$valid = false;
					}
				}

				if (!$valid) {
					$tmp->item($x)->removeAttribute($att->name);
				}
			}
		}

		$str = \blobfolio\common\dom::save_svg($dom);
		return strlen($str) > 0;
	}

	/**
	 * Timezone
	 *
	 * @param string $str Timezone.
	 * @return string Timezone or UTC on failure.
	 */
	public static function timezone(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::timezone($str[$k]);
			}
		}
		else {
			try {
				mb::strtoupper($str);
				static::whitespace($str);

				$timezones = \DateTimeZone::listIdentifiers();

				$found = false;
				foreach ($timezones as $timezone) {
					if (\blobfolio\common\mb::strtoupper($timezone) === $str) {
						$str = $timezone;
						$found = true;
						break;
					}
				}

				if (!$found) {
					$str = 'UTC';
				}
			} catch (\Throwable $e) {
				$str = 'UTC';
			}
		}

		return true;
	}

	/**
	 * Confine a Value to a Range
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Value.
	 */
	public static function to_range(&$value, $min=null, $max=null) {

		// Make sure min/max are in the right order.
		if (
			!is_null($min) &&
			!is_null($max) &&
			$min > $max
		) {
			\blobfolio\common\data::switcheroo($min, $max);
		}

		// Recursive.
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_range($v, $min, $max);
			}
		}
		else {
			$original = $value;

			try {
				if (!is_null($min) && $value < $min) {
					$value = $min;
				}
				if (!is_null($max) && $value > $max) {
					$value = $max;
				}
			} catch (\Throwable $e) {
				$value = $original;
			}
		}

		return true;
	}

	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $str URL.
	 * @return string URL.
	 */
	public static function url(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::url($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = filter_var($str, FILTER_SANITIZE_URL);
			if (preg_match('/^\/\//', $str)) {
				$str = "https:$str";
			}
			if (!filter_var($str, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function utf8(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::utf8($str[$k]);
			}
		}
		elseif (!is_numeric($str) && !is_bool($str)) {
			try {
				$str = (string) $str;
			} catch (\Throwable $e) {
				$str = '';
			}

			$str = \ForceUTF8\Encoding::toUTF8($str);
			$str = (1 === @preg_match('/^./us', $str)) ? $str : '';
		}

		return true;
	}

	/**
	 * Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space, and constrict consecutive newlines.
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace(&$str='', int $newlines=0) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::whitespace($str[$k], $newlines);
			}
		}
		else {
			cast::string($str);
			static::to_range($newlines, 0);

			if (!$newlines) {
				$str = preg_replace('/\s+/u', ' ', $str);
				$str = preg_replace('/^\s+/u', '', $str);
				$str = preg_replace('/\s+$/u', '', $str);
				return true;
			}

			// Sanitize newlines.
			$str = preg_replace('/^\s+/u', '', $str);
			$str = preg_replace('/\s+$/u', '', $str);
			$str = str_replace("\r\n", "\n", $str);
			$str = preg_replace('/\v/u', "\n", $str);

			// Now go through line by line.
			$str = explode("\n", $str);
			static::whitespace($str);
			$str = implode("\n", $str);

			$str = preg_replace('/\n{' . ($newlines + 1) . ',}/', str_repeat("\n", $newlines), $str);

			$str = trim($str);
		}

		return true;
	}

	/**
	 * Whitespace Multiline
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace_multiline(&$str='', int $newlines=1) {
		static::to_range($newlines, 1);

		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::whitespace_multiline($str[$k], $newlines);
			}
		}
		else {
			static::whitespace($str, $newlines);
		}

		return true;
	}

	/**
	 * US ZIP5
	 *
	 * @param string $str ZIP Code.
	 * @return string ZIP Code.
	 */
	public static function zip5(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::zip5($str[$k]);
			}
		}
		else {
			cast::string($zip5);
			$str = preg_replace('/[^\d]/', '', $str);

			if (\blobfolio\common\mb::strlen($str) < 5) {
				$str = sprintf('%05d', $str);
			}
			elseif (\blobfolio\common\mb::strlen($str) > 5) {
				$str = \blobfolio\common\mb::substr($str, 0, 5);
			}

			if ('00000' === $str) {
				$str = '';
			}
		}

		return true;
	}
}


