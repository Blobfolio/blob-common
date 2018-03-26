<?php
/**
 * Spacetime Functions
 *
 * This file contains functions related to geography,
 * time, files, etc.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

use \blobfolio\common\constants;
use \blobfolio\common\data;
use \blobfolio\common\file as v_file;
use \blobfolio\common\format as v_format;
use \blobfolio\common\mb as v_mb;
use \blobfolio\common\mime;
use \blobfolio\common\ref\file as r_file;
use \blobfolio\common\ref\sanitize as r_sanitize;
use \blobfolio\common\sanitize as v_sanitize;

// ---------------------------------------------------------------------
// Geography
// ---------------------------------------------------------------------

if (!function_exists('common_get_us_states')) {
	/**
	 * Return US States
	 *
	 * @param bool $include_other Include territories, APO, etc.
	 * @param bool $uppercase Uppercase names (for backward compatibility).
	 * @return array States {abbr:name}.
	 */
	function common_get_us_states($include_other=true, $uppercase=true) {
		$states = constants::STATES;
		$other = array('AA', 'AE', 'AP', 'AS', 'FM', 'GU', 'MH', 'MP', 'PW', 'PR', 'VI');

		// Originally all results were returned in uppercase,
		// but this is a bit limiting. Raw data is now stored
		// in title case, but can be uppercased as needed for
		// backward compatibility.
		if ($uppercase) {
			$states = array_map('strtoupper', $states);
		}

		// Remove others.
		if (!$include_other) {
			$states = array_diff_key($states, array_flip($other));
		}

		return $states;
	}
}

if (!function_exists('common_get_ca_provinces')) {
	/**
	 * Return Canadian Provinces
	 *
	 * @param bool $uppercase Uppercase names (for backward compatibility).
	 * @return array Provinces {abbr:name}.
	 */
	function common_get_ca_provinces($uppercase=true) {
		$provinces = constants::PROVINCES;

		// Originally all results were returned in uppercase,
		// but this is a bit limiting. Raw data is now stored
		// in title case, but can be uppercased as needed for
		// backward compatibility.
		if ($uppercase) {
			$provinces = array_map('strtoupper', $provinces);
		}

		return $provinces;
	}
}

