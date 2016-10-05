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

//-------------------------------------------------
// Remove Emoji
//
// @param n/a
// @return n/a
if(!function_exists('common_disable_wp_emojicons')){
	function common_disable_wp_emojicons(){
		//all actions related to emojis
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'print_emoji_detection_script', 7 );
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');

		//filter to remove TinyMCE emojis
		add_filter('tiny_mce_plugins', 'common_disable_emojicons_tinymce');
	}
	if(defined('WP_DISABLE_EMOJI') && WP_DISABLE_EMOJI)
		add_action('init', 'common_disable_wp_emojicons');

	//and remove from TinyMCE
	function common_disable_emojicons_tinymce($plugins){
		if(is_array($plugins))
			return array_diff($plugins, array('wpemoji'));

		return array();
	}
}

//-------------------------------------------------
// Add more cron settings
//
// @param schedules
// @return schedules
if(!function_exists('common_cron_schedules')){
	function common_cron_schedules($schedules){

		//every minute
		$schedules['oneminute'] = array(
			'interval' => 60,
			'display' => 'Every 1 minute'
		);

		//every other minute
		$schedules['twominutes'] = array(
			'interval' => 120,
			'display' => 'Every 2 minutes'
		);

		//every five minutes
		$schedules['fiveminutes'] = array(
			'interval' => 300,
			'display' => 'Every 5 minutes'
		);

		//every ten minutes
		$schedules['tenminutes'] = array(
			'interval' => 600,
			'display' => 'Every 10 minutes'
		);

		//ever half hour
		$schedules['halfhour'] = array(
			'interval' => 1800,
			'display' => 'Every 30 minutes'
		);

		return $schedules;
	}
	add_filter('cron_schedules', 'common_cron_schedules');
}

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
if(!function_exists('common_mail')){
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
if(!function_exists('common_mail_html_content_type')){
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
if(!function_exists('common_debug_mail')){
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
		} catch(Exception $e){ return false; }

		return true;
	}
}

//--------------------------------------------------------------------- end email



//---------------------------------------------------------------------
// Files
//---------------------------------------------------------------------

//-------------------------------------------------
// Get file path from url
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param url
// @return path
if(!function_exists('common_get_path_by_url')){
	function common_get_path_by_url($url){

		$from = trailingslashit(site_url());
		$to = trailingslashit(ABSPATH);

		return str_replace($from, $to, $url);
	}
}

//-------------------------------------------------
// Get url from path
//
// this will only work for web-accessible files,
// and only on servers that have the right kind of
// directory separators (i.e. Linux)
//
// @param path
// @return url
if(!function_exists('common_get_url_by_path')){
	function common_get_url_by_path($path){

		$from = trailingslashit(ABSPATH);
		$to = trailingslashit(site_url());

		return str_replace($from, $to, $path);
	}
}

//-------------------------------------------------
// Is a directory empty?
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

