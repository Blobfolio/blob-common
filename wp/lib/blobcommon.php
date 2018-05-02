<?php
/**
 * Tutan Common: blob-common Updater
 *
 * This handles updates to the blob-common library, which occur
 * independently of the plugin, and also the plugin itself.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\wp\common;

class blobcommon {
	const OPTION_NAME = 'blobcommon_library';
	const FILEPATH = BLOBCOMMON_ROOT . '/lib/blob-common.phar';
	const REMOTE = 'https://raw.githubusercontent.com/Blobfolio/blob-common/master/bin/';
	const PLUGIN_PATH = 'blob-common/index.php';

	protected static $_init = false;
	protected static $_plugin;

	/**
	 * Init
	 *
	 * @return bool True.
	 */
	public static function init() {
		// Only need to run once.
		if (static::$_init) {
			return true;
		}
		static::$_init = true;

		// Has a new library version been downloaded? If so, we need to finish
		// the update before loading the library or Phar will complain.
		if (@file_exists(static::FILEPATH . '.new')) {
			if (false !== @rename(static::FILEPATH . '.new', static::FILEPATH)) {
				@unlink(static::FILEPATH . '.new');
			}
		}

		// Now we need to load the library.
		@require_once(static::FILEPATH);

		// No sense calling this a bunch.
		$class = get_called_class();

		// Library updates.
		add_action('cron_' . static::OPTION_NAME, array($class, 'upgrade'));
		if (false === ($timestamp = wp_next_scheduled('cron_' . static::OPTION_NAME))) {
			wp_schedule_event(time(), 'daily', 'cron_' . static::OPTION_NAME);
		}
		register_deactivation_hook(BLOBCOMMON_INDEX, array($class, 'unschedule'));

		// Plugin updates.
		if (BLOBCOMMON_MUST_USE) {
			// Must-Use doesn't have normal version management, but
			// we can add filters for Musty in case someone's using
			// that.
			add_filter('musty_download_version_' . static::PLUGIN_PATH, array($class, 'musty_download_version'));
			add_filter('musty_download_uri_' . static::PLUGIN_PATH, array($class, 'musty_download_uri'));
		}
		else {
			// Normal plugins are... more normal.
			add_filter('transient_update_plugins', array($class, 'update_plugins'));
			add_filter('site_transient_update_plugins', array($class, 'update_plugins'));
		}

		return true;
	}



	// ------------------------------------------------------------------
	// Library
	// ------------------------------------------------------------------

	/**
	 * Unschedule Library Updates
	 *
	 * @return void Nothing.
	 */
	public static function unschedule() {
		if (false !== ($timestamp = wp_next_scheduled('cron_' . static::OPTION_NAME))) {
			wp_unschedule_event($timestamp, 'cron_' . static::OPTION_NAME);
		}
	}

	/**
	 * Check for Library Updates
	 *
	 * @return bool True/false.
	 */
	public static function upgrade() {
		$current = get_option(static::OPTION_NAME, '');
		$response = wp_remote_get(static::REMOTE . 'version.json');
		if (200 === wp_remote_retrieve_response_code($response)) {
			try {
				$data = json_decode(wp_remote_retrieve_body($response), true);
				if (
					!is_array($data) ||
					!isset($data['date']) ||
					!isset($data['checksum'])
				) {
					return false;
				}

				$date = \blobfolio\common\sanitize::datetime($data['date']);
				$checksum = $data['checksum'];
			} catch (Throwable $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
		else {
			return false;
		}

		// No update needed.
		if ($current >= $date) {
			return true;
		}

		// Update if we can.
		$response = wp_remote_get(static::REMOTE . 'blob-common.phar');
		if (200 === wp_remote_retrieve_response_code($response)) {
			try {
				$data = wp_remote_retrieve_body($response);
				if (strlen($data)) {
					try {
						if (false !== @file_put_contents(static::FILEPATH . '.tmp', $data)) {
							if (
								(@md5_file(static::FILEPATH . '.tmp') === $checksum) &&
								(false !== @rename(static::FILEPATH . '.tmp', static::FILEPATH . '.new'))
							) {
								update_option(static::OPTION_NAME, $date);
								@chmod(static::FILEPATH . '.new', BLOBCOMMON_CHMOD_FILE);
								return true;
							}
							elseif (@file_exists(static::FILEPATH . '.tmp')) {
								@unlink(static::FILEPATH . '.tmp');
							}
						}
					} catch (Throwable $e) {
						return false;
					} catch (Exception $e) {
						return false;
					}
				}
			} catch (Throwable $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}

		return false;
	}

	// ------------------------------------------------------------------ end library



	// ------------------------------------------------------------------
	// Plugin
	// ------------------------------------------------------------------

	/**
	 * Release Info
	 *
	 * Pull what we can from the local plugin, grab
	 * the rest from Github.
	 *
	 * @param string $key Key.
	 * @return mixed Details, detail, false.
	 */
	public static function release_info($key=null) {
		if (is_null(static::$_plugin)) {
			require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');

			// Start by pulling details from the header.
			static::$_plugin = get_plugin_data(BLOBCOMMON_INDEX, false, false);

			// Unfortunately that function lacks a filter,
			// so we need one more call to get the remote URI.
			$extra = get_file_data(
				BLOBCOMMON_INDEX,
				array('InfoURI'=>'Info URI'),
				'plugin'
			);
			static::$_plugin['InfoURI'] = $extra['InfoURI'];
			static::$_plugin['DownloadVersion'] = '';
			static::$_plugin['DownloadURI'] = '';

			// Now grab the remote info, if applicable. Cache it
			// a bit to save round trips.
			$transient_key = 'blobcommon_' . md5(BLOBCOMMON_INDEX . static::$_plugin['InfoURI']);
			if (false === ($response = get_transient($transient_key))) {
				$response = wp_remote_get(static::$_plugin['InfoURI']);
				if (200 === wp_remote_retrieve_response_code($response)) {
					$response = wp_remote_retrieve_body($response);
					$response = json_decode($response, true);
					if (is_array($response)) {
						set_transient($transient_key, $response, 7200);
					}
				}
			}

			if (
				is_array($response) &&
				isset($response['Version']) &&
				isset($response['DownloadURI'])
			) {
				static::$_plugin['DownloadVersion'] = $response['Version'];
				static::$_plugin['DownloadURI'] = $response['DownloadURI'];
			}
		}

		if (!is_null($key)) {
			return array_key_exists($key, static::$_plugin) ? static::$_plugin[$key] : false;
		}

		return static::$_plugin;
	}

	/**
	 * Musty Callback: Download Version
	 *
	 * MU plugins aren't part of the usual version management.
	 * We'll pass the info to the WP-CLI plugin Musty in case
	 * someone is using that.
	 *
	 * @param string $version Version.
	 * @return string $version Version.
	 */
	public static function musty_download_version($version='') {
		$version = static::release_info('DownloadVersion');
		if (!$version) {
			$version = '';
		}

		return $version;
	}

	/**
	 * Musty Callback: Download URI
	 *
	 * MU plugins aren't part of the usual version management.
	 * We'll pass the info to the WP-CLI plugin Musty in case
	 * someone is using that.
	 *
	 * @param string $uri URI.
	 * @return string $uri URI.
	 */
	public static function musty_download_uri($uri='') {
		$uri = static::release_info('DownloadURI');
		if (!$uri) {
			$uri = '';
		}

		return $uri;
	}

	/**
	 * Update Check
	 *
	 * Inject blob-cache into the updateable plugin object
	 * if there's an update available so WP can do its thing.
	 *
	 * @param object $updates Updates.
	 * @return object $updates Updates.
	 */
	public static function update_plugins($updates) {
		// Needs to make sense.
		if (!is_object($updates) || !isset($updates->response)) {
			return $updates;
		}

		$me = static::release_info();

		if (
			!$me['Version'] ||
			!$me['DownloadVersion'] ||
			!$me['DownloadURI'] ||
			version_compare($me['Version'], $me['DownloadVersion']) >= 0
		) {
			return $updates;
		}

		$updates->response[static::PLUGIN_PATH] = new \stdClass();
		$updates->response[static::PLUGIN_PATH]->id = 0;
		$updates->response[static::PLUGIN_PATH]->new_version = $me['DownloadVersion'];
		$updates->response[static::PLUGIN_PATH]->package = $me['DownloadURI'];
		$updates->response[static::PLUGIN_PATH]->plugin = static::PLUGIN_PATH;
		$updates->response[static::PLUGIN_PATH]->slug = $me['TextDomain'];
		$updates->response[static::PLUGIN_PATH]->url = $me['PluginURI'];

		return $updates;
	}

	// ------------------------------------------------------------------ end plugin
}
