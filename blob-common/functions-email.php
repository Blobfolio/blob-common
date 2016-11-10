<?php
//---------------------------------------------------------------------
// FUNCTIONS: EMAIL
//---------------------------------------------------------------------
// This file contains functions relating to email sending

//this must be called through WordPress
if(!defined('ABSPATH'))
	exit;



//-------------------------------------------------
// HTML WP Mail Wrapper
//
// @param to
// @param subject
// @param msg
// @param from (e.g. headers)
// @param attachments
// @return true
if(!function_exists('common_mail')){
	function common_mail($to, $subject, $msg, $from=null, $attachments=null){
		if(is_null($from))
			$from = common_sanitize_name(get_bloginfo('name')) . ' <' . get_bloginfo('admin_email') . '>';

		//engage our filters
		add_filter('wp_mail_content_type', 'common_mail_html_content_type');

		//send the mail
		wp_mail($to, $subject, $msg, "From: $from\r\nReply-To: $from\r\n", $attachments);

		//remove our filters
		remove_filter('wp_mail_content_type', 'common_mail_html_content_type');

		return true;
	}
}

//-------------------------------------------------
// Set E-mail Content Type to HTML
//
// @param n/a
// @return text/html
if(!function_exists('common_mail_html_content_type')){
	function common_mail_html_content_type(){
		return 'text/html';
	}
}
?>