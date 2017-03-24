<?php
/**
 * Multi-Byte.
 *
 * Functions for multi-byte string handling.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class mb {

	/**
	 * Parse URL
	 *
	 * @see {http://php.net/manual/en/function.parse-url.php#114817}
	 * @see {https://github.com/jeremykendall/php-domain-parser/}
	 *
	 * @param string $url URL.
	 * @param int $component Component.
	 * @return mixed Array, Component, or Null.
	 */
	public static function parse_url($url, $component = -1) {
		ref\cast::string($url, true);
		$url = preg_replace('/^\s+/u', '', $url);
		$url = preg_replace('/\s+$/u', '', $url);

		// Before we start, let's fix scheme-agnostic URLs.
		$url = preg_replace('/^:?\/\//', 'https://', $url);

		// If an IPv6 address is passed on its own, we
		// need to shove it in brackets.
		if (filter_var($url, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$url = "[$url]";
		}

		// The trick is to urlencode (most) parts before passing
		// them to the real parse_url().
		$encoded = preg_replace_callback(
			'%([a-zA-Z][a-zA-Z0-9+\-.]*)?(:?//)?([^:/@?&=#\[\]]+)%usD',
			function ($matches) {
				$matches[3] = urldecode($matches[3]);
				return $matches[1] . $matches[2] . $matches[3];
			},
			$url
		);

		// Before getting the real answer, make sure
		// there is a scheme, otherwise PHP will assume
		// all there is is a path, which is stupid.
		if (PHP_URL_SCHEME !== $component) {
			$test = parse_url($encoded, PHP_URL_SCHEME);
			if (!$test) {
				$encoded = "blobfolio://$encoded";
			}
		}

		$parts = parse_url($encoded, $component);

		// And now decode what we've been giving. Let's
		// also take a moment to translate Unicode hosts
		// to ASCII.
		if (is_string($parts) && PHP_URL_SCHEME !== $component) {
			$parts = urldecode($parts);

			if (PHP_URL_HOST === $component) {
				// Fix Unicode.
				if (function_exists('idn_to_ascii')) {
					$parts = explode('.', $parts);
					$parts = array_map('idn_to_ascii', $parts);
					$parts = implode('.', $parts);
				}

				// Lowercase it.
				\blobfolio\common\ref\mb::strtolower($parts);

				// Get rid of trailing periods.
				$parts = ltrim($parts, '.');
				$parts = rtrim($parts, '.');

				// Standardize IPv6 formatting.
				if (
					'[' === static::substr($parts, 0, 1)
				) {
					$parts = str_replace(array('[',']'), '', $parts);
					ref\sanitize::ip($parts, true);
					$parts = "[{$parts}]";
				}
			}
		}
		elseif (is_array($parts)) {
			foreach ($parts as $k=>$v) {
				if (!is_string($v)) {
					continue;
				}

				if ('scheme' !== $k) {
					$parts[$k] = urldecode($v);
				}
				// Remove our pretend scheme.
				elseif ('blobfolio' === $v) {
					unset($parts[$k]);
					continue;
				}

				if ('host' === $k) {
					// Fix Unicode.
					if (function_exists('idn_to_ascii')) {
						$parts[$k] = explode('.', $parts[$k]);
						$parts[$k] = array_map('idn_to_ascii', $parts[$k]);
						$parts[$k] = implode('.', $parts[$k]);
					}

					// Lowercase it.
					\blobfolio\common\ref\mb::strtolower($parts[$k]);

					// Get rid of trailing periods.
					$parts[$k] = ltrim($parts[$k], '.');
					$parts[$k] = rtrim($parts[$k], '.');

					// Standardize IPv6 formatting.
					if ('[' === static::substr($parts[$k], 0, 1)) {
						$parts[$k] = str_replace(array('[',']'), '', $parts[$k]);
						ref\sanitize::ip($parts[$k], true);
						$parts[$k] = "[{$parts[$k]}]";
					}
				}
			}
		}

		return $parts;
	}


	/**
	 * Wrapper For parse_str()
	 *
	 * @param string $str String.
	 * @param mixed $result Result.
	 * @return bool True/false.
	 */
	public static function parse_str($str, &$result) {
		if (function_exists('mb_parse_str')) {
			return mb_parse_str($str, $result);
		}
		else {
			return parse_str($str, $result);
		}
	}

	/**
	 * Wrapper For str_split()
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @return array|bool Split string or false.
	 */
	public static function str_split($str, int $split_length=1) {
		ref\cast::string($str, true);
		if ($split_length < 1) {
			return false;
		}

		$str_length = static::strlen($str);
		$out = array();

		for ($i = 0; $i < $str_length; $i += $split_length) {
			$out[] = static::substr($str, $i, $split_length);
		}

		return $out;
	}

	/**
	 * Wrapper For strlen()
	 *
	 * @param string $str String.
	 * @return int String length.
	 */
	public static function strlen($str) {
		ref\cast::string($str, true);

		if (function_exists('mb_strlen')) {
			return (int) mb_strlen($str, 'UTF-8');
		}
		else {
			return strlen($str);
		}
	}

	/**
	 * Wrapper For str_pad()
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @return string Padded string.
	 */
	public static function str_pad($str='', int $pad_length, $pad_string=' ', $pad_type=null) {
		ref\cast::string($string, true);
		ref\cast::string($pad_string, true);

		$current_length = static::strlen($str);
		$pad_string_length = static::strlen($pad_string);

		if ($pad_length <= $current_length || !$pad_string_length) {
			return $str;
		}

		// Pad left.
		if (STR_PAD_LEFT === $pad_type) {
			$str = str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length)) . $str;
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, $new_length - $pad_length);
			}
		}
		// Pad both.
		elseif (STR_PAD_BOTH === $pad_type) {
			$leftright = 'right';
			while (static::strlen($str) < $pad_length) {
				$leftright = 'left' === $leftright ? 'right' : 'left';
				if ('left' === $leftright) {
					$str = "{$pad_string}{$str}";
				}
				else {
					$str .= $pad_string;
				}
			}

			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				if ('left' === $leftright) {
					$str = static::substr($str, $new_length - $pad_length);
				}
				else {
					$str = static::substr($str, 0, $pad_length);
				}
			}
		}
		// Pad right.
		else {
			$str .= str_repeat($pad_string, ceil(($pad_length - $current_length) / $pad_string_length));
			$new_length = static::strlen($str);
			if ($new_length > $pad_length) {
				$str = static::substr($str, 0, $pad_length);
			}
		}

		return $str;
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool First occurrence or false.
	 */
	public static function strpos($haystack, $needle, $offset=0) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_strpos')) {
			return mb_strpos($haystack, $needle, $offset, 'UTF-8');
		}
		else {
			return strpos($haystack, $needle, $offset);
		}
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool Last occurrence or false.
	 */
	public static function strrpos($haystack, $needle, $offset=0) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_strrpos')) {
			return mb_strrpos($haystack, $needle, $offset, 'UTF-8');
		}
		else {
			return strrpos($haystack, $needle, $offset);
		}
	}

	/**
	 * Wrapper For strtolower()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function strtolower($str='') {
		ref\mb::strtolower($str);
		return $str;
	}

	/**
	 * Wrapper For strtoupper()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function strtoupper($str='') {
		ref\mb::strtoupper($str);
		return $str;
	}

	/**
	 * Wrapper For substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @return string String.
	 */
	public static function substr($str, $start=0, $length=null) {
		ref\cast::string($str, true);

		if (function_exists('mb_substr')) {
			return mb_substr($str, $start, $length, 'UTF-8');
		}
		else {
			return substr($str, $start, $length);
		}
	}

	/**
	 * Wrapper For substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @return int Count.
	 */
	public static function substr_count($haystack, $needle) {
		ref\cast::string($haystack, true);
		ref\cast::string($needle, true);

		if (function_exists('mb_substr_count')) {
			return mb_substr_count($haystack, $needle, 'UTF-8');
		}
		else {
			return substr_count($haystack, $needle);
		}
	}

	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function ucfirst($str='') {
		ref\mb::ucfirst($str);
		return $str;
	}

	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond
	 * the native PHP functions.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function ucwords($str='') {
		ref\mb::ucwords($str);
		return $str;
	}
}


