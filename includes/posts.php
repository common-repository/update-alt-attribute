<?php

namespace MauiMarketing\UpdateAltAtribute\Posts;

use MauiMarketing\UpdateAltAtribute\Logs;

function count_posts_for_indexing(){
    
    global $wpdb;
    
    $query = "
        
        SELECT      COUNT( DISTINCT( post.ID ) )
        
        FROM        $wpdb->posts AS post
        
        LEFT JOIN   $wpdb->postmeta AS postmeta
            ON      post.ID = postmeta.post_id
            AND     postmeta.meta_key LIKE 'uaa_image%'
        
        WHERE       post.post_type      IN ( 'post', 'page', 'product' )
            AND     post.post_status    != 'draft'
            AND     postmeta.meta_key   IS NULL
            AND     COALESCE( post.post_content, '' ) != ''
    ";

    // Logs\debug_log( $query, "count_posts_for_indexing-query" );
    
    $count = $wpdb->get_var( $query );
    
    return $count;
}

function get_post_ids_for_indexing( $limit = 50 ){
    
    global $wpdb;
    
    $query = "
        
        SELECT      DISTINCT( post.ID )
        
        FROM        $wpdb->posts AS post
        
        LEFT JOIN   $wpdb->postmeta AS postmeta
            ON      post.ID = postmeta.post_id
            AND     postmeta.meta_key LIKE 'uaa_image%'
        
        WHERE       post.post_type      IN ( 'post', 'page', 'product' )
            AND     post.post_status    != 'draft'
            AND     postmeta.meta_key   IS NULL
            AND     COALESCE( post.post_content, '' ) != ''
            AND     post.ID NOT IN (
                SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'uaa_no_images'
            )
        
        LIMIT       $limit
    ";

    // Logs\debug_log( $query, "get_post_ids_for_indexing-query" );
    
    $ids = $wpdb->get_col( $query );
    
    return $ids;
}

function get_posts_for_indexing( $limit = 50 ){
    
    $ids = get_post_ids_for_indexing( $limit );
    
    if( empty( $ids ) ){
        return [];
    }
    
    // Logs\debug_log( implode( ",", $ids ), "get_posts_for_indexing-ids-implode" );
    // Logs\debug_log( $ids, "get_posts_for_indexing-ids" );
    
    $posts = new \WP_Query([
        "post__in"              => $ids,
        'post_type'             => [ 'post', 'page', 'product' ],
        'post_status'           => 'any',
        "posts_per_page "       => -1,
        'nopaging'              => true,
        'no_found_rows'         => true,
        "ignore_sticky_posts"   => true,
        "orderby"               => 'post__in',
    ]);
    
    return $posts->get_posts();
}

function get_new_indexed_post_meta( $post ){
    
    $wp_uploads_url = wp_upload_dir()["baseurl"];
    // Logs\debug_log( $wp_uploads_url, "get_new_indexed_post_meta-wp_uploads_url" );
    
    
    $images_found = [];
    
    // TODO Won't work correctly with http: sites
    $regex = '/' . str_replace( 'https', 'https?', str_replace( '/', '\/', preg_quote( $wp_uploads_url ) ) ) . '[\/|.|\w|\s|-]*\.(?:jpe?g|gif|png|bmp)/Ui';
    // Logs\debug_log( $regex, "get_new_indexed_post_meta-regex" );
    
    preg_match_all( $regex, $post->post_content, $images_found );
    // Logs\debug_log( $images_found, "get_new_indexed_post_meta-images_found" );
    
    
    $image_urls = array_unique( $images_found[0] );
    
    $meta = [];
    
    if( count( $image_urls ) >= 1 ){
        
        foreach( $image_urls as $index => $image_url ){
            
            $meta[ "uaa_image_" . $index ] = $image_url;
        }
        
    } else {
        
        $meta["uaa_no_images"] = "yes";
    }
    
    return $meta;
}

function index_saved_post( $post_id, $post, $update ){
    
    if( ! in_array( $post->post_type, [ 'post', 'page', 'product' ] ) ){
        return;
    }
    
    global $wpdb;
    
    $query = "
        DELETE FROM $wpdb->postmeta WHERE post_id = $post_id AND meta_key LIKE 'uaa_image%'
    ";
    
    $deleted = $wpdb->query( $query );
    
    
    
    $meta = get_new_indexed_post_meta( $post );
    
    // Logs\debug_log( $meta, "index_saved_post-meta" );
    
    remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
    $update = wp_update_post( [ "ID" => $post->ID, "meta_input" => $meta ], false, false );
    add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
    
}
add_action( 'save_post', __NAMESPACE__ . '\\' . 'index_saved_post', 10, 3 );
