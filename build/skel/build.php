<?php
/**
 * Build Helper - Index.php
 *
 * Composer is useful for pulling in and parsing the various
 * dependencies, but we don't want to actually use its
 * autoloader because it will blow up if the phar is included
 * in more than one plugin in a single project.
 *
 * So, let's parse the map and build our own autoloader!
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

define('SRC_DIR', dirname(dirname(__FILE__)) . '/src/vendor/');
define('OUT_DIR', dirname(dirname(__FILE__)) . '/out/');
define('BIN_DIR', dirname(dirname(dirname(__FILE__))) . '/bin/');

// Cut out the $classMap.
$raw = file_get_contents(SRC_DIR . 'composer/autoload_static.php');
if (false === ($start = strpos($raw, '$classMap ='))) {
	exit;
}
if (false === ($end = strpos($raw, ');', $start))) {
	exit;
}

$map = substr($raw, $start, ($end - $start + 2));
$map = str_replace("__DIR__ . '/..' . ", '', $map);
eval($map);

// Move files to our output directory and generate
// code version of $classMap.
$out = array();
foreach ($classMap as $k=>$v) {
	$subdir = explode('/', ltrim($v, '/'));
	$subdir = $subdir[0];
	if (file_exists(SRC_DIR . $subdir)) {
		rename(SRC_DIR . $subdir, OUT_DIR . "lib/$subdir");
	}

	$classMap[$k] = '/lib/' . ltrim($v, '/');
	$out[] = "'" . addslashes($k) . "'=>'" . addslashes(ltrim($classMap[$k], '/')) . "'";
}

// Generate the index.
$index = file_get_contents(__DIR__ . '/index.php');
$index = str_replace('CLASSMAP', "\n\t\t\t" . implode(",\n\t\t\t", $out) . "\n\t\t", $index);
file_put_contents(OUT_DIR . 'index.php', $index);

// Build the phar!
if (file_exists(BIN_DIR . 'blob-common.phar')) {
	unlink(BIN_DIR . 'blob-common.phar');
}
$phar = new Phar(
	BIN_DIR . 'blob-common.phar',
	FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::SKIP_DOTS
);
$phar->startBuffering();
$stub = '<?php
if (!defined("BLOBCOMMON_AUTOLOADER")) {
	define("BLOBCOMMON_AUTOLOADER", true);
	require "phar://" . __FILE__ . "/index.php";
}
__HALT_COMPILER();
';
$phar->setStub($stub);
$phar->buildFromDirectory(OUT_DIR);
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
