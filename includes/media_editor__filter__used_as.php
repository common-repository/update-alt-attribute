<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

use MauiMarketing\UpdateAltAtribute\Logs;

// return;

function load_scripts__used_as(){
    
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
    
	if( $page == 'uaa_media_editor' ){
        
		wp_enqueue_script( 'uaa-media_editor-filter-used_as', UAA_PLUGIN_URL . 'js/media_editor__filter__used_as.js', [ 'uaa-media_editor' ] );
        
	}
    
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\' . 'load_scripts__used_as' );

function display_filter__used_as(){
    $echo = 'MauiMarketing\UpdateAltAtribute\Core\fnc';
    
    $used_as = isset( $_GET['used_as'] ) ? $_GET['used_as'] : "any";
    
    $html = <<<EOF
        <select id="filter__used_as" name="filter__used_as">
            <option value="show_all"            {$echo( $used_as == "show_all"          ? 'selected' : '' )}>Filter off: Used As</option>
            <option value="featured_image"      {$echo( $used_as == "featured_image"    ? 'selected' : '' )}>Featured Image</option>
            <option value="first_image"         {$echo( $used_as == "first_image"       ? 'selected' : '' )}>First Image</option>
            <option value="in_content"          {$echo( $used_as == "in_content"        ? 'selected' : '' )}>In the Content</option>
            <option value="not_used"            {$echo( $used_as == "not_used"          ? 'selected' : '' )}>Not used</option>
            <option value="used"                {$echo( $used_as == "used"              ? 'selected' : '' )}>Used</option>
        </select>
EOF;
    
    
    echo $html;
}
add_action( 'uaa_media_editor_filters', __NAMESPACE__ . '\\' . 'display_filter__used_as' );

function alter_wp_query__used_as( $args ){
    
    $valid_filters = [
        "featured_image",
        "first_image",
        "in_content",
        "not_used",
        "used",
    ];
    
    if( empty( $_GET["used_as"] ) || ! in_array( $_GET["used_as"], $valid_filters ) ){
        return $args;
    }
    
    global $wpdb;
    
    if( $_GET["used_as"] == "featured_image" ){
        
        $query = "
            SELECT      image.ID
            FROM        $wpdb->posts AS image
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND     image.ID IN (
                            SELECT      meta_value
                            FROM        $wpdb->postmeta
                            WHERE       meta_key = '_thumbnail_id'
                        )
        ";
        
        // Logs\debug_log( $query, "filter_by_used_as-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["used_as"] == "first_image" ){
        
        $query = "
            SELECT      image.ID
            FROM        $wpdb->posts AS image
            
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND     EXISTS(
                            SELECT      uaa_image.post_id
                            FROM        $wpdb->postmeta AS uaa_image
                            WHERE       uaa_image.meta_key   = 'uaa_image_0'
                                AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                        )

        ";
        
        // Logs\debug_log( $query, "filter_by_used_as-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["used_as"] == "in_content" ){
        
        $query = "
            SELECT      image.ID
            FROM        $wpdb->posts AS image
            
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND     EXISTS(
                            SELECT      uaa_image.post_id
                            FROM        $wpdb->postmeta AS uaa_image
                            WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                        )

        ";
        
        // Logs\debug_log( $query, "filter_by_used_as-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["used_as"] == "not_used" ){
        
        $query = "
            SELECT      image.ID
            FROM        $wpdb->posts AS image
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND NOT (
                            EXISTS(
                                SELECT      uaa_image.post_id
                                FROM        $wpdb->postmeta AS uaa_image
                                WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                    AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                            )
                        OR  image.ID IN (
                                SELECT      meta_value
                                FROM        $wpdb->postmeta
                                WHERE       meta_key = '_thumbnail_id'
                            )
                        )
        ";
        
        // Logs\debug_log( $query, "filter_by_used_as-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["used_as"] == "used" ){
        
        $query = "
            SELECT      image.ID
            FROM        $wpdb->posts AS image
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND     (
                            EXISTS(
                                SELECT      uaa_image.post_id
                                FROM        $wpdb->postmeta AS uaa_image
                                WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                    AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                            )
                        OR  image.ID IN (
                                SELECT      meta_value
                                FROM        $wpdb->postmeta
                                WHERE       meta_key = '_thumbnail_id'
                            )
                        )
        ";
        
        // Logs\debug_log( $query, "filter_by_used_as-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( empty( $image_ids ) ){
        
        $image_ids = [-1, -2, -3 ];
    }
    
    
    $args["post__in"] = empty( $args["post__in"] ) ? $image_ids : array_values( array_unique( array_intersect( $args["post__in"], $image_ids ) ) );
    
    // Logs\debug_log( $args, "filter_by_used_as-args" );
    
    return $args;
}
add_filter( 'uaa_media_editor_args', __NAMESPACE__ . '\\' . 'alter_wp_query__used_as' );
