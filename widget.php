<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Register and load the widget
function cppp_widget_load_widget() {
	$status = get_option('cppp_publisher_id');
	if($status)
    register_widget('cppp_widget');
}
add_action('widgets_init', 'cppp_widget_load_widget');

// Creating the widget 
class cppp_widget extends WP_Widget {
 
function __construct() {
	parent::__construct(
	// Base ID of the widget
	'cppp_widget', 
	// Widget name will appear in UI
	__('CODEC Monetizing', 'cppp_widget_domain'), 
	// Widget description
	array( 'description' => __('CODEC Widget', 'cppp_widget_domain'), ) 
	);
}

// Creating widget front-end
public function widget( $args, $instance ) {
	$us = get_option('cppp_publisher_id');
	if($us && !is_admin()){
		$js_vue = "//admin.codecprime.com/lib/vue.js";
        $js_router = "//admin.codecprime.com/lib/vue-router.min.js";
		wp_enqueue_script('codec-vue', $js_vue, ['jquery'], null, true);
		wp_enqueue_script('codec-router', $js_router, ['jquery'], null, true);

        $default_styles = "//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-grid.min.css";
		wp_enqueue_style('cppp_default_widgets_styles', $default_styles, [], cppp_widget_version);

		//$custom_styles = "https://codecprime.com/partner/widgets/$us/widget.css";
		//wp_enqueue_style('cppp_user_widgets_styles', $custom_styles, [], cppp_widget_version);
		//$output = "<div id='codec_result_$us"."_$wid_id'></div>";
        $type = isset($instance['widget_type']) ? $instance['widget_type'] : 0;
        if($type=='vertical'){
            $output = '<h2>Trending now:</h2><script async src="//admin.codecprime.com/get_code_wp/'.$us.'/vert"></script><div id="app_codec_widget_vert"></div>';
        }else if($type=='horizontal'){
            $output = '<h2>Trending now:</h2><script async src="//admin.codecprime.com/get_code_wp/'.$us.'/hori"></script><div id="app_codec_widget"></div>';
        }
	} else {
		$output = '';
	}
	echo __($output, 'cppp_widget_domain' );
}
         
// Widget Backend 
public function form($instance) {
	
	//$js = plugins_url("js/codec-widgets-page.js", __FILE__);
	//wp_enqueue_script('Codec api script', $js, array ('jquery'), cppp_widget_version, true);
	
	if(isset($instance['title'])) {
		$title = $instance['title'];
	}
	else {
		$title = __('Vertical (3)', 'cppp_widget_domain');
	}

	if(isset($instance['widget_type'])) {
		$widget_type = $instance['widget_type'];
	}
	else {
		$widget_type = 'vertical';
	}

	$widget_types = ['horizontal','vertical'];
	
	if(!isset($instance['widget_posts'])) {
		$instance['widget_posts'] = [3,6];
	}
	if(!isset($instance['widget_post'])) {
		$instance['widget_post'] = 3;
	}

	// Widget admin form
	?>

	<p>
	<label style='display:none' for="<?php echo $this->get_field_id('title'); ?>"><?php _e('<b>Title:</b>'); ?></label> 
	<input style='display:none' class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p><b>Widget type:</b><br>
	<?php
	$name = $this->get_field_name( 'widget_type' );
	echo "<div class='cppp_widget_types'>";
	foreach($widget_types as $key=>$widg) {
		$widg_name = ucfirst($widg);
		$checked = '';
		if($widget_type == $widg) $checked = 'checked';
		echo "<label><input  autocomplete='off' type='radio' id='codec$key' name='$name' value='$widg' $checked/>$widg_name</label><br>";
	}
	echo "</div>";
	?>
	</p>

	<?php 
	$posts_selected = $instance['widget_post'] ?: '';
	$name_sel = $this->get_field_name('widget_post');
	echo "<p><b>Number of posts</b><br><select class='widget_post' name='$name_sel'>";
	foreach($instance['widget_posts'] as $num) {
		$selected = '';
		if($num == $posts_selected) $selected = 'selected';
		echo "<option value='$num' $selected>$num</option>";
	}
	echo "</select></p>";

}
     
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['widget_type'] = (!empty($new_instance['widget_type'])) ? strip_tags($new_instance['widget_type']) : '';
        $instance['widget_post'] = (!empty($new_instance['widget_post'])) ? strip_tags($new_instance['widget_post']) : '3';

        $title = $instance['widget_type'] . " (" . $instance['widget_post'] . ")";
        $instance['title'] = ucfirst($title);

        if($new_instance['widget_post'] == 'Select') $instance['widget_post'] = 3;

        $type = $instance['widget_type'];
        if($type == 'horizontal') {
            $instance['widget_posts'] = [3,6];
        }
        if($type == 'vertical') {
            $instance['widget_posts'] = [3,4,6];
        }
        //if save count update widget
        if(!empty($instance['widget_post'])){
            $count_post_old = $old_instance['widget_post'];
            $count_post = $instance['widget_post'];
            $us = get_option('cppp_publisher_id');
            if(!empty($us) && !empty($type)){
                if($count_post_old!=$count_post) {
                    $url = 'https://admin.codecprime.com/update_wp_widget_count/' . $us . '/' . $count_post . '/' . $type;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $resp = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }
        return $instance;
    }
} // Class cppp_widget ends here
