<?php
function ctopt_track_logged_in() {
  if(!is_user_logged_in()) {
    return true; // always track users that are not logged in
  }
  $options = CallToOptimizeOptions::get();
  return $options['trackLoggedIn'] == 'true'; // only track logged in users when the option says so
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
         . $id . ",'" . $uid . "','" . $type . "')";
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
