<?php
//---------------------------------------------------------------------
// FUNCTIONS: LOCALITY, SPACE, TIME, ETC.
//---------------------------------------------------------------------
// This file includes functions related to space, time, etc.

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//---------------------------------------------------------------------
// Geography
//---------------------------------------------------------------------

//-------------------------------------------------
// Return US States
//
// @param include other?
// @param uppercase (for backward compatibility)
// @return states
if(!function_exists('common_get_us_states')){
	function common_get_us_states($include_other=true, $uppercase=true){
		$states = array(
			'AL' => 'Alabama',
			'AK' => 'Alaska',
			'AZ' => 'Arizona',
			'AR' => 'Arkansas',
			'CA' => 'California',
			'CO' => 'Colorado',
			'CT' => 'Connecticut',
			'DE' => 'Delaware',
			'DC' => 'District of Columbia',
			'FL' => 'Florida',
			'GA' => 'Georgia',
			'HI' => 'Hawaii',
			'ID' => 'Idaho',
			'IL' => 'Illinois',
			'IN' => 'Indiana',
			'IA' => 'Iowa',
			'KS' => 'Kansas',
			'KY' => 'Kentucky',
			'LA' => 'Louisiana',
			'ME' => 'Maine',
			'MD' => 'Maryland',
			'MA' => 'Massachusetts',
			'MI' => 'Michigan',
			'MN' => 'Minnesota',
			'MS' => 'Mississippi',
			'MO' => 'Missouri',
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',
			'OK' => 'Oklahoma',
			'OR' => 'Oregon',
			'PA' => 'Pennsylvania',
			'RI' => 'Rhode Island',
			'SC' => 'South Carolina',
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',
			'TX' => 'Texas',
			'UT' => 'Utah',
			'VT' => 'Vermont',
			'VA' => 'Virginia',
			'WA' => 'Washington',
			'WV' => 'West Virginia',
			'WI' => 'Wisconsin',
			'WY' => 'Wyoming'
		);

		$other = array(
			'AA' => 'Armed Forces Americas',
			'ae' => 'Armed Forces Europe',
			'ap' => 'Armed Forces Pacific',
			'as' => 'American Samoa',
			'fm' => 'Federated States of Micronesia',
			'gu' => 'Guam Gu',
			'mh' => 'Marshall Islands',
			'mp' => 'Northern Mariana Islands',
			'pw' => 'Palau',
			'pr' => 'Puerto Rico',
			'vi' => 'Virgin Islands'
		);

		//originally all results were returned in uppercase,
		//but this is a bit limiting. raw data is now stored
		//in title case, but can be uppercased as needed for
		//backward compatibility
		if($uppercase){
			$states = array_map('strtoupper', $states);
			$other = array_map('strtoupper', $states);
		}

		if($include_other)
			return array_merge($states, $other);
		else
			return $states;
	}
}

//-------------------------------------------------
// Return Canadian Provinces
//
// @param uppercase (for backward compatibility)
// @return provinces
if(!function_exists('common_get_ca_provinces')){
	function common_get_ca_provinces($uppercase=true){
		$provinces = array(
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland',
			'NT' => 'Northwest Territories',
			'NS' => 'Nova Scotia',
			'NU' => 'Nunavut',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'QC' => 'Quebec',
			'SK' => 'Saskatchewan',
			'YT' => 'Yukon'
		);

		//originally all results were returned in uppercase,
		//but this is a bit limiting. raw data is now stored
		//in title case, but can be uppercased as needed for
		//backward compatibility
		if($uppercase)
			$provinces = array_map('strtoupper', $provinces);

		return $provinces;
	}
}

//-------------------------------------------------
// Return Countries
//
// ISO Code => Name
//
// @param uppercase
// @return countries
if(!function_exists('common_get_countries')){
	function common_get_countries($uppercase=false){
		$countries = array(
			'US' => 'USA',
			'CA' => 'Canada',
			'GB' => 'United Kingdom',
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'AR' => 'Argentina',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'BD' => 'Bangladesh',
			'BE' => 'Belgium',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia and Herzegovina',
			'BR' => 'Brazil',
			'BG' => 'Bulgaria',
			'KH' => 'Cambodia',
			'CL' => 'Chile',
			'CN' => 'China',
			'CO' => 'Colombia',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'DE' => 'Germany',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'HT' => 'Haiti',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KR' => 'Korea, South',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MT' => 'Malta',
			'MX' => 'Mexico',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar (Burma)',
			'NA' => 'Namibia',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NO' => 'Norway',
			'PK' => 'Pakistan',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'SM' => 'San Marino',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'ZA' => 'South Africa',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'VI' => 'U.S. Virgin Islands',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VA' => 'Vatican City',
			'VE' => 'Venezuela',
			'VU' => 'Vietnam'
		);

		//unlike state/province functions, these have always
		//been stored in title case. however for the sake of
		//consistency, an uppercase flag has been added.
		if($uppercase)
			$countries = array_map('strtoupper', $countries);

		return $countries;
	}
}

//--------------------------------------------------------------------- end geography




//---------------------------------------------------------------------
// File Handling
//---------------------------------------------------------------------

