<?php
//---------------------------------------------------------------------
// FUNCTIONS: COMMON
//---------------------------------------------------------------------
// These are functions and helpers used across many different projects
// because of their general usefulness.



//---------------------------------------------------------------------
// Theme/System tweaks
//---------------------------------------------------------------------

//do not include back/next in meta
add_filter('previous_post_rel_link', '__return_false');
add_filter('next_post_rel_link', '__return_false');

//--------------------------------------------------------------------- end theme/system



//---------------------------------------------------------------------
// Email
//---------------------------------------------------------------------

//-------------------------------------------------
// WP Mail wrapper
//
// this ensures mail is sent in HTML
//
// @param to
// @param subject
// @param msg
// @param from (e.g. headers)
// @param attachments
// @return true
if(!function_exists('common_mail'))
{
	function common_mail($to, $subject, $msg, $from=null, $attachments=null){
		if(is_null($from))
			$from = common_sanitize_name(get_bloginfo('name')) . ' <' . get_bloginfo('admin_email') . '>';

		//engage our filters
		add_filter('wp_mail_content_type', 'common_mail_html_content_type');

		//send the mail
		wp_mail($to, $subject, $msg, "From: $from\r\nReply-To: $from\r\n", $attachments);

		//remove our filters
		remove_filter('wp_mail_content_type', 'common_mail_html_content_type');

		return true;
	}
}

//-------------------------------------------------
// Set e-mail content type to HTML
//
// @param n/a
// @return text/html
if(!function_exists('common_mail_html_content_type'))
{
	function common_mail_html_content_type(){
		return 'text/html';
	}
}

//-------------------------------------------------
// Developer Debug Email
//
// ever need to quickly email yourself a print_r
// of a variable to see what happened?
//
// will go to blog email unless WP_DEBUG_EMAIL is
// defined
//
// @param variable
// @param subject
// @param use mail() instead of wp_mail()
// @return true/false
if(!function_exists('common_debug_mail'))
{
	function common_debug_mail($variable, $subject=null, $mail=true){
		$mto = defined('WP_DEBUG_EMAIL') ? common_sanitize_email(WP_DEBUG_EMAIL) : get_bloginfo('admin_email');
		$msub = common_sanitize_whitespace('[' . get_bloginfo('name') . '] ' . (is_null($subject) ? 'Debug' : $subject));
		ob_start();
		print_r($variable);
		$mbody = ob_get_clean();

		try {
			if($mail)
				mail($mto, $msub, $mbody);
			else
				wp_mail($mto, $msub, $mbody);
		} catch(Exception $e) { return false; }

		return true;
	}
}

//--------------------------------------------------------------------- end email



//---------------------------------------------------------------------
// Forms
//---------------------------------------------------------------------

//-------------------------------------------------
// Generate form timestamp
//
// this field can be used to prevent rapid
// form submissions by robots
//
// @param n/a
// @return hash
if(!function_exists('common_get_form_timestamp'))
{
	function common_get_form_timestamp(){
		$time = time();
		return "$time," . md5($time . NONCE_KEY);
	}
}

//-------------------------------------------------
// Validate form timestamp
//
// @param hash
// @param time elapsed (must be >= this value)
// @return true/false
if(!function_exists('common_check_form_timestamp'))
{
	function common_check_form_timestamp($hash='', $elapsed=5){
		if(!preg_match('/^\d+,([\da-f]{32})$/i', $hash))
			return false;
		list($t,$h) = explode(',', $hash);
		return ($h === md5($t . NONCE_KEY) && time() - $t >= $elapsed);
	}
}

//--------------------------------------------------------------------- end forms



//---------------------------------------------------------------------
// Images
//---------------------------------------------------------------------

//-------------------------------------------------
// Allow SVG uploads
//
// @param image types
// @return image types
if(!function_exists('common_upload_mimes'))
{
	add_filter('upload_mimes', 'common_upload_mimes');
	function common_upload_mimes ($existing_mimes=array()){
		// add the file extension to the array
		$existing_mimes['svg'] = 'image/svg+xml';
		// call the modified list of extensions
		return $existing_mimes;
	}
}

