<?php

define('BLOBCOMMON_HAS_EXT', false);
//define('BLOBCOMMON_HAS_EXT', false);

$arr1 = array('Hello', 'World', 'Order');
$arr2 = array('World');
print_r(array_diff($arr1, $arr2));
exit;

require(__DIR__ . '/../../../lib/vendor/autoload.php');

$phone = '12015550123';

var_dump(new \blobfolio\phone\phone($phone));
var_dump(\Blobfolio\Phones::parsePhone($phone));
exit;

/*
$data = array(
	'au'=>constants::STATES_AU,
	'ca'=>constants::PROVINCES,
	'countries'=>constants::COUNTRIES,
	'timezones'=>constants::TIMEZONES,
	'us'=>constants::STATES,
);
file_put_contents(__DIR__ . '/geo.json', json_encode($data));
exit;
*/




$file = file_get_contents(__DIR__ . '/sample.json');

$test = "Hey\nHow are you\rDoing?";

$one = explode("\n", preg_replace("/\\v/u", "\n", $test));
$two = preg_split('/\v/u', $test);
print_r($one);
print_r($two);
exit;

$start = microtime(true);
$one = \Blobfolio\Strings::wordwrap($file, 35, "\n");
echo "\033[2m01: \033[0m" . sprintf('%0.10f', microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$two = \Blobfolio\Strings::wordwrap2($file, 35, "\n");
echo "\033[2m02: \033[0m" . sprintf('%0.10f', microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$three = \blobfolio\common\mb::wordwrap($file, 35, "\n");
echo "\033[2m03: \033[0m" . sprintf('%0.10f', microtime(true) - $start) . " seconds\n";

var_dump($one === $two);
var_dump($two === $three);

exit;

$data = array(
	'Björk Guðmundsdóttir',
	trim(file_get_contents(__DIR__ . '/test1.txt')),
	trim(file_get_contents(__DIR__ . '/test1Latin.txt')),
	array('hello'),
);

$lib = 0;
$ext = 0;

foreach ($data as $v) {
	var_dump($v);

	$start = microtime(true);
	var_dump(\blobfolio\common\cast::string($v, true));
	//\blobfolio\common\file::scandir(__DIR__);
	$lib += (microtime(true) - $start);

	$start = microtime(true);
	var_dump(Blobfolio\Cast::toString($v, true));
	//Blobfolio\Files::scandir(__DIR__);
	$ext += (microtime(true) - $start);

	echo "\n\n";
}

echo "\033[2mLib: \033[0m" . sprintf('%0.10f', $lib) . " seconds\n";
echo "\033[2mExt: \033[0m" . sprintf('%0.10f', $ext) . " seconds\n";

var_dump(BLOBCOMMON_HAS_EXT);
