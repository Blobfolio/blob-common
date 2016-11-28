<?php
//---------------------------------------------------------------------
// FUNCTIONS: SANITIZE/VALIDATE
//---------------------------------------------------------------------
// This file contains functions related to sanitizing and validating
// data

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//---------------------------------------------------------------------
// Misc Formatting
//---------------------------------------------------------------------

//-------------------------------------------------
// Format money
//
// @param amount
// @param cents (if under $1, use ¢ sign)
// @return money
if(!function_exists('common_format_money')){
	function common_format_money($amount, $cents=false){
		//convert back to dollars if it is so easy
		if(preg_match('/^[\d]+¢$/', $amount))
			$amount = round(common_sanitize_number($amount) / 100, 2);
		else
			$amount = round(common_sanitize_number($amount), 2);

		$negative = $amount < 0;
		if($negative)
			$amount = abs($amount);

		if($amount >= 1 || $cents === false)
			return ($negative ? '-' : '') . '$' . number_format($amount,2,'.','');
		else
			return ($negative ? '-' : '') . (100 * $amount) . '¢';
	}
	add_filter('common_format_money', 'common_format_money', 5, 2);
}

//-------------------------------------------------
// Format phone
//
// again, this assumes north american formatting
//
// @param n/a
// @return phone (pretty)
if(!function_exists('common_format_phone')){
	function common_format_phone($value=''){
		$value = common_sanitize_phone($value);

		if(common_strlen($value) >= 10){
			$first10 = substr($value,0,10);
			return preg_replace("/^([0-9]{3})([0-9]{3})([0-9]{4})/i", "(\\1) \\2-\\3", $first10) . (common_strlen($value) > 10 ? ' x' . substr($value,10) : '');
		}

		return $value;
	}
}

