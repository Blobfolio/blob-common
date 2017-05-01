<?php
/**
 * Tutan Common: blob-common Updater
 *
 * The blob-common library is updated independently of
 * the plugin or other dependencies.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\wp\common;

class blobcommon {
	const OPTION_NAME = 'blobcommon_library';
	const FILEPATH = BLOB_COMMON_ROOT . '/lib/blob-common.phar';
	const REMOTE = 'https://raw.githubusercontent.com/Blobfolio/blob-common/master/bin/';

	/**
	 * Init
	 *
	 * @return void Nothing.
	 */
	public static function init() {
		// Include the library.
		require_once(static::FILEPATH);

		// Schedule updates.
		add_action('cron_' . static::OPTION_NAME, array(get_called_class(), 'upgrade'));
		static::schedule();

		// Unschedule updates.
		$file = str_replace('\\', '/', __FILE__);
		$base = str_replace('\\', '/', trailingslashit(WP_PLUGIN_DIR));
		$file = preg_replace(
			'/^' . preg_quote($base, '/') . '([a-z0-9\-\_]+).*/i',
			$base . '$1/index.php',
			$file
		);
		register_deactivation_hook($file, array(get_called_class(), 'unschedule'));
	}

	/**
	 * Schedule Updates
	 *
	 * @return void Nothing.
	 */
	public static function schedule() {
		if (false === ($timestamp = wp_next_scheduled('cron_' . static::OPTION_NAME))) {
			wp_schedule_event(time(), 'daily', 'cron_' . static::OPTION_NAME);
		}
	}

	/**
	 * Unschedule Updates
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
						if (false !== file_put_contents(static::FILEPATH . '.tmp', $data)) {
							if (
								(md5_file(static::FILEPATH . '.tmp') === $checksum) &&
								(false !== rename(static::FILEPATH . '.tmp', static::FILEPATH))
							) {
								update_option(static::OPTION_NAME, $date);
								return true;
							}
							elseif (file_exists(static::FILEPATH . '.tmp')) {
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
}
