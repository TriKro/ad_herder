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
  echo '<div>';
  echo '<p>Calls to action reports will go here</p>';
  echo '</div>';
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
