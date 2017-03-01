<?php
// ---------------------------------------------------------------------
// FUNCTIONS: LOCALITY, SPACE, TIME, ETC.
// ---------------------------------------------------------------------
// This file includes functions related to space, time, etc.

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}



// ---------------------------------------------------------------------
// Geography
// ---------------------------------------------------------------------

// -------------------------------------------------
// Return US States
//
// @param include other?
// @param uppercase (for backward compatibility)
// @return states
if (!function_exists('common_get_us_states')) {
	function common_get_us_states($include_other=true, $uppercase=true) {
		$states = \blobfolio\common\constants::STATES;
		$other = array('AA','AE','AP','AS','FM','GU','MH','MP','PW','PR','VI');

		// originally all results were returned in uppercase,
		// but this is a bit limiting. raw data is now stored
		// in title case, but can be uppercased as needed for
		// backward compatibility
		if ($uppercase) {
			$states = array_map('strtoupper', $states);
		}

		// remove others
		if (!$include_other) {
			$states = array_diff_key($states, array_flip($other));
		}

		return $states;
	}
}

// -------------------------------------------------
// Return Canadian Provinces
//
// @param uppercase (for backward compatibility)
// @return provinces
if (!function_exists('common_get_ca_provinces')) {
	function common_get_ca_provinces($uppercase=true) {
		$provinces = \blobfolio\common\constants::PROVINCES;

		// originally all results were returned in uppercase,
		// but this is a bit limiting. raw data is now stored
		// in title case, but can be uppercased as needed for
		// backward compatibility
		if ($uppercase) {
			$provinces = array_map('strtoupper', $provinces);
		}

		return $provinces;
	}
}

// -------------------------------------------------
// Return Countries
//
// ISO Code => Name
//
// @param uppercase
// @return countries
if (!function_exists('common_get_countries')) {
	function common_get_countries($uppercase=false) {
		$countries = array();
		foreach (\blobfolio\common\constants::COUNTRIES as $k=>$v) {
			$countries[$k] = $v['name'];
		}

		// unlike state/province functions, these have always
		// been stored in title case. however for the sake of
		// consistency, an uppercase flag has been added.
		if ($uppercase) {
			$countries = array_map('strtoupper', $countries);
		}

		return $countries;
	}
}

// --------------------------------------------------------------------- end geography




// ---------------------------------------------------------------------
// File Handling
// ---------------------------------------------------------------------

// -------------------------------------------------
// Readfile() in chunks
//
// this greatly reduces the server resource demands
// compared with reading a file all in one go
//
// @param file
// @param bytes
// @return bytes or false
if (!function_exists('common_readfile_chunked')) {
	function common_readfile_chunked($file, $retbytes=true) {
		return \blobfolio\common\file::readfile_chunked($file, $retbytes);
	}
}

// -------------------------------------------------
// Return Data URI
//
// @param path
// @return data
if (!function_exists('common_get_data_uri')) {
	function common_get_data_uri($path) {
		return \blobfolio\common\file::data_uri($path);
	}
}

// -------------------------------------------------
// Get Mime Type by file path
//
// why is this so hard?! the fileinfo extension is
// not reliably present, and even when it is it
// kinda sucks, and WordPress' internal function
// excludes a lot. let's do it ourselves then
//
// @param file
// @return type
if (!function_exists('common_get_mime_type')) {
	function common_get_mime_type($file) {
		$finfo = \blobfolio\common\mime::finfo($file);
		return $finfo['mime'];
	}
}

// --------------------------------------------------------------------- end files



// ---------------------------------------------------------------------
// IPs
// ---------------------------------------------------------------------

// -------------------------------------------------
// IP as Number
//
// convert an IP to a number for cleaner comparison
//
// @param IP
// @return num or false
if (!function_exists('common_ip_to_number')) {
	function common_ip_to_number($ip) {
		return \blobfolio\common\format::ip_to_number($ip);
	}
}

// -------------------------------------------------
// Convert Netblock to Min/Max IPs
//
// @param cidr
// @return array or false
if (!function_exists('common_cidr_to_range')) {
	function common_cidr_to_range($cidr) {
		return \blobfolio\common\format::cidr_to_range($cidr);
	}
}

// --------------------------------------------------------------------- end IPs



// ---------------------------------------------------------------------
// Paths & URLs
// ---------------------------------------------------------------------

// -------------------------------------------------
// Get File Path From URL
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param url
// @return path
if (!function_exists('common_get_path_by_url')) {
	function common_get_path_by_url($url) {
		$from = common_strtolower(trailingslashit(site_url()));
		$to = trailingslashit(ABSPATH);

		// query strings and hashes aren't part of files
		if (false !== common_strpos($url, '?')) {
			$url = explode('?', $url);
			$url = common_array_pop_top($url);
		}
		if (false !== common_strpos($url, '#')) {
			$url = explode('#', $url);
			$url = common_array_pop_top($url);
		}

		if (common_strtolower(common_substr($url, 0, common_strlen($from))) === $from) {
			return $to . common_substr($url, common_strlen($from));
		}

		return false;
	}
}

