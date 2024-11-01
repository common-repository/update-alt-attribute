<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

use MauiMarketing\UpdateAltAtribute\Logs;

// return;

function alter_wp_query__search_titles( $args ){
    
    if( empty( $_GET["search_titles"] ) ){
        return $args;
    }
    
    global $wpdb;
    
    $query = "
        SELECT      image.ID
        FROM        $wpdb->posts AS image
        
        WHERE       image.post_type       = 'attachment'
            AND     image.post_status     = 'inherit'
            AND     image.post_mime_type  LIKE 'image%'
            AND     EXISTS(
                        SELECT      post.ID
                        
                        FROM        $wpdb->posts AS post
                        
                        LEFT JOIN   $wpdb->postmeta AS uaa_image
                            ON      post.ID = uaa_image.post_id
                            
                        WHERE       post.post_title      LIKE %s
                            AND     (
                                        (
                                            uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                                        AND
                                            uaa_image.meta_key LIKE 'uaa_image%'
                                        )
                                    OR
                                        (
                                            uaa_image.meta_key = '_thumbnail_id'
                                        AND
                                            uaa_image.meta_value = image.ID
                                        )
                                    )
                    )
    ";
    
    // Logs\debug_log( $query, "alter_wp_query__search_titles-query" );
    
    $image_ids = $wpdb->get_col( $wpdb->prepare( $query, '%%' . $_GET["search_titles"] . '%%' ) );
    
    
    if( empty( $image_ids ) ){
        
        $image_ids = [-1, -2, -3 ];
    }
    
    
    $args["post__in"] = empty( $args["post__in"] ) ? $image_ids : array_values( array_unique( array_intersect( $args["post__in"], $image_ids ) ) );
    
    // Logs\debug_log( $args, "alter_wp_query__search_titles-args" );
    
    return $args;
}
add_filter( 'uaa_media_editor_args', __NAMESPACE__ . '\\' . 'alter_wp_query__search_titles' );
