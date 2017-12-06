<?php
/**
 * Files - By Reference
 *
 * Functions for sanitizing and handling files.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

class file {

	/**
	 * idn_to_ascii Workaround (PHP 7.2+)
	 *
	 * PHP 7.2 deprecates a constant used by the Intl extension, and
	 * that won't likely change until 7.4. This wrapper will help make
	 * sure things don't explode in the meantime.
	 *
	 * @param string|array $url URL.
	 * @return bool True/false.
	 */
	public static function idn_to_ascii(&$url) {
		// The Intl extension has to exist.
		if (!function_exists('idn_to_ascii')) {
			return false;
		}

		// Recurse for arrays.
		if (is_array($url)) {
			foreach ($url as $k=>$v) {
				static::idn_to_ascii($url[$k]);
			}
		}
		else {
			if (defined('INTL_IDNA_VARIANT_UTS46')) {
				$url = idn_to_ascii($url, 0, INTL_IDNA_VARIANT_UTS46);
			}
			else {
				$url = idn_to_ascii($url);
			}
		}

		return true;
	}

	/**
	 * idn_to_utf8 Workaround (PHP 7.2+)
	 *
	 * PHP 7.2 deprecates a constant used by the Intl extension, and
	 * that won't likely change until 7.4. This wrapper will help make
	 * sure things don't explode in the meantime.
	 *
	 * @param string|array $url URL.
	 * @return bool True/false.
	 */
	public static function idn_to_utf8(&$url) {
		// The Intl extension has to exist.
		if (!function_exists('idn_to_utf8')) {
			return false;
		}

		// Recurse for arrays.
		if (is_array($url)) {
			foreach ($url as $k=>$v) {
				static::idn_to_utf8($url[$k]);
			}
		}
		else {
			if (defined('INTL_IDNA_VARIANT_UTS46')) {
				$url = idn_to_utf8($url, 0, INTL_IDNA_VARIANT_UTS46);
			}
			else {
				$url = idn_to_utf8($url);
			}
		}

		return true;
	}

	/**
	 * Add Leading Slash
	 *
	 * @param string $path Path.
	 * @return bool True.
	 */
	public static function leadingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::leadingslash($path[$k]);
			}
		}
		else {
			cast::to_string($path);
			static::unleadingslash($path);
			$path = "/$path";
		}

		return true;
	}

	/**
	 * Fix Path Formatting
	 *
	 * @param string $path Path.
	 * @param bool $validate Require valid file.
	 * @return bool True.
	 */
	public static function path(&$path='', $validate=true) {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::path($path[$k], $validate);
			}
		}
		else {
			cast::to_string($path);

			// This might be a URL rather than something local.
			// Only focus on the main ones.
			if (preg_match('/^(https?|ftps?|sftp)/iu', $path)) {
				sanitize::url($path);
				return true;
			}

			// Strip leading file:// scheme.
			if ('file://' === substr($path, 0, '7')) {
				$path = substr($path, 7);
			}

			static::unixslash($path);
			cast::to_bool($validate, true);

			$original = $path;
			try {
				$path = realpath($path);
			} catch (\Throwable $e) {
				$path = false;
			} catch (\Exception $e) {
				$path = false;
			}

			if ($validate && false === $path) {
				$path = false;
				return false;
			}
			elseif (false === $path) {
				// Try just the directory.
				try {
					$path = $original;
					if (false !== $dir = realpath(dirname($path))) {
						static::trailingslash($dir);
						$path = $dir . basename($path);
					}
					else {
						$path = $original;
					}
				} catch (\Throwable $e) {
					$path = $original;
				} catch (\Exception $e) {
					$path = $original;
				}
			}

			$original = $path;
			try {
				if (is_dir($path)) {
					static::trailingslash($path);
				}
			} catch (\Throwable $e) {
				$path = $original;
			} catch (\Exception $e) {
				$path = $original;
			}
		}

		return true;
	}

	/**
	 * Add Trailing Slash
	 *
	 * @param string $path Path.
	 * @return bool True.
	 */
	public static function trailingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::trailingslash($path[$k]);
			}
		}
		else {
			cast::to_string($path);
			static::untrailingslash($path);
			$path .= '/';
		}

		return true;
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $path Path.
	 * @return bool True.
	 */
	public static function unixslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::unixslash($path[$k]);
			}
		}
		else {
			cast::to_string($path);
			$path = str_replace('\\', '/', $path);
			$path = str_replace('/./', '//', $path);
			$path = preg_replace('/\/{2,}/u', '/', $path);
		}

		return true;
	}

	/**
	 * Strip Leading Slash
	 *
	 * @param string $path Path.
	 * @return bool True.
	 */
	public static function unleadingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::unleadingslash($path[$k]);
			}
		}
		else {
			cast::to_string($path);
			static::unixslash($path);
			$path = ltrim($path, '/');
		}

		return true;
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $path Path.
	 * @return bool True.
	 */
	public static function untrailingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::untrailingslash($path[$k]);
			}
		}
		else {
			cast::to_string($path);
			static::unixslash($path);
			$path = rtrim($path, '/');
		}

		return true;
	}

}


