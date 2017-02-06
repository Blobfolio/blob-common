<?php
//---------------------------------------------------------------------
// Cast Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Type Casting

$data = array(
	5,
	5.6,
	'5',
	'$5.00',
	true,
	'5%',
	'off',
	array(1,2,3)
);

//array
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::array', array($v), \blobfolio\common\cast::array($v));
}
\blobfolio\test\cli::record('cast::array', array($data), \blobfolio\common\cast::array($data));

//bool
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::bool', array($v), \blobfolio\common\cast::bool($v));
}
\blobfolio\test\cli::record('cast::bool', array($data), \blobfolio\common\cast::bool($data));
\blobfolio\test\cli::record('cast::bool', array($data, true), \blobfolio\common\cast::bool($data, true));

//float
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::float', array($v), \blobfolio\common\cast::float($v));
}
\blobfolio\test\cli::record('cast::float', array($data), \blobfolio\common\cast::float($data));
\blobfolio\test\cli::record('cast::float', array($data, true), \blobfolio\common\cast::float($data, true));

//integers
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::int', array($v), \blobfolio\common\cast::int($v));
}
\blobfolio\test\cli::record('cast::int', array($data), \blobfolio\common\cast::int($data));
\blobfolio\test\cli::record('cast::int', array($data, true), \blobfolio\common\cast::int($data, true));

//number
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::number', array($v), \blobfolio\common\cast::number($v));
}
\blobfolio\test\cli::record('cast::number', array($data), \blobfolio\common\cast::number($data));
\blobfolio\test\cli::record('cast::number', array($data, true), \blobfolio\common\cast::number($data, true));

//string
foreach ($data as $v) {
	\blobfolio\test\cli::record('cast::string', array($v), \blobfolio\common\cast::string($v));
}
\blobfolio\test\cli::record('cast::string', array($data), \blobfolio\common\cast::string($data));
\blobfolio\test\cli::record('cast::string', array($data, true), \blobfolio\common\cast::string($data, true));



//-------------------------------------------------
// To Type

\blobfolio\test\cli::record('cast::to_type', array($data, 'bool'), \blobfolio\common\cast::to_type($data, 'bool'));
\blobfolio\test\cli::record('cast::to_type', array($data, 'string'), \blobfolio\common\cast::to_type($data, 'string'));
\blobfolio\test\cli::record('cast::to_type', array($data, 'string', true), \blobfolio\common\cast::to_type($data, 'string', true));



//-------------------------------------------------
// Array Type

$data = array(
	'fruit'=>'apples'
);
\blobfolio\test\cli::record('cast::array_type', array($data), \blobfolio\common\cast::array_type($data));
$data = array();
\blobfolio\test\cli::record('cast::array_type', array($data), \blobfolio\common\cast::array_type($data));
$data = array('apples','oranges','bananas');
\blobfolio\test\cli::record('cast::array_type', array($data), \blobfolio\common\cast::array_type($data));



\blobfolio\test\cli::print('CAST FUNCTIONS');
?>