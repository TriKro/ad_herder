<?php
/*
Plugin Name: Call To Optimize
Plugin URI: http://www.streamhead.com
Description: Displays call to actions, tracks their performance and optimizes placement
Version: 1.0
Author: Peter Backx
Author URI: http://www.streamhead.com
*/

require_once(plugin_dir_path(__FILE__)."/call-to-optimize-admin.php");
require_once(plugin_dir_path(__FILE__)."/call-to-optimize-display.php");
require_once(plugin_dir_path(__FILE__)."/call-to-optimize-functions.php");
require_once(plugin_dir_path(__FILE__)."/call-to-optimize-install.php");

// register widget
add_action('widgets_init', create_function('', 'return register_widget("CtoptWidget");'));

// register custom post type
add_action('init', 'ctopt_register_custom_post_type');

// click tracking
add_action('init', 'ctopt_track', 11);
// install click tracking database table on activation
register_activation_hook(__FILE__, 'ctopt_install');

// columns in admin interface
add_filter('manage_edit-co-call_sortable_columns', 'ctopt_column_register_sortable');
add_filter('posts_orderby', 'ctopt_column_orderby', 10, 2);
add_action("manage_posts_custom_column", "ctopt_column");
add_filter("manage_edit-co-call_columns", "ctopt_columns");

// add JavaScript
add_action('wp_enqueue_scripts', 'ctopt_enqueue_scripts');
function ctopt_enqueue_scripts() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('ctopt', plugins_url('/call-to-optimize/js/ctopt.js'));
}

?>
