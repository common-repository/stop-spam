<?php
/*
Stop-spam settings code
used WordPress Settings API - http://codex.wordpress.org/Settings_API
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}


function webssp_menu() { // add menu item
	add_options_page('Stop-Spam', 'Stop-Spam', 'manage_options', 'stop-spam', 'webssp_settings');
}
add_action('admin_menu', 'webssp_menu');


function webssp_admin_init() {
	register_setting('stopspam_settings_group', 'stopspam_settings', 'webssp_settings_validate');

	add_settings_section('stopspam_settings_automatic_section', '', 'webssp_section_callback', 'stopspam_automatic_page');

	add_settings_field('save_spam_comments', 'Save spam comments', 'webssp_field_save_spam_comments_callback', 'stopspam_automatic_page', 'stopspam_settings_automatic_section');

}
add_action('admin_init', 'webssp_admin_init');


function webssp_settings_init() { // set default settings
	global $stopspam_settings;
	$stopspam_settings = webssp_get_settings();
	update_option('stopspam_settings', $stopspam_settings);
}
add_action('admin_init', 'webssp_settings_init');


function webssp_settings_validate($input) {
	$default_settings = webssp_get_settings();
	
	// checkbox
	$output['save_spam_comments'] = $input['save_spam_comments'];

	return $output;
}


function webssp_section_callback() { // Stop-spam settings description
	echo '';
}


function webssp_field_save_spam_comments_callback() {
	$settings = webssp_get_settings();
	echo '<label><input type="checkbox" name="stopspam_settings[save_spam_comments]" '.checked(1, $settings['save_spam_comments'], false).' value="1" />';
	echo ' Save spam comments into spam section</label>';
	echo '<p class="description">Useful for testing how the plugin works. <a href="'. admin_url( 'edit-comments.php?comment_status=spam' ) . '">View spam section</a>.</p>';
}


function webssp_settings() { //stopspamsettings
	$stopspam_stats = get_option('stopspam_stats', array());
	if (isset($stopspam_stats['blocked_total'])) {
		$blocked_total = $stopspam_stats['blocked_total'];
	}
	if (empty($blocked_total)) {
		$blocked_total = 0;
	}
	?>
	<div class="wrap">
		
		<h2><span class="dashicons dashicons-admin-generic"></span> Stop-spam</h2>

		<div class="stopspam-panel-info">
			<p style="margin: 0;">
				<span class="dashicons dashicons-chart-bar"></span>
				<strong><?php echo $blocked_total; ?></strong> spam comments were blocked by <a href="https://wordpress.org/plugins/stop-spam/" target="_blank">Stop-Spam</a> plugin so far.
			</p>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields('stopspam_settings_group'); ?>
			<div class="stopspam-group-automatic">
				<?php do_settings_sections('stopspam_automatic_page'); ?>
			</div>
			<?php submit_button(); ?>
		</form>

	</div>
	<?php
}
