<?php

require(__DIR__ . '/../../lib/vendor/autoload.php');


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
	$lib += (microtime(true) - $start);

	$start = microtime(true);
	var_dump(Blobfolio\Cast::toString($v, true));
	$ext += (microtime(true) - $start);

	echo "\n\n";
}

echo "\033[2mLib: \033[0m" . sprintf('%0.10f', $lib) . " seconds\n";
echo "\033[2mExt: \033[0m" . sprintf('%0.10f', $ext) . " seconds\n";

var_dump(BLOBCOMMON_HAS_EXT);
