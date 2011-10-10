<?php
/**
 * Setup the admin functions for the plugin.
 * 
 * Registers a custom post type, called "Ad". Adds menu items
 *  & sorting options
 * 
 */
function adherder_admin_setup() 
{
	// add options and reporting menu items.
	$reportsMenu = add_submenu_page('edit.php?post_type=co-call', 'Ad Herder reports', 'Reports', 'edit_posts', 'co-reporting-menu', 'callopt_reporting');
	add_submenu_page('edit.php?post_type=co-call', 'AdHerder admin', 'Options', 'manage_options', 'co-admin-menu', 'callopt_admin');

	// customize the columns in the admin interface
	add_filter('manage_edit-co-call_sortable_columns', 'ctopt_column_register_sortable');
	add_filter('posts_orderby', 'ctopt_column_orderby', 10, 2);
	add_action('manage_posts_custom_column', 'ctopt_column');
	add_filter('manage_edit-co-call_columns', 'ctopt_columns');

	// add JavaScript for reporting only
	add_action('load-'.$reportsMenu, 'adherder_report_scripts');
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
      $message = 'The call id you entered is incorrect.';
    }
  }
  if(isset($_POST['ctopt_clearHistory'])) {
    $call_id   = $_POST['ctopt_clearCallId'];
    $call_post = get_post($call_id);
    if($call_post && $call_post->post_type == 'co-call') {
      CallToOptimizeGateway::deleteForPost($call_id);
      update_post_meta($call_id, 'ctopt_impressions', 0);
      update_post_meta($call_id, 'ctopt_clicks', 0);
      $message = 'Cleared all data for call with id ' . $call_id;
    } else {
      $message = 'Id ' . $call_id . ' is not a valid call to action id'; 
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

function callopt_admin() {
  $message = ''; 
  $options = CallToOptimizeOptions::get();
  if(isset($_POST['ctopt_updateOptions'])) {
    if(isset($_POST['ctopt_normalWeight'])) {
      $nw = $_POST['ctopt_normalWeight'];
      if(preg_match('/^\d+$/', $nw)) {
        $options['normalWeight'] = $nw;
      } else {
        $message = 'Weight must be a positive or zero number';
      }
    }
    if(isset($_POST['ctopt_convertedWeight'])) {
      $cw = $_POST['ctopt_convertedWeight'];
      if(preg_match('/^\d+$/', $cw)) {
        $options['convertedWeight'] = $cw;
      } else {
        $message = 'Weight must be a positive or zero number';
      }
    }
    if(isset($_POST['ctopt_seenWeight'])) {
      $sw = $_POST['ctopt_seenWeight'];
      if(preg_match('/^\d+$/', $sw)) {
        $options['seenWeight'] = $sw;
      } else {
        $message = 'Weight must be a positive or zero number';
      }
    }
    if(isset($_POST['ctopt_seenLimit'])) {
      $sl = $_POST['ctopt_seenLimit'];
      if(preg_match('/^\d+$/', $sl)) {
        $options['seenLimit'] = $sl;
      } else {
        $message = 'Weight must be a positive or zero number';
      }
    }
    if(isset($_POST['ctopt_trackLoggedIn'])) {
      $options['trackLoggedIn'] = $_POST['ctopt_trackLoggedIn'];
    }
    if(isset($_POST['ctopt_ajaxWidget'])) {
      $options['ajaxWidget'] = $_POST['ctopt_ajaxWidget'];
    }
    update_option(CallToOptimizeOptions::OPTIONS_NAME , $options);
  }
  include(plugin_dir_path(__FILE__).'/../template/options.php');
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
