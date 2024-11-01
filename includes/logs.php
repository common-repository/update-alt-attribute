<?php

namespace MauiMarketing\UpdateAltAtribute\Logs;

function write_to_log_file( $item ){
    
    if( ! defined('ALT_PLUGIN_DEBUG') || ALT_PLUGIN_DEBUG !== true ){
        return;
    }
    
    // TODO change to path to wp-content/uploads/
    $file_path = UAA_PLUGIN_DIR . "logs/cron_alt.txt";
    $openfile = fopen( $file_path, "a+" ) or die("Unable to open file!");
    
    fwrite( $openfile, $item );
    fclose( $openfile );
}

function debug_log ( $log, $text = "debug_log: ", $delete = false )  {
    
    if( $delete ){
        unlink( WP_CONTENT_DIR . '/debug.log' );
    }
    
	if ( is_array( $log ) || is_object( $log ) ) {
		error_log( $text . PHP_EOL . print_r( $log, true ) . PHP_EOL, 3, WP_CONTENT_DIR . '/debug.log' );
	} else {
		error_log( $text . PHP_EOL . $log . PHP_EOL, 3, WP_CONTENT_DIR . '/debug.log' );
	}
}