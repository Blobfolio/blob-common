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

$logpath = trailingslashit(ABSPATH) . 'wp-content/debug.log';
$log = file_exists($logpath) ? @file_get_contents($logpath) : '';

// @codingStandardsIgnoreStart
?><style type="text/css">
	.blobfolio-about-logo {
		transition: color .3s ease;
	}

	.blobfolio-about-logo svg {
		transition: color .3s ease;
		display: block;
		width: 100%;
		height: auto;
	}

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

				<div class="postbox">
					<div class="inside blobfolio-about">
						<a href="https://blobfolio.com/" target="_blank" class="blobfolio-about-logo"><?php echo file_get_contents(dirname(__FILE__) . '/img/blobfolio.svg'); ?></a>

						<p>We hope you find this plugin useful!  If you do, you might be interested in our other plugins, which are also completely free (and useful).</p>
						<ul>
							<li><a href="https://wordpress.org/plugins/apocalypse-meow/" target="_blank" title="Apocalypse Meow">Apocalypse Meow</a>: a simple, light-weight collection of tools to help protect wp-admin, including password strength requirements and brute-force log-in prevention.</li>
							<li><a href="https://wordpress.org/plugins/look-see-security-scanner/" target="_blank" rel="noopener" title="Look-See Security Scanner">Look-See Security Scanner</a>: a simple and efficient set of tools to locate file irregularities, configuration weaknesses, and vulnerabilities.</li>
							<li><a href="https://wordpress.org/plugins/sockem-spambots/" target="_blank" rel="noopener" title="Sock'Em SPAMbots">Sock'Em SPAMbots</a>: a more seamless approach to deflecting the vast majority of SPAM comments.</li>
							<li><a href="https://wordpress.org/plugins/wherewithal/" target="_blank" rel="noopener" title="Wherewithal Enhanced Search">Wherewithal Enhanced Search</a>: extend the default WP search to pull matches from custom fields, taxonomy, and more.</li>
						</ul>
					</div>
				</div>
			</div><!--.postbox-container-->

			<div class="postbox-container" id="postbox-container-2">
				<div class="postbox">
					<h3 class="hndle">Debug Log</h3>
					<div class="inside">
						<div id="debug-log"></div>
						<p class="description"><?php echo $logpath; ?>wp-content/debug.log</p>
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