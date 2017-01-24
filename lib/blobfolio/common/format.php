<?php
//---------------------------------------------------------------------
// FORMAT
//---------------------------------------------------------------------
// format data



namespace blobfolio\common;

class format {

	//-------------------------------------------------
	// Array to Indexed
	//
	// convert a k=>v array to an array containing as
	// values the k=>v pair
	//
	// @param array
	// @return true
	public static function array_to_indexed($arr) {
		ref\format::array_to_indexed($arr);
		return $arr;
	}

	//-------------------------------------------------
	// CIDR to Range
	//
	// @param cidr
	// @return range or false
	public static function cidr_to_range($cidr) {
		ref\cast::string($cidr);

		$range = array('min'=>0, 'max'=>0);
		$cidr = explode('/', $cidr);

		//ipv4?
		if (filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			if (count($cidr) === 1) {
				$range['min'] = $range['max'] = sanitize::ip($cidr[0]);
			}
			else {
				$range['min'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int) $cidr[1]))));
				$range['max'] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int) $cidr[1])) - 1);
			}
			return $range;
		}

		//ipv6?  of course a little more complicated
		if (filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			if (count($cidr) === 1) {
				$range['min'] = $range['max'] = sanitize::ip($cidr[0]);
				return $range;
			}

			//parse the address into a binary string
			$firstaddrbin = inet_pton($cidr[0]);

			//convert the binary string to a string with hexadecimal characters (bin2hex)
			$tmp = unpack('H*', $firstaddrbin);
			$firstaddrhex = reset($tmp);

			//overwriting first address string to make sure notation is optimal
			$cidr[0] = inet_ntop($firstaddrbin);

			//calculate the number of 'flexible' bits
			$flexbits = 128 - $cidr[1];

			//build the hexadecimal string of the last address
			$lastaddrhex = $firstaddrhex;

			//we start at the end of the string (which is always 32 characters long)
			$pos = 31;
			while ($flexbits > 0) {
				//get the character at this position
				$orig = substr($lastaddrhex, $pos, 1);

				//convert it to an integer
				$origval = hexdec($orig);

				//OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
				$newval = $origval | (pow(2, min(4, $flexbits)) - 1);

				//convert it back to a hexadecimal character
				$new = dechex($newval);

				//and put that character back in the string
				$lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

				//we processed one nibble, move to previous position
				$flexbits -= 4;
				$pos -= 1;
			}

			//convert the hexadecimal string to a binary string (hex2bin)
			$lastaddrbin = pack('H*', $lastaddrhex);

			//and create an IPv6 address from the binary string
			$lastaddrstr = inet_ntop($lastaddrbin);

			//pack and done!
			$range['min'] = sanitize::ip($cidr[0]);
			$range['max'] = sanitize::ip($lastaddrstr);
			return $range;
		}

		return false;
	}

	//-------------------------------------------------
	// Excerpt
	//
	// @param string
	// @param args
	public static function excerpt($str='', $args=null) {
		ref\cast::string($str);
		ref\sanitize::whitespace($str);
		$str = strip_tags($str);

		$options = data::parse_args($args, constants::EXCERPT);
		if ($options['length'] < 1) {
			return '';
		}

		ref\mb::strtolower($options['unit']);
		if (mb::substr($options['unit'], 0, 4) === 'char') {
			$options['unit'] = 'character';
		}
		elseif (mb::substr($options['unit'], 0, 4) === 'word') {
			$options['unit'] = 'word';
		}

		//character limit
		if ($options['unit'] === 'character' && mb::strlen($str) > $options['length']) {
			$str = trim(mb::substr($str, 0, $options['length'])) . $options['suffix'];
		}
		//word limit
		elseif ($options['unit'] === 'word' && mb::substr_count($str, ' ') > $options['length'] - 1) {
			$str = explode(' ', $str);
			$str = array_slice($str, 0, $options['length']);
			$str = implode(' ', $str) . $options['suffix'];
		}

		return $str;
	}

	//-------------------------------------------------
	// Inflect
	//
	// @param count
	// @param single
	// @param plural
	// @return inflection
	public static function inflect($count, string $single, string $plural) {
		if (is_array($count)) {
			$count = count($count);
		}
		else {
			ref\cast::number($count);
		}
		ref\sanitize::utf8($single);
		ref\sanitize::utf8($plural);

		if ($count === (float) 1) {
			return sprintf($single, $count);
		}
		else {
			return sprintf($plural, $count);
		}
	}

	//-------------------------------------------------
	// IP to Number
	//
	// @param ip
	// @return number or false
	public static function ip_to_number($ip) {
		ref\format::ip_to_number($ip);
		return $ip;
	}

	//-------------------------------------------------
	// Money
	//
	// @param value
	// @param cents
	// @param separator
	// @return value
	public static function money($value=0, bool $cents=false, string $separator='') {
		ref\format::money($value, $cents, $separator);
		return $value;
	}

	//-------------------------------------------------
	// Phone
	//
	// @param str
	// @param mobile only
	// @return str
	public static function phone($str='', bool $mobile=false) {
		ref\format::phone($str, $mobile);
		return $str;
	}

	//-------------------------------------------------
	// To CSV
	//
	// @param data
	// @param headers
	// @param delimiter
	// @param eol
	// @return csv
	public static function to_csv($data=null, $headers=null, string $delimiter=',', string $eol="\n") {
		ref\cast::array($data);
		$data = array_values(array_filter($data, 'is_array'));
		ref\cast::array($headers);

		$out = array();

		//grab headers from data?
		if (!count($headers) && count($data) && cast::array_type($data[0]) === 'associative') {
			$headers = array_keys($data[0]);
		}

		//output headers, if applicable
		if (count($headers)) {
			ref\sanitize::csv($headers);
			$out[] = '"' . implode('"' . $delimiter . '"', $headers) . '"';
		}

		//output data
		if (count($data)) {
			foreach ($data as $line) {
				ref\sanitize::csv($line);
				$out[] = '"' . implode('"' . $delimiter . '"', $line) . '"';
			}
		}

		return implode($eol, $out);
	}

	//-------------------------------------------------
	// Convert Timezones
	//
	// @param date
	// @param from
	// @param to
	// @return date
	public static function to_timezone($date, $from='UTC', $to='UTC') {
		ref\format::to_timezone($date, $from, $to);
		return $date;
	}

	//-------------------------------------------------
	// To XLS
	//
	// use Microsoft's XML format
	//
	// @param data
	// @param headers
	// @return xls
	public static function to_xls($data=null, $headers=null) {
		ref\cast::array($data);
		$data = array_values(array_filter($data, 'is_array'));
		ref\cast::array($headers);

		$out = array(
			'<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>',
			'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">',
			'<Styles>',
			'<Style ss:ID="s0">',
			'<NumberFormat ss:Format="True/False"/>',
			'</Style>',
			'<Style ss:ID="s1">',
			'<NumberFormat ss:Format="General Date"/>',
			'</Style>',
			'<Style ss:ID="s2">',
			'<NumberFormat ss:Format="Short Date"/>',
			'</Style>',
			'<Style ss:ID="s3">',
			'<NumberFormat ss:Format="Long Time"/>',
			'</Style>',
			'<Style ss:ID="s4">',
			'<NumberFormat ss:Format="Percent"/>',
			'</Style>',
			'<Style ss:ID="s5">',
			'<NumberFormat ss:Format="Currency"/>',
			'</Style>',
			'</Styles>',
			'<Worksheet>',
			'<Table>',
			'<Column ss:Index="1" ss:AutoFitWidth="0" ss:Width="110"/>'
		);

		//grab headers from data?
		if (!count($headers) && count($data) && cast::array_type($data[0]) === 'associative') {
			$headers = array_keys($data[0]);
		}

		//output headers, if applicable
		if (count($headers)) {
			$out[] = '<Row>';
			foreach ($headers as $cell) {
				$cell = htmlspecialchars(strip_tags(sanitize::quotes(sanitize::whitespace($cell))), ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
				$out[] = '<Cell><Data ss:Type="String"><b>' . $cell . '</b></Data></Cell>';
			}
			$out[] = '</Row>';
		}

		//output data
		if (count($data)) {
			foreach ($data as $line) {
				$out[] = '<Row>';
				foreach ($line as $cell) {
					//different types of data need to be treated differently
					$type = gettype($cell);
					$format = null;
					if ($type === 'boolean' || $type === 'bool') {
						$type = 'Boolean';
						$format = '0';
						$cell = $cell ? 1 : 0;
					}
					elseif (is_numeric($cell)) {
						$type = 'Number';
						ref\cast::number($cell);
					}
					else {
						ref\sanitize::whitespace($cell, 2);
						//date and time
						if (preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $cell)) {
							$type = 'DateTime';
							$format = '1';
							$cell = str_replace(' ', 'T', $cell);
						}
						//date
						elseif (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $cell)) {
							$type = 'DateTime';
							$format = '2';
							$cell .= 'T00:00:00';
						}
						//time
						elseif (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $cell)) {
							$type = 'DateTime';
							$format = '3';
							$cell = "0000-00-00T$cell";
							if (mb::substr_count($cell, ':') === 2) {
								$cell .= ':00';
							}
						}
						//percent
						elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $cell)) {
							$type = 'Number';
							$format = '4';
							ref\cast::number($cell);
						}
						//currency
						elseif (preg_match('/^\-\$?[\d,]*\.?\d+$/', $cell) || preg_match('/^\-?[\d,]*\.?\d+¢$/', $cell)) {
							$type = 'Number';
							$format = '5';
							ref\cast::number($cell);
						}
						//everything else
						else {
							$type = 'String';
							$cell = htmlspecialchars(strip_tags(sanitize::quotes($cell)), ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
						}
					}

					$out[] = '<Cell' . (!is_null($format) ? ' ss:StyleID="s' . $format . '"' : '') . '><Data ss:Type="' . $type . '">' . $cell . '</Data></Cell>';
				}
				$out[] = '</Row>';
			}
		}

		//close it off
		$out[] = '</Table>';
		$out[] = '</Worksheet>';
		$out[] = '</Workbook>';

		return implode("\r\n", $out);
	}
}

?>