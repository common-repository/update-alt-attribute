<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

use MauiMarketing\UpdateAltAtribute\Logs;

// return;

function alter_wp_query__search_attributes( $args ){
    
    if( empty( $_GET["search_attributes"] ) ){
        return $args;
    }
    
    global $wpdb;
    
    $query = "
        SELECT      image.ID
        
        FROM        $wpdb->posts AS image
        
        LEFT JOIN   $wpdb->postmeta AS image_meta
            ON      image.ID              = image_meta.post_id
            AND     image_meta.meta_key   = '_wp_attachment_image_alt'
            
        WHERE       image.post_type       = 'attachment'
            AND     image.post_status     = 'inherit'
            AND     image.post_mime_type  LIKE 'image%'
            AND     (
                        image.post_title        LIKE %s
                    OR
                        image.post_content      LIKE %s
                    OR
                        image.post_excerpt      LIKE %s
                    OR
                        image_meta.meta_value   LIKE %s
                    )
    ";
    
    // Logs\debug_log( $query, "alter_wp_query__search_attributes-query" );
    
    $image_ids = $wpdb->get_col( $wpdb->prepare( $query, [
        '%%' . $_GET["search_attributes"] . '%%',
        '%%' . $_GET["search_attributes"] . '%%',
        '%%' . $_GET["search_attributes"] . '%%',
        '%%' . $_GET["search_attributes"] . '%%',
    ]));
    
    
    if( empty( $image_ids ) ){
        
        $image_ids = [-1, -2, -3 ];
    }
    
    
    $args["post__in"] = empty( $args["post__in"] ) ? $image_ids : array_values( array_unique( array_intersect( $args["post__in"], $image_ids ) ) );
    
    // Logs\debug_log( $args, "alter_wp_query__search_attributes-args" );
    
    return $args;
}
add_filter( 'uaa_media_editor_args', __NAMESPACE__ . '\\' . 'alter_wp_query__search_attributes' );
