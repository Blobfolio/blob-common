<?php
/*
Plugin Name: Tutan Common
Plugin URI: https://blobfolio.com/repo/blob-common/
Description: Functions to assist common theme operations.
Version: 1.0.8
Author: Blobfolio, LLC
Author URI: https://blobfolio.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Copyright © 2016  Blobfolio, LLC  (email: hello@blobfolio.com)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/



//the helper functions that are the purpose of this plugin are stored externally
@require_once(dirname(__FILE__) . '/functions-common.php');



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

	if(is_null($info))
	{
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

	if(is_null($info))
	{
		$info = array();
		if(false !== ($url = blobcommon_get_info('PluginURI')))
		{
			$data = wp_remote_get($url);
			if(is_array($data) && array_key_exists('body', $data))
			{
				try {
					$response = json_decode($data['body'], true);
					foreach($response AS $k=>$v)
						$info[$k] = $v;
				}
				catch(Exception $e) { }
			}
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