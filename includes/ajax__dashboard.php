<?php

namespace MauiMarketing\UpdateAltAtribute\Ajax;

use MauiMarketing\UpdateAltAtribute\Logs;
use MauiMarketing\UpdateAltAtribute\Posts;


function index_posts(){
    
    // Logs\debug_log( $_POST, "index_posts-_POST" );
    
    $data = $_POST;
    if( isset($_POST["posts_indexed"]) && isset( $_POST["posts_to_index"] ) ){
        $return = [
            "message"           => "Success!",
            "posts_indexed"     => ! empty( $_POST["posts_indexed"] ) ? $_POST["posts_indexed"] : [],
        ];
        
        
        if( empty( $_POST["posts_to_index"] ) || $_POST["posts_to_index"] < 0 ){
            
            $count = Posts\count_posts_for_indexing();
            
            
            if( empty( $count ) || $count < 1 ){
                
                update_option( "uaa_done_indexing", "yes" );
                
                $return["message"] = "All posts are already indexed.";
                wp_send_json_success( $return );
            }
            
            $return["posts_to_index"] = $count;
        }
        
        
        $posts = Posts\get_posts_for_indexing();
        
        if( empty( $posts ) ){
            
            update_option( "uaa_done_indexing", "yes" );
            
            $return["message"] = "All posts are already indexed.";
            wp_send_json_success( $return );
        }
        
        $indexed_posts = [];
        
        foreach( $posts as $post ){
            
            
            $meta = Posts\get_new_indexed_post_meta( $post );
            
            // Logs\debug_log( $meta, "index_posts-meta" );
            
            
            remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
            $update = wp_update_post( [ "ID" => $post->ID, "meta_input" => $meta ], false, false );
            add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
            
            if( $update !== false ){
                
                $indexed_posts[] = $post->ID;
                
            }
            
        }
        
        if( empty( $indexed_posts ) ){
            $return["message"] = "For some reason, no posts were indexed.";
            wp_send_json_error( $return );
        }
        
        
        
        
        // $return["posts_indexed"] = array_unique( array_merge( $return["posts_indexed"], $indexed_posts ) );
        $return["posts_indexed"] = array_merge( $return["posts_indexed"], $indexed_posts );
        
        if( count( $return["posts_indexed"] ) >= $_POST["posts_to_index"] ){
            
            update_option( "uaa_done_indexing", "yes" );
        }
        
        wp_send_json_success( $return );
    }
    
}
add_action( 'wp_ajax_'        . 'uaa_' . 'index_posts', __NAMESPACE__ . '\\' . 'index_posts' );
add_action( 'wp_ajax_nopriv_' . 'uaa_' . 'index_posts', __NAMESPACE__ . '\\' . 'index_posts' );
