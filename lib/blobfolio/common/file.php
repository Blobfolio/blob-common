<?php
//---------------------------------------------------------------------
// FILE
//---------------------------------------------------------------------
// files and paths



namespace blobfolio\common;

class file {

	//-------------------------------------------------
	// Data URI
	//
	// @param path
	// @return uri or false
	public static function data_uri($path='') {
		ref\cast::string($path, true);
		ref\file::path($path, true);
		try {
			if (false !== $path && is_file($path)) {
				$content = base64_encode(@file_get_contents($path));
				$finfo = mime::finfo($path);
				return 'data:' . $finfo['mime'] . ';base64,' . $content;
			}
		} catch (\Throwable $e) {
			print_r($e);
			return false;
		}

		return false;
	}

	//-------------------------------------------------
	// Empty Dir
	//
	// @param path
	// @return true/false
	public static function empty_dir($path='') {
		try {
			ref\cast::string($path);
			if (!is_readable($path) || !is_dir($path)) {
				return false;
			}

			//scan all files in dir
			$handle = opendir($path);
			while (false !== ($entry = readdir($handle))) {
				//anything but a dot === not empty
				if ($entry !== '.' && $entry !== '..') {
					return false;
				}
			}

			//nothing found
			return true;
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// Leadingslash
	//
	// @param path
	// @return path
	public static function leadingslash($path='') {
		ref\file::leadingslash($path);
		return $path;
	}

	//-------------------------------------------------
	// Path
	//
	// @param path
	// @param validate
	// @return path
	public static function path($path='', bool $validate=true) {
		ref\file::path($path, $validate);
		return $path;
	}

	//-------------------------------------------------
	// Readfile() in chunks
	//
	// this greatly reduces the server resource demands
	// compared with reading a file all in one go
	//
	// @param file
	// @param bytes
	// @return bytes or false
	public static function readfile_chunked(string $file, $retbytes=true) {
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

		//return number of bytes delivered like readfile() does
		if ($retbytes && $status) {
			return $cnt;
		}

		return $status;
	}

	//-------------------------------------------------
	// Redirect
	//
	// @param to
	// @return n/a
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

	//-------------------------------------------------
	// Trailingslash
	//
	// @param path
	// @return path
	public static function trailingslash($path='') {
		ref\file::trailingslash($path);
		return $path;
	}

	//-------------------------------------------------
	// Fix Slashes
	//
	// @param path
	// @return path
	public static function unixslash($path='') {
		ref\file::unixslash($path);
		return $path;
	}

	//-------------------------------------------------
	// Unleadingslash
	//
	// @param path
	// @return path
	public static function unleadingslash($path='') {
		ref\file::unleadingslash($path);
		return $path;
	}

	//-------------------------------------------------
	// Reverse parse_url() to get back to a URL
	//
	// @param parsed
	// @return url or false
	public static function unparse_url(array $parsed=null) {
		$url = '';
		$parsed = data::parse_args($parsed, constants::URL_PARTS);

		//to simplify, unset anything without length
		$parsed = array_map('trim', $parsed);
		$parsed = array_filter($parsed, 'strlen');

		//we don't really care about validating url integrity,
		//but if nothing at all was passed then it is trash
		if (!count($parsed)) {
			return false;
		}

		if (isset($parsed['scheme'])) {
			$url = "{$parsed['scheme']}:";
		}

		if (isset($parsed['host'])) {
			$url .= '//';

			//is this a user:pass situation?
			if (isset($parsed['user'])) {
				$url .= $parsed['user'];
				if (isset($parsed['pass'])) {
					$url .= ":{$parsed['pass']}";
				}
				$url .= '@';
			}

			//finally the host
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

		ref\sanitize::url($url);

		return mb::strlen($url) ? $url : false;
	}

	//-------------------------------------------------
	// Untrailingslash
	//
	// @param path
	// @return path
	public static function untrailingslash($path='') {
		ref\file::untrailingslash($path);
		return $path;
	}

}

?>