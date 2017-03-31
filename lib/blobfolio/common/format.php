<?php
/**
 * Formatting
 *
 * Functions for formatting data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

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
	 * @return array Array.
	 */
	public static function array_to_indexed($arr) {
		ref\format::array_to_indexed($arr);
		return $arr;
	}

	/**
	 * CIDR to IP Range
	 *
	 * Find the minimum and maximum IPs in a
	 * given CIDR range.
	 *
	 * @param string $cidr CIDR.
	 * @return array|bool Range or false.
	 */
	public static function cidr_to_range($cidr) {
		ref\cast::string($cidr, true);

		$range = array('min'=>0, 'max'=>0);
		$cidr = explode('/', $cidr);

		// IPv4?
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

		// IPv6? Of course a little more complicated.
		if (filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			if (count($cidr) === 1) {
				$range['min'] = $range['max'] = sanitize::ip($cidr[0]);
				return $range;
			}

			// Parse the address into a binary string.
			$firstaddrbin = inet_pton($cidr[0]);

			// Convert the binary string to a string with hexadecimal characters (bin2hex).
			$tmp = unpack('H*', $firstaddrbin);
			$firstaddrhex = reset($tmp);

			// Overwriting first address string to make sure notation is optimal.
			$cidr[0] = inet_ntop($firstaddrbin);

			// Calculate the number of 'flexible' bits.
			$flexbits = 128 - $cidr[1];

			// Build the hexadecimal string of the last address.
			$lastaddrhex = $firstaddrhex;

			// We start at the end of the string (which is always 32 characters long).
			$pos = 31;
			while ($flexbits > 0) {
				// Get the character at this position.
				$orig = substr($lastaddrhex, $pos, 1);

				// Convert it to an integer.
				$origval = hexdec($orig);

				// OR it with (2^flexbits)-1, with flexbits limited to 4 at a time.
				$newval = $origval | (pow(2, min(4, $flexbits)) - 1);

				// Convert it back to a hexadecimal character.
				$new = dechex($newval);

				// And put that character back in the string.
				$lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

				// We processed one nibble, move to previous position.
				$flexbits -= 4;
				$pos -= 1;
			}

			// Convert the hexadecimal string to a binary string (hex2bin).
			$lastaddrbin = pack('H*', $lastaddrhex);

			// And create an IPv6 address from the binary string.
			$lastaddrstr = inet_ntop($lastaddrbin);

			// Pack and done!
			$range['min'] = sanitize::ip($cidr[0]);
			$range['max'] = sanitize::ip($lastaddrstr);
			return $range;
		}

		return false;
	}

	/**
	 * Decode HTML Entities
	 *
	 * Decode all HTML entities back into their char
	 * counterparts, recursively until every last one
	 * is captured.
	 *
	 * @param string $str String.
	 * @return string HTML.
	 */
	public static function decode_entities($str='') {
		ref\format::decode_entities($str);
		return $str;
	}

	/**
	 * Generate Text Except
	 *
	 * @param string $str String.
	 * @param mixed $args Arguments.
	 *
	 * @arg int $length Length limit.
	 * @arg string $unit Unit to examine, "character" or "word".
	 * @arg string $suffix Suffix, e.g. ...
	 *
	 * @return string Excerpt.
	 */
	public static function excerpt($str='', $args=null) {
		ref\cast::string($str, true);
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

		// Character limit.
		if ('character' === $options['unit'] && mb::strlen($str) > $options['length']) {
			$str = trim(mb::substr($str, 0, $options['length'])) . $options['suffix'];
		}
		// Word limit.
		elseif ('word' === $options['unit'] && mb::substr_count($str, ' ') > $options['length'] - 1) {
			$str = explode(' ', $str);
			$str = array_slice($str, 0, $options['length']);
			$str = implode(' ', $str) . $options['suffix'];
		}

		return $str;
	}

	/**
	 * Inflect
	 *
	 * Inflect a phrase given a count. `sprintf` formatting
	 * is supported. If an array is passed as $count, its
	 * size will be used for inflection.
	 *
	 * @param int|array $count Count.
	 * @param string $single Singular.
	 * @param string $plural Plural.
	 * @return string Inflected string.
	 */
	public static function inflect($count, string $single, string $plural) {
		if (is_array($count)) {
			$count = count($count);
		}
		else {
			ref\cast::number($count);
		}
		ref\sanitize::utf8($single);
		ref\sanitize::utf8($plural);

		if (( (float) 1) === $count) {
			return sprintf($single, $count);
		}
		else {
			return sprintf($plural, $count);
		}
	}

	/**
	 * IP to Number
	 *
	 * @param string $ip IP.
	 * @return int|bool IP or false.
	 */
	public static function ip_to_number($ip) {
		ref\format::ip_to_number($ip);
		return $ip;
	}

	/**
	 * Linkify Text
	 *
	 * Make link-like text things clickable HTML links.
	 *
	 * @param string $str String.
	 * @param array $args Arguments.
	 * @param int $pass Pass (1=URL, 2=EMAIL).
	 *
	 * @arg array $class Class(es).
	 * @arg string $rel Rel.
	 * @arg string $target Target.
	 *
	 * @return bool True.
	 */
	public static function links($str, $args=null, int $pass=1) {
		ref\format::links($str, $args, $pass);
		return $str;
	}

	/**
	 * Money (USD)
	 *
	 * @param float $value Value.
	 * @param bool $cents Return sub-$1 values with ¢.
	 * @param string $separator Separator.
	 * @param bool $no00 Remove trailing cents if none.
	 * @return string Value.
	 */
	public static function money($value=0, bool $cents=false, string $separator='', bool $no00=false) {
		ref\format::money($value, $cents, $separator, $no00);
		return $value;
	}

	/**
	 * Phone
	 *
	 * @param string $str Phone.
	 * @param string $country Country.
	 * @param array $types Types, e.g. Mobile.
	 * @return string Phone in International Format.
	 */
	public static function phone($str='', $country='', $types=array()) {
		ref\format::phone($str, $country, $types);
		return $str;
	}

	/**
	 * Generate CSV from Data
	 *
	 * @param array $data Data (row=>cells).
	 * @param array $headers Headers.
	 * @param string $delimiter Delimiter.
	 * @param string $eol Line ending type.
	 * @return string CSV content.
	 */
	public static function to_csv($data=null, $headers=null, string $delimiter=',', string $eol="\n") {
		ref\cast::array($data);
		$data = array_values(array_filter($data, 'is_array'));
		ref\cast::array($headers);

		$out = array();

		// Grab headers from data?
		if (!count($headers) && count($data) && cast::array_type($data[0]) === 'associative') {
			$headers = array_keys($data[0]);
		}

		// Output headers, if applicable.
		if (count($headers)) {
			foreach ($headers as $k=>$v) {
				ref\cast::string($headers[$k], true);
			}
			ref\sanitize::csv($headers);
			$out[] = '"' . implode('"' . $delimiter . '"', $headers) . '"';
		}

		// Output data.
		if (count($data)) {
			foreach ($data as $line) {
				foreach ($line as $k=>$v) {
					ref\cast::string($line[$k], true);
				}
				ref\sanitize::csv($line);
				$out[] = '"' . implode('"' . $delimiter . '"', $line) . '"';
			}
		}

		return implode($eol, $out);
	}

	/**
	 * Convert Timezones
	 *
	 * @param string $date Date.
	 * @param string $from Original Timezone.
	 * @param string $to New Timezone.
	 * @return string Date.
	 */
	public static function to_timezone($date, $from='UTC', $to='UTC') {
		ref\format::to_timezone($date, $from, $to);
		return $date;
	}

	/**
	 * Generate XLS from Data
	 *
	 * This uses Microsoft's XML spreadsheet format.
	 *
	 * @param array $data Data (row=>cells).
	 * @param array $headers Headers.
	 * @return string XLS content.
	 */
	public static function to_xls($data=null, $headers=null) {
		ref\cast::array($data);
		$data = array_values(array_filter($data, 'is_array'));
		ref\cast::array($headers);

		// @codingStandardsIgnoreStart
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
		// @codingStandardsIgnoreEnd

		// Grab headers from data?
		if (!count($headers) && count($data) && cast::array_type($data[0]) === 'associative') {
			$headers = array_keys($data[0]);
		}

		// Output headers, if applicable.
		if (count($headers)) {
			foreach ($headers as $k=>$v) {
				ref\cast::string($headers[$k], true);
			}

			$out[] = '<Row>';
			foreach ($headers as $cell) {
				$cell = htmlspecialchars(strip_tags(sanitize::quotes(sanitize::whitespace($cell))), ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
				$out[] = '<Cell><Data ss:Type="String"><b>' . $cell . '</b></Data></Cell>';
			}
			$out[] = '</Row>';
		}

		// Output data.
		if (count($data)) {
			foreach ($data as $line) {
				$out[] = '<Row>';
				foreach ($line as $cell) {
					// Different types of data need to be treated differently.
					$type = gettype($cell);
					$format = null;
					if ('boolean' === $type || 'bool' === $type) {
						$type = 'Boolean';
						$format = '0';
						$cell = $cell ? 1 : 0;
					}
					elseif (is_numeric($cell)) {
						$type = 'Number';
						ref\cast::number($cell);
					}
					else {
						ref\cast::string($cell, true);
						ref\sanitize::whitespace($cell, 2);
						// Date and time.
						if (preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $cell)) {
							$type = 'DateTime';
							$format = '1';
							$cell = str_replace(' ', 'T', $cell);
						}
						// Date.
						elseif (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $cell)) {
							$type = 'DateTime';
							$format = '2';
							$cell .= 'T00:00:00';
						}
						// Time.
						elseif (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $cell)) {
							$type = 'DateTime';
							$format = '3';
							$cell = "0000-00-00T$cell";
							if (mb::substr_count($cell, ':') === 2) {
								$cell .= ':00';
							}
						}
						// Percent.
						elseif (preg_match('/^\-?[\d,]*\.?\d+%$/', $cell)) {
							$type = 'Number';
							$format = '4';
							ref\cast::number($cell);
						}
						// Currency.
						elseif (preg_match('/^\-\$?[\d,]*\.?\d+$/', $cell) || preg_match('/^\-?[\d,]*\.?\d+¢$/', $cell)) {
							$type = 'Number';
							$format = '5';
							ref\cast::number($cell);
						}
						// Everything else.
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

		// Close it off.
		$out[] = '</Table>';
		$out[] = '</Worksheet>';
		$out[] = '</Workbook>';

		return implode("\r\n", $out);
	}
}


