<?php
/*
Plugin Name: AdHerder
Plugin URI: http://www.streamhead.com
Description: Displays call to actions, tracks their performance and optimizes placement
Version: 1.0
Author: Peter Backx
Author URI: http://www.streamhead.com
*/

add_action( 'plugins_loaded', 'adherder_plugin_setup' );

function adherder_plugin_setup() {
	if ( !defined('ADHERDER_VERSION') ) {
		define('ADHERDER_VERSION', 'adherder_version');
	}
	if ( !defined('ADHERDER_VERSION_NUM') ) {
		define('ADHERDER_VERSION_NUM', '1.0');
	}
	add_option(ADHERDER_VERSION, ADHERDER_VERSION_NUM);
	
	// code that should always be loaded
	require_once(plugin_dir_path(__FILE__)."/includes/database.php");
	require_once(plugin_dir_path(__FILE__)."/includes/display.php");
	require_once(plugin_dir_path(__FILE__)."/includes/functions.php");
	require_once(plugin_dir_path(__FILE__)."/includes/ajax.php");

	// register AdHerder post type
	add_action( 'init', 'adherder_register_post_type');

	// register widget
	add_action('widgets_init', create_function('', 'return register_widget("CtoptWidget");'));

	// add the administrative functions only when in the admin interface
	if ( is_admin() ) {
		require_once(plugin_dir_path(__FILE__).'/includes/admin.php' );
		add_action('admin_menu', 'adherder_admin_setup');
	}

	// install click tracking database table on activation
	register_activation_hook(__FILE__, array('CallToOptimizeGateway','install'));

	// add JavaScript files and Ajax methods
	add_action('wp_enqueue_scripts', 'adherder_client_scripts');
	add_action('wp_ajax_nopriv_ctopt-track', 'ctopt_ajax_register_track');
	add_action('wp_ajax_ctopt-track', 'ctopt_ajax_register_track');
	add_action('wp_ajax_nopriv_ctopt-impression', 'ctopt_ajax_register_impression');
	add_action('wp_ajax_ctopt-impression', 'ctopt_ajax_register_impression');
}
?>
