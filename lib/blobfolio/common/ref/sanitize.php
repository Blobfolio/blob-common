<?php
//---------------------------------------------------------------------
// SANITIZE
//---------------------------------------------------------------------
// sanitize data



namespace blobfolio\common\ref;

class sanitize {

	//-------------------------------------------------
	// Strip Accents
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Credit Card
	//
	// @param str
	// @return str
	public static function cc(&$ccnum='') {
		//digits only
		cast::string($ccnum);

		$ccnum = preg_replace('/[^\d]/', '', $ccnum);
		$str = $ccnum;

		//different cards have different length requirements
		switch (\blobfolio\common\mb::substr($ccnum, 0, 1)) {
			//Amex
			case 3:
				if (\blobfolio\common\mb::strlen($ccnum) !== 15 || !preg_match('/3[47]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			//Visa
			case 4:
				if (!in_array(\blobfolio\common\mb::strlen($ccnum), array(13,16))) {
					$ccnum = false;
					return false;
				}
				break;
			//MC
			case 5:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || !preg_match('/5[1-5]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			//Disc
			case 6:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || intval(\blobfolio\common\mb::substr($ccnum, 0, 4)) !== 6011) {
					$ccnum = false;
					return false;
				}
				break;
			//There is nothing else...
			default:
				$ccnum = false;
				return false;
		}

		//MOD10 checks
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

	//-------------------------------------------------
	// Country
	//
	// @param str
	// @return str
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
				//maybe a name?
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

	//-------------------------------------------------
	// Sanitize CSV
	//
	// @param str
	// @return str
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

			//strip existing double quotes
			while (false !== \blobfolio\common\mb::strpos($str, '""')) {
				$str = str_replace('""', '"', $str);
			}

			//double quotes
			$str = str_replace('"', '""', $str);
		}

		return true;
	}

	//-------------------------------------------------
	// Datetime
	//
	// @param date
	// @return date
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

	//-------------------------------------------------
	// Date
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Domain Name
	//
	// @param str
	// @return str
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

			//we only want ASCII domains
			if ($host !== filter_var($host, FILTER_SANITIZE_URL)) {
				$str = '';
				return true;
			}

			//does our host kinda match domain standards?
			if (!preg_match('/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})$/', $host)) {
				$str = '';
				return true;
			}

			$str = $host;
		}

		return true;
	}

	//-------------------------------------------------
	// Email
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Sanitize File Extension
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// HTML
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Get Domain Host
	//
	// @param domain
	// @param keep www
	// @return host or false
	public static function hostname(string &$domain, bool $www=false) {
		cast::string($domain);
		static::whitespace($domain);
		mb::strtolower($domain);

		if (!\blobfolio\common\mb::strlen($domain)) {
			$domain = false;
			return false;
		}

		//maybe it is a full URL
		$host = parse_url($domain, PHP_URL_HOST);

		//nope...
		if (is_null($host)) {
			$host = $domain;
			//maybe there's a path?
			if (false !== \blobfolio\common\mb::strpos($host, '/')) {
				$host = explode('/', $host);
				$host = \blobfolio\common\data::array_pop_top($host);
			}
			//and/or a query?
			if (false !== \blobfolio\common\mb::strpos($host, '?')) {
				$host = explode('?', $host);
				$host = \blobfolio\common\data::array_pop_top($host);
			}
		}
		else {
			$domain = $host;
		}

		//remove leading www.
		if (\blobfolio\common\mb::strlen($host) && !$www) {
			$host = preg_replace('/^www\./', '', $host);
		}

		if (!$host) {
			$host = false;
		}

		return true;
	}

	//-------------------------------------------------
	// IP
	//
	// @param str
	// @param allow restricted range?
	// @return str
	public static function ip(&$str='', bool $restricted=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ip($str[$k], $restricted);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);

			//start by getting rid of obviously bad data
			$str = preg_replace('/[^\d\.\:a-f]/', '', $str);

			//try to compact ipv6
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

	//-------------------------------------------------
	// JS Variable
	//
	// @param str
	// @param quote type
	// @return str
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

			if ($quote === "'") {
				$str = str_replace("'", "\'", $str);
			}
			elseif ($quote === '"') {
				$str = str_replace('"', '\"', $str);
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Sanitize MIME type
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Sanitize name (like a person's name)
	//
	// @param name
	// @return name
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

	//-------------------------------------------------
	// Password
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Printable
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// Canadian Province
	//
	// @param str
	// @return str
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
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::PROVINCES))) {
					$str = '';
				}
			}
		}

		return true;
	}

	//-------------------------------------------------------
	// Quotes
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// State
	//
	// @param str
	// @return str
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
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::STATES))) {
					$str = '';
				}
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Timezone
	//
	// @param timezone
	// @return timezone
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

	//-------------------------------------------------
	// Force a value to fall within a range
	//
	// @param value
	// @param min
	// @param max
	// @return value
	public static function to_range(&$value, $min=null, $max=null) {

		//max sure min/max are in the right order
		if (
			!is_null($min) &&
			!is_null($max) &&
			$min > $max
		) {
			\blobfolio\common\data::switcheroo($min, $max);
		}

		//recursive
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

	//-------------------------------------------------
	// URL
	//
	// @param str
	// @return str
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

	//-------------------------------------------------
	// UTF-8
	//
	// @param str
	// @return str
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

	//-------------------------------------------------------
	// Whitespace
	//
	// @param str
	// @param number consecutive linebreaks allowed
	// @return str
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
				$str = trim(preg_replace('/\s+/u', ' ', $str));
				return true;
			}

			//sanitize newlines
			$str = trim($str);
			$str = str_replace("\r\n", "\n", $str);
			$str = preg_replace('/\v/u', "\n", $str);

			//now go through line by line
			$str = explode("\n", $str);
			static::whitespace($str);
			$str = implode("\n", $str);

			$str = preg_replace('/\n{' . ($newlines + 1) . ',}/', str_repeat("\n", $newlines), $str);

			$str = trim($str);
		}

		return true;
	}

	//-------------------------------------------------------
	// Whitespace (allow new lines)
	//
	// @param str
	// @param consecutive linebreaks allowed
	// @return str
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

	//-------------------------------------------------
	// Zip5
	//
	// @param str
	// @return str
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

			if ($str === '00000') {
				$str = '';
			}
		}

		return true;
	}
}

?>