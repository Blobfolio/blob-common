<?php
//---------------------------------------------------------------------
// FUNCTIONS: SANITIZE/VALIDATE
//---------------------------------------------------------------------
// This file contains functions related to sanitizing, validating,
// and formatting data

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//---------------------------------------------------------------------
// Case Conversion
//---------------------------------------------------------------------

//-------------------------------------------------
// Lower Case
//
// will return multi-byte lowercase if capabale,
// otherwise regular lowercase
//
// @param str
// @return str
if(!function_exists('common_strtolower')){
	function common_strtolower($str=''){
		$extra = array(
			"\xC7\x85"=>"\xC7\x86",			//453=>454
			"\xC7\x88"=>"\xC7\x89",			//456=>457
			"\xC7\x8B"=>"\xC7\x8C",			//459=>460
			"\xC7\xB2"=>"\xC7\xB3",			//498=>499
			"\xCF\xB7"=>"\xCF\xB8",			//1015=>1016
			"\xCF\xB9"=>"\xCF\xB2",			//1017=>1010
			"\xCF\xBA"=>"\xCF\xBB",			//1018=>1019
			"\xE1\xBE\x88"=>"\xE1\xBE\x80",	//8072=>8064
			"\xE1\xBE\x89"=>"\xE1\xBE\x81",	//8073=>8065
			"\xE1\xBE\x8A"=>"\xE1\xBE\x82",	//8074=>8066
			"\xE1\xBE\x8B"=>"\xE1\xBE\x83",	//8075=>8067
			"\xE1\xBE\x8C"=>"\xE1\xBE\x84",	//8076=>8068
			"\xE1\xBE\x8D"=>"\xE1\xBE\x85",	//8077=>8069
			"\xE1\xBE\x8E"=>"\xE1\xBE\x86",	//8078=>8070
			"\xE1\xBE\x8F"=>"\xE1\xBE\x87",	//8079=>8071
			"\xE1\xBE\x98"=>"\xE1\xBE\x90",	//8088=>8080
			"\xE1\xBE\x99"=>"\xE1\xBE\x91",	//8089=>8081
			"\xE1\xBE\x9A"=>"\xE1\xBE\x92",	//8090=>8082
			"\xE1\xBE\x9B"=>"\xE1\xBE\x93",	//8091=>8083
			"\xE1\xBE\x9C"=>"\xE1\xBE\x94",	//8092=>8084
			"\xE1\xBE\x9D"=>"\xE1\xBE\x95",	//8093=>8085
			"\xE1\xBE\x9E"=>"\xE1\xBE\x96",	//8094=>8086
			"\xE1\xBE\x9F"=>"\xE1\xBE\x97",	//8095=>8087
			"\xE1\xBE\xA8"=>"\xE1\xBE\xA0",	//8104=>8096
			"\xE1\xBE\xA9"=>"\xE1\xBE\xA1",	//8105=>8097
			"\xE1\xBE\xAA"=>"\xE1\xBE\xA2",	//8106=>8098
			"\xE1\xBE\xAB"=>"\xE1\xBE\xA3",	//8107=>8099
			"\xE1\xBE\xAC"=>"\xE1\xBE\xA4",	//8108=>8100
			"\xE1\xBE\xAD"=>"\xE1\xBE\xA5",	//8109=>8101
			"\xE1\xBE\xAE"=>"\xE1\xBE\xA6",	//8110=>8102
			"\xE1\xBE\xAF"=>"\xE1\xBE\xA7",	//8111=>8103
			"\xE1\xBE\xBC"=>"\xE1\xBE\xB3",	//8124=>8115
			"\xE1\xBF\x8C"=>"\xE1\xBF\x83",	//8140=>8131
			"\xE1\xBF\xBC"=>"\xE1\xBF\xB3",	//8188=>8179
			"\xE2\x85\xA0"=>"\xE2\x85\xB0",	//8544=>8560
			"\xE2\x85\xA1"=>"\xE2\x85\xB1",	//8545=>8561
			"\xE2\x85\xA2"=>"\xE2\x85\xB2",	//8546=>8562
			"\xE2\x85\xA3"=>"\xE2\x85\xB3",	//8547=>8563
			"\xE2\x85\xA4"=>"\xE2\x85\xB4",	//8548=>8564
			"\xE2\x85\xA5"=>"\xE2\x85\xB5",	//8549=>8565
			"\xE2\x85\xA6"=>"\xE2\x85\xB6",	//8550=>8566
			"\xE2\x85\xA7"=>"\xE2\x85\xB7",	//8551=>8567
			"\xE2\x85\xA8"=>"\xE2\x85\xB8",	//8552=>8568
			"\xE2\x85\xA9"=>"\xE2\x85\xB9",	//8553=>8569
			"\xE2\x85\xAA"=>"\xE2\x85\xBA",	//8554=>8570
			"\xE2\x85\xAB"=>"\xE2\x85\xBB",	//8555=>8571
			"\xE2\x85\xAC"=>"\xE2\x85\xBC",	//8556=>8572
			"\xE2\x85\xAD"=>"\xE2\x85\xBD",	//8557=>8573
			"\xE2\x85\xAE"=>"\xE2\x85\xBE",	//8558=>8574
			"\xE2\x85\xAF"=>"\xE2\x85\xBF",	//8559=>8575
			"\xE2\x92\xB6"=>"\xE2\x93\x90",	//9398=>9424
			"\xE2\x92\xB7"=>"\xE2\x93\x91",	//9399=>9425
			"\xE2\x92\xB8"=>"\xE2\x93\x92",	//9400=>9426
			"\xE2\x92\xB9"=>"\xE2\x93\x93",	//9401=>9427
			"\xE2\x92\xBA"=>"\xE2\x93\x94",	//9402=>9428
			"\xE2\x92\xBB"=>"\xE2\x93\x95",	//9403=>9429
			"\xE2\x92\xBC"=>"\xE2\x93\x96",	//9404=>9430
			"\xE2\x92\xBD"=>"\xE2\x93\x97",	//9405=>9431
			"\xE2\x92\xBE"=>"\xE2\x93\x98",	//9406=>9432
			"\xE2\x92\xBF"=>"\xE2\x93\x99",	//9407=>9433
			"\xE2\x93\x80"=>"\xE2\x93\x9A",	//9408=>9434
			"\xE2\x93\x81"=>"\xE2\x93\x9B",	//9409=>9435
			"\xE2\x93\x82"=>"\xE2\x93\x9C",	//9410=>9436
			"\xE2\x93\x83"=>"\xE2\x93\x9D",	//9411=>9437
			"\xE2\x93\x84"=>"\xE2\x93\x9E",	//9412=>9438
			"\xE2\x93\x85"=>"\xE2\x93\x9F",	//9413=>9439
			"\xE2\x93\x86"=>"\xE2\x93\xA0",	//9414=>9440
			"\xE2\x93\x87"=>"\xE2\x93\xA1",	//9415=>9441
			"\xE2\x93\x88"=>"\xE2\x93\xA2",	//9416=>9442
			"\xE2\x93\x89"=>"\xE2\x93\xA3",	//9417=>9443
			"\xE2\x93\x8A"=>"\xE2\x93\xA4",	//9418=>9444
			"\xE2\x93\x8B"=>"\xE2\x93\xA5",	//9419=>9445
			"\xE2\x93\x8C"=>"\xE2\x93\xA6",	//9420=>9446
			"\xE2\x93\x8D"=>"\xE2\x93\xA7",	//9421=>9447
			"\xE2\x93\x8E"=>"\xE2\x93\xA8",	//9422=>9448
			"\xE2\x93\x8F"=>"\xE2\x93\xA9",	//9423=>9449
			"\xF0\x90\xA6"=>"\xF0\x91\x8E",	//66598=>66638
			"\xF0\x90\xA7"=>"\xF0\x91\x8F"	//66599=>66639
		);

		$str = common_sanitize_string($str);
		//prefer mbstring
		if(function_exists('mb_strtolower'))
			$str = mb_strtolower($str, 'UTF-8');
		else
			$str = strtolower($str);

		//adjust some special unicode characters too
		$from = array_keys($extra); //but: for efficiency you should
		$to = array_values($extra); //pre-calculate these two arrays
		return str_replace($from, $to, $str);
	}
}

