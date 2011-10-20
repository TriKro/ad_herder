<?php
/*
Copyright 2011 Tristan Kromer, Peter Backx (email : tristan@grasshopperherder.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

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
	register_post_type('adherder_ad',$args);
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

		adherder_database_track($id, 'impression');
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

		adherder_database_track($id, 'click');
	}
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