//-------------------------------------------------
// Get featured image src
//
// @param post id
// @param size
// @param return attributes (ambiguous keys correspond to wp_get_attachment_image_src)
//		0 URL
//		1 width
//		2 height
//		3 is resized
// @return array, src or false
if(!function_exists('common_get_featured_image_src'))
{
	function common_get_featured_image_src($id=0, $size=null, $attributes=false){
		$id = (int) $id;

		$tmp = get_post_thumbnail_id($id);
		if($tmp)
		{
			$tmp2 = wp_get_attachment_image_src($tmp, $size);
			if(is_array($tmp2) && filter_var($tmp2[0], FILTER_VALIDATE_URL))
				return $attributes === true ? $tmp2 : $tmp2[0];
		}

		return false;
	}
}

//-------------------------------------------------
// Get featured image path
//
// @param post id
// @param size
// @return path or false
if(!function_exists('common_get_featured_image_path'))
{
	function common_get_featured_image_path($id=0, $size=null){
		//surprisingly, there isn't a built-in function for this, so
		//let's just convert the URL back into the path
		if(false === ($url = common_get_featured_image_src($id, $size)))
			return false;

		return common_get_path_by_url($url);
	}
}

//-------------------------------------------------
// Get file path from url
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param url
// @return path
if(!function_exists('common_get_path_by_url'))
{
	function common_get_path_by_url($url){

		$from = site_url();
		$to = ABSPATH;

		//make sure both from and to end with a slash
		if(substr($from, -1) !== '/')
			$from .= "/";
		if(substr($to, -1) !== '/')
			$to .= "/";

		return str_replace($from, $to, $url);
	}
}

//-------------------------------------------------
// Clean SVG
//
// strip out XML headers and garbage that might be
// collected at the top of the file to make for a
// better inline file
//
// @param file path
// @return svg data or false
if(!function_exists('common_get_clean_svg'))
{
	function common_get_clean_svg($path){
		if(!@file_exists($path))
			return false;

		//start by cleaning up whitespace
		$svg = common_sanitize_whitespace(@file_get_contents($path));

		//fix a couple common Illustrator bugs
		$svg = str_replace(array('xmlns="&ns_svg;"','xmlns:xlink="&ns_xlink;"','id="Layer_1"'), array('xmlns="http://www.w3.org/2000/svg"','xmlns:xlink="http://www.w3.org/1999/xlink"',''), $svg);

		//drop spaces between tags
		$svg = str_replace("> <", "><", $svg);

		//find out where our SVG starts and ends
		if(false === ($start = strpos($svg, '<svg')) || false === ($end = strpos($svg, '</svg>')))
			return false;

		//and done!
		return common_sanitize_whitespace(substr($svg, $start, ($end - $start + 6)));
	}
}

//-------------------------------------------------
// Return Data URI image
//
// @param path
// @return data
if(!function_exists('common_get_data_uri'))
{
	function common_get_data_uri($path){
		if(!@file_exists($path))
			return false;

		$type = wp_check_filetype($path);

		return "data: {$type['type']};base64," . base64_encode(file_get_contents($path));
	}
}

//--------------------------------------------------------------------- end images



//---------------------------------------------------------------------
// Localities
//---------------------------------------------------------------------

