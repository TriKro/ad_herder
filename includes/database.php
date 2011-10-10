<?php
class CallToOptimizeOptions {
  const OPTIONS_NAME = "AdHerderOptions";

  static function get() {
    $options = array(
      'normalWeight' => 2,
      'convertedWeight' => 1,
      'seenWeight' => 1,
      'seenLimit' => 3,
      'trackLoggedIn' => 'true',
      'ajaxWidget' => 'false'
    );
    $dbOptions = get_option(self::OPTIONS_NAME);
    if(!empty($dbOptions)) {
      foreach($dbOptions as $key => $option) {
        $options[$key] = $option;
      }
    }
    update_option(self::OPTIONS_NAME, $options);
    return $options;
  }
}

class CallToOptimizeGateway {
  static function install() {
    global $wpdb;

    ob_start();
  
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "CREATE TABLE " . $table_name . " (
  	    id mediumint(9) NOT NULL AUTO_INCREMENT,
   	    track_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	    post_id int NOT NULL,
	    user_id varchar(50) NULL,
	    track_type varchar(10) NOT NULL,
	    PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $error_message = ob_get_contents();
    if(!empty($error_message)) {
      error_log($error_message);
    }
    ob_end_clean();  
  }

  static function findOldTracking() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "SELECT * FROM " . $table_name . " 
             WHERE track_time < DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH) 
               AND track_type = 'impression'";
    return $wpdb->get_results($sql);
  }

  static function delete($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "DELETE FROM " . $table_name . " WHERE ID = " . $id;
    $wpdb->query($sql); 
  }

  static function deleteForPost($postId) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "DELETE FROM " . $table_name . " WHERE POST_ID = " . $postId;
    $wpdb->query($sql); 
  }

  /* Checks if the user has already clicked or converted on an add */
  static function hasConverted($uid, $callid) {
    if(!preg_match("/^ctopt-uid-/", $uid))
      return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "SELECT * FROM " . $table_name . "
             WHERE user_id = '" . mysql_real_escape_string($uid) . "'
               AND track_type = 'click'
               AND post_id = " . $callid;
    $conversions = $wpdb->get_results($sql);
    return !empty($conversions);
  }

  static function hasSeen($uid, $callid, $times) {
    if(!preg_match("/^ctopt-uid-/", $uid))
      return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'c2o_tracking';
    $sql = "SELECT COUNT(1) >= " . $times . " FROM " . $table_name . "
             WHERE user_id = '" . mysql_real_escape_string($uid) . "'
               AND track_type = 'impression'
               AND post_id = " . $callid;
    return $wpdb->get_var($sql); 
  }

  static function findReports() {
    global $wpdb;
    $reports = $wpdb->get_results("SELECT 
      id, post_title, post_status, 
      IFNULL((select meta_value from wp_postmeta where post_id = id and meta_key = 'ctopt_impressions'),0) as impressions, 
      IFNULL((select meta_value from wp_postmeta where post_id = id and meta_key = 'ctopt_clicks'), 0) as clicks 
      FROM wp_posts p WHERE post_type = 'co-call'");
    foreach($reports as $report) {
      $conversion = 0;
      if($report->impressions != '0') {
        $conversion = ($report->clicks * 100) / $report->impressions;
        $conversion = round($conversion, 2);
      }
      $report->conversion = $conversion;
    }
    return $reports;
  }
}
?>
