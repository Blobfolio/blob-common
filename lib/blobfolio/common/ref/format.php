<?php
//---------------------------------------------------------------------
// FORMAT
//---------------------------------------------------------------------
// format data



namespace blobfolio\common\ref;

class format {

	//-------------------------------------------------
	// Array to Indexed
	//
	// convert a k=>v array to an array containing as
	// values the k=>v pair
	//
	// @param array
	// @return true
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

	//-------------------------------------------------
	// IP to Number
	//
	// @param ip
	// @return number or false
	public static function ip_to_number(&$ip) {
		cast::string($ip);

		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			$ip = false;
			return true;
		}

		//ipv4 is easy
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$ip = ip2long($ip);
			return true;
		}

		//ipv6 is a little more roundabout
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			try {
				$ip_n = inet_pton($ip);
				$bin = '';
				for ($bit = \blobfolio\common\mb::strlen($ip_n) - 1; $bit >= 0; $bit--) {
					$bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
				}
				$dec = '0';
				for ($i = 0; $i < \blobfolio\common\mb::strlen($bin); $i++) {
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

	//-------------------------------------------------
	// Money
	//
	// @param value
	// @param cents
	// @param separator
	// @return value
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

			if ($value >= 1 || $cents === false) {
				$value = ($negative ? '-' : '') . '$' . number_format($value, 2, '.', $separator);
			}
			else {
				$value = ($negative ? '-' : '') . (100 * $value) . '¢';
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Phone
	//
	// @param str
	// @param mobile only
	// @return str
	public static function phone(&$str='', bool $mobile=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::phone($str[$k], $mobile);
			}
		}
		else {
			cast::string($str);
			sanitize::whitespace($str);

			if (!\blobfolio\common\mb::strlen($str)) {
				$str = '';
				return false;
			}

			try {
				$strUtil = \libphonenumber\PhoneNumberUtil::getInstance();
				$str = $strUtil->parse($str, 'US');
				if (!$strUtil->isValidNumber($str)) {
					$str = '';
					return false;
				}

				//should be mobile
				if ($mobile) {
					$type = $strUtil->getNumberType($str);
					if (
						\libphonenumber\PhoneNumberType::MOBILE !== $type &&
						\libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE !== $type
					) {
						$str = '';
						return false;
					}
				}

				$str = $strUtil->format($str, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
			} catch (\Throwable $e) {
				$str = '';
				return false;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Convert Timezones
	//
	// @param date
	// @param from
	// @param to
	// @return true
	public static function to_timezone(&$date, $from='UTC', $to='UTC') {
		cast::string($date);
		cast::string($from);
		cast::string($to);

		sanitize::datetime($date);
		sanitize::timezone($from);
		sanitize::timezone($to);

		if ($date === '0000-00-00 00:00:00' || $from === $to) {
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

?>