//-------------------------------------------------
// Return array of us states
//
// @param include other?
// @return states
if(!function_exists('common_get_us_states'))
{
	function common_get_us_states($include_other=true){
		$states = array('AL' => 'ALABAMA',
					'AK' => 'ALASKA',
					'AZ' => 'ARIZONA',
					'AR' => 'ARKANSAS',
					'CA' => 'CALIFORNIA',
					'CO' => 'COLORADO',
					'CT' => 'CONNECTICUT',
					'DE' => 'DELAWARE',
					'DC' => 'DISTRICT OF COLUMBIA',
					'FL' => 'FLORIDA',
					'GA' => 'GEORGIA',
					'HI' => 'HAWAII',
					'ID' => 'IDAHO',
					'IL' => 'ILLINOIS',
					'IN' => 'INDIANA',
					'IA' => 'IOWA',
					'KS' => 'KANSAS',
					'KY' => 'KENTUCKY',
					'LA' => 'LOUISIANA',
					'ME' => 'MAINE',
					'MD' => 'MARYLAND',
					'MA' => 'MASSACHUSETTS',
					'MI' => 'MICHIGAN',
					'MN' => 'MINNESOTA',
					'MS' => 'MISSISSIPPI',
					'MO' => 'MISSOURI',
					'MT' => 'MONTANA',
					'NE' => 'NEBRASKA',
					'NV' => 'NEVADA',
					'NH' => 'NEW HAMPSHIRE',
					'NJ' => 'NEW JERSEY',
					'NM' => 'NEW MEXICO',
					'NY' => 'NEW YORK',
					'NC' => 'NORTH CAROLINA',
					'ND' => 'NORTH DAKOTA',
					'OH' => 'OHIO',
					'OK' => 'OKLAHOMA',
					'OR' => 'OREGON',
					'PA' => 'PENNSYLVANIA',
					'RI' => 'RHODE ISLAND',
					'SC' => 'SOUTH CAROLINA',
					'SD' => 'SOUTH DAKOTA',
					'TN' => 'TENNESSEE',
					'TX' => 'TEXAS',
					'UT' => 'UTAH',
					'VT' => 'VERMONT',
					'VA' => 'VIRGINIA',
					'WA' => 'WASHINGTON',
					'WV' => 'WEST VIRGINIA',
					'WI' => 'WISCONSIN',
					'WY' => 'WYOMING');

		$other = array('AA' => 'ARMED FORCES AMERICAS',
					'AE' => 'ARMED FORCES EUROPE',
					'AP' => 'ARMED FORCES PACIFIC',
					'AS' => 'AMERICAN SAMOA',
					'FM' => 'FEDERATED STATES OF MICRONESIA',
					'GU' => 'GUAM GU',
					'MH' => 'MARSHALL ISLANDS',
					'MP' => 'NORTHERN MARIANA ISLANDS',
					'PW' => 'PALAU',
					'PR' => 'PUERTO RICO',
					'VI' => 'VIRGIN ISLANDS');

		if($include_other)
			return array_merge($states, $other);
		else
			return $states;
	}
}

//-------------------------------------------------
// Return canadian provinces
//
// @param n/a
// @return provinces
if(!function_exists('common_get_ca_provinces'))
{
	function common_get_ca_provinces(){
		return array('AB'=>'ALBERTA',
					 'BC'=>'BRITISH COLUMBIA',
					 'MB'=>'MANITOBA',
					 'NB'=>'NEW BRUNSWICK',
					 'NL'=>'NEWFOUNDLAND',
					 'NT'=>'NORTHWEST TERRITORIES',
					 'NS'=>'NOVA SCOTIA',
					 'NU'=>'NUNAVUT',
					 'ON'=>'ONTARIO',
					 'PE'=>'PRINCE EDWARD ISLAND',
					 'QC'=>'QUEBEC',
					 'SK'=>'SASKATCHEWAN',
					 'YT'=>'YUKON');
	}
}

//-------------------------------------------------
// Datediff
//
// a simple function to count the number of days
// between two dates
//
// @param date1
// @param date2
// @return days
if(!function_exists('common_datediff'))
{
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
		catch(Exception $e) {
			//this is a simple fallback using unix timestamps
			//however it will fail to consider things like
			//daylight saving
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		}
	}
}

