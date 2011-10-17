<?php
/**
 * Register a custom AdHerder post type that will hold the ads
 * 
 */
function adherder_register_post_type() {
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
}

function ctopt_track_logged_in() {
  if(!is_user_logged_in()) {
    return true; // always track users that are not logged in
  }
  $options = get_option('adherder_options');
  return $options['track_logged_in']; // only track logged in users when the option says so
}

function ctopt_register_impression($id) {
	if (ctopt_track_logged_in()) {
		if(get_post_custom_keys($id)&&in_array('ctopt_impressions',get_post_custom_keys($id))){
			$ctopt_impressions = get_post_meta($id,'ctopt_impressions',true);
		}
		if (!isset($ctopt_impressions)){
			$ctopt_impressions = 0;
		}
		$ctopt_impressions++;
		update_post_meta($id, 'ctopt_impressions', $ctopt_impressions);

		ctopt_db_track($id, 'impression');
	}
}

function ctopt_register_click($id) {
	if (ctopt_track_logged_in()) {
		if(get_post_custom_keys($id)&&in_array('ctopt_clicks',get_post_custom_keys($id))){
			$ctopt_clicks = get_post_meta($id,'ctopt_clicks',true);
		}
		if (!isset($ctopt_clicks)){
			$ctopt_clicks = 0;
		}
		$ctopt_clicks++;
		update_post_meta($id, 'ctopt_clicks', $ctopt_clicks);

		ctopt_db_track($id, 'click');
	}
}

function ctopt_db_track($id, $type) {
  global $wpdb;
  $uid = $_COOKIE['ctopt_uid'];
  $sql = 'INSERT INTO ' . $wpdb->prefix . 'c2o_tracking(post_id, user_id, track_type) VALUES ('
         . esc_sql($id) . ",'" . esc_sql($uid) . "','" . esc_sql($type) . "')";
  $wpdb->query($sql);
}

function ctopt_get_impressions($id) {
	if(get_post_custom_keys($id)&&in_array('ctopt_impressions',get_post_custom_keys($id))){
		return get_post_meta($id,'ctopt_impressions',true);
	} else {
	   return 0;
	}
}
function ctopt_get_clicks($id) {
	if(get_post_custom_keys($id)&&in_array('ctopt_clicks',get_post_custom_keys($id))){
		return get_post_meta($id,'ctopt_clicks',true);
	} else {
	   return 0;
	}
}

?>
