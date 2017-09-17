<?php
/**
 * Files
 *
 * Functions for sanitizing and handling files.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common;

class file {

	/**
	 * Get Data-URI From File
	 *
	 * @param string $path Path.
	 * @return string|bool Data-URI or false.
	 */
	public static function data_uri($path='') {
		ref\cast::to_string($path, true);
		ref\file::path($path, true);
		try {
			if (false !== $path && is_file($path)) {
				$content = base64_encode(@file_get_contents($path));
				$finfo = mime::finfo($path);
				return 'data:' . $finfo['mime'] . ';base64,' . $content;
			}
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		return false;
	}

	/**
	 * Is Directory Empty?
	 *
	 * @param string $path Path.
	 * @return bool True/false.
	 */
	public static function empty_dir($path='') {
		try {
			ref\cast::to_string($path);
			if (!is_readable($path) || !is_dir($path)) {
				return false;
			}

			// Scan all files in dir.
			$handle = opendir($path);
			while (false !== ($entry = readdir($handle))) {
				// Anything but a dot === not empty.
				if ('.' !== $entry && '..' !== $entry) {
					return false;
				}
			}
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Add Leading Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	public static function leadingslash($path='') {
		ref\file::leadingslash($path);
		return $path;
	}

	/**
	 * Fix Path Formatting
	 *
	 * @param string $path Path.
	 * @param bool $validate Require valid file.
	 * @return string Path.
	 */
	public static function path($path='', $validate=true) {
		ref\file::path($path, $validate);
		return $path;
	}

	/**
	 * Read File in Chunks
	 *
	 * This greatly reduces overhead if serving
	 * files through a PHP gateway script.
	 *
	 * @param string $file Path.
	 * @param bool $retbytes Return bytes served like `readfile()`.
	 * @return mixed Bytes served or status.
	 */
	public static function readfile_chunked($file, $retbytes=true) {
		ref\cast::to_string($file, true);
		ref\cast::to_bool($retbytes, true);

		$buffer = '';
		$cnt = 0;
		$chunk_size = 1024 * 1024;

		if (false === ($handle = fopen($file, 'rb'))) {
			return false;
		}

		while (!feof($handle)) {
			$buffer = fread($handle, $chunk_size);
			echo $buffer;
			ob_flush();
			flush();
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}

		$status = fclose($handle);

		// Return number of bytes delivered like readfile() does.
		if ($retbytes && $status) {
			return $cnt;
		}

		return $status;
	}

	/**
	 * Redirect Wrapper
	 *
	 * Will issue redirect headers or print Javascript
	 * if headers have already been sent.
	 *
	 * @param string $to URL.
	 * @return void Nothing.
	 */
	public static function redirect(string $to) {
		ref\sanitize::url($to);

		unset($_POST);
		unset($_GET);
		unset($_REQUEST);

		if (!headers_sent()) {
			header("Location: $to");
		}
		else {
			echo "<script>top.location.href='" . str_replace("'", "\'", $to) . "';</script>";
		}
		exit;
	}

	/**
	 * Recursively Remove A Directory
	 *
	 * @param string $path Path.
	 * @return bool True/false.
	 */
	public static function rmdir($path='') {
		try {
			ref\file::path($path, true);
			if (!@is_readable($path) || !@is_dir($path)) {
				return false;
			}

			// Scan all files in dir.
			$handle = opendir($path);
			while (false !== ($entry = readdir($handle))) {
				// Anything but a dot === not empty.
				if (('.' === $entry) || ('..' === $entry)) {
					continue;
				}

				$file = "{$path}{$entry}";

				// Delete files.
				if (@is_file($file)) {
					@unlink($file);
				}
				// Recursively delete directories.
				else {
					static::rmdir($file);
				}
			}
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		if (static::empty_dir($path)) {
			@rmdir($path);
		}

		return !@file_exists($path);
	}

	/**
	 * Add Trailing Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	public static function trailingslash($path='') {
		ref\file::trailingslash($path);
		return $path;
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	public static function unixslash($path='') {
		ref\file::unixslash($path);
		return $path;
	}

	/**
	 * Strip Leading Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	public static function unleadingslash($path='') {
		ref\file::unleadingslash($path);
		return $path;
	}

	/**
	 * Reverse `parse_url()`
	 *
	 * @param array $parsed Parsed data.
	 * @return string URL.
	 */
	public static function unparse_url($parsed=null) {
		$url = '';
		$parsed = data::parse_args($parsed, constants::URL_PARTS);

		// To simplify, unset anything without length.
		$parsed = array_map('trim', $parsed);
		$parsed = array_filter($parsed, 'strlen');

		// We don't really care about validating url integrity,
		// but if nothing at all was passed then it is trash.
		if (!count($parsed)) {
			return false;
		}

		if (isset($parsed['scheme'])) {
			$url = "{$parsed['scheme']}:";
		}

		if (isset($parsed['host'])) {
			if ($url) {
				$url .= '//';
			}

			// Is this a user:pass situation?
			if (isset($parsed['user'])) {
				$url .= $parsed['user'];
				if (isset($parsed['pass'])) {
					$url .= ":{$parsed['pass']}";
				}
				$url .= '@';
			}

			// Finally the host.
			$url .= (filter_var($parsed['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? "[{$parsed['host']}]" : $parsed['host']);

			if (isset($parsed['port'])) {
				$url .= ":{$parsed['port']}";
			}

			if (isset($parsed['path']) && mb::substr($parsed['path'], 0, 1) !== '/') {
				$url .= '/';
			}
		}

		if (isset($parsed['path'])) {
			$url .= $parsed['path'];
		}

		if (isset($parsed['query'])) {
			$url .= "?{$parsed['query']}";
		}

		if (isset($parsed['fragment'])) {
			$url .= "#{$parsed['fragment']}";
		}

		return $url ? $url : false;
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $path Path.
	 * @return string Path.
	 */
	public static function untrailingslash($path='') {
		ref\file::untrailingslash($path);
		return $path;
	}

}


