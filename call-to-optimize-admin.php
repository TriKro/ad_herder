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
}

function callopt_reporting_menu() {
  add_submenu_page('edit.php?post_type=co-call', 'Call to Action reports', 'Reports', 'edit_posts', 'co-reporting-menu', 'callopt_reporting');
}

function callopt_reporting() { 
  global $wpdb;
  $reports = $wpdb->get_results("SELECT 
    id, post_title, post_status, 
    IFNULL((select meta_value from wp_postmeta where post_id = id and meta_key = 'ctopt_impressions'),0) as impressions, 
    IFNULL((select meta_value from wp_postmeta where post_id = id and meta_key = 'ctopt_clicks'), 0) as clicks 
    FROM wp_posts p WHERE post_type = 'co-call'");
?><div>
<h2>Calls to Action Engagement Reports</h2>
<div id="report_div"></div>
<div id="legend_div"></div>
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
        data.addRows(<?php echo count($reports); ?>);
        <?php 
        $count = 0;
        foreach($reports as $report) {
        echo "data.setValue(" . $count . ", 0, '" . $report->id . "');\n";
        echo "data.setValue(" . $count . ", 1, '" . $report->post_title . "');\n";
        echo "data.setValue(" . $count . ", 2, '" . $report->post_status . "');\n";
        echo "data.setValue(" . $count . ", 3,  " . $report->impressions . ");\n";
        echo "data.setValue(" . $count . ", 4,  " . $report->clicks . ");\n";
        $count++;
        } ?>

        var chartView = new google.visualization.DataView(data);
        chartView.hideColumns([1,2]);
        var chart = new google.visualization.ColumnChart(document.getElementById('report_div'));
        chart.draw(chartView, {width: 400, height: 240, title: 'Call engagement',
                          hAxis: {title: 'Call id', titleTextStyle: {color: 'red'}}
                         });
        table = new google.visualization.Table(document.getElementById('legend_div'));
        table.draw(data, {});
//	google.visualization.events.addListener(table, 'select', selectHandler);
      }
      function selectHandler() {
        var selection = table.getSelection();
        if(selection.length == 0)
 	  return;
        var item = selection[0];
        alert("select " + data.getFormattedValue(item.row, 0));
      }
</script>
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
