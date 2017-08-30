<?php
/**
 * Admin Debug Log Viewer
 *
 * This allows the contents of debug.log to be parsed/displayed
 * in environments where direct access may not be possible.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (!defined('ABSPATH')) {
	exit;
}

$requires = defined('WP_DEBUG_LOG_CAP') ? WP_DEBUG_LOG_CAP : 'manage_options';
if (!current_user_can($requires)) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
}

/**
 * Sister Plugins
 *
 * Get a list of other plugins by Blobfolio.
 *
 * @return array Plugins.
 */
function sister_plugins() {
	require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin.php');
	require_once(trailingslashit(ABSPATH) . 'wp-admin/includes/plugin-install.php');
	$response = plugins_api(
		'query_plugins',
		array(
			'author'=>'blobfolio',
			'per_page'=>20,
		)
	);

	if (!isset($response->plugins) || !is_array($response->plugins)) {
		return array();
	}

	// We want to know whether a plugin is on the system, not
	// necessarily whether it is active.
	$plugin_base = dirname(BLOBCOMMON_ROOT) . '/';
	$plugins = array();
	foreach ($response->plugins as $p) {
		if (('blob-common' === $p->slug) || file_exists("{$plugin_base}{$p->slug}")) {
			continue;
		}

		$plugins[] = array(
			'name'=>$p->name,
			'slug'=>$p->slug,
			'description'=>$p->short_description,
			'url'=>$p->homepage,
			'version'=>$p->version,
		);
	}

	usort(
		$plugins,
		function($a, $b) {
			if ($a['name'] === $b['name']) {
				return 0;
			}

			return $a['name'] > $b['name'] ? 1 : -1;
		}
	);

	return $plugins;
}

$logpath = trailingslashit(WP_CONTENT_DIR) . 'debug.log';

// @codingStandardsIgnoreStart
?><style type="text/css">

	.sister-plugins {
		padding: 0 10px;
	}

	.sister-plugins--blobfolio {
		display: block;
		width: 230px;
		height: 60px;
		margin: 30px auto;
	}

	.sister-plugins--blobfolio svg {
		display: block;
		width: 100%;
		height: 100%;
		color: #23282D;
		transition: color .3s;
	}

	.sister-plugins--blobfolio:hover svg { color: #0073AA; }


	.sister-plugins--intro {
		font-style: italic;
		padding: 0 10px;
		text-align: center;
		color: #aaa;
		margin: 30px 0;
	}

	.sister-plugin {
		padding: 10px 0;
	}

	.sister-plugin:first-child { padding-top: 0; }
	.sister-plugin--name { display: block; }
	.sister-plugin + .sister-plugin { border-top: 1px solid #F1F1F1; }

	#debug-log {
		display: block;
		width: 100%;
		height: 70vh;
		font-size: 12px;
		font-family: monospace;
		overflow-y: scroll;
		color: #fff;
		background-color: #23282d;
		padding: 10px;
		word-break: break-word;
		box-sizing: border-box;
	}

	#debug-log .log-date { color: #cf694a; }
	#debug-log .log-line { color: #f9ee9a; }
	#debug-log .log-path { color: #919e6b; }
	#debug-log .log-type { font-weight: bold; }

	#debug-log .log-comment,
	#debug-log .log-comment > * {
		font-style: italic;
		color: #787878!important;
		font-weight: normal;
	}
	#debug-log .log-comment { padding: 0 2em; }

	#debug-log .log-happy {
		font-weight: bold;
		color: #0073aa;
	}

	#debug-log .log-entry:not(.log-comment) { margin-top: 1em; }
	#debug-log .log-entry:first-child { margin-top: 0; }

	#debug-log .log-entry:not(.log-comment) + .log-comment { margin-top: 1em; }

	.debug-log-search-settings th {
		width: 65px;
		font-size: 11px;
		vertical-align: top;
		text-align: right;
		padding-right: 5px;
	}

	.debug-log-search-settings input[type=number] {
		width: 100px;
		display: block;
	}

	.wp-core-ui .button-evil,
	.wp-core-ui .button-evil:hover,
	.wp-core-ui .button-evil:active {
		background-color: #DC3232;
		color: #fff;
		border-color: #CB2323;
		box-shadow: 0 1px 0 #CB2323;
	}

	.wp-core-ui .button-evil:hover,
	.wp-core-ui .button-evil:active {
		background-color: #E74141;
	}

