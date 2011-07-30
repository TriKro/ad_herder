<?php

function ctopt_track() {
	if ($qs = $_SERVER['REQUEST_URI']) {
		$pos = strpos($qs, 'ctopt_track');
		if (!(false === $pos)) {
            //TODO get uid cookie value & register per user click 
            $cocall_id = $_GET['ctopt_track'];
    		ctopt_register_click($cocall_id);
			exit(1);
		}
	} 
}


function ctopt_register_impression($id) {
	if (!is_admin()) {
		if(get_post_custom_keys($id)&&in_array('ctopt_impressions',get_post_custom_keys($id))){
			$ctopt_impressions = get_post_meta($id,'ctopt_impressions',true);
		}
		if (!isset($ctopt_impressions)){
			$ctopt_impressions = 0;
		}
		$ctopt_impressions++;
		update_post_meta($id, 'ctopt_impressions', $ctopt_impressions);
	}
}

function ctopt_register_click($id) {
	if (!is_admin()) {
		if(get_post_custom_keys($id)&&in_array('ctopt_clicks',get_post_custom_keys($id))){
			$ctopt_clicks = get_post_meta($id,'ctopt_clicks',true);
		}
		if (!isset($ctopt_clicks)){
			$ctopt_clicks = 0;
		}
		$ctopt_clicks++;
		update_post_meta($id, 'ctopt_clicks', $ctopt_clicks);
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
