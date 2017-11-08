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

define('BIN_DIR', dirname(dirname(dirname(__FILE__))) . '/wp/lib/');

// Generate the index.
$index = file_get_contents(__DIR__ . '/test.php.template');

// Build the phar!
if (file_exists(BIN_DIR . 'test.phar')) {
	unlink(BIN_DIR . 'test.phar');
}
$phar = new Phar(
	BIN_DIR . 'test.phar',
	0
);
$phar->startBuffering();
$phar->addFromString('dummy.php', "<?php\n// Comment.");
$phar->setStub($index);
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
