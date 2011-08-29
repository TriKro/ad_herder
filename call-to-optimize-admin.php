<?php
function ctopt_register_custom_post_type() 
{
  $labels = array(
    'name' => __('Ads'),
    'singular_name' => __('Ad'),
    'add_new' => __('Add New'),
    'add_new_item' => __('Add New Ad'),
    'edit_item' => __('Edit Ad'),
    'new_item' => __('New Ad'),
    'view_item' => __('View Ad'),
    'search_items' => __('Search Ads'),
    'not_found' =>  __('No Ads found'),
    'not_found_in_trash' => __('No Ads found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => __('Ads'),

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => false,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor','author')
  ); 
  
  register_post_type('co-call',$args);

  add_action('admin_menu', 'callopt_reporting_menu');
  add_action('admin_menu', 'callopt_admin_menu');
}

function callopt_reporting_menu() {
  add_submenu_page('edit.php?post_type=co-call', 'Ad Herder reports', 'Reports', 'edit_posts', 'co-reporting-menu', 'callopt_reporting');
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
  include('template/report.php');
}

function callopt_admin_menu() {
  add_submenu_page('edit.php?post_type=co-call', 'AdHerder admin', 'Options', 'manage_options', 'co-admin-menu', 'callopt_admin');
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
    update_option(CallToOptimizeOptions::OPTIONS_NAME , $options);
  }
?>
<h2>AdHerder configuration</h2>
<div class="wrap">
  <?php if($message) {
    echo '<p>' . $message . '</p>';
  } ?>
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <h3>Ad selection</h3>
    <p>The different weights (numeric and &gt;0) with which to select the calls. A higher value means they are more likely to be displayed. It is not suggested to put any of them at 0, but it is possible (they won't be displayed)</p>
    <table>
      <tr>
        <td><label for="ctopt_normalWeight">Normal/new Ad</label></td>
        <td><input name="ctopt_normalWeight" id="ctopt_normalWeight" type="text" value="<?php echo $options['normalWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_convertedWeight">Ad for which a user has already converted</label></td>
        <td><input name="ctopt_convertedWeight" id="ctopt_convertedWeight" type="text" value="<?php echo $options['convertedWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_seenWeight">Ad that has been seen (see below)</label></td>
        <td><input name="ctopt_seenWeight" id="ctopt_seenWeight" type="text" value="<?php echo $options['seenWeight']; ?>" /></td>
      </tr>
    </table>
    <h3>Display limit</h3>
    <p><label for="ctopt_seenLimit">Number of times an ad is displayed before it is considered "seen"</label></p>
    <p><input type="text" name="ctopt_seenLimit" id="ctopt_seenLimit" value="<?php echo $options['seenLimit']; ?>" /></p>
    <div class="submit">
      <input type="submit" name="ctopt_updateOptions" value="Update Settings" />
    </div>
  </form>
</div>
<?php }

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
