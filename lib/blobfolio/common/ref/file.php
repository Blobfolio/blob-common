<?php
//---------------------------------------------------------------------
// FILE
//---------------------------------------------------------------------
// files and paths



namespace blobfolio\common\ref;

class file {

	//-------------------------------------------------
	// Leadingslash
	//
	// @param path
	// @return path
	public static function leadingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::leadingslash($path[$k]);
			}
		}
		else {
			cast::string($path);
			static::unleadingslash($path);
			$path = "/$path";
		}

		return true;
	}

	//-------------------------------------------------
	// Path
	//
	// @param path
	// @param validate
	// @return path
	public static function path(&$path='', bool $validate=true) {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::path($path[$k], $validate);
			}
		}
		else {
			cast::string($path);
			static::unixslash($path);

			$original = $path;
			try {
				$path = realpath($path);
			} catch (\Throwable $e) {
				$path = false;
			}

			if ($validate && false === $path) {
				$path = false;
				return false;
			}
			elseif (false === $path) {
				//try just the directory
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
				}
			}

			$original = $path;
			try {
				if (is_dir($path)) {
					static::trailingslash($path);
				}
			} catch (\Throwable $e) {
				$path = $original;
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Trailingslash
	//
	// @param path
	// @return path
	public static function trailingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::trailingslash($path[$k]);
			}
		}
		else {
			cast::string($path);
			static::untrailingslash($path);
			$path .= '/';
		}

		return true;
	}

	//-------------------------------------------------
	// Fix Slashes
	//
	// @param path
	// @return path
	public static function unixslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::unixslash($path[$k]);
			}
		}
		else {
			cast::string($path);
			$path = str_replace('\\', '/', $path);
			$path = str_replace('/./', '//', $path);
			$path = preg_replace('/\/{2,}/', '/', $path);
		}

		return true;
	}

	//-------------------------------------------------
	// Unleadingslash
	//
	// @param path
	// @return path
	public static function unleadingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::unleadingslash($path[$k]);
			}
		}
		else {
			cast::string($path);
			static::unixslash($path);
			$path = ltrim($path, '/');
		}

		return true;
	}

	//-------------------------------------------------
	// Untrailingslash
	//
	// @param path
	// @return path
	public static function untrailingslash(&$path='') {
		if (is_array($path)) {
			foreach ($path as $k=>$v) {
				static::untrailingslash($path[$k]);
			}
		}
		else {
			cast::string($path);
			static::unixslash($path);
			$path = rtrim($path, '/');
		}

		return true;
	}

}

?>