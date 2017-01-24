<?php
//---------------------------------------------------------------------
// MIME Types
//---------------------------------------------------------------------
// handle MIME types and file extensions



namespace blobfolio\common;

class mime {

	//our data
	protected static $by_mime;
	protected static $by_ext;



	//---------------------------------------------------------------------
	// Data Population
	//---------------------------------------------------------------------

	//-------------------------------------------------
	// Load JSON
	//
	// load JSON from a file
	//
	// @param path
	// @return JSON or Exception
	protected static function load_json(string $path = '') {
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "$path";
		ref\file::path($path, true);

		if (false === $path) {
			throw new \Exception('Invalid JSON path.');
		}

		$data = cast::string(@file_get_contents($path));
		$data = json_decode($data, true);

		if (!is_array($data)) {
			throw new \Exception('Invalid JSON data.');
		}

		return $data;
	}

	//-------------------------------------------------
	// Load MIMEs
	//
	// @param n/a
	// @return true
	protected static function load_mimes() {
		//populate MIME data if needed
		if (!is_array(static::$by_mime)) {
			if (false === static::$by_mime = static::load_json(constants::BY_MIME_FILE)) {
				throw new \Exception('Could not load MIME database.');
			}
		}

		return true;
	}

	//-------------------------------------------------
	// Load Extensions
	//
	// @param n/a
	// @return true
	protected static function load_extensions() {
		if (!is_array(static::$by_ext)) {
			if (false === static::$by_ext = static::load_json(constants::BY_EXT_FILE)) {
				throw new \Exception('Could not load file extension database.');
			}
		}

		return true;
	}

	//--------------------------------------------------------------------- end data population



	//---------------------------------------------------------------------
	// Public Data Access
	//---------------------------------------------------------------------

	//-------------------------------------------------
	// Get All MIMEs
	//
	// @param n/a
	// @return mimes
	public static function get_mimes() {
		static::load_mimes();
		return static::$by_mime;
	}

	//-------------------------------------------------
	// Get MIME entry
	//
	// @param mime
	// @return data or false
	public static function get_mime(string $mime = '') {
		ref\sanitize::mime($mime);
		static::load_mimes();
		return isset(static::$by_mime[$mime]) ? static::$by_mime[$mime] : false;
	}

	//-------------------------------------------------
	// Get All Extensions
	//
	// @param n/a
	// @return extensions
	public static function get_extensions() {
		static::load_extensions();
		return static::$by_ext;
	}

	//-------------------------------------------------
	// Get extension entry
	//
	// @param ext
	// @return data or false
	public static function get_extension(string $ext = '') {
		ref\sanitize::file_extension($ext);
		static::load_extensions();
		return isset(static::$by_ext[$ext]) ? static::$by_ext[$ext] : false;
	}

	//-------------------------------------------------
	// Verify MIME and Extension pair
	//
	// @param ext
	// @param mime
	// @param soft pass
	// @return true/false
	public static function check_ext_and_mime(string $ext = '', string $mime = '', bool $soft=true) {
		ref\sanitize::file_extension($ext);
		if (!mb::strlen($ext)) {
			return false;
		}

		ref\sanitize::mime($mime);
		if (!strlen($mime) || ($soft && constants::MIME_DEFAULT === $mime)) {
			return true;
		}

		//soft pass on extension fail
		if (false === $ext = static::get_extension($ext)) {
			return $soft;
		}

		//loose mime check
		$real = $ext['mime'];
		$test = array($mime);

		//we want to also look for x-type variants
		$parts = explode('/', $mime);
		if (preg_match('/^x\-/', $parts[count($parts) - 1])) {
			$parts[count($parts) - 1] = preg_replace('/^x\-/', '', $parts[count($parts) - 1]);
		}
		else {
			$parts[count($parts) - 1] = 'x-' . $parts[count($parts) - 1];
		}
		$test[] = implode('/', $parts);

		//any overlap?
		$found = array_intersect($real, $test);
		return count($found) > 0;
	}

	//-------------------------------------------------
	// Get File Info
	//
	// @param path
	// @param true name, for e.g. tmp uploads
	// @return info or false
	public static function finfo(string $path = '', string $nice = null) {
		$out = array(
			'dirname'=>'',
			'basename'=>'',
			'extension'=>'',
			'filename'=>'',
			'path'=>'',
			'mime'=>constants::MIME_DEFAULT,
			'suggested_filename'=>array(),
		);

		//path might just be an extension
		ref\cast::string($path);
		if (false === mb::strpos($path, '.') &&
			false === mb::strpos($path, '/') &&
			false === mb::strpos($path, '\\')
		) {
			$out['extension'] = sanitize::file_extension($path);
			if (false !== ($ext = static::get_extension($path))) {
				$out['mime'] = $ext['primary'];
			}

			return $out;
		}

		//path is something path-like
		ref\file::path($path, false);
		$out['path'] = $path;
		$out = data::parse_args(pathinfo($path), $out);

		if (is_string($nice)) {
			$pathinfo = pathinfo($nice);
			$out['filename'] = isset($pathinfo['filename']) ? $pathinfo['filename'] : '';
			$out['extension'] = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
		}

		ref\sanitize::file_extension($out['extension']);

		//pull the mimes from the extension
		if (false !== ($ext = static::get_extension($out['extension']))) {
			$out['mime'] = $ext['primary'];
		}

		//try to read the magic mime, if possible
		try {
			//find the real path, if possible
			if (false !== ($path = realpath($path))) {
				$out['path'] = $path;
				$out['dirname'] = dirname($path);
			}

			//lookup magic mime, if possible
			if (
				false !== $path &&
				function_exists('finfo_file') &&
				defined('FILEINFO_MIME_TYPE') &&
				is_file($path)
			) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$magic_mime = sanitize::mime(finfo_file($finfo, $path));
				if (
					$magic_mime &&
					constants::MIME_DEFAULT !== $magic_mime &&
					(constants::MIME_DEFAULT === $out['mime'] || !preg_match('/^text\//', $magic_mime)) &&
					!static::check_ext_and_mime($out['extension'], $magic_mime)
				) {
					//if we have an alternative magic mime and it is legit,
					//it should override what we derived from the name
					if (false !== ($mime = static::get_mime($magic_mime))) {
						$out['mime'] = $magic_mime;
						$out['extension'] = $mime['ext'][0];
						foreach ($mime['ext'] as $ext) {
							$out['suggested_filename'][] = "{$out['filename']}.$ext";
						}
					}
				}
			}
		} catch (\Throwable $e) {
			return $out;
		}

		return $out;
	}

	//--------------------------------------------------------------------- end public data access
}


?>