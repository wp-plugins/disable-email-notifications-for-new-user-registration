<?php
/*
Plugin Name: Disable Email Notifications in Wordpress 4.x for new user registration
Plugin URI: http://wpgeeks.net/
Version: 1.0
Author: Chaudhry Waqas
Description: Disable Email Notifications in Wordpress 4.x

*/

/*Security Note: Consider blocking direct access to your plugin PHP files by adding the following line at the top of each of them, or be sure to refrain from executing sensitive standalone PHP code before calling any WordPress functions.*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function den_load_my_plugin_first() {
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	
	if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
		array_splice($active_plugins, $this_plugin_key, 1);
		array_unshift($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
	}
}

//add action to load this plugin before all other plugins
add_action("activated_plugin", "den_load_my_plugin_first");

// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {

	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {

		$user = new WP_User( $user_id );

		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		$message  = sprintf( __('Hi Admin New user registration on %s:'), get_option('blogname') ) . "\r\n\r\n";
		$message .= sprintf( __('Username: %s'), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __('E-mail: %s'), $user_email ) . "\r\n";

		@wp_mail(
			"test@gmail.com",
			sprintf(__('[%s] New User Registration'), get_option('blogname') ),
			$message
		);

		if ( empty( $plaintext_pass ) )
			return;
	}
}
?>