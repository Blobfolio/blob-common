<?php
//---------------------------------------------------------------------
// Tool Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
@require_once(dirname(__FILE__) . '/test.php');



$data = array(
	'pet'=>'animal',
	'off'
);

\blobfolio\test\cli::record('common_array_type', array($data), common_array_type($data));

$arr2 = array('foobar');
\blobfolio\test\cli::record('common_array_compare', array($data, $arr2), common_array_compare($data, $arr2));
\blobfolio\test\cli::record('common_array_compare', array($data, $data), common_array_compare($data, $data));

\blobfolio\test\cli::record('common_iin_array', array('off', $data), common_iin_array('off', $data));
\blobfolio\test\cli::record('common_iin_array', array('OFF', $data), common_iin_array('OFF', $data));

\blobfolio\test\cli::record('common_iarray_key_exists', array('OFF', $data), common_iarray_key_exists('PET', $data));

$str = 'The Man Of The Hour';
\blobfolio\test\cli::record('common_isubstr_count', array($str, 'E'), common_isubstr_count($str, 'E'));

\blobfolio\test\cli::record('common_strlen', array($str), common_strlen($str));

\blobfolio\test\cli::record('common_strpos', array($str, 'M'), common_strpos($str, 'M'));

\blobfolio\test\cli::record('common_to_char_array', array($str), common_to_char_array($str));

\blobfolio\test\cli::record('common_random_int', array(0, 10), common_random_int(0, 10));
\blobfolio\test\cli::record('common_random_int', array(0, 10), common_random_int(0, 10));
\blobfolio\test\cli::record('common_random_int', array(0, 10), common_random_int(0, 10));

\blobfolio\test\cli::record('common_array_pop', array($data), common_array_pop($data));
\blobfolio\test\cli::record('common_array_pop_top', array($data), common_array_pop_top($data));

$arr2 = array(
	'pet'=>array(),
	'fruit'=>'dog'
);
\blobfolio\test\cli::record('common_parse_args', array($data, $arr2), common_parse_args($data, $arr2));
\blobfolio\test\cli::record('common_parse_args', array($data, $arr2, true), common_parse_args($data, $arr2, true));

$str = '{"pet":"dog"}';
\blobfolio\test\cli::record('common_parse_json_args', array($str, $arr2), common_parse_json_args($str, $arr2));

\blobfolio\test\cli::record('common_generate_random_string', array(), common_generate_random_string());
\blobfolio\test\cli::record('common_generate_random_string', array(), common_generate_random_string());

\blobfolio\test\cli::record('common_get_cc_exp_years', array(), common_get_cc_exp_years());
\blobfolio\test\cli::record('common_get_cc_exp_months', array(), common_get_cc_exp_months());

\blobfolio\test\cli::print('TOOL FUNCTIONS');
?>