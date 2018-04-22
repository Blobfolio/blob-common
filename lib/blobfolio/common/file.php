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
	 * Recursive Copy
	 *
	 * @param string $from Source.
	 * @param string $to Destination.
	 * @return bool True/false.
	 */
	public static function copy($from, $to) {
		ref\file::path($from, true);
		if (!$from) {
			return false;
		}

		ref\file::path($to, false);
		if (!$to || ($from === $to)) {
			return false;
		}

		// Recurse directories.
		if (is_dir($from)) {
			ref\file::trailingslash($from);
			ref\file::trailingslash($to);

			if (!is_dir($to)) {
				$dir_chmod = (fileperms($from) & 0777 | 0755);
				if (!static::mkdir($to, $dir_chmod)) {
					return false;
				}
			}

			// Copy all files and directories within.
			if ($handle = opendir($from)) {
				while (false !== ($file = readdir($handle))) {
					// Ignore dots.
					if (('.' === $file) || ('..' === $file)) {
						continue;
					}

					// Recurse.
					static::copy("{$from}{$file}", "{$to}{$file}");
				}
				closedir($handle);
			}

			return true;
		}
		// Let PHP handle it.
		elseif (is_file($from)) {
			$dir_from = dirname($from);
			$dir_to = dirname($to);

			// Make the TO directory if it doesn't exist.
			if (!is_dir($dir_to)) {
				$dir_chmod = (fileperms($dir_from) & 0777 | 0755);
				if (!static::mkdir($dir_to, $dir_chmod)) {
					return false;
				}
			}

			// Copy the file.
			if (!@copy($from, $to)) {
				return false;
			}
			$file_chmod = (fileperms($from) & 0777 | 0644);
			@chmod($to, $file_chmod);

			return true;
		}

		return false;
	}

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
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					// Anything but a dot === not empty.
					if (('.' !== $entry) && ('..' !== $entry)) {
						return false;
					}
				}
				closedir($handle);
			}
		} catch (\Throwable $e) {
			return false;
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Hash Directory
	 *
	 * Generate a hash of all child files within a directory. Any
	 * algorithm supported by hash_file() is supported here, but be
	 * careful not to combine a slow algorithm with a large directory.
	 *
	 * @param string $path Directory.
	 * @param string $dir_algo Result hashing algorithm.
	 * @param string $file_algo File hashing algorithm.
	 * @return string|bool Hash or false.
	 */
	public static function hash_dir($path, $dir_algo='md5', $file_algo=null) {
		// We definitely need a valid directory algorithm.
		if (!$dir_algo || !in_array($dir_algo, hash_algos(), true)) {
			return false;
		}

		// If the file algorithm is bad or missing, we can just use the
		// same method as we are for our result.
		if (!$file_algo || !in_array($file_algo, hash_algos(), true)) {
			$file_algo = $dir_algo;
		}

		$files = static::scandir($path, true, false);
		if (!count($files)) {
			return hash($dir_algo, 'empty');
		}

		// Add up the file hashes.
		$soup = '';
		foreach ($files as $v) {
			$soup .= hash_file($file_algo, $v);
		}

		return hash($dir_algo, $soup);
	}

	/**
	 * Workaround: idn_to_ascii (PHP 7.2+)
	 *
	 * PHP 7.2 deprecates a constant used by the Intl extension, and
	 * that won't likely change until 7.4. This wrapper will help make
	 * sure things don't explode in the meantime.
	 *
	 * @param string|array $url URL.
	 * @return string|array URL.
	 */
	public static function idn_to_ascii($url) {
		ref\file::idn_to_ascii($url);
		return $url;
	}

	/**
	 * Workaround: idn_to_utf8 (PHP 7.2+)
	 *
	 * PHP 7.2 deprecates a constant used by the Intl extension, and
	 * that won't likely change until 7.4. This wrapper will help make
	 * sure things don't explode in the meantime.
	 *
	 * @param string|array $url URL.
	 * @return string|array URL.
	 */
	public static function idn_to_utf8($url) {
		ref\file::idn_to_utf8($url);
		return $url;
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
	 * Resursively Make Directory
	 *
	 * PHP's mkdir function can be recursive, but the permissions are
	 * only set correctly on the innermost folder created.
	 *
	 * @param string $path Path.
	 * @param int $chmod CHMOD.
	 * @return bool True/false.
	 */
	public static function mkdir($path='', $chmod=null) {
		// Figure out a good default CHMOD.
		if (!$chmod || !is_numeric($chmod)) {
			$chmod = (fileperms(dirname(__FILE__)) & 0777 | 0755);
		}

		// Sanitize the path.
		ref\file::path($path, false);
		if (!$path || (false !== mb::strpos($path, '://'))) {
			return false;
		}

		// We only need to proceed if the path doesn't exist.
		if (!is_dir($path)) {
			ref\file::untrailingslash($path);

			// Figure out where we need to begin.
			$base = dirname($path);
			while ($base && ('.' !== $base) && !is_dir($base)) {
				$base = dirname($base);
			}

			// Make it.
			if (!@mkdir($path, 0777, true)) {
				return false;
			}

			// Fix permissions.
			if ($path !== $base) {
				// If we fell deep enough that base became relative,
				// let's move it back.
				if (!$base || ('.' === $base)) {
					$base = dirname(__FILE__);
				}

				// Base should be inside path. If not, something weird
				// has happened.
				if (0 !== mb::strpos($path, $base)) {
					return true;
				}

				$path = mb::substr($path, mb::strlen($base));
				ref\file::unleadingslash($path);
				$parts = explode('/', $path);
				$path = $base;

				// Loop through each subdirectory to set the appropriate
				// permissions.
				foreach ($parts as $v) {
					$path .= ('/' === substr($path, -1)) ? $v : "/$v";
					if (!@chmod($path, $chmod)) {
						return true;
					}
				}
			}
			else {
				@chmod($path, $chmod);
			}
		}

		return true;
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
	 * This greatly reduces overhead if serving files through a PHP
	 * gateway script.
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
	 * Will issue redirect headers or print Javascript if headers have
	 * already been sent.
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
			if ($handle = opendir($path)) {
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
				closedir($handle);
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
	 * Recursive Scandir
	 *
	 * @param string $path Path.
	 * @param bool $show_files Include files.
	 * @param bool $show_dirs Include directories.
	 * @return array Path(s).
	 */
	public static function scandir($path, $show_files=true, $show_dirs=true) {
		ref\file::path($path, true);
		if (!$path || !@is_dir($path) || (!$show_files && !$show_dirs)) {
			return array();
		}

		$out = array();
		if ($handle = opendir($path)) {
			ref\file::trailingslash($path);
			while (false !== ($file = readdir($handle))) {
				// Always ignore dots.
				if (('.' === $file) || ('..' === $file)) {
					continue;
				}

				// This is a file.
				if (@is_file("{$path}{$file}")) {
					if ($show_files) {
						$out[] = "{$path}{$file}";
					}
				}
				elseif (@is_dir("{$path}{$file}")) {
					if ($show_dirs) {
						$out[] = "{$path}{$file}";
					}
					$out = array_merge($out, static::scandir("{$path}{$file}", $show_files, $show_dirs));
				}
			}
			closedir($handle);
		}

		sort($out);
		return $out;
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


