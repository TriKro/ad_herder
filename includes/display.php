<?php
function adherder_display(){
  //override display via request parameter
  $qs    = $_SERVER['REQUEST_URI'];
  $qsPos = strpos($qs, 'adherder_ad');
  if(!(false === $qsPos)) {
    $cocall_id   = $_GET['adherder_ad'];
    $cta         = get_post($cocall_id);
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
      $cta_content = $cta->post_content;
		} else {
			// no calls yet
			$cocall_id   = -1;
			$cta_content = "You still need to define some Ads before they can be displayed.";
		}
	}

	return '<div class="ctopt ctoptid-' . $cocall_id . '">' . $cta_content . '</div>';
}

//=============================================
// Create 'Call to Action' Widget
//=============================================
class Adherder_Widget extends WP_Widget {
	
    /** constructor */
    function Adherder_Widget() {
        parent::WP_Widget(false, $name = 'AdHerder widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
		extract( $args );
		$options = CallToOptimizeOptions::get();
		echo $before_widget;
		if($options['ajaxWidget'] == 'true') {
			echo '<div class="adherder_placeholder">loading...</div>';
		} else {
			echo adherder_display();
		}
		echo $after_widget;
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