//-------------------------------------------------
// Singular/Plural inflection based on number
//
// @param number
// @param single
// @param plural
// @return string
if(!function_exists('common_inflect')){
	function common_inflect($num, $single='', $plural=''){
		$num = (int) $num;
		if($num === 1)
			return sprintf($single, $num);
		else
			return sprintf($plural, $num);
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
if(!function_exists('common_get_excerpt')){
	function common_get_excerpt($str, $length=200, $append='...', $method='chars'){
		$str = trim(common_sanitize_whitespace(strip_tags(common_sanitize_whitespace($str))));

		//limit string to X characters
		if($method === 'chars' && common_strlen($str) > $length)
				$str = trim(substr($str, 0, $length)) . $append;
		//limit string to X words
		elseif($method === 'words' && substr_count($str, ' ') > $length + 1)
			$str = implode(' ', array_slice(explode(' ', $str), 0, $length)) . $append;

		return $str;
	}
}

//-------------------------------------------------
// Unix slashes
//
// fix backward Windows slashes, and also get
// rid of double slashes and dot paths
//
// @param path
// @return path
if(!function_exists('common_unixslashit')){
	function common_unixslashit($path=''){
		$path = str_replace('\\', '/', $path);
		$path = str_replace('/./', '//', $path);
		return preg_replace('/\/{2,}/', '/', $path);
	}
}

//-------------------------------------------------
// Unleading Slash
//
// WP doesn't have leading slash functions for
// some reason
//
// @param path
// @return path
if(!function_exists('common_unleadingslashit')){
	function common_unleadingslashit($path=''){
		$path = common_unixslashit($path);
		return ltrim($path, '/');
	}
}

//-------------------------------------------------
// Leading Slash
//
// WP doesn't have leading slash functions for
// some reason
//
// @param path
// @return path
if(!function_exists('common_leadingslashit')){
	function common_leadingslashit($path=''){
		return '/' . common_unleadingslashit($path);
	}
}

//-------------------------------------------------
// Convert a k=>v associative array to an indexed
// array
//
// @param arr
// @return arr
if(!function_exists('common_array_to_indexed')){
	function common_array_to_indexed($arr){
		$out = array();
		if(!is_array($arr) || !count($arr))
			return $out;

		foreach($arr AS $k=>&$v){
			$out[] = array(
				'key'=>$k,
				'value'=>$v
			);
		}

		return $out;
	}
}

//--------------------------------------------------------------------- end formatting



//---------------------------------------------------------------------
// Sanitization
//---------------------------------------------------------------------

//-------------------------------------------------
// Force a value to fall within a range
//
// @param value
// @param min
// @param max
// @return value
if(!function_exists('common_to_range')){
	function common_to_range($value, $min=null, $max=null){

		//max sure min/max are in the right order
		if(!is_null($min) && !is_null($max) && $min > $max)
			common_switcheroo($min, $max);

		//recursive
		if(is_array($value)){
			foreach($value AS $k=>$v)
				$value[$k] = common_to_range($v, $min, $max);
		}
		else {
			if(!is_null($min) && $value < $min)
				$value = $min;
			if(!is_null($max) && $value > $max)
				$value = $max;
		}

		return $value;
	}
}

//-------------------------------------------------
// Check if a value is within range
//
// @param value
// @param min
// @param max
// @return true/false
if(!function_exists('common_in_range')){
	function common_in_range($value, $min=null, $max=null){
		return $value === common_to_range($value, $min, $max);
	}
}

//-------------------------------------------------
// Check if a string's length is within range
//
// @param str
// @param min
// @param max
// @return true/false
if(!function_exists('common_length_in_range')){
	function common_length_in_range($str, $min=null, $max=null){
		$str = (string) $str;
		$length = common_strlen($str);

		if(!is_null($min))
			$min = common_sanitize_int($min);
		if(!is_null($max))
			$max = common_sanitize_int($max);

		return $length === common_to_range($length, $min, $max);
	}
}

//-------------------------------------------------
// Convert to UTF-8
//
// @param string
// @return string or false
if(!function_exists('common_utf8')){
	function common_utf8($str){
		@require_once(dirname(__FILE__) . '/utf8.php');

		//we don't need to worry about certain types
		if(is_numeric($str) || is_bool($str) || (is_string($str) && !strlen($str)))
			return $str;

		$str = (string) $str;

		$str = \blobcommon\utf8::toUTF8($str);
		return (1 === @preg_match('/^./us', $str)) ? $str : false;
	}
}
//alias
if(!function_exists('common_sanitize_utf8')){
	function common_sanitize_utf8($str){ return common_utf8($str); }
}

//-------------------------------------------------
// Sanitize name (like a person's name)
//
// @param name
// @return name
if(!function_exists('common_sanitize_name')){
	function common_sanitize_name($str=''){
		$str = common_utf8($str);
		return ucwords(common_sanitize_whitespace(preg_replace('/[^\p{L}\p{Zs}\p{Pd}\d\'\"\,\.]/u', '', common_sanitize_quotes($str))));
	}
}

//-------------------------------------------------
// Sanitize printable
//
// @param str
// @return str
if(!function_exists('common_sanitize_printable')){
	function common_sanitize_printable($str=''){
		$str = common_utf8($str);
		return preg_replace('/[^[:print:]]/u', '', $str);
	}
}

//-------------------------------------------------
// Sanitize CSV
//
// @param field
// @return field
if(!function_exists('common_sanitize_csv')){
	function common_sanitize_csv($str=''){
		$str = common_sanitize_quotes($str);
		//remove backslashed quotes, if any
		while(substr_count($str, '\"'))
			$str = str_replace('\"', '"', $str);
		//reapply backslashed quotes and sanitize whitespace
		return common_sanitize_whitespace(str_replace('"', '\"', $str));
	}
}

//-------------------------------------------------
// Consistent new lines (\n)
//
// @param str
// @return str
if(!function_exists('common_sanitize_newlines')){
	function common_sanitize_newlines($str='', $newlines=2){
		$str = common_utf8($str);
		$newlines = common_to_range(intval($newlines), 0);
		$str = str_replace("\r\n", "\n", $str);
		$str = preg_replace('/\v/u', "\n", $str);

		//trim each line so we don't miss anything
		$str = implode("\n", array_map('trim', explode("\n", $str)));

		$str = preg_replace('/\n{' . ($newlines + 1) . ',}/', str_repeat("\n", $newlines), $str);
		return trim($str);
	}
}

//-------------------------------------------------
// Single spaces
//
// @param str
// @return str
if(!function_exists('common_sanitize_spaces')){
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
if(!function_exists('common_sanitize_whitespace')){
	function common_sanitize_whitespace($str='', $multiline=false){

		//convert all white space to a regular " "
		if(!$multiline)
			return trim(preg_replace('/\s{1,}/u', ' ', common_utf8($str)));

		$newlines = 2;
		if(is_int($multiline))
			$newlines = $multiline;

		$str = common_sanitize_spaces($str);
		$str = common_sanitize_newlines($str, $newlines);

		return $str;
	}
}

//-------------------------------------------------
// Make consistent quotes
//
// @param str
// @return str
if(!function_exists('common_sanitize_quotes')){
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
			"\xE2\x80\xBA" => "'"	// U+203A single right-pointing angle quotation mark
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
if(!function_exists('common_sanitize_js_variable')){
	function common_sanitize_js_variable($str=''){
		return str_replace("'", "\'", common_sanitize_whitespace(common_sanitize_quotes($str)));
	}
}

//-------------------------------------------------
// Better email sanitizing
//
// @param email
// @return email
if(!function_exists('common_sanitize_email')){
	function common_sanitize_email($email=''){
		return strtolower(str_replace(array("'", '"'), '', common_sanitize_quotes(sanitize_email($email))));
	}
}

//-------------------------------------------------
// Sanitize a US zip5 code
//
// @param zip
// @return zip
if(!function_exists('common_sanitize_zip5')){
	function common_sanitize_zip5($zip){
		$zip = preg_replace('/[^\d]/', '', $zip);
		if(common_strlen($zip) < 5)
			$zip = sprintf('%05d', $zip);
		elseif(common_strlen($zip) > 5)
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
if(!function_exists('common_sanitize_ip')){
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
// Remove non-numeric chars from str
//
// @param num
// @return num (float)
if(!function_exists('common_sanitize_number')){
	function common_sanitize_number($num){
		//let's convert cents back into proper dollars
		if(preg_match('/^[\d]+¢$/', $num))
			$num = substr($num, 0, -1) / 100;

		return (float) filter_var($num, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
}

//-------------------------------------------------
// Bool
//
// @param value
// @return true/false
if(!function_exists('common_sanitize_bool')){
	function common_sanitize_bool($value=false){
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}
}
//alias
if(!function_exists('common_sanitize_boolean')){
	function common_sanitize_boolean($value=false){ return common_sanitize_bool($value); }
}

//-------------------------------------------------
// Float
//
// @param value
// @return true/false
if(!function_exists('common_sanitize_float')){
	function common_sanitize_float($num=0){
		$num = common_sanitize_number($num);
		if(false === $num = filter_var($num, FILTER_VALIDATE_FLOAT))
			return (float) 0;

		return $num;
	}
}
//alias
if(!function_exists('common_doubleval')){
	function common_doubleval($num=0){ return common_sanitize_float($num); }
}
if(!function_exists('common_floatval')){
	function common_floatval($num=0){ return common_sanitize_float($num); }
}

//-------------------------------------------------
// Sanitize by Type
//
// @param value
// @param type
// @return value
if(!function_exists('common_sanitize_by_type')){
	function common_sanitize_by_type($value, $type=null){
		if(!is_string($type) || !strlen($type))
			return $value;

		if($type === 'boolean' || $type === 'bool')
			return common_sanitize_bool($value);
		elseif($type === 'integer' || $type === 'int')
			return common_sanitize_int($value);
		elseif($type === 'double' || $type === 'float')
			return common_sanitize_float($value);
		elseif($type === 'string')
			return common_sanitize_string($value);
		elseif($type === 'array')
			return common_sanitize_array($value);

		return $value;
	}
}

//-------------------------------------------------
// Int
//
// @param value
// @return true/false
if(!function_exists('common_sanitize_int')){
	function common_sanitize_int($num=0){
		$num = common_sanitize_number($num);
		if(false === $num = filter_var($num, FILTER_VALIDATE_INT))
			return (int) 0;

		return $num;
	}
}
//another wrapper
if(!function_exists('common_intval')){
	function common_intval($num=0){ return common_sanitize_int($num); }
}

//-------------------------------------------------
// String
//
// @param value
// @return value
if(!function_exists('common_sanitize_string')){
	function common_sanitize_string($value=''){
		$value = (string) common_utf8($value);
		return $value ? $value : '';
	}
}
//alias
if(!function_exists('common_strval')){
	function common_strval($value=''){ return common_sanitize_string($value); }
}

//-------------------------------------------------
// Array
//
// @param value
// @return value
if(!function_exists('common_sanitize_array')){
	function common_sanitize_array($value=''){
		return (array) $value;
	}
}

//-------------------------------------------------
// Datetime
//
// @param date
// @return date
if(!function_exists('common_sanitize_datetime')){
	function common_sanitize_datetime($date){
		$default = '0000-00-00 00:00:00';
		if($date === $default)
			return $date;

		if(is_numeric($date))
			$date = round($date);
		else {
			if(false === $date = strtotime($date))
				return $default;
		}

		return date('Y-m-d H:i:s', $date);
	}
}
//wrapper for just the date half
if(!function_exists('common_sanitize_date')){
	function common_sanitize_date($date){ return substr(common_sanitize_datetime($date), 0, 10); }
}

//-------------------------------------------------
// Sanitize phone number
//
// this function should only be used on north
// american numbers, like: (123) 456-7890 x12345
//
// @param phone
// @return phone
if(!function_exists('common_sanitize_phone')){
	function common_sanitize_phone($value=''){
		$value = preg_replace('/[^\d]/', '', $value);

		//if this looks like a 10-digit number with the +1 on it, chop it off
		if(common_strlen($value) === 11 && intval(substr($value,0,1)) === 1)
			$value = substr($value, 1);

		return $value;
	}
}

//-------------------------------------------------
// Sanitize domain name
//
// this does not strip invalid characters; it
// merely attempts to extract the hostname portion
// of a URL-like string
//
// @param domain
// @return domain or false
if(!function_exists('common_sanitize_domain_name')){
	function common_sanitize_domain_name($domain){
		$domain = (string) $domain;
		$domain = filter_var(common_sanitize_whitespace(strtolower($domain)), FILTER_SANITIZE_URL);

		if(!common_strlen($domain))
			return false;

		//maybe it is a full URL
		$host = parse_url($domain, PHP_URL_HOST);

		//nope...
		if(is_null($host)){
			$host = $domain;
			//maybe there's a path?
			if(substr_count($host, '/')){
				$host = explode('/', $host);
				$host = common_array_pop_top($host);
			}
			//and/or a query?
			if(substr_count($host, '?')){
				$host = explode('?', $host);
				$host = common_array_pop_top($host);
			}
			//maybe a port?
			if(substr_count($host, ':')){
				$host = explode(':', $host);
				$host = common_array_pop_top($host);
			}
		}

		return $host;
	}
}

//--------------------------------------------------------------------- end sanitize



//---------------------------------------------------------------------
// Validate
//---------------------------------------------------------------------

//-------------------------------------------------
// Check for UTF-8
//
// @param string
// @return true/false
if(!function_exists('common_is_utf8')){
	function common_is_utf8($str){
		return (bool) is_string($str) && (!common_strlen($str) || preg_match('//u', $str));
	}
}

//-------------------------------------------------
// Validate an email (FQDN)
//
// @param email
// @return true/false
if(!function_exists('common_validate_email')){
	function common_validate_email($email=''){
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^.+\@.+\..+$/', $email);
	}
}

//-------------------------------------------------
// Validate north american phone number
//
// the first 10 digits must match standards
//
// @param phone
// @return true/false
if(!function_exists('common_validate_phone')){
	function common_validate_phone($value=''){
		//match the first 10
		$first10 = substr($value, 0, 10);
		return preg_match("/^[2-9][0-8][0-9][2-9][0-9]{2}[0-9]{4}$/i", $first10);
	}
}

//-------------------------------------------------
// Validate credit card
//
// @param card
// @return true/false
if(!function_exists('common_validate_cc')){
	function common_validate_cc( $ccnum=''){

		//digits only
		$ccnum = preg_replace('/[^\d]/', '', $ccnum);

		//different cards have different length requirements
		switch (substr($ccnum,0,1)){
			//Amex
			case 3:
				if(common_strlen($ccnum) != 15 || !preg_match('/3[47]/', $ccnum)) return false;
				break;
			//Visa
			case 4:
				if(!in_array(common_strlen($ccnum), array(13,16))) return false;
				break;
			//MC
			case 5:
				if(common_strlen($ccnum) != 16 || !preg_match('/5[1-5]/', $ccnum)) return false;
				break;
			//Disc
			case 6:
				if(common_strlen($ccnum) != 16 || substr($ccnum, 0, 4) != '6011') return false;
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
// Validate domain name
//
// @param domain
// @param live (does it have an IP?)
// @return true/false
if(!function_exists('common_validate_domain_name')){
	function common_validate_domain_name($domain, $live=true){
		if(false === $host = common_sanitize_domain_name($domain))
			return false;

		//we only want ASCII domains
		if($host !== filter_var($host, FILTER_SANITIZE_URL))
			return false;

		//does our host kinda match domain standards?
		if(!preg_match('/^(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})$/', $host))
			return false;

		//does it have an A record?
		if($live && !filter_var(gethostbyname($host), FILTER_VALIDATE_IP))
			return false;

		return true;
	}
}

//--------------------------------------------------------------------- end validate
?>