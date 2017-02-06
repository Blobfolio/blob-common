<?php
//---------------------------------------------------------------------
// CSV Test
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');

$data = array(
	array(1, 'Apple', '$5'),
	array(1, 'Banana', '$2')
);
$headers = array('Quantity','Item','Price');

echo \blobfolio\common\format::to_xls($data, $headers);

?>