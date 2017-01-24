<?php
//---------------------------------------------------------------------
// MB Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Case

$data = array(
	'quEen BjöRk Ⅷ loVes aPplEs.',
	'THE lazY Rex ⅸ eAtS f00d.'
);

foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::strtolower', array($d), \blobfolio\common\mb::strtolower($d));
}
\blobfolio\test\cli::record('mb::strtolower', array($data), \blobfolio\common\mb::strtolower($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::strtoupper', array($d), \blobfolio\common\mb::strtoupper($d));
}
\blobfolio\test\cli::record('mb::strtoupper', array($data), \blobfolio\common\mb::strtoupper($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::ucfirst', array($d), \blobfolio\common\mb::ucfirst($d));
}
\blobfolio\test\cli::record('mb::ucfirst', array($data), \blobfolio\common\mb::ucfirst($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::ucwords', array($d), \blobfolio\common\mb::ucwords($d));
}
\blobfolio\test\cli::record('mb::ucwords', array($data), \blobfolio\common\mb::ucwords($data));



//-------------------------------------------------
// Other

$parse = 'foo=BjöRk&bar=Ⅷ loVes';
\blobfolio\common\mb::parse_str($parse, $parsed);
\blobfolio\test\cli::record('mb::parse_str', array($parse), $parsed);

\blobfolio\test\cli::record('mb::str_split', array($data[1], 5), \blobfolio\common\mb::str_split($data[1], 5));
\blobfolio\test\cli::record('mb::str_split', array($data[0]), \blobfolio\common\mb::str_split($data[0]));


foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::strlen', array($d), \blobfolio\common\mb::strlen($d));
}

foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::substr', array($d, 0, 15), \blobfolio\common\mb::substr($d, 0, 15));
}
foreach ($data as $d) {
	\blobfolio\test\cli::record('mb::substr', array($d, -15), \blobfolio\common\mb::substr($d, -15));
}

\blobfolio\test\cli::record('mb::strpos', array($data[0], 'ö'), \blobfolio\common\mb::strpos($data[0], 'ö'));
\blobfolio\test\cli::record('mb::strpos', array($data[1], 'ö'), \blobfolio\common\mb::strpos($data[1], 'ö'));

\blobfolio\test\cli::record('mb::substr_count', array($data[1], 'e'), \blobfolio\common\mb::substr_count($data[1], 'e'));

foreach (array(STR_PAD_LEFT,STR_PAD_RIGHT,STR_PAD_BOTH) as $pad) {
	foreach ($data as $d) {
		\blobfolio\test\cli::record('mb::str_pad', array($d, 50, '<>', $pad), \blobfolio\common\mb::str_pad($d, 50, '<>', $pad));
	}
}


\blobfolio\test\cli::print('MULTI-BYTE FUNCTIONS');
?>