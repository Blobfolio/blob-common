<?php
/**
 * Email Functions
 *
 * This file contains functions related to sending email.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

// This must be called through WordPress.
if (! \defined('ABSPATH')) {
	exit;
}

use blobfolio\common\sanitize as v_sanitize;

if (! \function_exists('common_mail')) {
	/**
	 * Send HTML Email
	 *
	 * This works exactly the same as `wp_mail()` but
	 * sends messages in text/html format.
	 *
	 * @param string|array $to To.
	 * @param string $subject Subject.
	 * @param string $msg Message.
	 * @param string|array $from From (e.g. headers).
	 * @param string|array $attachments Attachments.
	 * @return bool True.
	 */
	function common_mail($to, $subject, $msg, $from=null, $attachments=null) {
		if (\is_null($from)) {
			$from = v_sanitize::name(\get_bloginfo('name')) . ' <' . \get_bloginfo('admin_email') . '>';
		}

		// Engage our filters.
		\add_filter('wp_mail_content_type', 'common_mail_html_content_type');

		// Send the mail.
		\wp_mail($to, $subject, $msg, "From: $from\r\nReply-To: $from\r\n", $attachments);

		// Remove our filters.
		\remove_filter('wp_mail_content_type', 'common_mail_html_content_type');

		return true;
	}
}

if (! \function_exists('common_mail_html_content_type')) {
	/**
	 * HTML Content Type
	 *
	 * @return string Content type.
	 */
	function common_mail_html_content_type() {
		return 'text/html';
	}
}

