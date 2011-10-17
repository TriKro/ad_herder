<?php
/**
 * Setup the admin functions for the plugin.
 * 
 * Registers a custom post type, called "Ad". Adds menu items
 *  & sorting options
 * 
 */
function adherder_admin_menu() {
	// add options and reporting menu items.
	$reportsMenu = add_submenu_page('edit.php?post_type=co-call', 'Ad Herder reports', 'Reports', 'edit_posts', 'co-reporting-menu', 'callopt_reporting');
	add_options_page('AdHerder Options', 'AdHerder Options', 'manage_options', 'adherder_options', 'adherder_options_page');

	// customize the columns in the admin interface
	add_filter('manage_edit-co-call_sortable_columns', 'ctopt_column_register_sortable');
	add_filter('posts_orderby', 'ctopt_column_orderby', 10, 2);
	add_action('manage_posts_custom_column', 'ctopt_column');
	add_filter('manage_edit-co-call_columns', 'ctopt_columns');

	// add JavaScript for reporting only
	add_action('load-'.$reportsMenu, 'adherder_report_scripts');
}

function adherder_admin_init() {
	register_setting('adherder_options', 'adherder_options', 'adherder_validate_options');
	
	add_settings_section('adherder_ad_selection', 'Ad selection', 'adherder_ad_selection_text', 'adherder_options');
	add_settings_field('adherder_normal_weight', 'Normal/New Ad', 'adherder_normal_weight_input', 'adherder_options', 'adherder_ad_selection');
	add_settings_field('adherder_converted_weight', 'Ad for which a user has already converted', 'adherder_converted_weight_input', 'adherder_options', 'adherder_ad_selection');
	add_settings_field('adherder_seen_weight', 'Ad that has been seen (see below)', 'adherder_seen_weight_input', 'adherder_options', 'adherder_ad_selection');
	
	add_settings_section('adherder_display_limit', 'Display limit', 'adherder_display_limit_text', 'adherder_options');
	add_settings_field('adherder_seen_limit', 'Number', 'adherder_seen_limit_input', 'adherder_options', 'adherder_display_limit');

	add_settings_section('adherder_track_logged_in_section', 'Track logged in users', 'adherder_track_logged_in_text', 'adherder_options');
	add_settings_field('adherder_track_logged_in', 'Track logged in users?', 'adherder_track_logged_in_input', 'adherder_options', 'adherder_track_logged_in_section');

	add_settings_section('adherder_ajax_widget_section', 'Use Ajax to display widget?', 'adherder_ajax_widget_section_text', 'adherder_options');
	add_settings_field('adherder_ajax_widget', 'Use Ajax', 'adherder_ajax_widget_input', 'adherder_options', 'adherder_ajax_widget_section');
}

function adherder_ad_selection_text() {
	echo '<p>The different weights (numeric and >0) with which to select the calls. A higher value means they are more likely to be displayed. It is not suggested to put any of them at 0, but it is possible (they won\'t be displayed)</p>';
}

function adherder_normal_weight_input() {
	$options = get_option('adherder_options');
	echo "<input id='normal_weight' name='adherder_options[normal_weight]' type='text' value='{$options['normal_weight']}' />";
}

function adherder_converted_weight_input() {
	$options = get_option('adherder_options');
	echo "<input id='converted_weight' name='adherder_options[converted_weight]' type='text' value='{$options['converted_weight']}' />";
}

function adherder_seen_weight_input() {
	$options = get_option('adherder_options');
	echo "<input id='seen_weight' name='adherder_options[seen_weight]' type='text' value='{$options['seen_weight']}' />";
}

function adherder_display_limit_text() {
	echo '<p>Entere here the number of times an ad is displayed before it is considered "seen"</p>';
}

function adherder_seen_limit_input() {
	$options = get_option('adherder_options');
	echo "<input id='seen_limit' name='adherder_options[seen_limit]' type='text' value='{$options['seen_limit']}' />";
}

function adherder_track_logged_in_text() {
	echo '<p>When this option is disabled, the plugin will not store tracking data or impressions/click counts for users that are logged in.</p>';
}

function adherder_track_logged_in_input() {
	$options = get_option('adherder_options');
	echo "<input id='track_logged_in' name='adherder_options[track_logged_in]' "; 
	checked($options['track_logged_in'], 1);
	echo " type='checkbox'  />";
}

function adherder_ajax_widget_section_text() {
	echo '<p>This will load the widget\'s content via an Ajax call. If you are using any kind of caching plugin and want correct results, you need to turn this on. But keep in mind that you might need to rewrite some ads that use JavaScript.</p>';
}

function adherder_ajax_widget_input() {
	$options = get_option('adherder_options');
	echo "<input id='ajax_widget' name='adherder_options[ajax_widget]' "; 
	checked($options['ajax_widget'], 1);
	echo " type='checkbox'  />";
}

