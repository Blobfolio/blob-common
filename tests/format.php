<?php
//---------------------------------------------------------------------
// Format Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Array to Indexed

$data = array(
	'cat'=>'Oscar',
	'dog'=>'Jasper'
);

\blobfolio\test\cli::record('format::array_to_indexed', array($data), \blobfolio\common\format::array_to_indexed($data));



//-------------------------------------------------
// CIDR to Range

$data = array(
	'50.116.18.174',
	'50.116.18.174/24',
	'2600:3c00::f03c:91ff:feae:0ff2/64'
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::cidr_to_range', array($d), \blobfolio\common\format::cidr_to_range($d));
}



//-------------------------------------------------
// Excerpt

$str = 'Hey Good Lookin\'';
\blobfolio\test\cli::record('format::excerpt', array($str), \blobfolio\common\format::excerpt($str));
\blobfolio\test\cli::record('format::excerpt', array($str, array('length'=>5,'unit'=>'char')), \blobfolio\common\format::excerpt($str, array('length'=>5,'unit'=>'char')));
\blobfolio\test\cli::record('format::excerpt', array($str, array('length'=>2,'unit'=>'word')), \blobfolio\common\format::excerpt($str, array('length'=>2,'unit'=>'word')));



//-------------------------------------------------
// Inflect

$data = array(
	array(1, '%d book', '%d books'),
	array(2, '%d book', '%d books'),
	array(1.5, '%d book', '%f books')
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::inflect', $d, \blobfolio\common\format::inflect($d[0], $d[1], $d[2]));
}



//-------------------------------------------------
// IP to Number

$data = array(
	'50.116.18.174',
	'2600:3c00::f03c:91ff:feae:0ff2'
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::ip_to_number', array($d), \blobfolio\common\format::ip_to_number($d));
}



//-------------------------------------------------
// Money

$data = array(
	'1000',
	2.666,
	.75,
	-60
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::money', array($d, true, ','), \blobfolio\common\format::money($d, true, ','));
}
\blobfolio\test\cli::record('format::money', array($data, true, ','), \blobfolio\common\format::money($data, true, ','));



//-------------------------------------------------
// Phone

$data = array(
	'1234567890',
	'(800) 331-0500'
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::phone', array($d, false), \blobfolio\common\format::phone($d, false));
}
\blobfolio\test\cli::record('format::phone', array($data, false), \blobfolio\common\format::phone($data, false));



//-------------------------------------------------
// To Time Zone

$data = array(
	array('2015-01-01 10:00:00', 'UTC', 'America/Los_Angeles'),
	array('2015-01-01 10:00:00', 'UTC', 'America/Chicago'),
);
foreach ($data as $d) {
	\blobfolio\test\cli::record('format::to_timezone', $d, \blobfolio\common\format::to_timezone($d[0], $d[1], $d[2]));
}


\blobfolio\test\cli::print('FORMAT FUNCTIONS');
?>