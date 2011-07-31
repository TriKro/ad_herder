<?php
function ctopt_install() {
  global $wpdb;

  ob_start();
  
  $table_name = $wpdb->prefix . 'c2o_tracking';
  $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  track_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  post_id int NOT NULL,
	  user_id varchar(50) NOT NULL,
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
?>
