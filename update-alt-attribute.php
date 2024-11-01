<?php
/*
  Plugin Name:  Update ALT Attribute
  Plugin URI:   https://mauimarketing.com/
  Description:  This plugin updates the alt attribute for all images that have null alt attribute text with the attached post/page title or the file name.
  Version:      2.4.6
  Author:       Maui Marketing
  Author URI:   https://mauimarketing.com/
  Text Domain:  update-alt-attribute
 */

define( 'UAA_VERSION', '2.4.6' );

define( 'UAA_PLUGIN_URL', plugin_dir_url(  __FILE__ ) );
define( 'UAA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require __DIR__ . '/vendor/autoload.php';

$uaa_appsero = new Appsero\Client( '5341b10d-0aff-4afe-b0c7-47829af9ea8b', 'Update Image Tag Alt Attribute', __FILE__ );
$uaa_appsero->insights()->init();


foreach( glob( plugin_dir_path( __FILE__ ) . 'includes/' . "*.php" ) as $file ){   
    require_once( "$file" ); 
}


function uaa_plugin_activated(){
    
    do_action("uaa_activated");
}
register_activation_hook( __FILE__, 'uaa_plugin_activated');

function uaa_plugin_deactivated(){
    
    do_action("uaa_deactivated");
}
register_deactivation_hook( __FILE__, 'uaa_plugin_deactivated' );