//-------------------------------------------------
// Upper Case
//
// will return multi-byte uppercase if capabale,
// otherwise regular uppercase
//
// @param str
// @return str
if(!function_exists('common_strtoupper')){
	function common_strtoupper($str=''){
		$extra = array(
			"\xC7\x86"=>"\xC7\x85",			//454=>453
			"\xC7\x89"=>"\xC7\x88",			//457=>456
			"\xC7\x8C"=>"\xC7\x8B",			//460=>459
			"\xC7\xB3"=>"\xC7\xB2",			//499=>498
			"\xCF\xB8"=>"\xCF\xB7",			//1016=>1015
			"\xCF\xB2"=>"\xCF\xB9",			//1010=>1017
			"\xCF\xBB"=>"\xCF\xBA",			//1019=>1018
			"\xE1\xBE\x80"=>"\xE1\xBE\x88",	//8064=>8072
			"\xE1\xBE\x81"=>"\xE1\xBE\x89",	//8065=>8073
			"\xE1\xBE\x82"=>"\xE1\xBE\x8A",	//8066=>8074
			"\xE1\xBE\x83"=>"\xE1\xBE\x8B",	//8067=>8075
			"\xE1\xBE\x84"=>"\xE1\xBE\x8C",	//8068=>8076
			"\xE1\xBE\x85"=>"\xE1\xBE\x8D",	//8069=>8077
			"\xE1\xBE\x86"=>"\xE1\xBE\x8E",	//8070=>8078
			"\xE1\xBE\x87"=>"\xE1\xBE\x8F",	//8071=>8079
			"\xE1\xBE\x90"=>"\xE1\xBE\x98",	//8080=>8088
			"\xE1\xBE\x91"=>"\xE1\xBE\x99",	//8081=>8089
			"\xE1\xBE\x92"=>"\xE1\xBE\x9A",	//8082=>8090
			"\xE1\xBE\x93"=>"\xE1\xBE\x9B",	//8083=>8091
			"\xE1\xBE\x94"=>"\xE1\xBE\x9C",	//8084=>8092
			"\xE1\xBE\x95"=>"\xE1\xBE\x9D",	//8085=>8093
			"\xE1\xBE\x96"=>"\xE1\xBE\x9E",	//8086=>8094
			"\xE1\xBE\x97"=>"\xE1\xBE\x9F",	//8087=>8095
			"\xE1\xBE\xA0"=>"\xE1\xBE\xA8",	//8096=>8104
			"\xE1\xBE\xA1"=>"\xE1\xBE\xA9",	//8097=>8105
			"\xE1\xBE\xA2"=>"\xE1\xBE\xAA",	//8098=>8106
			"\xE1\xBE\xA3"=>"\xE1\xBE\xAB",	//8099=>8107
			"\xE1\xBE\xA4"=>"\xE1\xBE\xAC",	//8100=>8108
			"\xE1\xBE\xA5"=>"\xE1\xBE\xAD",	//8101=>8109
			"\xE1\xBE\xA6"=>"\xE1\xBE\xAE",	//8102=>8110
			"\xE1\xBE\xA7"=>"\xE1\xBE\xAF",	//8103=>8111
			"\xE1\xBE\xB3"=>"\xE1\xBE\xBC",	//8115=>8124
			"\xE1\xBF\x83"=>"\xE1\xBF\x8C",	//8131=>8140
			"\xE1\xBF\xB3"=>"\xE1\xBF\xBC",	//8179=>8188
			"\xE2\x85\xB0"=>"\xE2\x85\xA0",	//8560=>8544
			"\xE2\x85\xB1"=>"\xE2\x85\xA1",	//8561=>8545
			"\xE2\x85\xB2"=>"\xE2\x85\xA2",	//8562=>8546
			"\xE2\x85\xB3"=>"\xE2\x85\xA3",	//8563=>8547
			"\xE2\x85\xB4"=>"\xE2\x85\xA4",	//8564=>8548
			"\xE2\x85\xB5"=>"\xE2\x85\xA5",	//8565=>8549
			"\xE2\x85\xB6"=>"\xE2\x85\xA6",	//8566=>8550
			"\xE2\x85\xB7"=>"\xE2\x85\xA7",	//8567=>8551
			"\xE2\x85\xB8"=>"\xE2\x85\xA8",	//8568=>8552
			"\xE2\x85\xB9"=>"\xE2\x85\xA9",	//8569=>8553
			"\xE2\x85\xBA"=>"\xE2\x85\xAA",	//8570=>8554
			"\xE2\x85\xBB"=>"\xE2\x85\xAB",	//8571=>8555
			"\xE2\x85\xBC"=>"\xE2\x85\xAC",	//8572=>8556
			"\xE2\x85\xBD"=>"\xE2\x85\xAD",	//8573=>8557
			"\xE2\x85\xBE"=>"\xE2\x85\xAE",	//8574=>8558
			"\xE2\x85\xBF"=>"\xE2\x85\xAF",	//8575=>8559
			"\xE2\x93\x90"=>"\xE2\x92\xB6",	//9424=>9398
			"\xE2\x93\x91"=>"\xE2\x92\xB7",	//9425=>9399
			"\xE2\x93\x92"=>"\xE2\x92\xB8",	//9426=>9400
			"\xE2\x93\x93"=>"\xE2\x92\xB9",	//9427=>9401
			"\xE2\x93\x94"=>"\xE2\x92\xBA",	//9428=>9402
			"\xE2\x93\x95"=>"\xE2\x92\xBB",	//9429=>9403
			"\xE2\x93\x96"=>"\xE2\x92\xBC",	//9430=>9404
			"\xE2\x93\x97"=>"\xE2\x92\xBD",	//9431=>9405
			"\xE2\x93\x98"=>"\xE2\x92\xBE",	//9432=>9406
			"\xE2\x93\x99"=>"\xE2\x92\xBF",	//9433=>9407
			"\xE2\x93\x9A"=>"\xE2\x93\x80",	//9434=>9408
			"\xE2\x93\x9B"=>"\xE2\x93\x81",	//9435=>9409
			"\xE2\x93\x9C"=>"\xE2\x93\x82",	//9436=>9410
			"\xE2\x93\x9D"=>"\xE2\x93\x83",	//9437=>9411
			"\xE2\x93\x9E"=>"\xE2\x93\x84",	//9438=>9412
			"\xE2\x93\x9F"=>"\xE2\x93\x85",	//9439=>9413
			"\xE2\x93\xA0"=>"\xE2\x93\x86",	//9440=>9414
			"\xE2\x93\xA1"=>"\xE2\x93\x87",	//9441=>9415
			"\xE2\x93\xA2"=>"\xE2\x93\x88",	//9442=>9416
			"\xE2\x93\xA3"=>"\xE2\x93\x89",	//9443=>9417
			"\xE2\x93\xA4"=>"\xE2\x93\x8A",	//9444=>9418
			"\xE2\x93\xA5"=>"\xE2\x93\x8B",	//9445=>9419
			"\xE2\x93\xA6"=>"\xE2\x93\x8C",	//9446=>9420
			"\xE2\x93\xA7"=>"\xE2\x93\x8D",	//9447=>9421
			"\xE2\x93\xA8"=>"\xE2\x93\x8E",	//9448=>9422
			"\xE2\x93\xA9"=>"\xE2\x93\x8F",	//9449=>9423
			"\xF0\x91\x8E"=>"\xF0\x90\xA6",	//66638=>66598
			"\xF0\x91\x8F"=>"\xF0\x90\xA7"	//66639=>66599
		);

		$str = common_sanitize_string($str);
		//prefer mbstring
		if(function_exists('mb_strtoupper'))
			$str = mb_strtoupper($str, 'UTF-8');
		else
			$str = strtoupper($str);

		//adjust some special unicode characters too
		$from = array_keys($extra); //but: for efficiency you should
		$to = array_values($extra); //pre-calculate these two arrays
		return str_replace($from, $to, $str);
	}
}

