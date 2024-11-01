<?php

namespace MauiMarketing\UpdateAltAtribute\Cron;


function mm_alt_cron_activation(){
    
	if( ! wp_next_scheduled( 'mm_hook_posts_using_attachment' ) ){
        // wp_schedule_event( time(), 'mmevery5minute', 'mm_hook_posts_using_attachment' );
	}
}
// add_action( "uaa_activated", __NAMESPACE__ . '\\' . "mm_alt_cron_activation" );


function mm_alt_cron_deactivate() {
    
	$timestamp = wp_next_scheduled('mm_hook_posts_using_attachment');
    
    if( $timestamp ){
        
        wp_unschedule_event( $timestamp, 'mm_hook_posts_using_attachment' );
    }
    
	wp_clear_scheduled_hook( 'mm_update_info_database' );
	wp_clear_scheduled_hook( 'mm_update_alt_post' );
    
}
add_action( "uaa_deactivated", __NAMESPACE__ . '\\' . "mm_alt_cron_deactivate" );


function mm_cron_add_5minute( $schedules ){

    $schedules['mmevery5minute'] = array(
	    'interval' => 5*60,
	    'display' => __( 'Every 5 Minutes' )
    );
    return $schedules;
}

add_filter( 'cron_schedules', __NAMESPACE__ . '\\' . 'mm_cron_add_5minute' );