//-------------------------------------------------
// IP as Number
//
// convert an IP to a number for cleaner comparison
//
// @param IP
// @return num or false
if(!function_exists('common_ip_to_number'))
{
	function common_ip_to_number($ip){
		if(!filter_var($ip, FILTER_VALIDATE_IP))
			return false;

		//ipv4 is easy
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return ip2long($ip);

		//ipv6 is a little more roundabout
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			$binNum = '';
			foreach (unpack('C*', inet_pton($ip)) as $byte) {
				$binNum .= str_pad(decbin($byte), 8, "0", STR_PAD_LEFT);
			}
			return base_convert(ltrim($binNum, '0'), 2, 10);
		}

		return false;
	}
}

//--------------------------------------------------------------------- end localities



//---------------------------------------------------------------------
// Miscellaneous
//---------------------------------------------------------------------

//-------------------------------------------------
// Database logging - query errors
//
// this logs invalid queries to db-debug.log if
// the WP_DB_DEBUG_LOG constant is set to true.
// this can be used independently of WP_DEBUG
//
// @param n/a
// @return n/a
if(!function_exists('common_db_debug_log'))
{
	function common_db_debug_log(){
		if(!defined('WP_DB_DEBUG_LOG') || !WP_DB_DEBUG_LOG)
			return;

		//WP already stores query errors in this obscure
		//global variable, so we can see what we've ended
		//up with just before shutdown
		global $EZSQL_ERROR;
		$log = ABSPATH . '/wp-content/db-debug.log';

		try {
			if(is_array($EZSQL_ERROR) && count($EZSQL_ERROR))
			{
				$xout = array();
				$xout[] = "DATE: " . date('r', current_time('timestamp'));
				$xout[] = "SITE: " . site_url();
				$xout[] = "IP: " . $_SERVER['REMOTE_ADDR'];
				$xout[] = "UA: " . $_SERVER['HTTP_USER_AGENT'];
				$xout[] = "SCRIPT: " . $_SERVER['SCRIPT_NAME'];
				$xout[] = "REQUEST: " . $_SERVER['REQUEST_URI'];
				foreach($EZSQL_ERROR AS $e)
					$xout[] = str_repeat('-', 50) . "\n" . implode("\n", $e) . "\n" . str_repeat('-', 50);
				$xout[] = "\n\n\n\n";

				@file_put_contents($log, implode("\n", $xout), FILE_APPEND);
			}
		} catch(Exception $e){ }

		return;
	}
	add_action('shutdown', 'common_db_debug_log');
}

//-------------------------------------------------
// Return the first index of an array
//
// this is like array_pop for the first entry
//
// @param array
// @return mixed or false
if(!function_exists('common_array_pop_top'))
{
	function common_array_pop_top(&$arr){
		if(!is_array($arr) || !count($arr))
			return false;

		reset($arr);
		return $arr[key($arr)];
	}
}

//-------------------------------------------------
// Return the last index of an array
//
// this is like array_pop but doesn't destroy the
// array
//
// @param array
// @return mixed or false
if(!function_exists('common_array_pop'))
{
	function common_array_pop(&$arr){
		if(!is_array($arr) || !count($arr))
			return false;

		$reversed = array_reverse($arr);
		return common_array_pop_top($reversed);
	}
}

//-------------------------------------------------
// Switch two variables
//
// @param var1
// @param var2
// @return true
if(!function_exists('common_switcheroo'))
{
	function common_switcheroo(&$var1, &$var2){
		$tmp = $var2;
		$var2 = $var1;
		$var1 = $tmp;

		return true;
	}
}

//-------------------------------------------------
// WP Parse Args wrapper
//
// remove extra keys from $args if they are not in
// $defaults
//
// @param args
// @param defaults
// @return parsed
if(!function_exists('common_parse_args'))
{
	function common_parse_args($args=null, $defaults=null){
		if(is_array($defaults) && is_array($args))
		{
			foreach($args AS $k=>$v)
			{
				if(!array_key_exists($k, $defaults))
					unset($args[$k]);
			}
		}

		return wp_parse_args($args, $defaults);
	}
}

