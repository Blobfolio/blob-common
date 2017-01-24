<?php
//---------------------------------------------------------------------
// Sanitize Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Sanitize Tests!

$str = 'Björk Guðmundsdóttir is a swan.';
\blobfolio\test\cli::record('sanitize::accents', array($str), \blobfolio\common\sanitize::accents($str));
\blobfolio\test\cli::record('sanitize::accents', array(array($str)), \blobfolio\common\sanitize::accents(array($str)));


$data = array('4242424242424242', '4242424242424241');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::cc', array($d), \blobfolio\common\sanitize::cc($d));
}

$data = array('US','USA','canada');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::country', array($d), \blobfolio\common\sanitize::country($d));
}
\blobfolio\test\cli::record('sanitize::country', array($data), \blobfolio\common\sanitize::country($data));

$data = array('2010', '01/01/2015', 1485211108);
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::date', array($d), \blobfolio\common\sanitize::date($d));
}
\blobfolio\test\cli::record('sanitize::date', array($data), \blobfolio\common\sanitize::date($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::datetime', array($d), \blobfolio\common\sanitize::datetime($d));
}
\blobfolio\test\cli::record('sanitize::datetime', array($data), \blobfolio\common\sanitize::datetime($data));

$data = array('apples','apple.com','http://apple.com');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::domain', array($d), \blobfolio\common\sanitize::domain($d));
}
\blobfolio\test\cli::record('sanitize::domain', array($data), \blobfolio\common\sanitize::domain($data));

$data = array('John.Doe@gmail.com', 'johndoe@localhost', 'john"doe@gmail.com');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::email', array($d), \blobfolio\common\sanitize::email($d));
}
\blobfolio\test\cli::record('sanitize::email', array($data), \blobfolio\common\sanitize::email($data));

$data = array('jpeg','.jpg',' *gif');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::file_extension', array($d), \blobfolio\common\sanitize::file_extension($d));
}
\blobfolio\test\cli::record('sanitize::file_extension', array($data), \blobfolio\common\sanitize::file_extension($data));

$str = '<img src="foobar.jpg">';
\blobfolio\test\cli::record('sanitize::html', array($str), \blobfolio\common\sanitize::html($str));
\blobfolio\test\cli::record('sanitize::html', array(array($str)), \blobfolio\common\sanitize::html(array($str)));

$data = array('50.116.18.174','127.0.0.1','2600:3c00::f03c:91ff:feae:0ff2');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::ip', array($d), \blobfolio\common\sanitize::ip($d));
}
\blobfolio\test\cli::record('sanitize::ip', array($data), \blobfolio\common\sanitize::ip($data));

$str = 'Apples to "apples", \'Dust to Dust\'';
\blobfolio\test\cli::record('sanitize::js', array($str, '"'), \blobfolio\common\sanitize::js($str, '"'));
\blobfolio\test\cli::record('sanitize::js', array($str, "'"), \blobfolio\common\sanitize::js($str, "'"));

$data = array('Application/Octet-Stream','image / gif');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::mime', array($d), \blobfolio\common\sanitize::mime($d));
}
\blobfolio\test\cli::record('sanitize::mime', array($data), \blobfolio\common\sanitize::mime($data));

$data = array('Björk Guðmundsdóttir', 'Henry!' . "\n123");
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::name', array($d), \blobfolio\common\sanitize::name($d));
}
\blobfolio\test\cli::record('sanitize::name', array($data), \blobfolio\common\sanitize::name($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::password', array($d), \blobfolio\common\sanitize::password($d));
}
\blobfolio\test\cli::record('sanitize::password', array($data), \blobfolio\common\sanitize::password($data));

$data = array('alberta','on');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::province', array($d), \blobfolio\common\sanitize::province($d));
}
\blobfolio\test\cli::record('sanitize::province', array($data), \blobfolio\common\sanitize::province($data));

$str = '“T’was the night before Christmas...”';
\blobfolio\test\cli::record('sanitize::quotes', array($str), \blobfolio\common\sanitize::quotes($str));

$data = array('Puerto Rico', 'Tx');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::state', array($d), \blobfolio\common\sanitize::state($d));
}
\blobfolio\test\cli::record('sanitize::state', array($data), \blobfolio\common\sanitize::state($data));

$data = array('foo','america/los_angeles');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::timezone', array($d), \blobfolio\common\sanitize::timezone($d));
}
\blobfolio\test\cli::record('sanitize::timezone', array($data), \blobfolio\common\sanitize::timezone($data));

\blobfolio\test\cli::record('sanitize::to_range', array(5,1,10), \blobfolio\common\sanitize::to_range(5, 1, 10));
\blobfolio\test\cli::record('sanitize::to_range', array('2014-01-01', '2015-01-01', '2015-01-15'), \blobfolio\common\sanitize::to_range('2014-01-01', '2015-01-01', '2015-01-15'));

$data = array('https://google.com','//google.com','@#google.com');
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::url', array($d), \blobfolio\common\sanitize::url($d));
}
\blobfolio\test\cli::record('sanitize::url', array($data), \blobfolio\common\sanitize::url($data));

$str = "Excellent!  Come one,\nCome all!\n\n\nBye.";
\blobfolio\test\cli::record('sanitize::whitespace', array($str), \blobfolio\common\sanitize::whitespace($str));
\blobfolio\test\cli::record('sanitize::whitespace', array($str, 2), \blobfolio\common\sanitize::whitespace($str, 2));
\blobfolio\test\cli::record('sanitize::whitespace_multiline', array($str, 2), \blobfolio\common\sanitize::whitespace_multiline($str, 2));

$data = array(33, '89123+2333', 0);
foreach ($data as $d) {
	\blobfolio\test\cli::record('sanitize::zip5', array($d), \blobfolio\common\sanitize::zip5($d));
}
\blobfolio\test\cli::record('sanitize::zip5', array($data), \blobfolio\common\sanitize::zip5($data));


\blobfolio\test\cli::print('SANITIZING FUNCTIONS');
?>