//--------------------------------------------------------------------- end files



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
if(!function_exists('common_get_form_timestamp')){
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
if(!function_exists('common_check_form_timestamp')){
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
if(!function_exists('common_upload_mimes')){
	function common_upload_mimes ($existing_mimes=array()){
		$existing_mimes['svg'] = 'image/svg+xml';
		return $existing_mimes;
	}
	add_filter('upload_mimes', 'common_upload_mimes');
}

//-------------------------------------------------
// SVG previews in media library
//
// @param response
// @param attachment
// @param meta
// @return response
if(!function_exists('common_svg_media_thumbnail')){
	function common_svg_media_thumbnails($response, $attachment, $meta){

		if(!is_array($response) || !isset($response['type'], $response['subtype']))
			return $response;

		if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')){
			try {
				$path = get_attached_file($attachment->ID);
				if(@file_exists($path)){
					$svg = new SimpleXMLElement(@file_get_contents($path));
					$src = $response['url'];
					$width = (int) $svg['width'];
					$height = (int) $svg['height'];

					//media gallery
					$response['image'] = compact('src', 'width', 'height');
					$response['thumb'] = compact('src', 'width', 'height');

					//media single
					$response['sizes']['full'] = array(
						'height'		=> $height,
						'width'			=> $width,
						'url'			=> $src,
						'orientation'	=> $height > $width ? 'portrait' : 'landscape',
					);
				}
			}
			catch(Exception $e){}
		}

		return $response;
	}
	add_filter('wp_prepare_attachment_for_js', 'common_svg_media_thumbnails', 10, 3);
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
// @param fallback
// @return array, src or false
if(!function_exists('common_get_featured_image_src')){
	function common_get_featured_image_src($id=0, $size=null, $attributes=false, $fallback=0){
		$id = (int) $id;
		$tmp = (int) get_post_thumbnail_id($id);

		//using a fallback?
		if(!$tmp && $fallback > 0)
			$tmp = $fallback;

		if($tmp){
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
if(!function_exists('common_get_featured_image_path')){
	function common_get_featured_image_path($id=0, $size=null){
		//surprisingly, there isn't a built-in function for this, so
		//let's just convert the URL back into the path
		if(false === ($url = common_get_featured_image_src($id, $size)))
			return false;

		return common_get_path_by_url($url);
	}
}

//-------------------------------------------------
// Blank Image
//
// @param n/a
// @return src
if(!function_exists('common_get_blank_image')){
	function common_get_blank_image(){
		return 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABA
AACAkQBADs=';
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
if(!function_exists('common_get_clean_svg')){
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
if(!function_exists('common_get_data_uri')){
	function common_get_data_uri($path){
		if(!@file_exists($path))
			return false;

		return "data:" . common_get_mime_type($path) . ";base64," . base64_encode(file_get_contents($path));
	}
}

//--------------------------------------------------------------------- end images



//---------------------------------------------------------------------
// Miscellaneous
//---------------------------------------------------------------------

//-------------------------------------------------
// String Length
//
// @param str
// @return length
if(!function_exists('common_strlen')){
	function common_strlen($str){
		//prefer mb_strlen
		if(function_exists('mb_strlen'))
			$length = mb_strlen($str);
		else
			$length = strlen($str);

		if(false === $length)
			$length = 0;

		return $length;
	}
}

//-------------------------------------------------
// Get Mime Type by file path
//
// why is this so hard?! the fileinfo extension is
// not reliably present, and even when it is it
// kinda sucks, and WordPress' internal function
// excludes a ton. well, let's do it ourselves then
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

//-------------------------------------------------
// Database logging - query errors
//
// this logs invalid queries to db-debug.log if
// the WP_DB_DEBUG_LOG constant is set to true.
// this can be used independently of WP_DEBUG
//
// @param n/a
// @return n/a
if(!function_exists('common_db_debug_log')){
	function common_db_debug_log(){
		if(!defined('WP_DB_DEBUG_LOG') || !WP_DB_DEBUG_LOG)
			return;

		//WP already stores query errors in this obscure
		//global variable, so we can see what we've ended
		//up with just before shutdown
		global $EZSQL_ERROR;
		$log = ABSPATH . '/wp-content/db-debug.log';

		try {
			if(is_array($EZSQL_ERROR) && count($EZSQL_ERROR)){
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
// Turn a string into an array of chars
//
// (this is used for CC validation)
//
// @param string
// @return array
if(!function_exists('common_to_char_array')){
	function common_to_char_array($input){
		return str_split($input);
	}
}

//-------------------------------------------------
// Recursive Array Map
//
// this should work on single values, arrays, and
// loopable objects
//
// @param callback
// @param var
// @return filtered
if(!function_exists('common_array_map_recursive')){
	function common_array_map_recursive(callable $func, $value){
		if(is_array($value)){
			foreach($value AS $k=>$v)
				$value[$k] = call_user_func($func, $value);
		}
		elseif(is_object($value)){
			try {
				foreach($value AS $k=>$v){
					$value->{$k} = call_user_func($func, $value);
				}
			} catch(Exception $e){ continue; }
		}
		else
			$value = call_user_func($func, $value);

		return $value;
	}
}

//-------------------------------------------------
// Get a Random Integer
//
// this will shoot for several implementations of
// randomness in order of preference until a
// supported one is found
//
// @param min
// @param max
// @return random
if(!function_exists('common_random_int')){
	function common_random_int($min=0, $max=1){
		static $random_int;

		if(is_null($random_int))
			$random_int = function_exists('random_int');

		$min = (int) $min;
		$max = (int) $max;
		if($min > $max)
			common_switcheroo($min, $max);

		if($random_int)
			return random_int($min, $max);
		else
			return mt_rand($min, $max);
	}
}

//-------------------------------------------------
// Return the first index of an array
//
// this is like array_pop for the first entry
//
// @param array
// @return mixed or false
if(!function_exists('common_array_pop_top')){
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
if(!function_exists('common_array_pop')){
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
if(!function_exists('common_switcheroo')){
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
// @param strict (force same type, one level deep)
// @return parsed
if(!function_exists('common_parse_args')){
	function common_parse_args($args=null, $defaults=null, $strict=false){
		$defaults = (array) $defaults;
		$args = (array) $args;

		if(!count($defaults))
			return array();

		foreach($defaults AS $k=>$v){
			if(array_key_exists($k, $args)){
				if($strict && !is_null($defaults[$k])){
					settype($args[$k], gettype($defaults[$k]));
				}
				$defaults[$k] = $args[$k];
			}
		}

		return $defaults;
	}
}

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
		if($min > $max)
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
// Generate a random string
//
// using only unambiguous letters
//
// @param length
// @param character set (optional)
// @return string
if(!function_exists('common_generate_random_string')){
	function common_generate_random_string($length=10, $soup=null){
		if(is_array($soup) && count($soup)){
			$soup = implode('', array_map('strval', $soup));
			$soup = preg_replace('/[^[:print:]]/u', '', $soup);	//strip non-printable
			$soup = preg_replace('/\s/u', '', $soup);			//strip whitespace
			$soup = array_unique(str_split($soup));
			$soup = array_values($soup);
			if(!count($soup))
				return '';
		}

		//use default soup
		if(!is_array($soup) || !count($soup))
			$soup = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','8','9');

		$length = (int) $length;
		if($length <= 0)
			return '';

		//pick nine entries at random
		$salt = '';
		$max = count($soup) - 1;
		for($x=0; $x<$length; $x++)
			$salt .= $soup[common_random_int(0, $max)];

		return $salt;
	}
}

//-------------------------------------------------
// Case-insensitive in_array()
//
// @param needle
// @param haystack
// @return true/false
if(!function_exists('common_iin_array()')){
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
if(!function_exists('common_isubstr_count()')){
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

//--------------------------------------------------------------------- end misc



//---------------------------------------------------------------------
// Sanitizing, validation, and formatting
//---------------------------------------------------------------------

//-------------------------------------------------
// Singular/Plural inflection based on number
//
// @param number
// @param single
// @param plural
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
// Check for UTF-8
//
// @param string
// @return true/false
if(!function_exists('common_is_utf8')){
	function common_is_utf8($str){
		return (bool) preg_match('//u', $str);
	}
}

//-------------------------------------------------
// Convert to UTF-8
//
// @param string
// @return string or false
if(!function_exists('common_utf8')){
	function common_utf8($str){
		if(common_is_utf8($str))
			return $str;
		else {
			try {
				$str = mb_convert_encoding($str, 'UTF-8');
				return $str;
			} catch(Exception $e){ return false; }
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
		return common_sanitize_whitespace(str_replace('"', '\"', common_sanitize_quotes($str)));
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
		return strtolower(str_replace(array("'", '"'), '', sanitize_email($email)));
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
//these are just wrappers
if(!function_exists('common_doubleval')){
	function common_doubleval($num){ return common_sanitize_float($num); }
}
if(!function_exists('common_floatval')){
	function common_floatval($num){ return common_sanitize_float($num); }
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
	function common_intval($num){ return common_sanitize_int($num); }
}

//-------------------------------------------------
// String
//
// @param value
// @return value
if(!function_exists('common_sanitize_string')){
	function common_sanitize_string($value=''){
		return (string) $value;
	}
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
	function common_sanitize_date($date){ return substr(common_sanitie_datetime($date), 0, 10); }
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

//--------------------------------------------------------------------- end sanitizing



//---------------------------------------------------------------------
// Credit cards
//---------------------------------------------------------------------

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
// Get Expiration Years
//
// @param n/a
// @return years
if(!function_exists('common_get_cc_exp_years')){
	function common_get_cc_exp_years(){
		$years = array();
		for($x=0; $x<10; $x++)
			$years[(intval(current_time('Y')) + $x)] = intval(current_time('Y')) + $x;

		return $years;
	}
}

//-------------------------------------------------
// Get Expiration Months
//
// @param format
// @return months
if(!function_exists('common_get_cc_exp_months')){
	function common_get_cc_exp_months($format='m - M'){
		$months = array();
		for($x=1; $x<=12; $x++)
			$months[$x] = date($format, strtotime("2000-" . sprintf('%02d', $x) . "-01"));

		return $months;
	}
}

//--------------------------------------------------------------------- end credit cards



//---------------------------------------------------------------------
// URLs
//---------------------------------------------------------------------

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
		$domain = common_sanitize_whitespace(strtolower($domain));

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
		}

		return $host;
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

//--------------------------------------------------------------------- end URLs

?>