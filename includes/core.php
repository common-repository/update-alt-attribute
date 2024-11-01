<?php

namespace MauiMarketing\UpdateAltAtribute\Core;

use MauiMarketing\UpdateAltAtribute\Charts;

function load_admin_scripts(){
    
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
    
    $uaa_object = [
        'ajax_url' => admin_url('admin-ajax.php'),
    ];
    
	if( $page == 'uaa_dashboard' ){
        
		enqueue_CSS( 'uaa-general', 'css/_general.css' );
        
		enqueue_CSS( 'uaa-dashboard', 'css/dashboard.css' );
		enqueue_JS(  'uaa-dashboard', 'js/dashboard.js'   );
		wp_localize_script('uaa-dashboard', 'uaa_object', $uaa_object );
        
        $done_indexing = get_option( "uaa_done_indexing", "no" );
        
        if( $done_indexing === "yes" ){
            
            $chart_object = Charts\get_dashboard_chart_data();
            
            wp_enqueue_script( 'uaa-chart_js', 'https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js' );
            enqueue_JS( 'uaa-chart', 'js/dashboard_chart.js', [ 'uaa-chart_js' ]    );
            wp_localize_script('uaa-chart', 'uaa_chart_object', $chart_object );
        }
	}
        
	if( $page == 'uaa_automatic_updaters' ){
        
		enqueue_CSS( 'uaa-general', 'css/_general.css' );
        
		enqueue_CSS( 'uaa-automatic_updaters', 'css/automatic_updaters.css' );
		enqueue_JS(  'uaa-automatic_updaters', 'js/automatic_updaters.js'   );
		wp_localize_script('uaa-automatic_updaters', 'uaa_object', $uaa_object );
	}
    
	if( $page == 'uaa_media_editor' ){
        
		enqueue_CSS( 'uaa-general', 'css/_general.css' );
        
        $uaa_object["loader"] = admin_url('images/wpspin_light.gif');
        
		enqueue_CSS( 'uaa-media_editor', 'css/media_editor.css' );
		enqueue_JS(  'uaa-media_editor', 'js/media_editor.js'   );
		wp_localize_script('uaa-media_editor', 'uaa_object', $uaa_object );
	}
    
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\' . 'load_admin_scripts' );

function load_front_scripts(){
    
    // This, if loaded, will run on every (front) page and try to populate empty alt tags with file name
	// wp_enqueue_script( 'uaa-front', UAA_PLUGIN_URL . 'js/front.js', array('jquery') );
    
}
// add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\' . 'load_front_scripts' );

function add_menu_items(){
    
	if( ! empty( $GLOBALS['admin_page_hooks']['uaa_dashboard'] ) ){
        return;
    }
    
    $done_indexing = get_option( "uaa_done_indexing", "no" );
    
	
	add_menu_page(
        'Update Images',
        'Update Images',
        'manage_options',
        'uaa_dashboard',
        __NAMESPACE__ . '\\' . 'load_page_template',
        UAA_PLUGIN_URL . 'assets/icon.png',
        11
    );
    
	add_submenu_page(
		'uaa_dashboard',
		__('Dashboard', 'update-alt-attribute' ),
		__('Dashboard', 'update-alt-attribute' ),
		'manage_options',
		'uaa_dashboard',
		__NAMESPACE__ . '\\' . 'load_page_template'
	);
    
    if( $done_indexing === "yes" ){
    
        add_submenu_page(
            'uaa_dashboard',
            __('Automatic Updaters', 'update-alt-attribute' ),
            __('Automatic Updaters', 'update-alt-attribute' ),
            'manage_options',
            'uaa_automatic_updaters',
            __NAMESPACE__ . '\\' . 'load_page_template'
        );
        add_submenu_page(
            'uaa_dashboard',
            __('Media Editor', 'update-alt-attribute' ),
            __('Media Editor', 'update-alt-attribute' ),
            'manage_options',
            'uaa_media_editor',
            __NAMESPACE__ . '\\' . 'load_page_template'
        );
    
    }
}
add_action( 'admin_menu', __NAMESPACE__ . '\\' . 'add_menu_items' );

function output_indexing_notice(){
    
    $done_indexing = get_option( "uaa_done_indexing", "no" );
    
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
    
    if( $done_indexing === "yes" || in_array( $page, [ 'uaa_dashboard' ] ) ){
        return;
    }
    
    echo '<div class="notice notice-warning">';
        echo '<p>';
        echo '<b>Update Alt Attributes: </b>';
        echo 'Before using the plugin you need to run the indexing of all the posts. ';
        echo 'You can do so on <a href="' . admin_url("admin.php?page=uaa_dashboard") . '">the Dashboard page</a>.';
        echo '</p>';
    echo '</div>';
    
}
add_action( 'admin_notices', __NAMESPACE__ . '\\' . 'output_indexing_notice' );

function load_page_template(){
    
    global $plugin_page;
    
    $slug = substr( $plugin_page, 4 );// remove the 'uaa_' prefix
    
    load_template_or_tab( "page__", $slug );
    
}

function load_template_or_tab( $prefix = 'page__', $slug = 'dashboard' ){
    
    $template_path = get_template_location( $prefix, $slug );
    
    if( $template_path ){
        
        include( $template_path );
    }
    
}

function get_template_location( $prefix = 'page__', $slug = 'dashboard' ){
    
    $folders_to_check = [
        UAA_PLUGIN_DIR . "templates/",
    ];
    
    $folders_to_check = apply_filters( "uaa_template_folders", $folders_to_check );
    
    foreach( $folders_to_check as $folder_path ){
        
        if( file_exists( $folder_path . $prefix . $slug . ".php" ) ){
            
            return $folder_path . $prefix . $slug . ".php";
        }
        
    }
    
    return false;
}

function fnc( $data ){
    
    return $data;
}


function enqueue_JS(  $scriptname, $filename, $dependency = array(), $is_footer = true ){

    $js_ver  = date("ymd-Gis", filemtime( 	UAA_PLUGIN_DIR . $filename  ));
	wp_enqueue_script( 	$scriptname, 		UAA_PLUGIN_URL . $filename, $dependency, $js_ver, $is_footer );
    
}

function enqueue_CSS( $scriptname, $filename, $dependency = array() ){

    $css_ver = date("ymd-Gis", filemtime( 	UAA_PLUGIN_DIR . $filename ));
	wp_register_style( 	$scriptname, 		UAA_PLUGIN_URL . $filename, $dependency, $css_ver );
    wp_enqueue_style ( 	$scriptname );
    
}

