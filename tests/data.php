<?php
//---------------------------------------------------------------------
// Data Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Array Compare

$arr1 = array('apples','bananas','oranges');
$arr2 = array('oranges');
\blobfolio\test\cli::record('data::array_compare', array($arr1, $arr2), \blobfolio\common\data::array_compare($arr1, $arr2));

$arr2 = array('bananas','apples','oranges');
\blobfolio\test\cli::record('data::array_compare', array($arr1, $arr2), \blobfolio\common\data::array_compare($arr1, $arr2));

$arr1 = array('fruit'=>'apples','vegetable'=>'carrots');
$arr2 = array('vegetable'=>'carrots', 'fruit'=>'apples');
\blobfolio\test\cli::record('data::array_compare', array($arr1, $arr2), \blobfolio\common\data::array_compare($arr1, $arr2));



//-----------------------------------------------
// Array Map Recursive

$data = array(
	'Hello',
	array(
		'World',
		'Cat',
		'dog'=>array('Animal')
	)
);
\blobfolio\test\cli::record('data::array_map_recursive', array('strtoupper', $data), \blobfolio\common\data::array_map_recursive('strtoupper', $data));



//-----------------------------------------------
// Array Pop

\blobfolio\test\cli::record('data::array_pop', array($arr1), \blobfolio\common\data::array_pop($arr1));
\blobfolio\test\cli::record('data::array_pop_top', array($arr1), \blobfolio\common\data::array_pop_top($arr1));



//-----------------------------------------------
// CC Fields

\blobfolio\test\cli::record('data::cc_exp_months', array(), \blobfolio\common\data::cc_exp_months());
\blobfolio\test\cli::record('data::cc_exp_months', array('F'), \blobfolio\common\data::cc_exp_months('F'));
\blobfolio\test\cli::record('data::cc_exp_years', array(), \blobfolio\common\data::cc_exp_years());
\blobfolio\test\cli::record('data::cc_exp_years', array(3), \blobfolio\common\data::cc_exp_years(3));



//-------------------------------------------------
// Datediff

$data = array('2015-01-01 10:00:00', '2015-02-01');
\blobfolio\test\cli::record('data::datediff', $data, \blobfolio\common\data::datediff($data[0], $data[1]));



//-------------------------------------------------
// In Range

$data = array(
	array('2015-02-01', '2015-01-01', '2015-03-01'),
	array(5.5, null, 10),
	array(3, 1, 2)
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('data::in_range', $d, \blobfolio\common\data::in_range($d[0], $d[1], $d[2]));
}



//-------------------------------------------------
// Is UTF-8

$data = array(
	'yes',
	30,
	iconv('UTF-8', 'ISO8601', 'Apples')
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('data::is_utf8', array($d), \blobfolio\common\data::is_utf8($d));
}



//-------------------------------------------------
// JSON

$data = array(
	'{"fruit":"apple","amount":"$5.65"}',
	array(
		'fruit'=>'banana',
		'animal'=>'dog',
		'amount'=>5.5
	)
);
\blobfolio\test\cli::record('data::json_decode_array', $data, \blobfolio\common\data::json_decode_array($data[0], $data[1]));



//-------------------------------------------------
// Length in Range

$str = 'Apples';
\blobfolio\test\cli::record('data::length_in_range', array($str, 0, 6), \blobfolio\common\data::length_in_range($str, 0, 6));




//-------------------------------------------------
// Parse Args

$arr1 = array(
	'animal'=>'dog',
	'fruit'=>'apple',
	'amount'=>array(
		'price'=>'66'
	)
);
$arr2 = array(
	'animal'=>array(),
	'fruit'=>'banana',
	'amount'=>array(
		'price'=>5,
		'sale'=>0.0
	)
);
\blobfolio\test\cli::record('data::parse_args', array($arr1, $arr2, false, true), \blobfolio\common\data::parse_args($arr1, $arr2, false, true));
\blobfolio\test\cli::record('data::parse_args', array($arr1, $arr2, true, false), \blobfolio\common\data::parse_args($arr1, $arr2, true, false));
\blobfolio\test\cli::record('data::parse_args', array($arr1, $arr2), \blobfolio\common\data::parse_args($arr1, $arr2));



//-------------------------------------------------
// Random Int

$data = array(
	array(0, 10),
	array(0, 10),
	array(0, 10)
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('data::random_int', $d, \blobfolio\common\data::random_int($d[0], $d[1]));
}

for ($x = 0; $x < 2; $x++) {
	\blobfolio\test\cli::record('data::random_string', array(5), \blobfolio\common\data::random_string(5));
}
$soup = str_split('lmnopqrst');
\blobfolio\test\cli::record('data::random_string', array(5, $soup), \blobfolio\common\data::random_string(5, $soup));



\blobfolio\test\cli::print('DATA/GENERAL FUNCTIONS');
?>