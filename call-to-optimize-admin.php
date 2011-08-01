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

function callopt_reporting() { ?>
<div>
<h2>Calls to Action Engagement Reports</h2>
<div id="report_div"></div>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart","table"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Call id');
        data.addColumn('number', 'Impressions');
        data.addColumn('number', 'Clicks');
        data.addRows(4);
        data.setValue(0, 0, '1');
        data.setValue(0, 1, 1000);
        data.setValue(0, 2, 400);
        data.setValue(1, 0, '2');
        data.setValue(1, 1, 1170);
        data.setValue(1, 2, 460);
        data.setValue(2, 0, '5');
        data.setValue(2, 1, 1660);
        data.setValue(2, 2, 1120);
        data.setValue(3, 0, '7');
        data.setValue(3, 1, 430);
        data.setValue(3, 2, 100);

        var chart = new google.visualization.ColumnChart(document.getElementById('report_div'));
        chart.draw(data, {width: 400, height: 240, title: 'Call engagement',
                          hAxis: {title: 'Call id', titleTextStyle: {color: 'red'}}
                         });
      }
</script>
<div id="legend_div"></div>
<script type='text/javascript'>
      google.setOnLoadCallback(drawTable);
      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'Call id');
        data.addColumn('string', 'Title');
        data.addColumn('number', 'Impressions');
        data.addColumn('number', 'Clicks');
        data.addRows(4);
        data.setCell(0, 0, 1);
        data.setCell(0, 1, 'Mailing list signup');
        data.setCell(0, 2, 1000);
        data.setCell(0, 3, 400);
        data.setCell(1, 0, 2);
        data.setCell(1, 1, 'Twitter');
        data.setCell(1, 2, 1170);
        data.setCell(1, 3, 460);
        data.setCell(2, 0, 5);
        data.setCell(2, 1, 'Facebook');
        data.setCell(2, 2, 1660);
        data.setCell(2, 3, 1120);
        data.setCell(3, 0, 7);
        data.setCell(3, 1, 'Link');
        data.setCell(3, 2, 430);
        data.setCell(3, 3, 100);

        var table = new google.visualization.Table(document.getElementById('legend_div'));
        table.draw(data, {});
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