//-------------------------------------------------
// Generate a random string
//
// using only unambiguous letters
//
// @param length
// @return string
if(!function_exists('common_generate_random_string'))
{
	function common_generate_random_string($length=10){
		$soup = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','8','9');

		$length = (int) $length;
		if($length <= 0)
			return '';

		//pick nine entries at random
		$salt = '';
		for($x=0; $x<$length; $x++)
			$salt .= $soup[array_rand($soup, 1)];

		return $salt;
	}
}

//-------------------------------------------------
// Case-insensitive in_array()
//
// @param needle
// @param haystack
// @return true/false
if(!function_exists('common_iin_array()'))
{
	function common_iin_array($needle, $haystack){
		$needle = strtolower($needle);
		$haystack = array_map('strtolower', $haystack);
		return in_array($needle, $haystack);
	}
}

//-------------------------------------------------
// Case-insensitive substr_count
//
// @param haystack
// @param needle
// @return true/false
if(!function_exists('common_isubstr_count()'))
{
	function common_isubstr_count($haystack, $needle){
		$needle = strtolower($needle);
		$haystack = strtolower($haystack);
		return substr_count($haystack, $needle);
	}
}

//-------------------------------------------------
// readfile() in chunks
//
// this greatly reduces the server resource demands
// compared with reading a file all in one go
//
// @param file
// @param bytes
// @return bytes or false
if(!function_exists('common_readfile_chunked'))
{
	function common_readfile_chunked($file, $retbytes=true){
		$buffer = '';
		$cnt = 0;
		$chunk_size = 1024*1024;

		if(false === ($handle = fopen($file, 'rb')))
			return false;
		while(!feof($handle))
		{
			$buffer = fread($handle, $chunk_size);
			echo $buffer;
			ob_flush();
			flush();
			if($retbytes)
				$cnt += strlen($buffer);
		}

		$status = fclose($handle);

 		//return number of bytes delivered like readfile() does
		if($retbytes && $status)
			return $cnt;

		return $status;
	}
}

//--------------------------------------------------------------------- end misc



//---------------------------------------------------------------------
// Sanitizing, validation, and formatting
//---------------------------------------------------------------------

//-------------------------------------------------
// Check for UTF-8
//
// @param string
// @return true/false
if(!function_exists('common_is_utf8'))
{
	function common_is_utf8($str){
		return (bool) preg_match('//u', $str);
	}
}

//-------------------------------------------------
// Convert to UTF-8
//
// @param string
// @return string or false
if(!function_exists('common_utf8'))
{
	function common_utf8($str){
		if(common_is_utf8($str))
			return $str;
		else
		{
			try {
				$str = mb_convert_encoding($str, 'UTF-8');
				return $str;
			} catch(Exception $e){
				return false;
			}
		}

		return false;
	}
}

//-------------------------------------------------
// Make excerpt (character length)
//
// @param string
// @param length
// @param append
// @param chop method (chars or words)
// @return excerpt
if(!function_exists('common_get_excerpt'))
{
	function common_get_excerpt($str, $length=200, $append='...', $method='chars'){
		$str = trim(common_sanitize_whitespace(strip_tags(common_sanitize_whitespace($str))));

		//limit string to X characters
		if($method === 'chars' && strlen($str) > $length)
				$str = trim(substr($str, 0, $length)) . $append;
		//limit string to X words
		elseif($method === 'words' && substr_count($str, ' ') > $length + 1)
			$str = implode(' ', array_slice(explode(' ', $str), 0, $length)) . $append;

		return $str;
	}
}

//-------------------------------------------------
// Sanitize name (like a person's name)
//
// @param name
// @return name
if(!function_exists('common_sanitize_name'))
{
	function common_sanitize_name($str=''){
		$str = common_utf8($str);
		return ucwords(common_sanitize_whitespace(preg_replace('/[^\p{L}\p{Zs}\p{Pd}\d\'\"\,\.]/u', '', common_sanitize_quotes($str))));
	}
}