// -------------------------------------------------
// Get URL From Path
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param path
// @return url
if (!function_exists('common_get_url_by_path')) {
	function common_get_url_by_path($path) {
		$path = common_unixslashit($path);
		$from = trailingslashit(ABSPATH);
		$to = trailingslashit(site_url());

		if (common_strtolower(common_substr($path, 0, common_strlen($from))) === $from) {
			return $to . common_substr($path, common_strlen($from));
		}

		return false;
	}
}

// -------------------------------------------------
// Is a Directory Empty?
//
// @param path
// @return true/false
if (!function_exists('common_is_empty_dir')) {
	function common_is_empty_dir($path) {
		return \blobfolio\common\file::empty_dir($path);
	}
}

// -------------------------------------------------
// Check whether a URL is local
//
// @param url
// @return true/false
if (!function_exists('common_is_site_url')) {
	function common_is_site_url($url) {
		$url = \blobfolio\common\sanitize::hostname($url, false);
		$site = \blobfolio\common\sanitize::hostname(site_url(), false);
		return $url === $site;
	}
}

// -------------------------------------------------
// Is a given URL being viewed?
//
// @param url to check against
// @param subpages to match subpages
// @return true/false
if (!function_exists('common_is_current_page')) {
	function common_is_current_page($url, $subpages=false) {

		if (!common_is_site_url($url)) {
			return false;
		}

		// ready the test URL for comparison
		$url = parse_url($url, PHP_URL_PATH);
		$url2 = parse_url(site_url($_SERVER['REQUEST_URI']), PHP_URL_PATH);

		// and check for a match
		return $subpages ? substr($url2, 0, common_strlen($url)) === $url : $url === $url2;
	}
}

// -------------------------------------------------
// Redirect wrapper
//
// clear $_REQUEST and exit
//
// @param url
// @param offsite
// @return n/a
if (!function_exists('common_redirect')) {
	function common_redirect($url=null, $offsite=false) {
		if (is_numeric($url)) {
			$url = get_permalink($url);
		}

		if (is_null($url) || (!$offsite && !common_is_site_url($url))) {
			$url = site_url();
		}

		\blobfolio\common\file::redirect($url);
	}
}

// -------------------------------------------------
// Get Site Hostname
//
// strip www., lowercase
//
// @param n/a
// @return hostname
if (!function_exists('common_get_site_hostname')) {
	function common_get_site_hostname() {
		return \blobfolio\common\sanitize::hostname(site_url(), true);
	}
}

// -------------------------------------------------
// Upload Path
//
// this works like site_url for upload directory
// paths
//
// @param subpath
// @param return url?
// @return path or url
if (!function_exists('common_upload_path')) {
	function common_upload_path($subpath=null, $url=false) {
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];
		$path = trailingslashit($dir);
		if (!is_null($subpath)) {
			$path .= common_unleadingslashit($subpath);
		}

		return $url ? common_get_url_by_path($path) : $path;
	}
}

// -------------------------------------------------
// Theme Path
//
// this works like site_url for theme directory
// paths
//
// @param subpath
// @param return url?
// @return path or url
if (!function_exists('common_theme_path')) {
	function common_theme_path($subpath=null, $url=false) {
		// this is a URL
		$dir = trailingslashit(get_stylesheet_directory_uri());
		$path = trailingslashit($dir);
		if (!is_null($subpath)) {
			$path .= common_unleadingslashit($subpath);
		}

		return $url ? $path : common_get_path_by_url($path);
	}
}

// --------------------------------------------------------------------- end paths



// ---------------------------------------------------------------------
// Time
// ---------------------------------------------------------------------

// -------------------------------------------------
// Datediff
//
// a simple function to count the number of days
// between two dates
//
// @param date1
// @param date2
// @return days
if (!function_exists('common_datediff')) {
	function common_datediff($date1, $date2) {
		return \blobfolio\common\data::datediff($date1, $date2);
	}
}

// -------------------------------------------------
// Local Time
//
// get a proper timezone for the blog
//
// @param n/a
// @return timezone
if (!function_exists('common_get_blog_timezone')) {
	function common_get_blog_timezone() {
		static $tz;

		if (is_null($tz)) {
			// try the timezone string
			if (false === $tz = get_option('timezone_string', false)) {

				// try a gmt offset
				if (0.0 === ($utc_offset = (float) get_option('gmt_offset', 0.0))) {
					$tz = 'UTC';
				}
				// pull proper tz abbreviation from the offset, or default to UTC
				elseif (false === $tz = timezone_name_from_abbr('', ($utc_offset * 3600), 0)) {
					$tz = 'UTC';
				}
			}

			\blobfolio\common\ref\sanitize::timezone($tz);
		}

		return $tz;
	}
}

// -------------------------------------------------
// To Local Time
//
// convert a datestring from one timezone to the
// blog timezone
//
// @param date
// @param original timezone
// @return date
if (!function_exists('common_to_blogtime')) {
	function common_to_blogtime($date, $from='UTC') {
		return \blobfolio\common\format::to_timezone($date, $from, common_get_blog_timezone());
	}
}

// -------------------------------------------------
// From Local Time
//
// convert a datestring from one timezone to the
// blog timezone
//
// @param date
// @param new timezone
// @return date
if (!function_exists('common_from_blogtime')) {
	function common_from_blogtime($date, $to='UTC') {
		return \blobfolio\common\format::to_timezone($date, common_get_blog_timezone(), $to);
	}
}

// --------------------------------------------------------------------- end time