function adherder_validate_options( $input ) {
	$valid   = array();
	$options = get_option('adherder_options');
	
	$input_normal_weight = $input['normal_weight'];
	if(is_numeric($input_normal_weight)) {
		$valid['normal_weight'] = absint($input_normal_weight);
	} else {
		add_settings_error('adherder_normal_weight', 'adherder_options_error', 'Weight must be >= 0');
		$valid['normal_weight'] = $options['normal_weight'];
	}
	
	$input_converted_weight = $input['converted_weight'];
	if(is_numeric($input_converted_weight)) {
		$valid['converted_weight'] = absint($input_converted_weight);
	} else {
		add_settings_error('adherder_converted_weight', 'adherder_options_error', 'Weight must be >= 0');
		$valid['converted_weight'] = $options['converted_weight'];
	}

	$input_seen_weight = $input['seen_weight'];
	if(is_numeric($input_seen_weight)) {
		$valid['seen_weight'] = absint($input_seen_weight);
	} else {
		add_settings_error('adherder_seen_weight', 'adherder_options_error', 'Weight must be >= 0');
		$valid['seen_weight'] = $options['seen_weight'];
	}
		
	$input_seen_limit = $input['seen_limit'];
	if(is_numeric($input_seen_limit)) {
		$valid['seen_limit'] = absint($input_seen_limit);
	} else {
		add_settings_error('adherder_seen_limit', 'adherder_options_error', 'Limit must be >= 0');
		$valid['seen_limit'] = $options['seen_limit'];
	}
	
	$valid['track_logged_in'] = isset($input['track_logged_in']);
	$valid['ajax_widget'] = isset($input['ajax_widget']);
	
	return $valid;
}

function adherder_options_page() {
  include(plugin_dir_path(__FILE__).'/../template/options.php');
}

/**
 * Enqueue the JavaScript used by the admin interface
 * 
 */
function adherder_report_scripts() {
	// the google chart API is used for reporting
	wp_enqueue_script('google-jsapi', 'https://www.google.com/jsapi');
}

function callopt_reporting() {
  $message = ''; 
  if(isset($_POST['ctopt_switchStatus'])) {
    $call_id   = $_POST['ctopt_switchCallId'];
    $call_post = get_post($call_id);
    if($call_post && $call_post->post_type == 'co-call') {
      $post_update       = array();
      $post_update['ID'] = $call_id;
      $post_changed      = false;
      if($call_post->post_status == 'publish') {
        $post_update['post_status'] = 'pending';
        $post_changed = true;
      } else if($call_post->post_status == 'pending') {
        $post_update['post_status'] = 'publish';
        $post_changed = true;
      } else {
        $message = 'The call id is not in status pending or publish, status was not updated.';
      }
      if($post_changed) {
        wp_update_post($post_update);
      }
    } else {
      $message = 'The ad id you entered is incorrect.';
    }
  }
  if(isset($_POST['ctopt_clearHistory'])) {
    $call_id   = $_POST['ctopt_clearCallId'];
    $call_post = get_post($call_id);
    if($call_post && $call_post->post_type == 'co-call') {
      CallToOptimizeGateway::deleteForPost($call_id);
      update_post_meta($call_id, 'ctopt_impressions', 0);
      update_post_meta($call_id, 'ctopt_clicks', 0);
      $message = 'Cleared all data for ad with id ' . $call_id;
    } else {
      $message = 'Id ' . $call_id . ' is not a valid ad id'; 
    }
  }
  if(isset($_POST['ctopt_cleanupOldTracking'])) {
    $oldData = CallToOptimizeGateway::findOldTracking();
    foreach($oldData as $data) {
      CallToOptimizeGateway::delete($oldData->id);
    }
    $message = 'Older impression data cleared';
  }
  $reports = CallToOptimizeGateway::findReports();
  include(plugin_dir_path(__FILE__).'/../template/report.php');
}

function ctopt_columns($columns)
{
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Action Title",
		"impressions" => "Impressions",
		"clicks" => "Clicks",
		"author" => "Author",
		"categories" => "Categories",
		"date" => "Date"
	);
	return $columns;
}

function ctopt_column($column)
{
	global $post;
	if ("ID" == $column) echo $post->ID;
	elseif ("impressions" == $column) echo ctopt_get_impressions($post->ID);
	elseif ("clicks" == $column)  echo ctopt_get_clicks($post->ID);
}
// Add the sorting SQL
function ctopt_column_orderby($orderby, $wp_query) {
	global $wpdb;
 
	$wp_query->query = wp_parse_args($wp_query->query);
 
	if ( 'impressions' == @$wp_query->query['orderby'] )
		$orderby = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND meta_key = 'ctopt_impressions') " . $wp_query->get('order');
 	
	if ( 'clicks' == @$wp_query->query['orderby'] )
		$orderby = "(SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND meta_key = 'ctopt_clicks') " . $wp_query->get('order');
		
	return $orderby;
}
// Register the column as sortable
function ctopt_column_register_sortable($columns) {
	$columns['impressions'] = 'impressions';
 	$columns['clicks'] = 'clicks';
	return $columns;
}
?>