//-------------------------------------------------
// Title Case
//
// will return multi-byte title case if capabale,
// otherwise regular title case
//
// @param str
// @return str
if(!function_exists('common_ucwords')){
	function common_ucwords($str=''){
		$str = common_sanitize_string($str);
		//prefer mbstring
		if(function_exists('mb_strtoupper'))
			return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
		else
			return ucwords($str);
	}
}

//-------------------------------------------------
// Sentence Case
//
// will return multi-byte sentence case if capabale,
// otherwise regular sentence case
//
// @param str
// @return str
if(!function_exists('common_ucfirst')){
	function common_ucfirst($str=''){
		$str = common_sanitize_string($str);
		//prefer mbstring
		if(function_exists('mb_strtoupper')){
			$first = common_strtoupper(common_substr($str, 0, 1));
			$rest = common_substr($str, 1);
			return $first . $rest;
		}
		else
			return ucfirst($str);
	}
}

//--------------------------------------------------------------------- end case

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
		$amount = common_sanitize_string($amount);
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
			$first10 = common_substr($value,0,10);
			return preg_replace("/^([0-9]{3})([0-9]{3})([0-9]{4})/i", "(\\1) \\2-\\3", $first10) . (common_strlen($value) > 10 ? ' x' . common_substr($value,10) : '');
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
		$single = common_sanitize_string($single);
		$plural = common_sanitize_string($plural);
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
				$str = trim(common_substr($str, 0, $length)) . $append;
		//limit string to X words
		elseif($method === 'words' && common_substr_count($str, ' ') > $length + 1)
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
		$path = common_sanitize_string($path);
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

