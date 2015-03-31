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
add_filter( 'previous_post_rel_link', '__return_false' );
add_filter( 'next_post_rel_link', '__return_false' );

//--------------------------------------------------------------------- end theme/system



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
// @return src or false
if(!function_exists('common_get_featured_image_src'))
{
	function common_get_featured_image_src($id=0, $size=null){
		$id = (int) $id;

		$tmp = get_post_thumbnail_id($id);
		if($tmp)
		{
			$tmp2 = wp_get_attachment_image_src($tmp, $size);
			if(is_array($tmp2) && filter_var($tmp2[0], FILTER_VALIDATE_URL))
				return $tmp2[0];
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

		$svg = preg_replace('/\s{1,}/u', ' ', @file_get_contents($path));

		//Illustrator has some annoying SVG bugs... fix those
		$svg = str_replace(array('xmlns="&ns_svg;"','xmlns:xlink="&ns_xlink;"','id="Layer_1"'), array('xmlns="http://www.w3.org/2000/svg"','xmlns:xlink="http://www.w3.org/1999/xlink"',''), $svg);

		if(false === ($start = strpos($svg, '<svg')) || false === ($end = strpos($svg, '</svg>')))
			return false;

		return preg_replace('/\s{1,}/u', ' ', substr($svg, $start, ($end - $start + 6)));
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

//--------------------------------------------------------------------- end form



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
	function common_get_us_states($include_other=true)
	{
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
	function common_get_ca_provinces()
	{
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

//--------------------------------------------------------------------- end localities



//---------------------------------------------------------------------
// Miscellaneous
//---------------------------------------------------------------------

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
// Make excerpt (character length)
//
// @param string
// @param length
// @param append
// @return excerpt
if(!function_exists('common_get_excerpt'))
{
	function common_get_excerpt($str, $length=200, $append='...'){
		$str = trim(strip_tags(common_sanitize_whitespace($str)));
		if(strlen($str) > $length)
			$str = trim(substr($str, 0, $length)) . $append;

		return $str;
	}
}

//-------------------------------------------------
// Check whether a URL is local
//
// @param url
// @return true/false
if(!function_exists('common_is_site_url'))
{
	function common_is_site_url($url){
		return filter_var($url, FILTER_VALIDATE_URL) && strtolower(substr($url, 0, strlen(site_url()))) === strtolower(site_url());
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
		//strip out the URL so we can compare just the relative uri
		$url = str_replace(site_url(), '', $url);
		if(substr($url, 0, 1) !== '/')
			$url = "/$url";
		if(substr($url, -1) !== '/')
			$url = "$url/";

		//and ready the actual URL for comparison
		$url2 = explode('?', $_SERVER['REQUEST_URI']);
		$url2 = common_array_pop_top($url2);
		if(substr($url2, 0, 1) !== '/')
			$url2 = "/$url";
		if(substr($url2, -1) !== '/')
			$url2 = "$url/";

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

//--------------------------------------------------------------------- end misc



//---------------------------------------------------------------------
// Sanitize and formatting
//---------------------------------------------------------------------

//-------------------------------------------------
// Sanitize name (like a person's name)
//
// @param name
// @return name
if(!function_exists('common_sanitize_name'))
{
	function common_sanitize_name($value=''){
		return ucwords(common_sanitize_quotes(common_sanitize_whitespace(preg_replace('/[^\p{L}\p{Zs}\p{P}]/u', '', $value))));
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
			return trim(preg_replace('/\s{1,}/u', ' ', $str));

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
		$str = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"), array("'", "'", '"', '"', '-', '--', '...'), $str);
		// Next, replace their Windows-1252 equivalents.
		return str_replace(array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)), array("'", "'", '"', '"', '-', '--', '...'), $str);
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
	function common_mail_html_content_type() {
		return 'text/html';
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

//--------------------------------------------------------------------- end sanitizing

?>