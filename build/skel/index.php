<?php
/**
 * Index
 *
 * This loads the Phar dependencies.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

if (defined('BLOBCOMMON_AUTOLOADER')) {
	return;
}

define('BLOBCOMMON_AUTOLOADER', true);

$blobcommon_phar = md5(__FILE__ . '/blob-common.phar') . '.phar';
phar::mapPhar($blobcommon_phar);

spl_autoload_register(
	function($class) use($blobcommon_phar) {
		$map = array(CLASSMAP);
		if ($class && isset($map[$class])) {
			require($map[$class]);
			return true;
		}

		return false;
	},
	true
);

__halt_compiler();
