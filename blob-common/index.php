<?php
/*
Plugin Name: Tutan Common
Plugin URI: https://github.com/Blobfolio/blob-common
Description: Functions to assist common theme operations.
Version: 1.2.0
Author: Blobfolio, LLC
Author URI: https://blobfolio.com/
License: WTFPL
License URI: http://www.wtfpl.net/

	Copyright © 2016  Blobfolio, LLC  (email: hello@blobfolio.com)

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	Version 2, December 2004

	Copyright (C) 2016 Sam Hocevar <sam@hocevar.net>

	Everyone is permitted to copy and distribute verbatim or modified
	copies of this license document, and changing it is allowed as long
	as the name is changed.

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

	0. You just DO WHAT THE FUCK YOU WANT TO.
*/



//most helper functions are here
@require_once(dirname(__FILE__) . '/functions-common.php');

//date, time, space functions
@require_once(dirname(__FILE__) . '/functions-localities.php');

//JIT image thumbnails
if(defined('WP_JIT_IMAGES') && WP_JIT_IMAGES)
	@require_once(dirname(__FILE__) . '/functions-jit-images.php');

//WebP images
if(defined('WP_WEBP_IMAGES') && WP_WEBP_IMAGES)
	@require_once(dirname(__FILE__) . '/functions-webp.php');



//---------------------------------------------------------------------
// SELF AWARENESS / UPDATES
//---------------------------------------------------------------------
// The plugin is not hosted in the WP plugin repository, so we need
// some helpers to help it help itself. :)

//-------------------------------------------------
// Get Plugin Info
//
// @param key (optional)
// @return info (all) or tidbit
function blobcommon_get_info($key = null){
	static $info;

	if(is_null($info)){
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		$info = get_plugin_data(__FILE__);
	}

	//return what corresponds to $key
	if(!is_null($key))
		return array_key_exists($key, $info) ? $info[$key] : false;

	//return the whole thing
	return $info;
}

//-------------------------------------------------
// Get Remote Info
//
// @param key (optional)
// @return info (all) or tidbit
function blobcommon_get_remote_info($key = null){
	static $info;
	$transient_key = 'blobcommon_remote_info';

	if(is_null($info) && false === $info = get_transient($transient_key)){
		$info = array();
		$data = wp_remote_get('https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/plugin.json');
		if(is_array($data) && array_key_exists('body', $data)){
			try {
				$response = json_decode($data['body'], true);
				if(is_array($response)){
					foreach($response AS $k=>$v)
						$info[$k] = $v;

					set_transient($transient_key, $info, 3600);
				}
			}
			catch(Exception $e){ }
		}
	}

	//return what corresponds to $key
	if(!is_null($key))
		return array_key_exists($key, $info) ? $info[$key] : false;

	//return the whole thing
	return $info;
}

//-------------------------------------------------
// Get Installed Version
//
// @param n/a
// @return version
function blobcommon_get_installed_version(){
	static $version;

	if(is_null($version))
		$version = (string) blobcommon_get_info('Version');

	return $version;
}

//-------------------------------------------------
// Get Latest Version
//
// @param n/a
// @return version
function blobcommon_get_latest_version(){
	static $version;

	if(is_null($version))
		$version = (string) blobcommon_get_remote_info('Version');

	return $version;
}

//-------------------------------------------------
// Check Update
//
// @param $options
// @return $options
function blobcommon_check_update($option, $cache=true){

	//make sure arguments make sense
	if(!is_object($option))
		return $option;

	//local and remote versions
	$installed = blobcommon_get_installed_version();
	$remote = blobcommon_get_latest_version();

	//bad data and/or match, nothing to do!
	if($remote === false || $installed === false || $remote <= $installed)
		return $option;

	//set up the entry
	$path = 'blob-common/index.php';
	if(!array_key_exists($path, $option->response))
		$option->response[$path] = new stdClass();

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