//-------------------------------------------------
// Sanitize CSV
//
// @param field
// @return field
if(!function_exists('common_sanitize_csv'))
{
	function common_sanitize_csv($str=''){
		return common_sanitize_whitespace(str_replace('"', '\"', common_sanitize_quotes($str)));
	}
}

//-------------------------------------------------
// Consistent new lines (\n)
//
// @param str
// @return str
if(!function_exists('common_sanitize_newlines'))
{
	function common_sanitize_newlines($str=''){
		$str = common_utf8($str);
		$str = str_replace("\r\n", "\n", $str);
		$str = preg_replace('/\v/u', "\n", $str);
		$str = preg_replace("/\n{2,}/", "\n\n", $str);
		return trim($str);
	}
}

//-------------------------------------------------
// Single spaces
//
// @param str
// @return str
if(!function_exists('common_sanitize_spaces'))
{
	function common_sanitize_spaces($str=''){
		$str = common_utf8($str);
		return trim(preg_replace('/\h{1,}/u', ' ', $str));
	}
}

//-------------------------------------------------
// Sanitize all white space
//
// @param str
// @param multiline
// @return str
if(!function_exists('common_sanitize_whitespace'))
{
	function common_sanitize_whitespace($str='', $multiline=false){

		//convert all white space to a regular " "
		if(!$multiline)
			return trim(preg_replace('/\s{1,}/u', ' ', common_utf8($str)));

		$str = common_sanitize_spaces($str);
		$str = common_sanitize_newlines($str);

		return $str;
	}
}

//-------------------------------------------------
// Make consistent quotes
//
// @param str
// @return str
if(!function_exists('common_sanitize_quotes'))
{
	function common_sanitize_quotes($str=''){
		$quotes = array(
			//Windows codepage 1252
			"\xC2\x82" => "'",		// U+0082⇒U+201A single low-9 quotation mark
			"\xC2\x84" => '"',		// U+0084⇒U+201E double low-9 quotation mark
			"\xC2\x8B" => "'",		// U+008B⇒U+2039 single left-pointing angle quotation mark
			"\xC2\x91" => "'",		// U+0091⇒U+2018 left single quotation mark
			"\xC2\x92" => "'",		// U+0092⇒U+2019 right single quotation mark
			"\xC2\x93" => '"',		// U+0093⇒U+201C left double quotation mark
			"\xC2\x94" => '"',		// U+0094⇒U+201D right double quotation mark
			"\xC2\x9B" => "'",		// U+009B⇒U+203A single right-pointing angle quotation mark

			//Regular Unicode		// U+0022 quotation mark (")
			                  		// U+0027 apostrophe     (')
			"\xC2\xAB"     => '"',	// U+00AB left-pointing double angle quotation mark
			"\xC2\xBB"     => '"',	// U+00BB right-pointing double angle quotation mark
			"\xE2\x80\x98" => "'",	// U+2018 left single quotation mark
			"\xE2\x80\x99" => "'",	// U+2019 right single quotation mark
			"\xE2\x80\x9A" => "'",	// U+201A single low-9 quotation mark
			"\xE2\x80\x9B" => "'",	// U+201B single high-reversed-9 quotation mark
			"\xE2\x80\x9C" => '"',	// U+201C left double quotation mark
			"\xE2\x80\x9D" => '"',	// U+201D right double quotation mark
			"\xE2\x80\x9E" => '"',	// U+201E double low-9 quotation mark
			"\xE2\x80\x9F" => '"',	// U+201F double high-reversed-9 quotation mark
			"\xE2\x80\xB9" => "'",	// U+2039 single left-pointing angle quotation mark
			"\xE2\x80\xBA" => "'",	// U+203A single right-pointing angle quotation mark
		);
		$from = array_keys($quotes); // but: for efficiency you should
		$to = array_values($quotes); // pre-calculate these two arrays
		return str_replace($from, $to, $str);
	}
}

//-------------------------------------------------
// Sanitize JS variable
//
// this should be used for var = 'variable';
//
// @param str
// @return str
if(!function_exists('common_sanitize_js_variable'))
{
	function common_sanitize_js_variable($str=''){
		return str_replace("'", "\'", common_sanitize_whitespace(common_sanitize_quotes($str)));
	}
}