//-------------------------------------------------
// CSV
//
// @param data
// @param headers
// @param delimiter
// @param EOL
// @return CSV
if(!function_exists('common_to_csv')){
	function common_to_csv($data=null, $headers=null, $delimiter=',', $eol="\n"){
		$data = common_sanitize_array($data);
		$data = array_values(array_filter($data, 'is_array'));
		$headers = common_sanitize_array($headers);

		$out = array();

		//grab headers from data?
		if(!count($headers) && count($data) && common_array_type($data[0]) === 'associative'){
			$headers = array_keys($data[0]);
		}

		//output headers, if applicable
		if(count($headers)){
			$headers = array_map('common_sanitize_csv', $headers);
			$out[] = '"' . implode('"' . $delimiter . '"', $headers) . '"';
		}

		//output data
		if(count($data)){
			foreach($data AS $line){
				$line = array_map('common_sanitize_csv', $line);
				$out[] = '"' . implode('"' . $delimiter . '"', $line) . '"';
			}
		}

		return implode($eol, $out);
	}
}

//-------------------------------------------------
// XLS
//
// use Microsoft's XML format
//
// @param data
// @param headers
// @return XLS
if(!function_exists('common_to_xls')){
	function common_to_xls($data=null, $headers=null){
		$data = common_sanitize_array($data);
		$data = array_values(array_filter($data, 'is_array'));
		$headers = common_sanitize_array($headers);

		$out = array(
			'<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>',
			'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">',
			'<Styles>',
			'<Style ss:ID="s0">',
			'<NumberFormat ss:Format="True/False"/>',
			'</Style>',
			'<Style ss:ID="s1">',
			'<NumberFormat ss:Format="General Date"/>',
			'</Style>',
			'<Style ss:ID="s2">',
			'<NumberFormat ss:Format="Short Date"/>',
			'</Style>',
			'<Style ss:ID="s3">',
			'<NumberFormat ss:Format="Long Time"/>',
			'</Style>',
			'<Style ss:ID="s4">',
			'<NumberFormat ss:Format="Percent"/>',
			'</Style>',
			'<Style ss:ID="s5">',
			'<NumberFormat ss:Format="Currency"/>',
			'</Style>',
			'</Styles>',
			'<Worksheet>',
			'<Table>',
			'<Column ss:Index="1" ss:AutoFitWidth="0" ss:Width="110"/>'
		);

		//grab headers from data?
		if(!count($headers) && count($data) && common_array_type($data[0]) === 'associative'){
			$headers = array_keys($data[0]);
		}

		//output headers, if applicable
		if(count($headers)){
			$out[] = '<Row>';
			foreach($headers AS $cell){
				$cell = htmlspecialchars(strip_tags(common_sanitize_quotes(common_sanitize_whitespace($cell))), ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
				$out[] = '<Cell><Data ss:Type="String"><b>' . $cell . '</b></Data></Cell>';
			}
			$out[] = '</Row>';
		}

		//output data
		if(count($data)){
			foreach($data AS $line){
				$out[] = '<Row>';
				foreach($line AS $cell){
					//different types of data need to be treated differently
					$type = gettype($cell);
					$format = null;
					if($type === 'boolean' || $type === 'bool'){
						$type = 'Boolean';
						$format = '0';
						$cell = $cell ? 1 : 0;
					}
					elseif(is_numeric($cell)){
						$type = 'Number';
						$cell = common_sanitize_number($cell);
					}
					else {
						$cell = common_sanitize_whitespace($cell, true);
						//date and time
						if(preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $cell)){
							$type = 'DateTime';
							$format = '1';
							$cell = str_replace(' ', 'T', $cell);
						}
						//date
						elseif(preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $cell)){
							$type = 'DateTime';
							$format = '2';
							$cell .= 'T00:00:00';
						}
						//time
						elseif(preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $cell)){
							$type = 'DateTime';
							$format = '3';
							$cell = "0000-00-00T$cell";
							if(common_substr_count($cell, ':') === 2)
								$cell .= ':00';
						}
						//percent
						elseif(preg_match('/^\-?[\.\d]+%$/', $cell)){
							$type = 'Number';
							$format = '4';
							$cell = common_sanitize_number($cell) / 100;
						}
						//currency
						elseif(preg_match('/^\-?\$\-?[\.\d]+$/', $cell) || preg_match('/^\-?[\.\d]+¢$/', $cell)){
							$type = 'Number';
							$format = '5';
							$cell = common_sanitize_number($cell);
						}
						//everything else
						else {
							$type = 'String';
							$cell = htmlspecialchars(strip_tags(common_sanitize_quotes($cell)), ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
						}
					}

					$out[] = '<Cell' . (!is_null($format) ? ' ss:StyleID="s' . $format . '"' : '') . '><Data ss:Type="' . $type . '">' . $cell . '</Data></Cell>';
				}
				$out[] = '</Row>';
			}
		}

		//close it off
		$out[] = '</Table>';
		$out[] = '</Worksheet>';
		$out[] = '</Workbook>';

		return implode("\r\n", $out);
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
		$str = common_sanitize_string($str);
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

		try {
			$str = (string) $str;
		} catch(Throwable $e){
			$str = '';
		} catch(Exception $e){
			$str = '';
		}

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
		return common_ucwords(common_sanitize_whitespace(preg_replace('/[^\p{L}\p{Zs}\p{Pd}\d\'\"\,\.]/u', '', common_sanitize_quotes($str))));
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
// @param allow newlines
// @return field
if(!function_exists('common_sanitize_csv')){
	function common_sanitize_csv($str='', $newlines=false){
		$str = common_sanitize_quotes($str);
		//remove backslashed quotes, if any
		while(false !== common_strpos($str, '""'))
			$str = str_replace('""', '"', $str);
		//reapply backslashed quotes and sanitize whitespace
		return common_sanitize_whitespace(str_replace('"', '""', $str), $newlines);
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
		$str = common_utf8($str);
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
		return common_strtolower(str_replace(array("'", '"'), '', common_sanitize_quotes(sanitize_email($email))));
	}
}

//-------------------------------------------------
// Sanitize a US zip5 code
//
// @param zip
// @return zip
if(!function_exists('common_sanitize_zip5')){
	function common_sanitize_zip5($zip){
		$zip = common_sanitize_string($zip);
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
		$ip = common_sanitize_string($ip);
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
		$num = common_sanitize_string($num);
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
	function common_sanitize_array($value=null){
		try {
			$value = (array) $value;
		}
		catch(Throwable $e) {
			$value = array();
		}
		catch(Exception $e){
			$value = array();
		}

		return $value;
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
		$value = common_sanitize_string($value);
		$value = preg_replace('/[^\d]/', '', $value);

		//if this looks like a 10-digit number with the +1 on it, chop it off
		if(strlen($value) === 11 && intval(substr($value,0,1)) === 1)
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
		$domain = common_sanitize_string($domain);
		$domain = filter_var(common_sanitize_whitespace(common_strtolower($domain)), FILTER_SANITIZE_URL);

		if(!common_strlen($domain))
			return false;

		//maybe it is a full URL
		$host = parse_url($domain, PHP_URL_HOST);

		//nope...
		if(is_null($host)){
			$host = $domain;
			//maybe there's a path?
			if(false !== common_strpos($host, '/')){
				$host = explode('/', $host);
				$host = common_array_pop_top($host);
			}
			//and/or a query?
			if(false !== common_strpos($host, '?')){
				$host = explode('?', $host);
				$host = common_array_pop_top($host);
			}
			//maybe a port?
			if(false !== common_strpos($host, ':')){
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
		return (is_string($str) && (!common_strlen($str) || preg_match('//u', $str)));
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
		$value = common_sanitize_string($value);
		$first10 = common_substr($value, 0, 10);
		return preg_match("/^[2-9][0-8][0-9][2-9][0-9]{2}[0-9]{4}$/i", $first10);
	}
}

//-------------------------------------------------
// Validate credit card
//
// @param card
// @return true/false
if(!function_exists('common_validate_cc')){
	function common_validate_cc($ccnum=''){

		//digits only
		$ccnum = common_sanitize_string($ccnum);
		$ccnum = preg_replace('/[^\d]/', '', $ccnum);

		//different cards have different length requirements
		switch (substr($ccnum,0,1)){
			//Amex
			case 3:
				if(common_strlen($ccnum) !== 15 || !preg_match('/3[47]/', $ccnum)) return false;
				break;
			//Visa
			case 4:
				if(!in_array(common_strlen($ccnum), array(13,16))) return false;
				break;
			//MC
			case 5:
				if(common_strlen($ccnum) !== 16 || !preg_match('/5[1-5]/', $ccnum)) return false;
				break;
			//Disc
			case 6:
				if(common_strlen($ccnum) !== 16 || substr($ccnum, 0, 4) !== '6011') return false;
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

		if(substr($validate, -1, 1) === '0')
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