<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

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
    }
    curl_close($ch);

delete_option('widget_cppp_widget');
delete_option('cppp_publisher_id');
delete_option('cppp_paypal');
delete_option('cppp_auto_widget_units');
delete_option('cppp_wizzard_finish_flag');
delete_option('first_start_codecp');
delete_option('cppp_disable_auto_widget');
delete_option('cppp_publisher_id');
