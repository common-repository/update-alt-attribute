<?php

namespace MauiMarketing\UpdateAltAtribute\Ajax;

function save_single_image() {
    
    $return = [ "message" => "All good, the image was saved." ];
    
    if( isset($_POST["post_id"]) ){
        $post_id = $_POST['post_id'];
        $post = array(
            'ID'           => $post_id,
            'post_title'   => $_POST['title'],
            'post_content' => $_POST['description'],
            'post_excerpt' => $_POST['caption'],
        );
        
        remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
        $update = wp_update_post( $post );
        add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
        
        if( $update ){
            
            update_post_meta( $post_id, '_wp_attachment_image_alt', $_POST['alt'] );
            
            wp_send_json_success( $return );
            
        } else {
            
            $return["message"] = "For some reason, the image was not saved.";
            wp_send_json_error( $return );
            
        }
    }
    
}
add_action( 'wp_ajax_'        . 'uaa_' . 'save_single_image', __NAMESPACE__ . '\\' . 'save_single_image' );
add_action( 'wp_ajax_nopriv_' . 'uaa_' . 'save_single_image', __NAMESPACE__ . '\\' . 'save_single_image' );

function save_all_images(){
    
    $return = [ "message" => "All good, the images were saved." ];
    if( isset($_POST["data_save"]) ){
        $list_data = $_POST['data_save'];
    
        if( ! empty( $list_data ) ){
            
            foreach( $list_data as $post_id => $data ){
                
                $post = [
                    'ID'            => $post_id,
                    'post_title'    => $data['title'],
                    'post_content'  => $data['description'],
                    'post_excerpt'  => $data['caption'],
                ];
                
                remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
                if( wp_update_post( $post ) ){
                    
                    update_post_meta( $post_id, '_wp_attachment_image_alt', $data['alt'] );
                }
                add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
            }
            
            wp_send_json_success( $return );
            
        } else {
            
            $return["message"] = "The data list was empty, the images were not saved.";
            wp_send_json_error( $return );
            
        }
    }
    
}
add_action( 'wp_ajax_'        . 'uaa_' . 'save_all_images', __NAMESPACE__ . '\\' . 'save_all_images' );
add_action( 'wp_ajax_nopriv_' . 'uaa_' . 'save_all_images', __NAMESPACE__ . '\\' . 'save_all_images' );
