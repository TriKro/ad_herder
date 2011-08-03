<?php
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