//-------------------------------------------------
// Better email sanitizing
//
// @param email
// @return email
if(!function_exists('common_sanitize_email'))
{
	function common_sanitize_email($email=''){
		return strtolower(str_replace(array("'", '"'), '', sanitize_email($email)));
	}
}

//-------------------------------------------------
// Validate an email (FQDN)
//
// @param email
// @return true/false
if(!function_exists('common_validate_email'))
{
	function common_validate_email($email=''){
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^.+\@.+\..+$/', $email);
	}
}

//-------------------------------------------------
// Sanitize a US zip5 code
//
// @param zip
// @return zip
if(!function_exists('common_sanitize_zip5'))
{
	function common_sanitize_zip5($zip){
		$zip = preg_replace('/[^\d]/', '', $zip);
		if(strlen($zip) < 5)
			$zip = sprintf('%05d', $zip);
		elseif(strlen($zip) > 5)
			$zip = substr($zip, 0, 5);

		if($zip === '00000')
			$zip = '';

		return $zip;
	}
}

//-------------------------------------------------
// Sanitize IP
//
// IPv6 addresses are compacted for consistency
//
// @param IP
// @return IP
if(!function_exists('common_sanitize_ip'))
{
	function common_sanitize_ip($ip){
		//start by getting rid of obviously bad data
		$ip = strtolower(preg_replace('/[^\d\.\:a-f]/i', '', $ip));

		//try to compact
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			$ip = inet_ntop(inet_pton($ip));

		return $ip;
	}
}

//-------------------------------------------------
// Format money
//
// @param amount
// @param cents (if under $1, use ¢ sign)
// @return money
if(!function_exists('common_format_money'))
{
	function common_format_money($amount, $cents=false){
		$amount = (double) preg_replace('/[^\d\.]/', '', $amount);
		$amount = round($amount,2);

		if($amount >= 1 || $cents === false)
			return '$' . number_format($amount,2,'.','');
		else
			return (100 * $amount) . '¢';
	}
}

//-------------------------------------------------
// Validate credit card
//
// @param card
// @return true/false
if(!function_exists('common_validate_cc'))
{
	function common_validate_cc( $ccnum='' ){

		//digits only
		$ccnum = preg_replace('/[^\d]/', '', $ccnum);

		//different cards have different length requirements
		switch (substr($ccnum,0,1)){
			//Amex
			case 3:
				if(strlen($ccnum) != 15 || !preg_match('/3[47]/', $ccnum)) return false;
				break;
			//Visa
			case 4:
				if(!in_array(strlen($ccnum), array(13,16))) return false;
				break;
			//MC
			case 5:
				if(strlen($ccnum) != 16 || !preg_match('/5[1-5]/', $ccnum)) return false;
				break;
			//Disc
			case 6:
				if(strlen($ccnum) != 16 || substr($ccnum, 0, 4) != '6011') return false;
				break;
			//There is nothing else...
			default:
				return false;
		}

		// Start MOD 10 checks
		$dig = common_to_char_array($ccnum);
		$numdig = count($dig);
		$j = 0;
		for ($i=($numdig-2); $i>=0; $i-=2){
			$dbl[$j] = $dig[$i] * 2;
			$j++;
		}
		$dblsz = count($dbl);
		$validate =0;
		for ($i=0;$i<$dblsz;$i++){
			$add = common_to_char_array($dbl[$i]);
			for ($j=0;$j<count($add);$j++){
				$validate += $add[$j];
			}
			$add = '';
		}
		for ($i=($numdig-1); $i>=0; $i-=2){
			$validate += $dig[$i];
		}

		if(substr($validate, -1, 1) == '0')
			return true;
		else
			return false;
	}
}