if (!function_exists('common_get_countries')) {
	/**
	 * Return Countries
	 *
	 * @param bool $uppercase Uppercase names (for backward compatibility).
	 * @return array Countries {iso:name}.
	 */
	function common_get_countries($uppercase=false) {
		$countries = array();
		foreach (constants::COUNTRIES as $k=>$v) {
			$countries[$k] = $v['name'];
		}

		// Unlike state/province functions, these have always
		// been stored in title case. However for the sake of
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

if (!function_exists('common_readfile_chunked')) {
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
	function common_readfile_chunked($file, $retbytes=true) {
		return v_file::readfile_chunked($file, $retbytes);
	}
}

if (!function_exists('common_get_data_uri')) {
	/**
	 * Get Data-URI From File
	 *
	 * @param string $path Path.
	 * @return string|bool Data-URI or false.
	 */
	function common_get_data_uri($path) {
		return v_file::data_uri($path);
	}
}

if (!function_exists('common_get_mime_type')) {
	/**
	 * Get MIME Type By File
	 *
	 * @param string $file File path.
	 * @return string MIME type.
	 */
	function common_get_mime_type($file) {
		$finfo = mime::finfo($file);
		return $finfo['mime'];
	}
}

// --------------------------------------------------------------------- end files



// ---------------------------------------------------------------------
// IPs
// ---------------------------------------------------------------------

if (!function_exists('common_ip_to_number')) {
	/**
	 * IP to Number
	 *
	 * @param string $ip IP.
	 * @return int|bool IP or false.
	 */
	function common_ip_to_number($ip) {
		return v_format::ip_to_number($ip);
	}
}

if (!function_exists('common_cidr_to_range')) {
	/**
	 * CIDR to IP Range
	 *
	 * Find the minimum and maximum IPs in a
	 * given CIDR range.
	 *
	 * @param string $cidr CIDR.
	 * @return array|bool Range or false.
	 */
	function common_cidr_to_range($cidr) {
		return v_format::cidr_to_range($cidr);
	}
}

// --------------------------------------------------------------------- end IPs



// ---------------------------------------------------------------------
// Paths & URLs
// ---------------------------------------------------------------------

if (!function_exists('common_get_path_by_url')) {
	/**
	 * Get File Path From URL
	 *
	 * This will convert a WordPress URL into a
	 * file path.
	 *
	 * @param string $url URL.
	 * @return string|bool Path or false.
	 */
	function common_get_path_by_url($url) {
		$from = v_mb::strtolower(trailingslashit(site_url()));
		$to = trailingslashit(ABSPATH);

		// Query strings and hashes aren't part of files.
		if (false !== ($match = v_mb::strpos($url, '?'))) {
			$url = v_mb::substr($url, 0, $match);
		}
		if (false !== ($match = v_mb::strpos($url, '#'))) {
			$url = v_mb::substr($url, 0, $match);
		}

		if (0 === v_mb::strpos($url, $from)) {
			return $to . v_mb::substr($url, v_mb::strlen($from));
		}

		return false;
	}
}

if (!function_exists('common_get_url_by_path')) {
	/**
	 * Get URL File Path
	 *
	 * This will convert a WordPress file path
	 * into a URL.
	 *
	 * @param string $path Path.
	 * @return string|bool URL or false.
	 */
	function common_get_url_by_path($path) {
		r_file::unixslash($path);
		$from = trailingslashit(ABSPATH);
		$to = trailingslashit(site_url());

		if (0 === v_mb::strpos($path, $from)) {
			return $to . v_mb::substr($path, v_mb::strlen($from));
		}

		return false;
	}
}

if (!function_exists('common_is_empty_dir')) {
	/**
	 * Is Directory Empty?
	 *
	 * @param string $path Path.
	 * @return bool True/false.
	 */
	function common_is_empty_dir($path) {
		return v_file::empty_dir($path);
	}
}

if (!function_exists('common_is_site_url')) {
	/**
	 * Check If a URL is On-Site
	 *
	 * @param string $url URL.
	 * @return bool True/false.
	 */
	function common_is_site_url($url) {
		r_sanitize::hostname($url, false);
		$site = v_sanitize::hostname(site_url(), false);
		return $url === $site;
	}
}

if (!function_exists('common_is_current_page')) {
	/**
	 * Check If a URL is Being Viewed
	 *
	 * @param string $url URL.
	 * @param bool $subpages Subpages match parent.
	 * @return bool True/false.
	 */
	function common_is_current_page($url, $subpages=false) {
		if (!common_is_site_url($url)) {
			return false;
		}

		// Ready the test URL for comparison.
		$url = v_mb::parse_url($url, PHP_URL_PATH);
		$url2 = v_mb::parse_url(site_url($_SERVER['REQUEST_URI']), PHP_URL_PATH);

		// And check for a match.
		if ($subpages) {
			return (0 === v_mb::strpos($url2, $url));
		}

		return $url === $url2;
	}
}

if (!function_exists('common_redirect')) {
	/**
	 * Redirect Wrapper
	 *
	 * Will issue redirect headers or print Javascript
	 * if headers have already been sent.
	 *
	 * @param string $url URL.
	 * @param bool $offsite Allow off-site redirects.
	 * @return void Nothing.
	 */
	function common_redirect($url=null, $offsite=false) {
		if (is_numeric($url)) {
			$url = get_permalink($url);
		}

		if (!$url || (!$offsite && !common_is_site_url($url))) {
			$url = site_url();
		}

		v_file::redirect($url);
	}
}

if (!function_exists('common_get_site_hostname')) {
	/**
	 * Get Site Hostname
	 *
	 * This returns the hostname portion of site_url(),
	 * minus any leading www.
	 *
	 * @return string Hostname.
	 */
	function common_get_site_hostname() {
		return v_sanitize::hostname(site_url(), false);
	}
}

if (!function_exists('common_upload_path')) {
	/**
	 * Upload Path
	 *
	 * This works like `site_url()` but for
	 * the uploads directory.
	 *
	 * @param string $subpath Subpath.
	 * @param bool $url Return a URL (instead of a path).
	 * @return string Upload path or URL.
	 */
	function common_upload_path($subpath=null, $url=false) {
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];
		$path = trailingslashit($dir);
		if (!is_null($subpath)) {
			$path .= v_file::unleadingslash($subpath);
		}

		return $url ? common_get_url_by_path($path) : $path;
	}
}

if (!function_exists('common_theme_path')) {
	/**
	 * Theme Path
	 *
	 * This works like `site_url()` but for
	 * the theme directory.
	 *
	 * @param string $subpath Subpath.
	 * @param bool $url Return a URL (instead of a path).
	 * @return string Theme path or URL.
	 */
	function common_theme_path($subpath=null, $url=false) {
		// This is a URL.
		$dir = trailingslashit(get_stylesheet_directory_uri());
		$path = trailingslashit($dir);
		if (!is_null($subpath)) {
			$path .= v_file::unleadingslash($subpath);
		}

		return $url ? $path : common_get_path_by_url($path);
	}
}

// --------------------------------------------------------------------- end paths



// ---------------------------------------------------------------------
// Time
// ---------------------------------------------------------------------

if (!function_exists('common_datediff')) {
	/**
	 * Days Between Dates
	 *
	 * @param string $date1 Date.
	 * @param string $date2 Date.
	 * @return int Difference in Days.
	 */
	function common_datediff($date1, $date2) {
		return data::datediff($date1, $date2);
	}
}

if (!function_exists('common_get_blog_timezone')) {
	/**
	 * Local Time
	 *
	 * Get the site's local timezone.
	 *
	 * @return string Timezone.
	 */
	function common_get_blog_timezone() {
		static $tz;

		if (is_null($tz)) {
			// Try the timezone string.
			if (false === $tz = get_option('timezone_string', false)) {

				// Try a GMT offset.
				if (0.0 === ($utc_offset = (float) get_option('gmt_offset', 0.0))) {
					$tz = 'UTC';
				}
				// Pull proper tz abbreviation from the offset, or default to UTC.
				elseif (false === $tz = timezone_name_from_abbr('', ($utc_offset * 3600), 0)) {
					$tz = 'UTC';
				}
			}

			r_sanitize::timezone($tz);
		}

		return $tz;
	}
}

if (!function_exists('common_to_blogtime')) {
	/**
	 * Convert Date to Local Time
	 *
	 * @param string $date Date.
	 * @param string $from Original Timezone.
	 * @return string Date.
	 */
	function common_to_blogtime($date, $from='UTC') {
		return v_format::to_timezone($date, $from, common_get_blog_timezone());
	}
}

if (!function_exists('common_from_blogtime')) {
	/**
	 * Convert Date from Local Time
	 *
	 * @param string $date Date.
	 * @param string $to New Timezone.
	 * @return string Date.
	 */
	function common_from_blogtime($date, $to='UTC') {
		return v_format::to_timezone($date, common_get_blog_timezone(), $to);
	}
}

// --------------------------------------------------------------------- end time

