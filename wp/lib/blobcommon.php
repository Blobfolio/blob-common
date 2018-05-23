<?php
/**
 * Tutan Common: blob-common Updater
 *
 * This handles updates to the blob-common phar library — the majority
 * of new functionality lands there — as well as the plugin itself.
 *
 * This file also dependency checks, which become important as servers
 * fail to update tech in a timely fashion.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\wp\common;

use \stdClass;
use \Exception;
use \Throwable;

class blobcommon {
	// What the phar release file looks like.
	const PHAR_TEMPLATE = array(
		'checksum'=>'',
		'date_created'=>'',
		'size'=>0,
		'url'=>'',
	);

	// What the plugin release file looks like. A lot of fields are
	// missing as they lack relevance here.
	const PLUGIN_TEMPLATE = array(
		'Name'=>'',
		'Version'=>'',
		'DownloadURI'=>'',
	);

	protected static $_hard_fail;
	protected static $_soft_fail = false;
	protected static $_init = false;
	protected static $_plugin;
	protected static $_release = array();



	// -----------------------------------------------------------------
	// Setup
	// -----------------------------------------------------------------

	/**
	 * Init
	 *
	 * @return void Nothing.
	 */
	public static function init() {
		// Only need to run once.
		if (static::$_init) {
			return;
		}
		static::$_init = true;

		// This soft fail exists for exactly one release. The purpose is
		// to notify site operators that they will no longer receive
		// updates because their PHP environment is too old.
		if (version_compare(PHP_VERSION, '7.1.0') < 0) {
			add_action('admin_notices', function() {
				global $pagenow;
				// But this should only go on the dashboard and plugin
				// screens.
				if (('plugins.php' !== $pagenow) && ('index.php' !== $pagenow)) {
					return;
				}
				?>
				<div class="notice notice-warning">
					<p>
						<strong><?=__('Warning', 'blob-common')?>:</strong>
						<?php echo sprintf(
							__('The version of PHP running on this server has reached its %s. To ensure WordPress continues to operate as expected, please update to PHP %s or newer as soon as possible.', 'blob-common'),
							'<a href="http://php.net/supported-versions.php" target="_blank" rel="noopener">' . __('End of Life', 'blob-common') . '</a>',
							'<code>7.1</code>'
						);
						?>
					</p>
				</div>
				<?php
			});
		}

		// If the plugin is responsible for library updates too,
		// bootstrap all that stuff.
		if (BLOBCOMMON_PHAR_UPDATES) {
			static::init_phar();
		}
		else {
			static::unschedule_phar();
		}

		// Load the library.
		require(BLOBCOMMON_PHAR_PATH);

		static::init_plugin();
	}

	/**
	 * Init Phar
	 *
	 * Set a daily CRON job to check for and download library updates,
	 * and maybe load a new version of the library if a previous check
	 * fetched one.
	 *
	 * @return void Nothing.
	 */
	protected static function init_phar() {
		// For atomicity, new phar archives are first saved to a
		// temporary location and only moved to the final place the next
		// go around.
		if (@is_file(BLOBCOMMON_PHAR_PATH . '.new')) {
			// Couldn't move the new one.
			if (!@rename(BLOBCOMMON_PHAR_PATH . '.new', BLOBCOMMON_PHAR_PATH)) {
				@unlink(BLOBCOMMON_PHAR_PATH . '.new');
			}
			clearstatcache();
		}

		// Scheduled library updates.
		$class = get_called_class();
		add_action(
			'cron_blobcommon_library',
			array($class, 'check_phar')
		);
		if (false === ($timestamp = wp_next_scheduled('cron_blobcommon_library'))) {
			wp_schedule_event(time(), 'daily', 'cron_blobcommon_library');
		}
		register_deactivation_hook(BLOBCOMMON_INDEX, array($class, 'unschedule_phar'));
	}

	/**
	 * Init Plugin
	 *
	 * The plugin is not hosted on WP.org, but it can still make use of
	 * the central update system by hooking into the right places.
	 *
	 * @return void Nothing.
	 */
	protected static function init_plugin() {
		// No sense calling this a bunch.
		$class = get_called_class();

		// Plugin updates.
		if (BLOBCOMMON_MUST_USE) {
			// Must-Use doesn't have normal version management, but we
			// can add filters for Musty in case someone's using that.
			add_filter(
				'musty_download_version_blob-common/index.php',
				array($class, 'musty_download_version')
			);
			add_filter(
				'musty_download_uri_blob-common/index.php',
				array($class, 'musty_download_uri')
			);
		}
		else {
			// Normal plugins are... more normal.
			add_filter(
				'transient_update_plugins',
				array($class, 'update_plugins')
			);
			add_filter(
				'site_transient_update_plugins',
				array($class, 'update_plugins')
			);
		}
	}

	/**
	 * Parse Release File
	 *
	 * Both the plugin and library have JSON files containing release
	 * information. These files tell WordPress whether or not an update
	 * is available, and where to find it.
	 *
	 * @param string $key Header key containing JSON URI.
	 * @param array $template Data template.
	 * @return array Info.
	 */
	protected static function get_release_info(string $key, $template) {
		// PHP 5.6.0 compatibility has been dropped, so nobody can
		// update until they update PHP.
		if (version_compare(PHP_VERSION, '7.1.0') < 0) {
			return false;
		}

		// Already pulled it?
		if (isset(static::$_release[$key])) {
			return static::$_release[$key];
		}

		// WordPress hides relevant functionality in this file.
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');

		$uri = get_file_data(
			BLOBCOMMON_INDEX,
			array('json'=>$key),
			'plugin'
		);
		$uri = $uri['json'];
		ref\sanitize::uri($uri);
		if (!$uri) {
			static::$_release[$key] = false;
			return static::$_release[$key];
		}

		// Cache this to the transient table for a little bit.
		$transient_key = 'blob-common_remote_' . md5($uri);
		if (false !== (static::$_release[$key] = get_transient($transient_key))) {
			return static::$_release[$key];
		}

		// Read the file.
		$response = wp_remote_get($uri);
		if (200 !== wp_remote_retrieve_response_code($response)) {
			static::$_release[$key] = false;
			return static::$_release[$key];
		}

		// Decode the JSON.
		static::$_release[$key] = data::json_decode_array(
			remote_retrieve_body($response),
			$template
		);

		// Add the URI we discovered, just in case.
		static::$_release[$key]['json_uri'] = $uri;

		// Cache it for a while.
		set_transient($transient_key, static::$_release[$key], HOUR_IN_SECONDS);

		// And we're done.
		return static::$_release[$key];
	}

	// ----------------------------------------------------------------- end setup



	// -----------------------------------------------------------------
	// Phar Library
	// -----------------------------------------------------------------

	/**
	 * Check Phar
	 *
	 * Download the remote release information for the phar version of
	 * the library and compare it against what we currently have.
	 *
	 * @return string Info URI.
	 */
	public static function check_phar() {
		// Not checking or can't check, we're done!
		if (!BLOBCOMMON_PHAR_UPDATES ||
			(false === ($remote = static::get_release_info('Phar URI', static::PHAR_TEMPLATE)))
		) {
			return;
		}

		$current = get_option('blob-common_installed', false);

		// Everything is current, let's get out of here!
		if (
			!$remote['date_created'] ||
			!$remote['url'] ||
			($remote['date_created'] === $current)
		) {
			return;
		}

		// Generate a unique name for the temporary download.
		$dir = dirname(BLOBCOMMON_PHAR_PATH) . '/';
		$file = 'blob-common.phar.new.' . microtime(true);
		$file = wp_unique_filename($dir, $file);

		// Download it.
		$response = wp_remote_get($remote['url']);
		if (200 !== wp_remote_retrieve_response_code($response)) {
			return;
		}

		// Save it.
		if (@file_put_contents("{$dir}{$file}", wp_remote_retrieve_body($response))) {
			@chmod("{$dir}{$file}", BLOBCOMMON_CHMOD_FILE);
			// Check the hash.
			if (@md5_file("{$dir}{$file}") === $remote['checksum']) {
				// Move the file somewhere more permanent. We won't
				// actually load it until the next go around.
				@rename("{$dir}{$file}", "{$dir}blob-common.phar.new");

				// Update the option so we don't redownload it.
				update_option('blob-common_installed', $remote['date_created']);
			}
			// It failed; delete it.
			else {
				@unlink("{$dir}{$file}");
			}
		}
	}

	/**
	 * Unschedule Library Updates
	 *
	 * Remove the daily update-checking CRON job.
	 *
	 * @return void Nothing.
	 */
	public static function unschedule_phar() {
		if (false !== ($timestamp = wp_next_scheduled('cron_blobcommon_library'))) {
			wp_unschedule_event($timestamp, 'cron_blobcommon_library');
		}
	}

	// ----------------------------------------------------------------- end library



	// -----------------------------------------------------------------
	// Plugin
	// -----------------------------------------------------------------

	/**
	 * Check Plugin Info
	 *
	 * Unlike with the Phar library, this function only gathers the
	 * release information. Downloads are handled some other way.
	 *
	 * @param string $key Key.
	 * @return mixed Details, detail, false.
	 */
	protected static function check_plugin($key=null) {
		if (is_null(static::$_plugin)) {
			// Pull the remote info and store it for later.
			if (false === ($remote = static::get_release_info('Info URI', static::PLUGIN_TEMPLATE))) {
				static::$_plugin = false;
				return static::$_plugin;
			}

			// Use the main plugin headers as the basis.
			static::$_plugin = get_plugin_data(BLOBCOMMON_INDEX, false, false);

			// And add in what we pulled remotely.
			static::$_plugin['InfoURI'] = $remote['InfoURI'];
			static::$_plugin['DownloadVersion'] = $remote['Version'];
			static::$_plugin['DownloadURI'] = $remote['DownloadURI'];
		}

		// Requesting just one key?
		if (!is_null($key)) {
			return array_key_exists($key, static::$_plugin) ? static::$_plugin[$key] : false;
		}

		// Send everything!
		return static::$_plugin;
	}

	/**
	 * Musty Callback: Download Version
	 *
	 * MU plugins aren't part of the usual version management. We'll
	 * pass the info to the WP-CLI plugin Musty in case someone is using
	 * that.
	 *
	 * @param string $version Version.
	 * @return string $version Version.
	 */
	public static function musty_download_version($version='') {
		$version = static::check_plugin('DownloadVersion');
		if (!$version) {
			$version = '';
		}

		return $version;
	}

	/**
	 * Musty Callback: Download URI
	 *
	 * MU plugins aren't part of the usual version management. We'll
	 * pass the info to the WP-CLI plugin Musty in case someone is using
	 * that.
	 *
	 * @param string $uri URI.
	 * @return string $uri URI.
	 */
	public static function musty_download_uri($uri='') {
		$uri = static::check_plugin('DownloadURI');
		if (!$uri) {
			$uri = '';
		}

		return $uri;
	}

	/**
	 * Update Check
	 *
	 * Inject blob-common into the Core update list whenever updates
	 * are available.
	 *
	 * @param object $updates Updates.
	 * @return object $updates Updates.
	 */
	public static function update_plugins($updates) {
		// Most of the time we don't actually need to do anything.
		if (
			!is_object($updates) ||
			!isset($updates->response) ||
			isset($updates->response['blob-common/index.php']->new_version) ||
			(false === ($me = static::check_plugin())) ||
			!$me['Version'] ||
			!$me['DownloadVersion'] ||
			!$me['DownloadURI'] ||
			version_compare($me['Version'], $me['DownloadVersion']) >= 0
		) {
			return $updates;
		}

		// Add ourselves to the list!
		$updates->response[static::PLUGIN_PATH] = new stdClass();
		$updates->response[static::PLUGIN_PATH]->id = 0;
		$updates->response[static::PLUGIN_PATH]->new_version = $me['DownloadVersion'];
		$updates->response[static::PLUGIN_PATH]->package = $me['DownloadURI'];
		$updates->response[static::PLUGIN_PATH]->plugin = 'blob-common/index.php';
		$updates->response[static::PLUGIN_PATH]->slug = $me['TextDomain'];
		$updates->response[static::PLUGIN_PATH]->url = $me['PluginURI'];

		return $updates;
	}

	// ----------------------------------------------------------------- end plugin
}