//-------------------------------------------------
// Turn a string into an array of chars
//
// (this is only used for CC validation)
//
// @param string
// @return array
if(!function_exists('common_to_char_array'))
{
	function common_to_char_array($input){
		$len = strlen($input);
		for ($j=0;$j<$len;$j++){
			$char[$j] = substr($input, $j, 1);
		}
		return ($char);
	}
}

//-------------------------------------------------
// Remove non-numeric chars from str
//
// @param num
// @return num (float)
if(!function_exists('common_sanitize_number'))
{
	function common_sanitize_number($num){
		//numbers and periods only
		$num = preg_replace('/[^\d\.]/', '', $num);

		//drop 2+ periods
		if(substr_count($num, '.') > 1)
		{
			$first = strpos($num, '.');
			$num = substr($num, 0, $first) . '.' . str_replace('.', '', substr($num, $first));
		}

		//done!
		return (float) $num;
	}
}

//-------------------------------------------------
// Typecast as (int)
//
// @param int-ish
// @return int
if(!function_exists('common_intval'))
{
	function common_intval($num){
		return intval(common_sanitize_number($num));
	}
}

//-------------------------------------------------
// Typecast as (double)... which is really just
// (float) for old people
//
// @param double-ish
// @return double
if(!function_exists('common_doubleval'))
{
	function common_doubleval($num){
		return (double) common_sanitize_number($num);
	}
}

//-------------------------------------------------
// Typecast as (float)
//
// @param float-ish
// @return float
if(!function_exists('common_floatval'))
{
	function common_floatval($num){
		return (float) common_sanitize_number($num);
	}
}

//-------------------------------------------------
// Sanitize phone number
//
// this function should only be used on north
// american numbers, like: (123) 456-7890 x12345
//
// @param phone
// @return phone
if(!function_exists('common_sanitize_phone'))
{
	function common_sanitize_phone($value=''){
		$value = preg_replace('/[^\d]/', '', $value);

		//if this looks like a 10-digit number with the +1 on it, chop it off
		if(strlen($value) === 11 && intval(substr($value,0,1)) === 1)
			$value = substr($value, 1);

		return $value;
	}
}

//-------------------------------------------------
// Validate north american phone number
//
// the first 10 digits must match standards
//
// @param phone
// @return true/false
if(!function_exists('common_validate_phone'))
{
	function common_validate_phone($value=''){
		//match the first 10
		$first10 = substr($value, 0, 10);
		return preg_match("/^[2-9][0-8][0-9][2-9][0-9]{2}[0-9]{4}$/i", $first10);
	}
}

//-------------------------------------------------
// Format phone
//
// again, this assumes north american formatting
//
// @param n/a
// @return phone (pretty)
if(!function_exists('common_format_phone'))
{
	function common_format_phone($value=''){
		$value = common_sanitize_phone($value);

		if(strlen($value) >= 10)
		{
			$first10 = substr($value,0,10);
			return preg_replace("/^([0-9]{3})([0-9]{3})([0-9]{4})/i", "(\\1) \\2-\\3", $first10) . (strlen($value) > 10 ? ' x' . substr($value,10) : '');
		}

		return $value;
	}
}

//--------------------------------------------------------------------- end sanitizing



//---------------------------------------------------------------------
// URLs
//---------------------------------------------------------------------

//-------------------------------------------------
// Check whether a URL is local
//
// @param url
// @return true/false
if(!function_exists('common_is_site_url'))
{
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
if(!function_exists('common_is_current_page'))
{
	function common_is_current_page($url, $subpages=false){

		//ready the test URL for comparison
		$url = parse_url($url, PHP_URL_PATH);
		$url2 = parse_url(site_url($_SERVER['REQUEST_URI']), PHP_URL_PATH);

		//and check for a match
		return $subpages ? substr($url2, 0, strlen($url)) === $url : $url === $url2;
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
if(!function_exists('common_redirect'))
{
	function common_redirect($url=null, $offsite=false){
		if(is_null($url) || (true !== $offsite && !common_is_site_url($url)))
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

//--------------------------------------------------------------------- end URLs

?>