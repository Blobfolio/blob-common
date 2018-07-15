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
	const MIME_DEFAULT = "application/octet-stream";
	const MIME_EMPTY = "inode/x-empty";

	private static _mimes_by_e;
	private static _mimes_by_m;



	// -----------------------------------------------------------------
	// Images
	// -----------------------------------------------------------------

	/**
	 * Determine SVG Dimensions
	 *
	 * @param string $svg SVG content or file path.
	 * @return array|bool Dimensions or false.
	 */
	public static function getSvgDimensions(string svg) -> bool | array {
		let svg = Cast::toString(svg, true);

		// Make sure this is SVG-looking.
		var start = stripos(svg, "<svg");
		if (false === start) {
			if (is_file(svg)) {
				let svg = file_get_contents(svg);
				let start = stripos(svg, "<svg");
				if (false === start) {
					return false;
				}
			}
			else {
				return false;
			}
		}

		// Chop the code to the opening <svg> tag.
		if (0 !== start) {
			let svg = substr(svg, start);
		}
		var end = strpos(svg, '>');
		if (false === end) {
			return false;
		}
		let svg = strtolower(substr(svg, 0, end + 1));

		// Hold our values.
		array out = [
			"width": null,
			"height": null
		];
		var viewbox = null;

		// Search for width, height, and viewbox.
		let svg = Strings::whitespace(svg, 0);
		var match;
		preg_match_all(
			"/(height|width|viewbox)\s*=\s*([\"'])((?:(?!\2).)*)\2/",
			svg,
			match,
			PREG_SET_ORDER
		);

		var k, v;
		if (("array" === typeof match) && count(match)) {
			for v in match {
				switch (v[1]) {
					case "width":
					case "height":
						let v[3] = Cast::toFloat($v[3], true);
						if (v[3] > 0.0) {
							let out[v[1]] = v[3];
						}

						break;
					case "viewbox":
						// Defer processing for later.
						let viewbox = v[3];
						break;
				}
			}
		}

		// If we have a width and height, we're done!
		if (!empty out["width"] && !empty out["height"]) {
			return out;
		}

		// Maybe pull from viewbox?
		if (!empty viewbox) {
			// Sometimes these are comma-separated.
			let viewbox = trim(str_replace(",", " ", viewbox));
			let viewbox = explode(" ", viewbox);

			for k, v in viewbox {
				let viewbox[k] = Cast::toFloat(v, true);
				if (viewbox[k] < 0.0) {
					let viewbox[k] = 0.0;
				}
			}
			if ((count(viewbox) === 4) && viewbox[2] > 0.0 && viewbox[3] > 0.0) {
				let out["width"] = viewbox[2];
				let out["height"] = viewbox[3];
				return out;
			}
		}

		return false;
	}



	// -----------------------------------------------------------------
	// MIMES
	// -----------------------------------------------------------------

	/**
	 * Nice File Extension
	 *
	 * @param string $ext Extension.
	 * @return string Extension.
	 */
	public static function niceFileExtension(string ext) -> string {
		let ext = Strings::strtolower(ext, false);
		let ext = preg_replace("/\s/u", "", ext);
		return ltrim(ext, "*.");
	}

	/**
	 * Nice MIME Type
	 *
	 * @param string $mime MIME.
	 * @return string MIME.
	 */
	public static function niceMime(string mime) -> string {
		let mime = strtolower(mime);
		let mime = preg_replace("#[^-+*.a-z0-9/]#", "", mime);

		if (
			(substr_count(mime, "/") !== 1) ||
			(0 === strpos(mime, "/")) ||
			("/" === substr(mime, -1))
		) {
			return "";
		}

		return mime;
	}

	/**
	 * Get MIME Type
	 *
	 * @param string $path Path.
	 * @return string Type.
	 */
	public static function getMimeType(string path) -> string {
		array finfo = (array) self::finfo(path);
		return finfo["mime"];
	}

	/**
	 * Get Extensions By MIME
	 *
	 * @param string $mime MIME.
	 * @return array|bool Extensions or false.
	 */
	public static function getMimeExtensions(const string mime) -> array | bool {
		array aliases = (array) self::getMimeMutations(mime);
		if (!count(aliases)) {
			return false;
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_blob_mimes")) {
			self::loadMimes();
		}

		array out = [];
		var v;

		for v in aliases {
			if (isset(self::_mimes_by_m[v])) {
				let out = array_merge(out, self::_mimes_by_m[v]);
			}
		}

		if (!count(out)) {
			return false;
		}
		elseif (count(out) > 1) {
			let out = array_unique(out);
			let out = array_values(out);
		}

		return out;
	}

	/**
	 * Get MIMES by Extension
	 *
	 * @param string $ext Extension.
	 * @return array|bool MIMEs or false.
	 */
	public static function getExtensionMimes(string ext) -> array | bool {
		let ext = self::niceFileExtension(ext);
		if (empty ext) {
			return false;
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_blob_mimes")) {
			self::loadMimes();
		}

		if (isset(self::_mimes_by_e[ext])) {
			return self::_mimes_by_e[ext];
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
	public static function getMimeMutations(string mime) -> array {
		let mime = self::niceMime(mime);
		if (empty mime) {
			return [];
		}

		if ((self::MIME_EMPTY === mime) || (self::MIME_DEFAULT === mime)) {
			return [mime];
		}

		array out = [];

		// Weird Microsoft type.
		if (0 === strpos(mime, "application/cdfv2")) {
			let out[] = "application/vnd.ms-office";
		}

		// Split it into type and subtype.
		array parts = (array) explode("/", mime);
		string subtype = (string) preg_replace("/^(x-|vnd\.)/", "", parts[1]);
		let out[] = parts[0] . "/x-" . subtype;
		let out[] = parts[0] . "/vnd." . subtype;
		let out[] = parts[0] . "/" . subtype;

		usort(out, [__CLASS__, "getMimeMutationsUsort"]);

		return out;
	}

	/**
	 * Sort Aliases
	 *
	 * @param string $a A.
	 * @param string $b B.
	 * @return int Priority.
	 */
	private static function getMimeMutationsUsort(string a, string b) -> int {
		if (preg_match("#/(x-|vnd\.)#", a)) {
			let a = "1_" . a;
		}
		else {
			let a = "0_" . a;
		}
		if (preg_match("#/(x-|vnd\.)#", b)) {
			let b = "1_" . b;
		}
		else {
			let b = "0_" . b;
		}

		return a < b ? -1 : 1;
	}

	/**
	 * Verify a MIME/ext Pairing
	 *
	 * @param string $ext Extension.
	 * @param string $mime MIME.
	 * @param bool $soft Soft check.
	 * @return bool True/false.
	 */
	public static function checkExtensionMimePair(string ext, string mime, const bool soft=true) {
		let ext = self::niceFileExtension(ext);
		if (empty ext) {
			return false;
		}

		let mime = self::niceMime(mime);
		if (empty mime) {
			return false;
		}

		// Soft pass for empty/unknown types.
		if (
			(self::MIME_EMPTY === mime) ||
			(soft && (self::MIME_DEFAULT === mime))
		) {
			return true;
		}

		// Also soft pass if we have no data for the extension.
		var mimes;
		let mimes = self::getExtensionMimes(ext);
		if (false === mimes) {
			return true;
		}

		return in_array(mime, mimes, true);
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
	public static function finfo(string path, string niceName="") -> array {
		let path = Strings::utf8(path);
		if (!empty niceName) {
			let niceName = Strings::utf8(niceName);
		}

		array out = [
			"dirname": "",
			"basename": "",
			"extension": "",
			"filename": "",
			"path": "",
			"mime": self::MIME_DEFAULT,
			"rename": []
		];
		string magicMime;
		var finfo;
		var mimes;
		var tmp;
		var v;

		// This could just be an extension.
		if (
			(false === strpos(path, ".")) &&
			(false === strpos(path, "/")) &&
			(false === strpos(path, "\\"))
		) {
			let out["extension"] = self::niceFileExtension(path);
			let mimes = self::getExtensionMimes(path);
			if (false !== mimes) {
				let out["mime"] = mimes[0];
			}

			return out;
		}

		// This should be a path of some sort.
		let path = self::path(path, false);
		let out["path"] = path;
		let out = Cast::parseArgs(pathinfo(path), out);

		// Apply a nice name.
		if (!empty niceName) {
			let tmp = pathinfo(niceName);

			if (isset(tmp["filename"])) {
				let out["filename"] = tmp["filename"];
			}
			else {
				let out["filename"] = "";
			}

			if (isset(tmp["extension"])) {
				let out["extension"] = tmp["extension"];
			}
			else {
				let out["extension"] = "";
			}
		}

		// Can't trust pathinfo to sanitize the extension.
		let out["extension"] = self::niceFileExtension(out["extension"]);

		// Pull MIMEs for the extension.
		let mimes = self::getExtensionMimes(out["extension"]);
		if (false !== mimes) {
			let out["mime"] = mimes[0];
		}

		// See if this is a real path.
		try {
			let path = realpath(path);
			if (!empty path) {
				let out["path"] = path;
				let out["dirname"] = dirname(path);
			}

			// Try Magic MIMEs.
			if (is_file(path) && filesize(path) > 0) {
				let finfo = finfo_open(FILEINFO_MIME_TYPE);
				let magicMime = (string) finfo_file(finfo, path);
				let magicMime = self::niceMime(magicMime);
				finfo_close(finfo);

				// Fileinfo can misidentify SVGs if they are missing
				// their XML tag or DOCTYPE.
				if (
					("svg" === out["extension"]) &&
					("image/svg+xml" !== magicMime)
				) {
					let tmp = file_get_contents(path);
					if (
						is_string(tmp) &&
						(false !== stripos(tmp, "<svg"))
					) {
						let magicMime = "image/svg+xml";
					}
				}

				// Okay, dive deeper into the magic.
				if (
					!empty magicMime &&
					(
						(self::MIME_DEFAULT !== out["mime"]) ||
						(0 !== strpos(magicMime, "text/"))
					) &&
					!static::checkExtensionMimePair(out["extension"], magicMime)
				) {
					// Override what we've found so far if the magic is
					// legit.
					let tmp = self::getMimeExtensions(magicMime);
					if (false !== tmp) {
						let out["mime"] = magicMime;
						let out["extension"] = tmp[0];

						// Build alternative names.
						for v in tmp {
							let out["rename"][] = out["filename"] . "." . v;
						}
					}
				}
			}
		}

		return out;
	}



	// -----------------------------------------------------------------
	// Path Formatting
	// -----------------------------------------------------------------

	/**
	 * Add Leading Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function leadingSlash(string str) -> string {
		return "/" . self::unleadingSlash(str);
	}

	/**
	 * Fix Path Formatting
	 *
	 * @param string $str Path.
	 * @param bool $validate Require valid file.
	 * @return bool|string Path or false.
	 */
	public static function path(string str, const bool validate=true) -> string | bool {
		let str = Strings::utf8(str);

		// This might be a URL rather than something local. We only want
		// to focus on local ones.
		if (preg_match("#^(https?|ftps?|sftp):#iu", str)) {
			let str = Domains::niceUrl(str);
			if (empty str) {
				return false;
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
			let str = "";
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
	public static function trailingSlash(string str) -> string {
		return self::untrailingSlash(str) . "/";
	}

	/**
	 * Fix Path Slashes
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function unixSlash(var str) -> string {
		let str = Strings::utf8(str);
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
	public static function unleadingSlash(var str) -> string {
		let str = self::unixSlash(str);
		return ltrim(str, "/");
	}

	/**
	 * Strip Trailing Slash
	 *
	 * @param string $str Path.
	 * @return string|array Path.
	 */
	public static function untrailingSlash(var str) -> string {
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
		let from = self::path(from, true);
		if (empty from) {
			return false;
		}

		let to = self::path(to, false);
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

		return false;
	}

	/**
	 * Get Data URI for File
	 *
	 * @param string $path Path.
	 * @return string URI.
	 */
	public static function dataUri(string path) -> string {
		let path = self::path(path, true);
		if (empty path || !is_file(path)) {
			return "";
		}

		string content = (string) base64_encode(file_get_contents(path));
		string mime = (string) self::getMimeType(path);

		return "data:" . mime . ";base64," . content;
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
	public static function hashDir(const string str, string dir_algo="md5", string file_algo="") -> string | bool {
		// We definitely need a valid directory algorithm.
		if (empty dir_algo || !in_array(dir_algo, hash_algos(), true)) {
			return false;
		}

		// If the file algorithm is bad or missing, we can just use the
		// same method as we are for our result.
		if (empty file_algo || !in_array(file_algo, hash_algos(), true)) {
			let file_algo = dir_algo;
		}

		array files = (array) self::scandir(str, true, false);
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
	public static function dirSize(const string str) -> int {
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
						let lines++;
					}
				}
				else {
					let lines++;
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
				if (0 !== mb_strpos(str, base, 0, "UTF-8")) {
					return true;
				}

				let str = mb_substr(
					str,
					(int) mb_strlen(base, "UTF-8"),
					null,
					"UTF-8"
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
		let str = self::path(str, true);
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
	public static function scandir(string str, const bool show_files=true, const bool show_dirs=true, const int depth=-1) -> array {
		let str = self::path(str, true);
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
	private static function loadMimes() -> void {
		// Gotta load it!
		string json = (string) Blobfolio::getDataDir("blob-mimes.json");
		if (empty json) {
			throw new \Exception("Missing MIME data.");
		}

		var tmp;
		let tmp = json_decode(json, true);
		if ("array" !== typeof tmp) {
			throw new \Exception("Could not parse MIME data.");
		}

		let self::_mimes_by_e = (array) tmp["extensions"];
		let self::_mimes_by_m = (array) tmp["mimes"];

		globals_set("loaded_blob_mimes", true);
	}
}