</style>
<div class="wrap">

	<div id="debug-log-errors"></div>

	<h2>Debug Log</h2>

	<div id="poststuff">
		<div id="post-body" class="meta-holder columns-2">

			<div class="postbox-container" id="postbox-container-1">
				<div class="postbox">
					<h3 class="hndle">View Options</h3>
					<div class="inside">
						<form method="post" id="debug-log-search" action="<?php echo admin_url('admin-ajax.php'); ?>">
							<input type="hidden" name="n" value="<?php echo wp_create_nonce('debug-log'); ?>" />
							<input type="hidden" name="action" value="common_ajax_debug_log" />

							<table class="debug-log-search-settings">
								<tbody>
									<tr>
										<th scope="row"><label for="debug-log-search-today">Just Today:</th>
										<td><input type="checkbox" name="today" value="1" id="debug-log-search-today" checked /></td>
									</tr>
									<tr>
										<th scope="row"><label for="debug-log-search-tail">Tail:</label></th>
										<td>
											<input type="number" step="1" required min="0" name="tail" value="50" id="debug-log-search-tail" />
											<p class="description">Restrict output to the most recent <code>X</code> lines. Enter <code>0</code> to see everything.</p>
										</td>
									</tr>
									<tr>
										<th scope="row"></th>
										<td><button type="submit" class="button button-primary">Load</button></td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				</div>

				<div class="postbox">
					<h3 class="hndle">Clean Up</h3>
					<div class="inside">
						<form method="post" id="debug-log-delete" action="<?php echo admin_url('admin-ajax.php'); ?>">
							<input type="hidden" name="n" value="<?php echo wp_create_nonce('debug-log'); ?>" />
							<input type="hidden" name="action" value="common_ajax_debug_log_delete" />

							<table class="debug-log-search-settings">
								<tbody>
									<tr>
										<th scope="row"></th>
										<td><button type="submit" class="button button-evil">Delete Log</button></td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				</div>

				<?php
				$plugins = sister_plugins();
				if (count($plugins)) {
					?>
					<div class="postbox">
						<div class="inside">
							<a href="https://blobfolio.com/" target="_blank" class="sister-plugins--blobfolio"><?php echo file_get_contents(BLOBCOMMON_ROOT . '/img/blobfolio.svg'); ?></a>

							<div class="sister-plugins--intro">
								We hope you find this plugin useful!  If you do, you might be interested in our other plugins, which are also completely free (and useful).
							</div>

							<nav class="sister-plugins">
								<?php foreach ($plugins as $p) { ?>
									<div class="sister-plugin">
										<a href="<?php echo esc_attr($p['url']); ?>" target="_blank" class="sister-plugin--name"><?php echo esc_html($p['name']); ?></a>

										<div class="sister-plugin--text"><?php echo esc_html($p['description']); ?></div>
									</div>
								<?php } ?>
							</nav>
						</div>
					</div>
				<?php } ?>

			</div><!--.postbox-container-->

			<div class="postbox-container" id="postbox-container-2">
				<div class="postbox">
					<h3 class="hndle">Debug Log</h3>
					<div class="inside">
						<div id="debug-log"></div>
						<p class="description"><?php echo $logpath; ?></p>
					</div><!--.inside-->
				</div><!--.postbox-->

			</div><!--.postbox-container-->

		</div><!--#post-body-->
	</div><!--#poststuff-->

</div><!--.wrap-->
<script>
jQuery(document).ready(function(){

	//search
	jQuery('#debug-log-search').submit(function(){
		var form = jQuery('#debug-log-search'),
			submit = jQuery('[type=submit]', form);

		submit.prop('disabled', true);
		jQuery('#debug-log-errors').html('');
		jQuery('#debug-log').html('<div class="log-entry">...loading...</div>');

		jQuery.post(form.attr('action'), form.serialize(), function(r){
			try {
				if(parseInt(r, 10) === 0){
					throw new Exception('Generic Fail!');
				}
				else if(r.errors.length){
					debug_log_errors(r.errors);
				}
				else if(r.log.length) {
					jQuery('#debug-log').html(r.log);
				}
			} catch(Ex){
				debug_log_errors(['The server response was invalid.']);
			}

			submit.prop('disabled', false);
		});

		return false;
	});

	//delete
	jQuery('#debug-log-delete').submit(function(){
		var form = jQuery('#debug-log-delete'),
			submit = jQuery('[type=submit]', form);

		submit.prop('disabled', true);
		jQuery('#debug-log-errors').html('');
		jQuery('#debug-log').html('<div class="log-entry">...deleting...</div>');

		jQuery.post(form.attr('action'), form.serialize(), function(r){
			try {
				if(parseInt(r, 10) === 0){
					throw new Exception('Generic Fail!');
				}
				else if(r.errors.length){
					debug_log_errors(r.errors);
				}
				else if(r.success) {
					jQuery('#debug-log-search').submit();
				}
			} catch(Ex){
				debug_log_errors(['The server response was invalid.']);
			}

			submit.prop('disabled', false);
		});

		return false;
	});

	//handle errors
	function debug_log_errors(errors){
		jQuery('#debug-log-errors').html('');
		jQuery('#debug-log').html('<div class="log-entry">The debug.log file could not be loaded.</div>');
		if(Array.isArray(errors) && errors.length){
			jQuery.each(errors, function(k,v){
				var error = jQuery('<div class="error"><p></p></div>');
				jQuery('p', error).text(v);
				jQuery('#debug-log-errors').append(error);
			});
		}
		return true;
	}

	//run search at load
	jQuery(window).load(function(){ jQuery('#debug-log-search').submit(); });

});
</script>
<?php // @codingStandardsIgnoreEnd ?>