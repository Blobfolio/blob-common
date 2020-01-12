<?php
/**
 * Blobfolio: Files
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Exception;
use Throwable;



final class Files {
	const MIME_DEFAULT = "application/octet-stream";
	const MIME_EMPTY = "inode/x-empty";

	private static $_loaded_blob_mimes = false;
	private static $_mimes_by_e;
	private static $_mimes_by_m;



	// -----------------------------------------------------------------
	// MIMES
	// -----------------------------------------------------------------

	/**
	 * Nice File Extension
	 *
	 * @param string $ext Extension.
	 * @param int $flags Flags.
	 * @return string Extension.
	 */
	public static function niceFileExtension(string $ext, int $flags=0) : string {
		$ext = \Blobfolio\Strings::toLower($ext, ($flags & globals_get("flag_trusted")));
		$ext = preg_replace("/\s/u", "", $ext);
		return rtrim(ltrim($ext, "*."), "*.");
	}

	/**
	 * Nice MIME Type
	 *
	 * @param string $mime MIME.
	 * @return string MIME.
	 */
	public static function niceMime(string $mime) : string {
		$mime = strtolower($mime);
		$mime = preg_replace("#[^-+*.a-z0-9/]#", "", $mime);

		if (
			(substr_count($mime, "/") !== 1) ||
			(0 === strpos($mime, "/")) ||
			("/" === substr($mime, -1))
		) {
			return "";
		}

		return $mime;
	}

	/**
	 * Get MIME Type
	 *
	 * @param string $path Path.
	 * @return string Type.
	 */
	public static function getMimeType(string $path) : string {
		$finfo = (array) self::finfo($path);
		return $finfo["mime"];
	}

	/**
	 * Get Extensions By MIME
	 *
	 * @param string $mime MIME.
	 * @return array|bool Extensions or false.
	 */
	public static function getMimeExtensions(string $mime) {
		$aliases = (array) self::getMimeMutations($mime);
		if (!count($aliases)) {
			return false;
		}

		// Make sure the data is loaded.
		self::loadMimes();

		$out = [];
		foreach ($aliases as $v) {
			if (isset(self::$_mimes_by_m[$v])) {
				$out = array_merge($out, self::$_mimes_by_m[$v]);
			}
		}

		if (!count($out)) {
			return false;
		}
		elseif (count($out) > 1) {
			$out = array_unique($out);
			$out = array_values($out);
		}

		return $out;
	}

	/**
	 * Get MIMES by Extension
	 *
	 * @param string $ext Extension.
	 * @return array|bool MIMEs or false.
	 */
	public static function getExtensionMimes(string $ext) {
		$ext = self::niceFileExtension($ext);
		if (empty($ext)) {
			return false;
		}

		// Make sure the data is loaded.
		self::loadMimes();

		if (isset(self::$_mimes_by_e[$ext])) {
			return self::$_mimes_by_e[$ext];
		}

		return false;
	}

	/**
	 * Get MIME Aliases
	 *
	 * This will mutate a given MIME to come up with x- and vnd.
	 * variants.
	 *
	 * @param string $mime MIME.
	 * @return array MIMEs.
	 */
	public static function getMimeMutations(string $mime) : array {
		$mime = self::niceMime($mime);
		if (empty($mime)) {
			return [];
		}

		if ((self::MIME_EMPTY === $mime) || (self::MIME_DEFAULT === $mime)) {
			return [$mime];
		}

		$out = [];

		// Weird Microsoft type.
		if (0 === strpos($mime, "application/cdfv2")) {
			$out[] = "application/vnd.ms-office";
		}

		// Split it into type and subtype.
		$parts = (array) explode("/", $mime);
		$subtype = (string) preg_replace("/^(x-|vnd\.)/", "", $parts[1]);
		$out[] = $parts[0] . "/x-" . $subtype;
		$out[] = $parts[0] . "/vnd." . $subtype;
		$out[] = $parts[0] . "/" . $subtype;

		usort($out, [static::class, "getMimeMutationsUsort"]);

		return $out;
	}

	/**
	 * Sort Aliases
	 *
	 * @param string $a A.
	 * @param string $b B.
	 * @return int Priority.
	 */
	private static function getMimeMutationsUsort(string $a, string $b) : int {
		if (preg_match("#/(x-|vnd\.)#", $a)) {
			$a = "1_" . $a;
		}
		else {
			$a = "0_" . $a;
		}
		if (preg_match("#/(x-|vnd\.)#", $b)) {
			$b = "1_" . $b;
		}
		else {
			$b = "0_" . $b;
		}

		return $a < $b ? -1 : 1;
	}

	/**
	 * Verify a MIME/ext Pairing
	 *
	 * @param string $ext Extension.
	 * @param string $mime MIME.
	 * @param bool $soft Soft check.
	 * @return bool True/false.
	 */
	public static function checkExtensionMimePair(string $ext, string $mime, bool $soft=true) {
		$ext = self::niceFileExtension($ext);
		if (empty($ext)) {
			return false;
		}

		$mime = self::niceMime($mime);
		if (empty($mime)) {
			return false;
		}

		// Soft pass for empty/unknown types.
		if (
			(self::MIME_EMPTY === $mime) ||
			($soft && (self::MIME_DEFAULT === $mime))
		) {
			return true;
		}

		// Also soft pass if we have no data for the extension.
		$mimes = self::getExtensionMimes($ext);
		if (false === $mimes) {
			return true;
		}

		return in_array($mime, $mimes, true);
	}

	/**
	 * Get File Info
	 *
	 * This function is a sexier version of finfo_open().
	 *
	 * @param string $path Path.
	 * @param string $niceName Alternate file name (for e.g. tmp uploads).
	 * @return array Data.
	 */
	public static function finfo(string $path, string $niceName="") : array {
		$path = \Blobfolio\Strings::utf8($path);
		if (!empty($niceName)) {
			$niceName = \Blobfolio\Strings::utf8($niceName);
		}

		$out = [
			"dirname"=>"",
			"basename"=>"",
			"extension"=>"",
			"filename"=>"",
			"path"=>"",
			"mime"=>self::MIME_DEFAULT,
			"rename"=>[]
		];

		// This could just be an extension.
		if (
			(false === strpos($path, ".")) &&
			(false === strpos($path, "/")) &&
			(false === strpos($path, "\\"))
		) {
			$out["extension"] = self::niceFileExtension($path);
			$mimes = self::getExtensionMimes($path);
			if (false !== $mimes) {
				$out["mime"] = $mimes[0];
			}

			return $out;
		}

		// This should be a path of some sort.
		$path = self::path($path);
		$out["path"] = $path;
		$out = \Blobfolio\Cast::parseArgs(pathinfo($path), $out);

		// Apply a nice name.
		if (!empty($niceName)) {
			$tmp = pathinfo($niceName);

			if (isset($tmp["filename"])) {
				$out["filename"] = $tmp["filename"];
			}
			else {
				$out["filename"] = "";
			}

			if (isset($tmp["extension"])) {
				$out["extension"] = $tmp["extension"];
			}
			else {
				$out["extension"] = "";
			}
		}

		// Can't trust pathinfo to sanitize the extension.
		$out["extension"] = self::niceFileExtension($out["extension"]);

		// Pull MIMEs for the extension.
		$mimes = self::getExtensionMimes($out["extension"]);
		if (false !== $mimes) {
			$out["mime"] = $mimes[0];
		}

		// See if this is a real path.
		try {
			$path = stream_resolve_include_path($path);
			if (!empty($path)) {
				$out["path"] = $path;
				$out["dirname"] = dirname($path);
			}

			// Try Magic MIMEs.
			if (is_file($path) && filesize($path) > 0) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$magicMime = (string) finfo_file($finfo, $path);
				$magicMime = self::niceMime($magicMime);
				finfo_close($finfo);

				// Fileinfo can misidentify SVGs if they are missing
				// their XML tag or DOCTYPE.
				if (
					("svg" === $out["extension"]) &&
					("image/svg+xml" !== $magicMime)
				) {
					$tmp = file_get_contents($path);
					if (
						is_string($tmp) &&
						(false !== stripos($tmp, "<svg"))
					) {
						$magicMime = "image/svg+xml";
					}
				}

				// Okay, dive deeper into the magic.
				if (
					!empty($magicMime) &&
					(
						(self::MIME_DEFAULT !== $out["mime"]) ||
						(0 !== strpos($magicMime, "text/"))
					) &&
					!self::checkExtensionMimePair($out["extension"], $magicMime)
				) {
					// Override what we've found so far if the magic is
					// legit.
					$tmp = self::getMimeExtensions($magicMime);
					if (false !== $tmp) {
						$out["mime"] = $magicMime;
						$out["extension"] = $tmp[0];

						// Build alternative names.
						foreach ($tmp as $v) {
							$out["rename"][] = $out["filename"] . "." . $v;
						}
					}
				}
			}
		}
		catch (Throwable $e) {
			return $out;
		}

		return $out;
	}



	// -----------------------------------------------------------------
	// Path Formatting
	// -----------------------------------------------------------------

	/**
	 * Add Leading Slash
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return string|array Path.
	 */
	public static function leadingSlash(string $str, int $flags=0) : string {
		return "/" . self::unleadingSlash($str, ($flags & globals_get("flag_trusted")));
	}

	/**
	 * Fix Path Formatting
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return bool|string Path or false.
	 */
	public static function path(string $str, int $flags=0) {
		$trusted = !! ($flags & globals_get("flag_trusted"));
		if (!$trusted) {
			$str = \Blobfolio\Strings::utf8($str);
		}

		$validate = !! ($flags & globals_get("flag_path_validate"));

		// This might be a URL rather than something local. We only want
		// to focus on local ones.
		if (preg_match("#^(https?|ftps?|sftp):#iu", $str)) {
			$str = \Blobfolio\Domains::niceUrl($str);
			if (empty($str)) {
				return false;
			}

			return $str;
		}

		// Strip leading file:// scheme.
		if (0 === strpos($str, "file://")) {
			$str = substr($str, 7);
		}

		// Fix up slashes.
		$str = self::unixSlash($str, globals_get("flag_trusted"));

		// Is this a real path?
		$old_str = $str;
		try {
			$str = stream_resolve_include_path($str);
		} catch (Throwable $e) {
			$str = "";
		}

		// A bad path.
		if (empty($str)) {
			// A valid path was required.
			if ($validate) {
				return false;
			}

			// Start again.
			$str = $old_str;
			try {
				$dir = stream_resolve_include_path(dirname($str));
				if ($dir) {
					$str = self::trailingSlash($dir, globals_get("flag_trusted")) . basename($str);
				}
			} catch (Throwable $e) {
				$str = $old_str;
			}
		}

		if (empty($str)) {
			return false;
		}

		// Always trail slashes on directories.
		if (is_dir($str)) {
			$str = self::trailingSlash($str, globals_get("flag_trusted"));
		}

		return $str;
	}

	/**
	 * Add Trailing Slash
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return string|array Path.
	 */
	public static function trailingSlash(string $str, int $flags=0) : string {
		return self::untrailingSlash($str, ($flags & globals_get("flag_trusted"))) . "/";
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return string|array Path.
	 */
	public static function unixSlash(string $str, int $flags=0) : string {
		$trusted = !! ($flags & globals_get("flag_trusted"));
		if (!$trusted) {
			$str = \Blobfolio\Strings::utf8($str);
		}

		$str = str_replace("\\", "/", $str);
		$str = str_replace("/./", "/", $str);
		return preg_replace("#/{2,}#u", "/", $str);
	}

	/**
	 * Strip Leading Slash
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return string|array Path.
	 */
	public static function unleadingSlash(string $str, int $flags=0) : string {
		$str = self::unixSlash($str, ($flags & globals_get("flag_trusted")));
		return ltrim($str, "/");
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $str Path.
	 * @param int $flags Flags.
	 * @return string|array Path.
	 */
	public static function untrailingSlash(string $str, int $flags=0) : string {
		$str = self::unixSlash($str, ($flags & globals_get("flag_trusted")));
		return rtrim($str, "/");
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Add BOM
	 *
	 * Place a BOM at the start of a string.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function addBom(string $str) : string {
		$bom = chr(239) . chr(187) . chr(191);
		$str = (string) str_replace($bom, "", $str);
		return $bom . $str;
	}

	/**
	 * Recursive Copy
	 *
	 * @param string $from Source.
	 * @param string $to Destination.
	 * @return bool True/false.
	 */
	public static function copy(string $from, string $to) : bool {
		// Double-check the from.
		$from = self::path($from, globals_get("flag_path_validate"));
		if (empty($from)) {
			return false;
		}

		$to = self::path($to);
		if (empty($to) || ($from === $to)) {
			return false;
		}

		// Recurse directories.
		if (is_dir($from)) {
			$to = self::trailingSlash($to, globals_get("flag_trusted"));

			// Make sure the destination root exists.
			if (is_dir($to)) {
				if (!self::mkdir($to)) {
					return false;
				}
			}

			// Copy all files and directories within.
			$handle = opendir($from);
			if ($handle) {
				$file = readdir($handle);
				while ($file) {
					// Ignore dots.
					if (("." === $file) || (".." === $file)) {
						$file = readdir($handle);
						continue;
					}

					// Recurse.
					self::copy($from . $file, $to . $file);
					$file = readdir($handle);
				}
				closedir($handle);
			}
			else {
				return false;
			}

			return true;
		}
		// Let PHP handle it.
		elseif (is_file($from)) {
			$dir_from = (string) dirname($from);
			$dir_to = (string) dirname($to);

			// Make sure the destination root exists.
			if (!is_dir($dir_to)) {
				$chmod = (fileperms($dir_from) & 0777 | 0755);
				if (!self::mkdir($dir_to, $chmod)) {
					return false;
				}
			}

			// Copy the file.
			if (!copy($from, $to)) {
				return false;
			}

			$chmod = (fileperms($from) & 0777 | 0644);
			chmod($to, $chmod);

			return true;
		}

		return false;
	}

	/**
	 * CSV Headers
	 *
	 * Read the first line of a CSV and locate the indexes of each
	 * column.
	 *
	 * If an array of columns is passed, only the indexes of those
	 * columns will be returned.
	 *
	 * If said argument is an associative array, the return value will
	 * use the argument keys rather than the CSV headers. This can be
	 * useful in cases where, e.g., a CSV has a stupid long header
	 * label.
	 *
	 * @param string $csv CSV file path.
	 * @param mixed $cols Filter columns.
	 * @param string $delimiter Delimiter.
	 * @return bool|array Headers.
	 */
	public static function csvHeaders(string $csv, $cols=false, string $delimiter=",") {
		// We definitely need a file.
		$csv = (string) self::path($csv, globals_get("flag_path_validate"));
		if (empty($csv) || !is_file($csv)) {
			return false;
		}

		// Are we looking for particular columns?
		$assoc = false;
		if (is_array($cols) && count($cols)) {
			if ("associative" === \Blobfolio\Arrays::getType($cols)) {
				$assoc = true;
			}
			$cols = array_flip($cols);
		}
		else {
			$cols = false;
		}

		// Open the CSV and look for the first line with stuff.
		$handle = fopen($csv, "r");
		if (false === $handle) {
			return false;
		}

		while (true) {
			$line = fgetcsv($handle, 0, $delimiter);

			// We only need to read one line, but if that fails, we're
			// done.
			if (false === $line) {
				return false;
			}

			// We can skip leading empties though.
			if (!isset($line[0]) || (null === $line[0])) {
				continue;
			}

			// Flip the line too.
			$line = array_flip($line);

			// If we aren't filtering columns, just cast and return.
			if (!$cols) {
				foreach ($line as $k=>$v) {
					$line[$k] = (int) v;
				}

				return $line;
			}

			$out = [];
			foreach ($cols as $k=>$v) {
				$key = $assoc ? v : k;
				$value = isset($line[$k]) ? (int) $line[$k] : false;
				$out[$key] = $value;
			}

			return $out;
		}

		// We shouldn't be here.
		return false;
	}

	/**
	 * Get Data URI for File
	 *
	 * @param string $path Path.
	 * @return string URI.
	 */
	public static function dataUri(string $path) : string {
		$path = self::path($path, globals_get("flag_path_validate"));
		if (empty($path) || !is_file($path)) {
			return "";
		}

		$content = (string) base64_encode(file_get_contents($path));
		$mime = (string) self::getMimeType($path);

		return "data:" . $mime . ";base64," . $content;
	}

	/**
	 * Hash Directory
	 *
	 * Generate an MD5 hash of all child files within a directory.
	 *
	 * @param string $str Directory.
	 * @return string|bool Hash or false.
	 */
	public static function hashDir(string $str) {
		$files = (array) self::scandir($str, true, false);
		if (!count($files)) {
			return md5("empty");
		}

		// Add up the file hashes.
		$soup = "";
		foreach ($files as $v) {
			$soup .= md5_file($v);
		}

		return md5($soup);
	}

	/**
	 * Directory Size
	 *
	 * @param string $path Path.
	 * @return int Size.
	 */
	public static function dirSize(string $str) : int {
		$size = 0;
		$files = (array) self::scandir($str, true, false);

		foreach ($files as $v) {
			$size += (int) filesize($v);
		}

		return $size;
	}

	/**
	 * Line Count
	 *
	 * Count the number of lines in a file as efficiently as possible.
	 *
	 * @param string $file File path.
	 * @param bool $trim Ignore whitespace-only lines.
	 * @return int Lines.
	 */
	public static function getLineCount(string $file, bool $trim=true) : int {
		// We definitely need a file.
		if (empty($file) || !is_file($file)) {
			return 0;
		}

		$lines = 0;

		// Unfortunately we still need to read the file line by line,
		// but at least we're only loading one line into memory at a
		// time. For large files, this makes a big difference.
		$handle = fopen($file, "r");
		if ($handle) {
			$line = fgets($handle);
			while ($line) {
				if ($trim) {
					if (trim($line)) {
						$lines++;
					}
				}
				else {
					$lines++;
				}

				$line = fgets($handle);
			}
			fclose($handle);
		}

		return $lines;
	}

	/**
	 * Is Directory Empty?
	 *
	 * @param string $str Path.
	 * @return bool True/false.
	 */
	public static function isEmptyDir(string $str) : bool {
		if (!is_readable($str) || !is_dir($str)) {
			return false;
		}

		// Scan all files in dir.
		$files = (array) self::scandir($str, true, true, 0);
		return empty($files);
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
	public static function mkdir(string $str, $chmod=null) : bool {
		// Figure out a good default CHMOD.
		if (empty($chmod) || !is_numeric($chmod)) {
			$chmod = (fileperms(getcwd()) & 0777 | 0755);
		}

		// Sanitize the path.
		$str = (string) self::path($str);
		if (empty($str) || (false !== strpos($str, "://"))) {
			return false;
		}

		// We only need to proceed if the path doesn't exist.
		if (!is_dir($str)) {
			$str = self::untrailingSlash($str, globals_get("flag_trusted"));

			// Figure out where we need to begin.
			$base = (string) dirname($str);
			while ($base && ("." !== $base) && !is_dir($base)) {
				$base = dirname($base);
			}

			// PHP can recursively make a directory; the real problem
			// is it doesn't fix permissions in the middle.
			if (!mkdir($str, 0777, true)) {
				return false;
			}

			// Fix permissions.
			if ($str !== $base) {
				// If we fell deep enough that base became relative,
				// let's move it back.
				if (empty($base) || ("." === $base)) {
					$base = (string) getcwd();
				}

				// Base should be inside path. If not, something weird
				// has happened.
				if (0 !== mb_strpos($str, $base, 0, "UTF-8")) {
					return true;
				}

				$str = mb_substr(
					$str,
					(int) mb_strlen($base, "UTF-8"),
					null,
					"UTF-8"
				);
				$str = self::unleadingSlash($str, globals_get("flag_trusted"));
				$parts = (array) explode("/", $str);
				$str = $base;

				// Loop through each subdirectory to set the appropriate
				// permissions.
				foreach ($parts as $v) {
					$str .= ("/" === substr($str, -1)) ? $v : "/" . $v;
					if (!chmod($str, $chmod)) {
						return true;
					}
				}
			}
			else {
				chmod($str, $chmod);
			}
		}

		return true;
	}

	/**
	 * Readfile Chunked
	 *
	 * @param string $file File.
	 * @param bool $retbytes Return bites served.
	 */
	public static function readfileChunked(string $file, bool $retbytes=true) {
		if (empty($file) || !is_file($file)) {
			return false;
		}

		$chunk_size = 1024 * 1024;
		$count = 0;
		$buffer = "";

		$handle = fopen($file, "rb");
		if (!$handle) {
			return false;
		}

		while (!feof($handle)) {
			$buffer = (string) fread($handle, $chunk_size);
			echo $buffer;
			ob_flush();
			flush();
			if ($retbytes) {
				$count += strlen($buffer);
			}
		}

		$status = fclose($handle);

		// Return the number of bytes delivered.
		if ($retbytes && $status) {
			return $count;
		}

		return $status;
	}

	/**
	 * Recursively Remove A Directory
	 *
	 * @param string $path Path.
	 * @return bool True/false.
	 */
	public static function rmdir(string $str) : bool {
		$str = self::path($str, globals_get("flag_path_validate"));
		if (empty($str) || !is_readable($str) || !is_dir($str)) {
			return false;
		}

		// Scan all files in dir.
		$handle = opendir($str);
		if ($handle) {
			$file = readdir($handle);
			while ($file) {
				// Anything but a dot === not empty.
				if (("." === $file) || (".." === $file)) {
					$file = readdir($handle);
					continue;
				}

				$path = $str . $file;

				// Delete files.
				if (is_file($path)) {
					unlink($path);
				}
				// Recursively delete directories.
				else {
					self::rmdir($path);
				}

				$file = readdir($handle);
			}
			closedir($handle);
		}

		if (self::isEmptyDir($str)) {
			rmdir($str);
		}

		return (false === stream_resolve_include_path($str));
	}

	/**
	 * Recursive Scandir
	 *
	 * @param string $str Path.
	 * @param bool $show_files Include files.
	 * @param bool $show_dirs Include directories.
	 * @param int $depth Depth.
	 * @return array Path(s).
	 */
	public static function scandir(string $str, bool $show_files=true, bool $show_dirs=true, int $depth=-1) : array {
		$str = self::path($str, globals_get("flag_path_validate"));
		if (empty($str) || !is_dir($str) || (!$show_files && !$show_dirs)) {
			return [];
		}

		// Set the depth for recursion.
		$inner_depth = -1;
		if ($depth > 1) {
			$inner_depth = $depth - 1;
		}
		elseif (1 === $depth) {
			$inner_depth = 0;
		}

		$out = [];
		$handle = opendir($str);
		if ($handle) {
			$str = self::trailingSlash($str, globals_get("flag_trusted"));
			$file = readdir($handle);
			while ($file) {
				// Always ignore dots.
				if (("." === $file) || (".." === $file)) {
					$file = readdir($handle);
					continue;
				}

				$path = $str . $file;

				// This is a file.
				if (is_file($path)) {
					if ($show_files) {
						$out[] = $path;
					}
				}
				elseif (is_dir($path)) {
					if ($show_dirs) {
						$out[] = $path . "/";
					}

					// Recurse?
					if (0 !== $inner_depth) {
						$out = (array) array_merge(
							$out,
							self::scandir(
								$path,
								$show_files,
								$show_dirs,
								$inner_depth
							)
						);
					}
				}

				$file = readdir($handle);
			}
			closedir($handle);
		}

		sort($out);
		return $out;
	}

	/**
	 * Strip BOM
	 *
	 * Remove BOM from the start of a string.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function stripBom(string $str) : string {
		$bom = chr(239) . chr(187) . chr(191);
		return (string) str_replace($bom, "", $str);
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Load MIME Data
	 *
	 * For performance reasons, this data stored in an external location
	 * and only loaded if/when needed.
	 *
	 * @return void Nothing.
	 * @throws Exception Error.
	 */
	private static function loadMimes() : void {
		// Don't allow accidental repeats.
		if (true === self::$_loaded_blob_mimes) {
			return;
		}

		// Gotta load it!
		$json = (string) \Blobfolio\Blobfolio::getDataDir("blob-mimes.json");
		if (empty($json)) {
			throw new Exception("Missing MIME data.");
		}

		$tmp = json_decode($json, true);
		if ("array" !== gettype($tmp)) {
			throw new Exception("Could not parse MIME data.");
		}

		self::$_mimes_by_e = (array) $tmp["extensions"];
		self::$_mimes_by_m = (array) $tmp["mimes"];

		self::$_loaded_blob_mimes = true;
	}
}
