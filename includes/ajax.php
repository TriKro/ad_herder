<?php
function adherder_client_scripts() {
	wp_enqueue_script('adherder', plugins_url('/adherder/js/adherder.js'), array('jquery'), ADHERDER_VERSION_NUM);
	wp_localize_script( 'adherder', 'AdHerder', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function adherder_ajax_init() {
	add_action('wp_enqueue_scripts', 'adherder_client_scripts');
	add_action('wp_ajax_nopriv_adherder_track_conversion', 'adherder_track_conversion');
	add_action('wp_ajax_adherder_track_conversion', 'adherder_track_conversion');
	add_action('wp_ajax_nopriv_adherder_track_impression', 'adherder_track_impression');
	add_action('wp_ajax_adherder_track_impression', 'adherder_track_impression');
	add_action('wp_ajax_nopriv_adherder_display_ajax', 'adherder_display_ajax');
	add_action('wp_ajax_adherder_display_ajax', 'adherder_display_ajax');
}

function adherder_track_conversion() {
  $callID = absint($_POST['ad_id']);
  ctopt_register_click($callID);

  $response = json_encode( array( 'ad_id' => $callID, 'success' => true ) );
  header( "Content-Type: application/json" );
  echo $response;
  die();
}

function adherder_track_impression() {
  $callID = absint($_POST['ad_id']);
  ctopt_register_impression($callID);

  $response = json_encode( array( 'ad_id' => $callID, 'success' => true ) );
  header( "Content-Type: application/json" );
  echo $response;
  die(); 
}

function adherder_display_ajax() {
	echo adherder_display();
	die();
}
?>
