//<?php
/**
 * Blobfolio: Files
 *
 * Filepath helpers.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Files {
	// -----------------------------------------------------------------
	// Path Formatting
	// -----------------------------------------------------------------

	/**
	 * Add Leading Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function leadingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::leadingSlash(v);
			}
			return str;
		}

		return "/" . self::unleadingSlash(str);
	}

	/**
	 * Fix Path Formatting
	 *
	 * @param string $str Path.
	 * @param bool $validate Require valid file.
	 * @return bool|string Path or false.
	 */
	public static function path(var str, const bool validate=true) -> string | bool {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::path(v, validate);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);

		// This might be a URL rather than something local. We only want
		// to focus on local ones.
		if (preg_match("#^(https?|ftps?|sftp):#iu", str)) {
			// TODO try not to depend on library methods.
			var class_name = "\\blobfolio\\common\\sanitize";
			if (class_exists(class_name)) {
				return {class_name}::url(str);
			}

			return str;
		}

		// Strip leading file:// scheme.
		if (0 === strpos(str, "file://")) {
			let str = substr(str, 7);
		}

		// Fix up slashes.
		let str = self::unixSlash(str);

		// Is this a real path?
		string old_str = str;
		try {
			let str = realpath(str);
		} catch Throwable {
			let str = false;
		}

		// A bad path.
		if (empty str) {
			// A valid path was required.
			if (validate) {
				return false;
			}

			// Start again.
			let str = old_str;
			try {
				var dir = realpath(dirname(str));
				if (dir) {
					let str = self::trailingSlash(dir) . basename(str);
				}
			} catch Throwable {
				let str = old_str;
			}
		}

		// Always trail slashes on directories.
		if (is_dir(str)) {
			let str = self::trailingSlash(str);
		}

		return str;
	}

	/**
	 * Add Trailing Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function trailingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::trailingSlash(v);
			}
			return str;
		}

		return self::untrailingSlash(str) . "/";
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function unixSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::unixSlash(v);
			}
			return str;
		}

		let str = (string) \Blobfolio\Cast::toString(str, true);
		let str = str_replace("\\", "/", str);
		let str = str_replace("/./", "/", str);
		return preg_replace("#/{2,}#u", "/", str);
	}

	/**
	 * Strip Leading Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function unleadingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::unleadingSlash(v);
			}
			return str;
		}

		let str = self::unixSlash(str);
		return ltrim(str, "/");
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function untrailingSlash(var str) -> string | array {
		// Recurse.
		if ("array" === typeof str) {
			var k, v;
			for k, v in str {
				let str[k] = self::untrailingSlash(v);
			}
			return str;
		}

		let str = self::unixSlash(str);
		return rtrim(str, "/");
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Recursive Copy
	 *
	 * @param string $from Source.
	 * @param string $to Destination.
	 * @return bool True/false.
	 */
	public static function copy(string from, string to) -> bool {
		// Double-check the from.
		let from = (string) self::path(from, true);
		if (empty from) {
			return false;
		}

		let to = (string) self::path(to, false);
		if (empty to || (from === to)) {
			return false;
		}

		// Recurse directories.
		if (is_dir(from)) {
			let to = self::trailingSlash(to);

			// Make sure the destination root exists.
			if (is_dir(to)) {
				if (!self::mkdir(to)) {
					return false;
				}
			}

			// Copy all files and directories within.
			var handle = opendir(from);
			if (handle) {
				var file = readdir(handle);
				while (file) {
					// Ignore dots.
					if (("." === file) || (".." === file)) {
						let file = readdir(handle);
						continue;
					}

					// Recurse.
					self::copy(from . file, to . file);
					let file = readdir(handle);
				}
				closedir(handle);
			}
			else {
				return false;
			}

			return true;
		}
		// Let PHP handle it.
		elseif (is_file(from)) {
			string dir_from = (string) dirname(from);
			string dir_to = (string) dirname(to);

			// Make sure the destination root exists.
			if (!is_dir(dir_to)) {
				var chmod = (fileperms(dir_from) & 0777 | 0755);
				if (!self::mkdir(dir_to, chmod)) {
					return false;
				}
			}

			// Copy the file.
			if (!copy(from, to)) {
				return false;
			}

			var chmod = (fileperms(from) & 0777 | 0644);
			chmod(to, chmod);

			return true;
		}

		echo "--BADDEFAULT--";
		return false;
	}

	/**
	 * Hash Directory
	 *
	 * Generate a hash of all child files within a directory. Any
	 * algorithm supported by hash_file() is supported here, but be
	 * careful not to combine a slow algorithm with a large directory.
	 *
	 * @param string $str Directory.
	 * @param string $dir_algo Result hashing algorithm.
	 * @param string $file_algo File hashing algorithm.
	 * @return string|bool Hash or false.
	 */
	public static function dirhash(const string str, string dir_algo="md5", string file_algo="") -> string | bool {
		// We definitely need a valid directory algorithm.
		if (empty dir_algo || !in_array(dir_algo, hash_algos(), true)) {
			return false;
		}

		// If the file algorithm is bad or missing, we can just use the
		// same method as we are for our result.
		if (empty file_algo || !in_array(file_algo, hash_algos(), true)) {
			let file_algo = dir_algo;
		}

		array files = (array) static::scandir(str, true, false);
		if (!count(files)) {
			return hash(dir_algo, "empty");
		}

		// Add up the file hashes.
		string soup = "";
		var v;
		for v in files {
			let soup .= hash_file(file_algo, v);
		}

		return hash(dir_algo, soup);
	}

	/**
	 * Directory Size
	 *
	 * @param string $path Path.
	 * @return int Size.
	 */
	public static function dirsize(const string str) -> int {
		int size = 0;
		array files = (array) self::scandir(str, true, false);

		var v;
		for v in files {
			let size += (int) filesize(v);
		}

		return size;
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
	public static function getLineCount(const string file, const bool trim=true) -> int {
		// We definitely need a file.
		if (empty file || !is_file(file)) {
			return 0;
		}

		int lines = 0;

		// Unfortunately we still need to read the file line by line,
		// but at least we're only loading one line into memory at a
		// time. For large files, this makes a big difference.
		var handle = fopen(file, "r");
		if (handle) {
			var line = fgets(handle);
			while (line) {
				if (trim) {
					if (trim(line)) {
						let lines += 1;
					}
				}
				else {
					let lines += 1;
				}

				let line = fgets(handle);
			}
			fclose(handle);
		}

		return lines;
	}

	/**
	 * Is Directory Empty?
	 *
	 * @param string $str Path.
	 * @return bool True/false.
	 */
	public static function isEmptyDir(string str) -> bool {
		if (!is_readable(str) || !is_dir(str)) {
			return false;
		}

		// Scan all files in dir.
		array files = (array) self::scandir(str, true, true, 0);
		return !count(files);
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
	public static function mkdir(string str, var chmod=null) -> bool {
		// Figure out a good default CHMOD.
		if (empty chmod || !is_numeric(chmod)) {
			let chmod = (fileperms(getcwd()) & 0777 | 0755);
		}

		// Sanitize the path.
		let str = (string) self::path(str, false);
		if (empty str || (false !== strpos(str, "://"))) {
			return false;
		}

		// We only need to proceed if the path doesn't exist.
		if (!is_dir(str)) {
			let str = self::untrailingSlash(str);

			// Figure out where we need to begin.
			string base = (string) dirname(str);
			while (base && ("." !== base) && !is_dir(base)) {
				let base = dirname(base);
			}

			// PHP can recursively make a directory; the real problem
			// is it doesn't fix permissions in the middle.
			if (!mkdir(str, 0777, true)) {
				return false;
			}

			// Fix permissions.
			if (str !== base) {
				// If we fell deep enough that base became relative,
				// let's move it back.
				if (empty base || ("." === base)) {
					let base = (string) getcwd();
				}

				// Base should be inside path. If not, something weird
				// has happened.
				if (0 !== \Blobfolio\Strings::strpos(str, base)) {
					return true;
				}

				let str = (string) \Blobfolio\Strings::substr(
					str,
					\Blobfolio\Strings::strlen(base),
					null
				);
				let str = self::unleadingSlash(str);
				array parts = (array) explode("/", str);
				let str = base;

				// Loop through each subdirectory to set the appropriate
				// permissions.
				var v;
				for v in parts {
					let str .= ("/" === substr(str, -1)) ? v : "/" . v;
					if (!chmod(str, chmod)) {
						return true;
					}
				}
			}
			else {
				chmod(str, chmod);
			}
		}

		return true;
	}

	/**
	 * Recursively Remove A Directory
	 *
	 * @param string $path Path.
	 * @return bool True/false.
	 */
	public static function rmdir(string str) -> bool {
		let str = (string) self::path(str, true);
		if (empty str || !is_readable(str) || !is_dir(str)) {
			return false;
		}

		// Scan all files in dir.
		var handle = opendir(str);
		if (handle) {
			var file = readdir(handle);
			while (file) {
				// Anything but a dot === not empty.
				if (("." === file) || (".." === file)) {
					let file = readdir(handle);
					continue;
				}

				string path = str . file;

				// Delete files.
				if (is_file(path)) {
					unlink(path);
				}
				// Recursively delete directories.
				else {
					self::rmdir(path);
				}

				let file = readdir(handle);
			}
			closedir(handle);
		}

		if (self::isEmptyDir(str)) {
			rmdir(str);
		}

		return !file_exists(str);
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
	public static function scandir(string str, const bool show_files=true, const bool show_dirs=true, const int depth=-1) {
		let str = (string) self::path(str, true);
		if (empty str || !is_dir(str) || (!show_files && !show_dirs)) {
			return [];
		}

		// Set the depth for recursion.
		int inner_depth = -1;
		if (depth > 1) {
			let inner_depth = depth - 1;
		}
		elseif (1 === depth) {
			let inner_depth = 0;
		}

		array out = [];
		var handle = opendir(str);
		if (handle) {
			let str = self::trailingSlash(str);
			var file = readdir(handle);
			while (file) {
				// Always ignore dots.
				if (("." === file) || (".." === file)) {
					let file = readdir(handle);
					continue;
				}

				string path = str . file;

				// This is a file.
				if (is_file(path)) {
					if (show_files) {
						let out[] = path;
					}
				}
				elseif (is_dir(path)) {
					if (show_dirs) {
						let out[] = path . "/";
					}

					// Recurse?
					if (0 !== inner_depth) {
						let out = (array) array_merge(
							out,
							self::scandir(
								path,
								show_files,
								show_dirs,
								inner_depth
							)
						);
					}
				}

				let file = readdir(handle);
			}
			closedir(handle);
		}

		sort(out);
		return out;
	}
}
