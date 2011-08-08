<?php
function ctopt_display($before_widget, $after_widget, $before_title, $after_title, $show_title = FALSE){
        //Get all calls to action
	$args = array('post_type' => 'co-call');
	$ctas = get_posts($args);

	if(count($ctas)>0){
		$uid = $_COOKIE['ctopt_uid'];
		$weights = array();
		foreach($ctas as $cta) {
        		$converted = CallToOptimizeGateway::hasConverted($uid, $cta->ID);
                        if($converted) {
				$weights[] = 1;
			} else {
				$weights[] = 2;
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

		$cta     = $ctas[$i];

		// http://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query
		setup_postdata($cta);
		
		$cocall_id = $cta->ID; 
		$title = $cta->post_title; 
		$cta_content = $cta->post_content;
	} else {
          // no calls yet
          $cocall_id = -1;
          $title = "No calls to action defined";
          $cta_content = "You still need to define calls before they can be displayed.";
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
	$content .= '</div><script type="text/javascript">jQuery(document).ready( function() {  jQuery(".ctopt a").click(function(e) { ctopt_track("' . $track_url . '"); }); });</script>';
    $content .= $after_widget;
	
	ctopt_register_impression($cocall_id);
	
	return $content;
}

//=============================================
// Create 'Call to Action' Widget
//=============================================
class CtoptWidget extends WP_Widget {
	
    /** constructor */
    function CtoptWidget() {
        parent::WP_Widget(false, $name = 'Call To Optimize widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {	
		extract( $args );
        echo ctopt_display($before_widget, $after_widget, $before_title, $after_title);   
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
