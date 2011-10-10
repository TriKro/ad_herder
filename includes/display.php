<?php
function adherder_display($before_widget, $after_widget, $before_title, $after_title, $show_title = FALSE){
  //override display via request parameter
  $qs    = $_SERVER['REQUEST_URI'];
  $qsPos = strpos($qs, 'adherder_ad');
  if(!(false === $qsPos)) {
    $cocall_id   = $_GET['adherder_ad'];
    $cta         = get_post($cocall_id);
    $title       = $cta->post_title; 
    $cta_content = $cta->post_content;
  }
  if(!$cta) {
    //Get all calls to action
    $args    = array('post_type' => 'co-call', 
                     'post_status' => 'publish',
                     'numberposts' => -1);
    $ctas    = get_posts($args);
    $options = CallToOptimizeOptions::get();

    if(count($ctas)>0){
      $uid = $_COOKIE['ctopt_uid'];
      $weights = array();
      foreach($ctas as $cta) {
        $converted = CallToOptimizeGateway::hasConverted($uid, $cta->ID);
        if($converted) {
          $weights[] = $options['convertedWeight'];
        } else {
          $seen = CallToOptimizeGateway::hasSeen($uid, $cta->ID, $options['seenLimit']);
          if($seen) {
            $weights[] = $options['seenWeight'];
          } else {
            $weights[] = $options['normalWeight'];
          }
        }
      }

      $num = mt_rand(0, array_sum($weights));
      $i = 0; $n = 0;
      while($i < count($ctas)) {
        $n += $weights[$i];
        if($n >= $num) {
          break;
        }
        $i++;
      }
      $cta = $ctas[$i];
      // http://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query
      setup_postdata($cta);
		
      $cocall_id   = $cta->ID; 
      $title       = $cta->post_title; 
      $cta_content = $cta->post_content;
    } else {
      // no calls yet
      $cocall_id   = -1;
      $title       = "No Ads defined";
      $cta_content = "You still need to define some Ads before they can be displayed.";
    }
  }

	$page      = get_option('siteurl');
	$page      = get_page_link();
	$symbol    = (preg_match('/\?/', $page)) ? '&' : '?';
	$track_url = $page . $symbol . 'ctopt_track=' . $cocall_id;
	
	$content = "";
	$content .= $before_widget . '<div class="ctopt ctoptid-' . $cocall_id . '">';
        if($show_title) {
		$content .= $before_title . $title . $after_title;
	}
	$content .= $cta_content;
    $content .= $after_widget;
	
	return $content;
}

//=============================================
// Create 'Call to Action' Widget
//=============================================
class CtoptWidget extends WP_Widget {
	
    /** constructor */
    function CtoptWidget() {
        parent::WP_Widget(false, $name = 'AdHerder widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
		extract( $args );
		$options = CallToOptimizeOptions::get();
		if($options['ajaxWidget'] == 'true') {
			echo 'ajax over here';
		} else {
			echo adherder_display($before_widget, $after_widget, $before_title, $after_title);   
		}
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		
    }

} 

?>
