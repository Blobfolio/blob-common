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
			} catch (\Exception $e) {
				$ip = false;
				return false;
			}
		}

		$ip = false;
		return true;
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
	public static function links(&$str, $args=null, $pass=1) {
		cast::string($str, true);
		cast::int($pass, true);

		// Build link attributes from our arguments, if any.
		$defaults = array(
			'class'=>array(),
			'rel'=>'',
			'target'=>''
		);
		$data = \blobfolio\common\data::parse_args($args, $defaults);
		$data['class'] = implode(' ', $data['class']);
		sanitize::html($data);
		$data = array_filter($data, 'strlen');
		$atts = array();
		foreach ($data as $k=>$v) {
			$atts[] = "$k=\"$v\"";
		}
		$atts = implode(' ', $atts);

		// Now look at the string.
		$str = preg_split('/(<.+?>)/is', $str, 0, PREG_SPLIT_DELIM_CAPTURE);
		$blacklist = implode('|', \blobfolio\common\constants::LINKS_BLACKLIST);
		$ignoring = false;
		foreach ($str as $k=>$v) {
			// Even keys exist between tags.
			if (0 === $k % 2) {
				// Skip it if we're waiting on a closing tag.
				if (false !== $ignoring) {
					continue;
				}

				// URL bits.
				if (1 === $pass) {
					// We can afford to be sloppy here, thanks to FDQN validation later.
					$str[$k] = preg_replace_callback(
						'/((ht|f)tps?:\/\/[^\s\'"\[\]\(\){}]+|[^\s\'"\[\]\(\){}]*xn--[^\s\'"\[\]\(\){}]+|[@]?[\w\.]+\.[\w\.]{2,}[^\s]*)/ui',
						function($matches) use($atts) {
							$raw = $matches[1];

							// Don't do email bits.
							if (0 === \blobfolio\common\mb::strpos($raw, '@')) {
								return $matches[1];
							}

							// We don't want trailing punctuation added to the link.
							if (preg_match('/([^\w\/]+)$/ui', $raw, $suffix)) {
								$suffix = $suffix[1];
								$raw = preg_replace('/([^\w\/]+)$/ui', '', $raw);
							}
							else {
								$suffix = '';
							}

							$link = \blobfolio\common\mb::parse_url($raw);
							if (!is_array($link) || !isset($link['host'])) {
								return $matches[1];
							}

							// Only linkify FQDNs.
							$domain = new \blobfolio\domain\domain($link['host']);
							if (!$domain->is_valid() || !$domain->is_fqdn()) {
								return $matches[1];
							}

							// Supply a scheme, if missing.
							if (!isset($link['scheme'])) {
								$link['scheme'] = 'http';
							}

							$link = \blobfolio\common\file::unparse_url($link);
							if (filter_var($link, FILTER_SANITIZE_URL) !== $link) {
								return $matches[1];
							}

							// Finally, make a link!
							sanitize::html($link);
							return '<a href="' . $link . '"' . ($atts ? " $atts" : '') . '>' . $raw . '</a>' . $suffix;
						},
						$str[$k]
					);
				}
				// Email address bits.
				elseif (2 === $pass) {
					// Again, we can be pretty careless here thanks to later checks.
					$str[$k] = preg_replace_callback(
						'/([\w\.\!#\$%&\*\+\=\?_~]+@[^\s\'"\[\]\(\){}@]{2,})/ui',
						function($matches) use($atts) {
							$raw = $matches[1];

							// We don't want trailing punctuation added to the link.
							if (preg_match('/([^\w]+)$/ui', $raw, $suffix)) {
								$suffix = $suffix[1];
								$raw = preg_replace('/([^\w]+)$/ui', '', $raw);
							}
							else {
								$suffix = '';
							}

							$link = \blobfolio\common\sanitize::email($raw);
							if (!$link) {
								return $matches[1];
							}

							// Finally, make a link!
							sanitize::html($link);

							return '<a href="mailto:' . $link . '"' . ($atts ? " $atts" : '') . '>' . $raw . '</a>' . $suffix;
						},
						$str[$k]
					);
				}
				// Phone numbers.
				elseif (3 === $pass) {
					// Again, we can be pretty careless here thanks to later checks.
					$str[$k] = preg_replace_callback(
						'/(\s)?(\+\d[\d\-\s]{5,}+|\(\d{3}\)\s[\d]{3}[\-\.\s]\d{4}|\d{3}[\-\.\s]\d{3}[\-\.\s]\d{4}|\+\d{7,})/ui',
						function($matches) use($atts) {
							$prefix = $matches[1];
							$raw = $matches[2];

							// We don't want trailing punctuation added to the link.
							if (preg_match('/([^\d]+)$/ui', $raw, $suffix)) {
								$suffix = $suffix[1];
								$raw = preg_replace('/([^\d]+)$/ui', '', $raw);
							}
							else {
								$suffix = '';
							}

							$link = \blobfolio\common\format::phone($raw);
							$link = preg_replace('/[^\d]/', '', $link);
							if (!$link) {
								return $matches[1] . $matches[2];
							}

							return $prefix . '<a href="tel:+' . $link . '"' . ($atts ? " $atts" : '') . '>' . $raw . '</a>' . $suffix;
						},
						$str[$k]
					);
				}
			}
			// Odd keys indicate a tag, opening or closing.
			else {
				// If we aren't already waiting on a closing tag...
				if (false === $ignoring) {
					// Start ignoring if this tag is blacklisted and not self-closing.
					if (preg_match("/<($blacklist).*(?<!\/)>$/is", $str[$k], $matches)) {
						$ignoring = preg_quote($matches[1], '/');
					}
				}
				// Otherwise wait until we find a corresponding closing tag.
				elseif (preg_match("/<\/\s*$ignoring>/i", $str[$k], $matches)) {
					$ignoring = false;
				}
			}
		}
		$str = implode($str);

		// Linkification is run in stages to prevent overlap issues.
		// Pass #1 is for URL-like bits, pass #2 for email addresses,
		// pass #3 for phone numbers.
		if (1 === $pass) {
			static::links($str, $args, 2);
		}
		elseif (2 === $pass) {
			static::links($str, $args, 3);
		}

		return true;
	}

	/**
	 * Money (USD)
	 *
	 * @param float $value Value.
	 * @param bool $cents Return sub-$1 values with ¢.
	 * @param string $separator Separator.
	 * @param bool $no00 Remove trailing cents if none.
	 * @return bool True.
	 */
	public static function money(&$value=0, $cents=false, $separator='', $no00=false) {
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::money($value[$k], $cents, $separator, $no00);
			}
		}
		else {
			cast::float($value);
			cast::bool($cents, true);
			cast::string($separator, true);
			cast::bool($no00, true);

			$value = round($value, 2);
			$negative = $value < 0;
			if ($negative) {
				$value = abs($value);
			}

			if ($value >= 1 || false === $cents) {
				$value = ($negative ? '-' : '') . '$' . number_format($value, 2, '.', $separator);
				if ($no00) {
					$value = preg_replace('/\.00$/', '', $value);
				}
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
		} catch (\Exception $e) {
			$date = $original;
		}

		return true;
	}

}


