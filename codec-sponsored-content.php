<?php
/*
   Plugin Name: CODEC Sponsored Content
   description: Premium monetizing system for quality blogs & publications. Generate revenue by displaying a widget with manually approved branded content.
   Version: 3.0.0
   Author: CODEC Prime Inc.
   Author URI: https://codecprime.com
   License: GPLv2
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('cppp_widget_version', '3.0.0' . date('ymdhis'));
$plugin = plugin_basename( __FILE__ );

//Add link to the plugin settings in the Admin sidebar
function cppp_admin_actions() {
	$plugin = plugin_basename( __FILE__ );
	$plugin =  dirname($plugin);
	$page = "$plugin/admin-page.php";
	$image = plugins_url('images/codec-logo-small.png', __FILE__);
	add_menu_page(__( 'CODEC Settings', ''), 'CODEC', 'manage_options',	$page, '', $image, 6);
}
//deactivation our plugin
register_deactivation_hook( __FILE__, 'cppp_codec_plugin_deactivation' );
function cppp_codec_plugin_deactivation(){
    // do what you need to do when you deactivate the plugin.
    $get_url = get_site_url();
    $get_url = str_replace('https://','',$get_url);
    $get_url = str_replace('http://','',$get_url);
    $url = 'https://admin.codecprime.com/api/wp_publisher/disconnected';
    $data = json_encode(["url" => $get_url]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);

    if ($resp === false) {
        echo 'cURL error: ' . curl_error($ch);
        wp_die();
    }
    curl_close($ch);
    $resp = json_decode($resp);
    print_r($resp);
    if(!empty($resp) && $resp->status == 'disconnected') {
        //disconect function
        update_option('widget_cppp_widget', '');
        update_option('cppp_publisher_id', '');
        update_option('cppp_paypal', '');
        update_option('cppp_auto_widget_units', '');
        update_option('cppp_wizzard_finish_flag', '');
        update_option('first_start_codecp', '');
    }
}

//Add settings button on Plugins page for this plugin
function cppp_add_settings_link($links) {
	$settings_link = '<a href="admin.php?page=codec-sponsored-content/admin-page.php">' . __( 'Settings' ) . '</a>';
	array_push($links, $settings_link);
	return $links;
}
function cppp_alert_msg() {
    $msg = get_option('cppp_message_showed');
    if(!$msg){ ?>
    <script>
    $j = jQuery
    $j().ready(function(){
    $j('.wrap > h2').parent().prev().after('<br><div class="update-nag notice notice-warning inline">CODEC Sponsored Content plugin: <a href="/wp-admin/admin.php?page=codec-sponsored-content/admin-page.php">Please click here to activate your earnings.</a></div>')
    })
    </script>
<?php }
} add_action('admin_head','cppp_alert_msg');

function cppp_if_codec_blank() {
	$current_page = sanitize_post($GLOBALS['wp_the_query']->get_queried_object());
	$res = '';
	if($current_page) {
		$slug = $current_page->post_name;
		if($slug == 'codec-news' || $slug == 'brandscovery-news') {
			$res = get_option('cppp_publisher_id');
		}
	}
	return $res ?: 0;
}

function cppp_fix_head() {
	$us = cppp_if_codec_blank();
	if($us) {
        $js_vue = "//admin.codecprime.com/lib/vue.js";
        $js_router = "//admin.codecprime.com/lib/vue-router.min.js";
        $js_load_content = '//admin.codecprime.com/get_code_wp/'.$us.'/vert';
        wp_enqueue_script('codec-vue', $js_vue, ['jquery'], null, true);
        wp_enqueue_script('codec-router', $js_router, ['jquery'], null, true);
        wp_enqueue_script('codec-load-content', $js_load_content, ['jquery'], null, true); ?>
<?php	}
}
if(!is_admin()){
	add_filter('wp_head', 'cppp_fix_head');
}

function add_async_attribute_codec_my_vue($tag, $handle) {
    if ( 'codec-vue' !== $handle || 'codec-router'!== $handle )
        return $tag;
    return str_replace( ' src', ' async="async" src', $tag );
}
add_filter('script_loader_tag', 'add_async_attribute_codec_my_vue', 10, 2);

function add_defer_attribute_codec($tag, $handle) {
    if ( 'codec-load-content' !== $handle )
	    return $tag;

    return str_replace( ' src', ' defer="defer" src', $tag );
}
add_filter('script_loader_tag', 'add_defer_attribute_codec', 10, 2);

//Onload filters and actions
add_filter("plugin_action_links_$plugin", 'cppp_add_settings_link');
add_action('admin_menu', 'cppp_admin_actions');

//Ajax actions
add_action('wp_ajax_cppp_page_status', 'cppp_page_status');
add_action('wp_ajax_cppp_create_page', 'cppp_create_page');
add_action('wp_ajax_cppp_paypal', 'cppp_paypal');
add_action('wp_ajax_cppp_empty_widgets', 'cppp_empty_widgets');
add_action('wp_ajax_cppp_site_url', 'cppp_site_url');
add_action('wp_ajax_cppp_auto_widget_enable', 'cppp_auto_widget_enable');
add_action('wp_ajax_cppp_wizzard_finish', 'cppp_wizzard_finish');
add_action('wp_ajax_cppp_disable_auto_widget', 'cppp_disable_auto_widget');

//Ajax functions
function cppp_auto_widget_enable(){
    $units = $_POST['units'];
    if(isset($units)){
        update_option('cppp_auto_widget_units', $units);
        $us = get_option('cppp_publisher_id');
        if($us) {
            $units = str_replace('u','', $units);
            $url = 'https://admin.codecprime.com/update_wp_widget_count/' . $us . '/' . $units . '/horizontal';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);

            if ($resp === false) {
                echo 'cURL error: ' . curl_error($ch);
                wp_die();
            }
            curl_close($ch);
        }

    }
    echo $units;
    wp_die();
}
//Ajax finctions
function cppp_wizzard_finish(){
    $cppp_widget = get_option('widget_cppp_widget');
    $cppp_auto_widget = get_option('cppp_auto_widget_units');
    $cppp_paypall = get_option('cppp_paypal');
    $us = get_option('cppp_publisher_id');
    if ( !empty($us) && !empty($cppp_auto_widget) && !empty($cppp_paypall) ){
        update_option('cppp_wizzard_finish_flag', true);
        echo 'register is done!';
    } else if(!empty($cppp_widget) && count($cppp_widget)!=1 && !empty($us) && !empty($cppp_paypall) ){
        update_option('cppp_wizzard_finish_flag', true);
        echo 'register is done!';
    }
    wp_die();
}
//Ajax func disable auto widget
function cppp_disable_auto_widget(){
    $dis_widget = (bool) strip_tags($_POST['dis_widget']);
    if($dis_widget){
        update_option('cppp_disable_auto_widget', 1);
        echo 1;
    }else{
        delete_option('cppp_disable_auto_widget');
        echo 0;
    }
    wp_die();
}
//Ajax funct
function cppp_site_url() {
    $status = $_POST['status'];
	$get_url = get_site_url();
	$get_url = str_replace('https://','',$get_url);
	$get_url = str_replace('http://','',$get_url);
	if($status=='connected') {
        update_option('cppp_message_showed', true);
    }
	$auto_widget = get_option('cppp_auto_widget_units');
	$paypall = get_option('cppp_paypal');
	if($auto_widget && empty($paypall)){
	    echo $get_url.', auto_widget_true';
    }elseif($auto_widget && $paypall ){
        echo $get_url.', auto_widget_true, '.$paypall;
    } else {
        echo $get_url;
    }
	wp_die();
}
//Button disckonect cliked. This is socks=)
//TODO: Changed it, We used only deactiv. and remove the plugin
function cppp_empty_widgets() {
	update_option('widget_cppp_widget', '');
	update_option('cppp_publisher_id', '');
    update_option('cppp_paypal', '');
	
	//Unpublish the codec-news page
	$page = get_page_by_path('brandscovery-news');
	if ($page) {
		$post_id = $page->ID;
		$my_post = ['ID' => $post_id, 'post_status'   =>  'draft'];
		wp_update_post($my_post);
	} 
	$page = get_page_by_path('codec-news');
	if ($page) {
		$post_id = $page->ID;
		$my_post = ['ID' => $post_id, 'post_status'   =>  'draft'];
		wp_update_post($my_post);
	} 
	
	echo "Updated";
	wp_die();
}

function cppp_paypal() {
	
	$val = $_POST['val'] ?: '';
	$val = sanitize_text_field($val);
	update_option('cppp_paypal', $val);
	//echo "Updated";
    $get_url = get_site_url();
    $get_url = str_replace('https://','',$get_url);
    $get_url = str_replace('http://','',$get_url);
    echo $get_url;
	wp_die();
}

function cppp_page_status() {
	$pub = $_POST['pub'] ?: '';
	$pub = sanitize_text_field($pub);
	
	$page = get_page_by_path('brandscovery-news');
	$page2 = get_page_by_path('codec-news');
	
	if(!$page && !$page2) {
		echo 'no';
	}
	else {
		$check = get_option('cppp_publisher_id');
		if(!$check) {
			if($pub) {
				update_option('cppp_publisher_id',$pub);
			}
		}
		echo 'yes';
	}
	wp_die();
}

function cppp_create_page() {
	
	$page_code = "<div class=\"codec_content\">&nbsp;</div>";

	$pub = $_POST['pub'] ?: '';
	
	if($pub) {
		$pub = sanitize_text_field($pub);
		update_option('cppp_publisher_id',$pub);
	}
	
	$blog_page = [
		'post_type' => 'page',
		'post_title' => 'Codec News',
		'post_content' => $page_code,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_slug' => 'codec-news'
	];
	
	$slug = 'codec-news';
	$query = new WP_Query(['name' => $slug, 'post_type' => 'page']);
	if ($query->post_count == 0) {
		$blog_page_id = wp_insert_post($blog_page);
		echo 'created';
	}
	else {
		$post_id = $query->post->ID;
		$post_content = $query->post->post_content;
		$my_post = array(
			'ID'           => $post_id,
			'post_content' => $page_code,
			'post_status' => 'publish'
		);
		wp_update_post($my_post);
		echo 'updated';
	}
	
	wp_die();
}

// sidbar
if (function_exists('register_sidebar')){
    register_sidebar( array(
        'name'          => 'CODEC After content (horizontal)',
        'id'            => 'widget_codec_horizont_3',
        'description'   => 'For better monetization, place the CODEC widget here.',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '<div class="clear"></div></aside>',
        'before_title'  => '<span class="widget-title">',
        'after_title'   => '</span>',
    ) );
}
/*function cppp_fix_blank_page($content) {
	$us = cppp_if_codec_blank();
	if($us) {
		global $wp_registered_sidebars;
		foreach($wp_registered_sidebars as $sidebar) {
			$id = $sidebar['id'];
			unregister_sidebar($id);
		}
		$content = '<div class="codec_content"></div>';
	}
	return $content;
}*/
//add_filter('the_content', 'cppp_fix_blank_page');