//-------------------------------------------------
// Readfile() in chunks
//
// this greatly reduces the server resource demands
// compared with reading a file all in one go
//
// @param file
// @param bytes
// @return bytes or false
if(!function_exists('common_readfile_chunked')){
	function common_readfile_chunked($file, $retbytes=true){
		$buffer = '';
		$cnt = 0;
		$chunk_size = 1024*1024;

		if(false === ($handle = fopen($file, 'rb')))
			return false;
		while(!feof($handle)){
			$buffer = fread($handle, $chunk_size);
			echo $buffer;
			ob_flush();
			flush();
			if($retbytes)
				$cnt += common_strlen($buffer);
		}

		$status = fclose($handle);

 		//return number of bytes delivered like readfile() does
		if($retbytes && $status)
			return $cnt;

		return $status;
	}
}

//-------------------------------------------------
// Return Data URI
//
// @param path
// @return data
if(!function_exists('common_get_data_uri')){
	function common_get_data_uri($path){
		if(!@file_exists($path))
			return false;

		return "data:" . common_get_mime_type($path) . ";base64," . base64_encode(file_get_contents($path));
	}
}

//-------------------------------------------------
// Get Mime Type by file path
//
// why is this so hard?! the fileinfo extension is
// not reliably present, and even when it is it
// kinda sucks, and WordPress' internal function
// excludes a lot. let's do it ourselves then
//
// @param file
// @return type
if(!function_exists('common_get_mime_type')){
	function common_get_mime_type($file){
		static $mimes;

		//first, load the mimes
		if(is_null($mimes))
			$mimes = json_decode(@file_get_contents(dirname(__FILE__) . '/mimes.json'), true);

		//extension
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		//done
		return common_strlen($ext) && isset($mimes[$ext]) ? $mimes[$ext] : 'application/octet-stream';
	}
}

//--------------------------------------------------------------------- end files



//---------------------------------------------------------------------
// IPs
//---------------------------------------------------------------------

//-------------------------------------------------
// IP as Number
//
// convert an IP to a number for cleaner comparison
//
// @param IP
// @return num or false
if(!function_exists('common_ip_to_number')){
	function common_ip_to_number($ip){
		if(!filter_var($ip, FILTER_VALIDATE_IP))
			return false;

		//ipv4 is easy
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return ip2long($ip);

		//ipv6 is a little more roundabout
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			$ip_n = inet_pton($ip);
			$bin = '';
			for($bit = common_strlen($ip_n) - 1; $bit >= 0; $bit--)
				$bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
			$dec = '0';
			for ($i = 0; $i < common_strlen($bin); $i++){
				$dec = bcmul($dec, '2', 0);
				$dec = bcadd($dec, $bin[$i], 0);
			}
			return $dec;
		}

		return false;
	}
}

