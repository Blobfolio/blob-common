<?php
/**
 * Index
 *
 * This loads the Phar dependencies.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */


/**
 * Autoloader
 *
 * @param string $class Class name.
 * @return void Nothing.
 */
spl_autoload_register(
	function($class) {
		$map = array(CLASSMAP);
		if ($class && isset($map[$class])) {
			require($map[$class]);
			return true;
		}

		return false;
	},
	true
);