function cppp_fix_blank_post($content) {
	if(is_single()){
        $cppp_widget = get_option('widget_cppp_widget');
        $cppp_paypall = get_option('cppp_paypal');
	    if ( is_active_sidebar( 'widget_codec_horizont_3' ) && !empty($cppp_widget) && count($cppp_widget)!=1){
	        ob_start();
            dynamic_sidebar( 'widget_codec_horizont_3' );
            $dd = ob_get_contents();
            ob_clean();
            return $content.'<br>'.$dd;
        } else if( (empty($cppp_widget) || count($cppp_widget)==1 ) && !empty($cppp_paypall) ){ //if empty them we set auto widget
            $us = get_option('cppp_publisher_id');
            $flag_disable_auto_widget = get_option('cppp_disable_auto_widget');
            if($us && !$flag_disable_auto_widget) {
                $default_styles = "//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-grid.min.css";
                wp_enqueue_style('cppp_default_widgets_styles', $default_styles, [], cppp_widget_version);
                $js_vue = "//admin.codecprime.com/lib/vue.js";
                $js_router = "//admin.codecprime.com/lib/vue-router.min.js";
                wp_enqueue_script('codec-vue', $js_vue, ['jquery'], null, true);
                wp_enqueue_script('codec-router', $js_router, ['jquery'], null, true);
                $output = '<h2>Trending now:</h2><script async src="//admin.codecprime.com/get_code_wp/'.$us.'/hori"></script><div id="app_codec_widget"></div>';
                return $content.'<br>'.$output;
            }
        }
    }
	return $content;
}
add_filter('the_content', 'cppp_fix_blank_post', 100);

//Widget code
include 'widget.php';