<?php
/*
Plugin Name: Stop-Spam
Description: No spam in comments. No captcha.
Version: 1.1
Author: Webspeed
Text Domain: stop-spam
Author URI: https://www.webspeed.co.uk/
License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}

define('STOPSTAM_PLUGIN_VERSION', '1.1');

include('stop-spam-functions.php');
include('stop-spam-settings.php');
include('stop-spam-info.php');


function webssp_enqueue_script() {
	global $withcomments; // WP flag to show comments on all pages
	if ((is_singular() || $withcomments) && comments_open()) { // load script only for pages with comments form
		wp_enqueue_script('stop-spam-script', plugins_url('/js/stop-spam-1.0.js', __FILE__), null, null, true);
	}
}
add_action('wp_enqueue_scripts', 'webssp_enqueue_script');


function webssp_form_part() {
	$newline = "\r\n"; // .chr(13).chr(10)

	if ( ! is_user_logged_in()) { // add stop-spam fields only for not logged in users
		echo $newline.'<!-- Stop-spam plugin v.'.STOPSPAM_PLUGIN_VERSION.' wordpress.org/plugins/stop-spam/ -->'.$newline;
		echo '		<p class="stopspam-group stopspam-group-q" style="clear: both;">
			<label>Current ye@r <span class="required">*</span></label>
			<input type="hidden" name="stpspm-a" class="stopspam-control stopspam-control-a" value="'.date('Y').'" />
			<input type="text" name="stpspm-q" class="stopspam-control stopspam-control-q" value="'.STOPSPAM_PLUGIN_VERSION.'" autocomplete="off" />
		</p>'.$newline; // question (hidden with js)
		echo '		<p class="stopspam-group stopspam-group-e" style="display: none;">
			<label>Leave this field empty</label>
			<input type="text" name="stpspm-e-email-url-website" class="stopspam-control stopspam-control-e" value="" autocomplete="off" />
		</p>'.$newline; // empty field (hidden with css); trap for spammers because many bots will try to put email or url here
	}
}
add_action('comment_form', 'webssp_form_part'); // add stop-spam inputs to the comment form


function webssp_check_comment($commentdata) {
	$stopspam_settings = webssp_get_settings();
	
	extract($commentdata);

	if ( ! is_user_logged_in() && $comment_type != 'pingback' && $comment_type != 'trackback') { // logged in user is not a spammer
		if( webssp_check_for_spam() ) {
			if( $stopspam_settings['save_spam_comments'] ) {
				webssp_store_comment($commentdata);
			}
			webssp_counter_stats();
			wp_die('Comment is a spam.'); // die - do not send comment and show error message
		}
	}
	
	if ($comment_type == 'trackback') {
		if( $stopspam_settings['save_spam_comments'] ) {
			webssp_store_comment($commentdata);
		}
		webssp_counter_stats();
		wp_die('Trackbacks are disabled.'); // die - do not send trackback and show error message
	}

	return $commentdata; // if comment does not looks like spam
}

if ( ! is_admin()) { // without this check it is not possible to add comment in admin section
	add_filter('preprocess_comment', 'webssp_check_comment', 1);
}
