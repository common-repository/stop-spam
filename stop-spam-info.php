<?php
if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}

function webssp_admin_notice() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php'):
		$user_id = get_current_user_id();
		$stopspam_info_visibility = get_user_meta($user_id, 'stopspam_info_visibility', true);
		if ($stopspam_info_visibility == 1 OR $stopspam_info_visibility == ''):
			$blocked_total = 0; // show 0 by default
			$stopspam_stats = get_option('stopspam_stats', array());
			if (isset($stopspam_stats['blocked_total'])) {
				$blocked_total = $stopspam_stats['blocked_total'];
			}
			?>
			<div class="update-nag stopspam-panel-info">
				<p style="margin: 0;">
					<?php echo $blocked_total; ?> spam comments were blocked by <a href="http://wordpress.org/plugins/stop-spam/">Stop-Spam</a> plugin so far.
				</p>
			</div>
			<?php
		endif; // end of if($stopspam_info_visibility)
	endif; // end of if($pagenow == 'edit-comments.php')
}
add_action('admin_notices', 'webssp_admin_notice');


function webssp_display_screen_option() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php'):
		$user_id = get_current_user_id();
		$stopspam_info_visibility = get_user_meta($user_id, 'stopspam_info_visibility', true);

		if ($stopspam_info_visibility == 1 OR $stopspam_info_visibility == '') {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		?>
		<script>
			jQuery(function($){
				$('.stopspam_screen_options_group').insertAfter('#screen-options-wrap #adv-settings');
			});
		</script>
		<form method="post" class="stopspam_screen_options_group" style="padding: 20px 0 5px 0;">
			<input type="hidden" name="stopspam_option_submit" value="1" />
			<label>
				<input name="stopspam_info_visibility" type="checkbox" value="1" <?php echo $checked; ?> />
				Stop-spam info
			</label>
			<input type="submit" class="button" value="<?php _e('Apply'); ?>" />
		</form>
		<?php
	endif; // end of if($pagenow == 'edit-comments.php')
}


function webssp_register_screen_option() {
	add_filter('screen_layout_columns', 'webssp_display_screen_option');
}
add_action('admin_head', 'webssp_register_screen_option');


function webssp_update_screen_option() {
// 	$stopspam_option_submit=sanitize_text_field($_POST['stopspam_option_submit']);
	if (isset($_POST['stopspam_option_submit']) AND sanitize_text_field($_POST['stopspam_option_submit']) == 1) {
		$user_id = get_current_user_id();
		var_dump($user_id);
		$stopspam_info_visibility=sanitize_text_field($_POST['stopspam_info_visibility']);
		if (isset($_POST['stopspam_info_visibility']) AND $stopspam_info_visibility == 1) {
			update_user_meta($user_id, 'stopspam_info_visibility', 1);
		} else {
			update_user_meta($user_id, 'stopspam_info_visibility', 0);
		}
	}
}
add_action('admin_init', 'webssp_update_screen_option');
