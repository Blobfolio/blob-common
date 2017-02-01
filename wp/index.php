<?php
/*
Plugin Name: Tutan Common
Plugin URI: https://github.com/Blobfolio/blob-common
Description: Functions to assist common theme operations.
Version: 7.0.4
Author: Blobfolio, LLC
Author URI: https://blobfolio.com/
License: WTFPL
License URI: http://www.wtfpl.net/

	Copyright © 2017  Blobfolio, LLC  (email: hello@blobfolio.com)

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	Version 2, December 2004

	Copyright (C) 2017 Sam Hocevar <sam@hocevar.net>

	Everyone is permitted to copy and distribute verbatim or modified
	copies of this license document, and changing it is allowed as long
	as the name is changed.

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

	0. You just DO WHAT THE FUCK YOU WANT TO.
*/

//this must be called through WordPress
if (!defined('ABSPATH')) {
	exit;
}



//the root path to the plugin
define('BLOB_COMMON_ROOT', dirname(__FILE__));
@require_once(BLOB_COMMON_ROOT . '/lib/vendor/autoload.php');		//autoload




//---------------------------------------------------------------------
// SELF AWARENESS/UPDATES
//---------------------------------------------------------------------
// The plugin is not hosted in the WP plugin repository, so we need
// some helpers to help it help itself. :)
//
// To reduce overhead, update checks are throttled to once per hour.

//-------------------------------------------------
// Get (Installed) Plugin Info
//
// @param key (optional)
// @return info (all) or tidbit
function blobcommon_get_info($key = null) {
	static $info;

	if (is_null($info)) {
		require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');
		$info = get_plugin_data(__FILE__);
	}

	//return what corresponds to $key
	if (!is_null($key)) {
		return array_key_exists($key, $info) ? $info[$key] : false;
	}

	//return the whole thing
	return $info;
}

//-------------------------------------------------
// Get Remote Branch
//
// current and legacy versions have different
// operational requirements, so we need to point
// installs to the correct file
//
// @param n/a
// @return JSON URL
function blobcommon_get_release_branch() {
	$branch = 'current';

	//we won't downgrade if already on the current branch
	$current = blobcommon_get_installed_version();
	if (version_compare($current, '7.0.0') < 0) {
		//if PHP is old, legacy it is!
		if (version_compare(PHP_VERSION, '7.0.0') < 0) {
			$branch = 'legacy';
		}
	}

	if ($branch === 'current') {
		return 'https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/current.json';
	}
	else {
		return 'https://raw.githubusercontent.com/Blobfolio/blob-common/1.5/release/plugin.json';
	}
}

//-------------------------------------------------
// Get (Remote) Plugin Info
//
// @param key (optional)
// @return info (all) or tidbit
function blobcommon_get_remote_info($key = null) {
	static $info;
	$transient_key = 'blobcommon_remote_info';

	if (is_null($info) && false === $info = get_transient($transient_key)) {
		$info = array();
		$data = wp_remote_get(blobcommon_get_release_branch());
		if (is_array($data) && array_key_exists('body', $data)) {
			try {
				$response = json_decode($data['body'], true);
				if (is_array($response)) {
					foreach ($response as $k=>$v) {
						$info[$k] = $v;
					}

					set_transient($transient_key, $info, 3600);
				}
			} catch (Exception $e) {
				$info = array();
			}
		}
	}

	//return what corresponds to $key
	if (!is_null($key)) {
		return array_key_exists($key, $info) ? $info[$key] : false;
	}

	//return the whole thing
	return $info;
}

//-------------------------------------------------
// Get Installed Version
//
// @param n/a
// @return version
function blobcommon_get_installed_version() {
	static $version;

	if (is_null($version)) {
		$version = (string) blobcommon_get_info('Version');
	}

	return $version;
}

//-------------------------------------------------
// Get Latest Version
//
// @param n/a
// @return version
function blobcommon_get_latest_version() {
	static $version;

	if (is_null($version)) {
		$version = (string) blobcommon_get_remote_info('Version');
	}

	return $version;
}

//-------------------------------------------------
// Check For Updates
//
// @param $options
// @return $options
function blobcommon_check_update($option, $cache=true) {

	//make sure arguments make sense
	if (!is_object($option)) {
		return $option;
	}

	//local and remote versions
	$installed = blobcommon_get_installed_version();
	$remote = blobcommon_get_latest_version();

	//bad data and/or match, nothing to do!
	if ($remote === false || $installed === false || $remote <= $installed) {
		return $option;
	}

	//set up the entry
	$path = 'blob-common/index.php';
	if (!array_key_exists($path, $option->response)) {
		$option->response[$path] = new stdClass();
	}

	$option->response[$path]->url = blobcommon_get_info('PluginURI');
	$option->response[$path]->slug = 'blob-common';
	$option->response[$path]->plugin = $path;
	$option->response[$path]->package = blobcommon_get_remote_info('DownloadURI');
	$option->response[$path]->new_version = $remote;
	$option->response[$path]->id = 0;

	//done
	return $option;
}
add_filter('transient_update_plugins', 'blobcommon_check_update');
add_filter('site_transient_update_plugins', 'blobcommon_check_update');

//--------------------------------------------------------------------- end updates

?>