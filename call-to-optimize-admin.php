<?php
function ctopt_register_custom_post_type() 
{
  $labels = array(
    'name' => __('Calls to Action'),
    'singular_name' => __('Call'),
    'add_new' => __('Add New'),
    'add_new_item' => __('Add New Call'),
    'edit_item' => __('Edit Call'),
    'new_item' => __('New Call'),
    'view_item' => __('View Call'),
    'search_items' => __('Search Calls'),
    'not_found' =>  __('No calls found'),
    'not_found_in_trash' => __('No calls found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => __('Calls'),

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
  add_submenu_page('edit.php?post_type=co-call', 'Call to Action reports', 'Reports', 'edit_posts', 'co-reporting-menu', 'callopt_reporting');
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
  if(isset($_POST['ctopt_cleanupOldTracking'])) {
    $oldData = CallToOptimizeGateway::findOldTracking();
    foreach($oldData as $data) {
      CallToOptimizeGateway::delete($oldData->id);
    }
  }
  $reports = CallToOptimizeGateway::findReports();
?>
<div>
<h2>Calls to Action Engagement Reports</h2>
<div id="report_div"></div>
<div id="legend_div"></div>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
  <?php if($message) {
    echo '<p>' . $message . '</p>';
  } ?>
  <p><label for="ctopt_switchCallId">Click on table or enter call id:</label>
     <input type="text" name="ctopt_switchCallId" id="ctopt_switchCallId" />
     <input type="submit" name="ctopt_switchStatus" value="Switch online/offline" />
  </p>
  <p><input type="submit" name="ctopt_cleanupOldTracking" value="Clean up old impression tracking data" /></p>
</form>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart","table"]});
      google.setOnLoadCallback(drawChart);
      var table, data;
      function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Call id');
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Impressions');
        data.addColumn('number', 'Clicks');
        data.addColumn('number', 'Conversion %');
        data.addRows(<?php echo count($reports); ?>);
        <?php 
        $count = 0;
        foreach($reports as $report) {
        echo "data.setValue(" . $count . ", 0, '" . $report->id . "');\n";
        echo "data.setValue(" . $count . ", 1, '" . $report->post_title . "');\n";
        echo "data.setValue(" . $count . ", 2, '" . $report->post_status . "');\n";
        echo "data.setValue(" . $count . ", 3,  " . $report->impressions . ");\n";
        echo "data.setValue(" . $count . ", 4,  " . $report->clicks . ");\n";
        echo "data.setValue(" . $count . ", 5,  " . $report->conversion . ");\n";
        $count++;
        } ?>

        var chartView = new google.visualization.DataView(data);
        chartView.hideColumns([1,2,5]);
        var chart = new google.visualization.ColumnChart(document.getElementById('report_div'));
        chart.draw(chartView, {width: 400, height: 240, title: 'Call engagement',
                          hAxis: {title: 'Call id', titleTextStyle: {color: 'red'}}
                         });
        table = new google.visualization.Table(document.getElementById('legend_div'));
        table.draw(data, {});
	google.visualization.events.addListener(table, 'select', selectHandler);
      }
      function selectHandler() {
        var selection = table.getSelection();
        if(selection.length == 0)
 	  return;
        var item = selection[0];
        var callId = data.getFormattedValue(item.row, 0);
        jQuery('#ctopt_switchCallId').val(callId);
      }
</script>
</div>
<?php }

function callopt_admin_menu() {
  add_submenu_page('edit.php?post_type=co-call', 'Call to Action admin', 'Options', 'manage_options', 'co-admin-menu', 'callopt_admin');
}

function callopt_admin() { 
  $options = CallToOptimizeOptions::get();
  if(isset($_POST['ctopt_updateOptions'])) {
    if(isset($_POST['ctopt_normalWeight'])) {
      $options['normalWeight'] = $_POST['ctopt_normalWeight'];
    }
    if(isset($_POST['ctopt_convertedWeight'])) {
      $options['convertedWeight'] = $_POST['ctopt_convertedWeight'];
    }
    if(isset($_POST['ctopt_seenWeight'])) {
      $options['seenWeight'] = $_POST['ctopt_seenWeight'];
    }
    if(isset($_POST['ctopt_seenLimit'])) {
      $options['seenLimit'] = $_POST['ctopt_seenLimit'];
    }
    update_option(CallToOptimizeOptions::OPTIONS_NAME , $options);
  }
?>
<h2>Calls to Action configuration</h2>
<div class="wrap">
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <h3>Call to action selection</h3>
    <p>The different weights (numeric and &gt;0) with which to select the calls. A higher value means they are more likely to be displayed. It is not suggested to put any of them at 0, but it is possible (they won't be displayed)</p>
    <table>
      <tr>
        <td><label for="ctopt_normalWeight">Normal/new Call</label></td>
        <td><input name="ctopt_normalWeight" id="ctopt_normalWeight" type="text" value="<?php echo $options['normalWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_convertedWeight">Call for which a user has already converted</label></td>
        <td><input name="ctopt_convertedWeight" id="ctopt_convertedWeight" type="text" value="<?php echo $options['convertedWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_seenWeight">Call that has been seen (see below)</label></td>
        <td><input name="ctopt_seenWeight" id="ctopt_seenWeight" type="text" value="<?php echo $options['seenWeight']; ?>" /></td>
      </tr>
    </table>
    <h3>Display limit</h3>
    <p><label for="ctopt_seenLimit">Number of times a call is displayed before it is considered "seen"</label></p>
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