//-------------------------------------------------
// Convert Netblock to Min/Max IPs
//
// @param cidr
// @return array or false
if(!function_exists('common_cidr_to_range')){
	function common_cidr_to_range($cidr){
		$range = array('min'=>0, 'max'=>0);
		$cidr = explode('/', $cidr);

		//ipv4?
		if(filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
			$range['min'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
			$range['max'] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
			return $range;
		}

		//ipv6?  of course a little more complicated
		if(filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			//parse the address into a binary string
			$firstaddrbin = inet_pton($cidr[0]);

			//convert the binary string to a string with hexadecimal characters (bin2hex)
			$firstaddrhex = reset(unpack('H*', $firstaddrbin));

			//overwriting first address string to make sure notation is optimal
			$cidr[0] = inet_ntop($firstaddrbin);

			//calculate the number of 'flexible' bits
			$flexbits = 128 - $cidr[1];

			//build the hexadecimal string of the last address
			$lastaddrhex = $firstaddrhex;

			//we start at the end of the string (which is always 32 characters long)
			$pos = 31;
			while($flexbits > 0){
				//get the character at this position
				$orig = substr($lastaddrhex, $pos, 1);

				//convert it to an integer
				$origval = hexdec($orig);

				//OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
				$newval = $origval | (pow(2, min(4, $flexbits)) - 1);

				//convert it back to a hexadecimal character
				$new = dechex($newval);

				//and put that character back in the string
				$lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

				//we processed one nibble, move to previous position
				$flexbits -= 4;
				$pos -= 1;
			}

			//convert the hexadecimal string to a binary string (hex2bin)
			$lastaddrbin = pack('H*', $lastaddrhex);

			//and create an IPv6 address from the binary string
			$lastaddrstr = inet_ntop($lastaddrbin);

			//pack and done!
			$range['min'] = common_sanitize_ip($cidr[0]);
			$range['max'] = common_sanitize_ip($lastaddrstr);
			return $range;
		}

		return false;
	}
}

//--------------------------------------------------------------------- end IPs



//---------------------------------------------------------------------
// Paths & URLs
//---------------------------------------------------------------------

//-------------------------------------------------
// Get File Path From URL
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param url
// @return path
if(!function_exists('common_get_path_by_url')){
	function common_get_path_by_url($url){
		$from = strtolower(trailingslashit(site_url()));
		$to = trailingslashit(ABSPATH);

		//query strings and hashes aren't part of files
		if(substr_count($url, '?')){
			$url = explode('?', $url);
			$url = common_array_pop_top($url);
		}
		if(substr_count($url, '#')){
			$url = explode('#', $url);
			$url = common_array_pop_top($url);
		}

		if(strtolower(substr($url, 0, strlen($from))) === $from)
			return $to . substr($url, strlen($from));

		return false;
	}
}

//-------------------------------------------------
// Get URL From Path
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param path
// @return url
if(!function_exists('common_get_url_by_path')){
	function common_get_url_by_path($path){
		$path = common_unixslashit($path);
		$from = trailingslashit(ABSPATH);
		$to = trailingslashit(site_url());

		if(strtolower(substr($path, 0, strlen($from))) === $from)
			return $to . substr($path, strlen($from));

		return false;
	}
}

//-------------------------------------------------
// Is a Directory Empty?
//
// @param path
// @return true/false
if(!function_exists('common_is_empty_dir')){
	function common_is_empty_dir($path){
		if(!is_readable($path) || !is_dir($path))
			return false;

		//scan all files in dir
		$handle = opendir($path);
		while(false !== ($entry = readdir($handle))){
			//anything but a dot === not empty
			if($entry !== "." && $entry !== "..")
				return false;
		}

		//nothing found
		return true;
	}
}

//-------------------------------------------------
// Check whether a URL is local
//
// @param url
// @return true/false
if(!function_exists('common_is_site_url')){
	function common_is_site_url($url){
		return filter_var($url, FILTER_VALIDATE_URL) && strtolower(parse_url($url, PHP_URL_HOST)) === strtolower(parse_url(site_url(), PHP_URL_HOST));
	}
}

//-------------------------------------------------
// Is a given URL being viewed?
//
// @param url to check against
// @param subpages to match subpages
// @return true/false
if(!function_exists('common_is_current_page')){
	function common_is_current_page($url, $subpages=false){

		//ready the test URL for comparison
		$url = parse_url($url, PHP_URL_PATH);
		$url2 = parse_url(site_url($_SERVER['REQUEST_URI']), PHP_URL_PATH);

		//and check for a match
		return $subpages ? substr($url2, 0, common_strlen($url)) === $url : $url === $url2;
	}
}

//-------------------------------------------------
// Redirect wrapper
//
// clear $_REQUEST and exit
//
// @param url
// @param offsite
// @return n/a
if(!function_exists('common_redirect')){
	function common_redirect($url=null, $offsite=false){
		if(is_numeric($url))
			$url = get_permalink($url);

		if(is_null($url) || (!$offsite && !common_is_site_url($url)))
			$url = site_url();

		unset($_POST);
		unset($_GET);
		unset($_REQUEST);

		if(headers_sent())
			echo "<script>top.location.href='" . esc_js($url) . "';</script>";
		else
			wp_redirect($url);

		exit;
	}
}

//-------------------------------------------------
// Get Site Hostname
//
// strip www., lowercase
//
// @param n/a
// @return hostname
if(!function_exists('common_get_site_hostname')){
	function common_get_site_hostname(){
		return preg_replace('/^www\./', '', parse_url(strtolower(site_url()), PHP_URL_HOST));
	}
}

//-------------------------------------------------
// Upload Path
//
// this works like site_url for upload directory
// paths
//
// @param subpath
// @param return url?
// @return path or url
if(!function_exists('common_upload_path')){
	function common_upload_path($subpath=null, $url=false){
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];
		$path = trailingslashit($dir);
		if(!is_null($subpath))
			$path .= common_unleadingslashit($subpath);

		return $url ? common_get_url_by_path($path) : $path;
	}
}

//-------------------------------------------------
// Theme Path
//
// this works like site_url for theme directory
// paths
//
// @param subpath
// @param return url?
// @return path or url
if(!function_exists('common_theme_path')){
	function common_theme_path($subpath=null, $url=false){
		//this is a URL
		$dir = trailingslashit(get_stylesheet_directory_uri());
		$path = trailingslashit($dir);
		if(!is_null($subpath))
			$path .= common_unleadingslashit($subpath);

		return $url ? $path : common_get_path_by_url($path);
	}
}

//--------------------------------------------------------------------- end paths



//---------------------------------------------------------------------
// Time
//---------------------------------------------------------------------

//-------------------------------------------------
// Datediff
//
// a simple function to count the number of days
// between two dates
//
// @param date1
// @param date2
// @return days
if(!function_exists('common_datediff')){
	function common_datediff($date1, $date2){
		$date1 = date('Y-m-d', strtotime($date1));
		$date2 = date('Y-m-d', strtotime($date2));

		//same date, tricky tricky!
		if($date1 === $date2)
			return 0;

		try {
			$date1 = new DateTime($date1);
			$date2 = new DateTime($date2);
			$diff = $date1->diff($date2);

			return abs($diff->days);
		}
		catch(Exception $e){
			//this is a simple fallback using unix timestamps
			//however it will fail to consider things like
			//daylight saving
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		}
	}
}

//--------------------------------------------------------------------- end time
?>