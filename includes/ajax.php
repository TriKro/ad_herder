<?php
function adherder_client_scripts() {
  wp_enqueue_script('ctopt', plugins_url('/adherder/js/ctopt.js'), array('jquery'), ADHERDER_VERSION_NUM);
  wp_localize_script( 'ctopt', 'AdHerder', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function ctopt_ajax_register_track() {
  $callID = $_POST['callID'];
  ctopt_register_click($callID);

  $response = json_encode( array( 'callID' => $callID, 'success' => true ) );
  header( "Content-Type: application/json" );
  echo $response;
  exit;
}

function ctopt_ajax_register_impression() {
  $callID = $_POST['callID'];
  ctopt_register_impression($callID);

  $response = json_encode( array( 'callID' => $callID, 'success' => true ) );
  header( "Content-Type: application/json" );
  echo $response;
  exit; 
}
?>
