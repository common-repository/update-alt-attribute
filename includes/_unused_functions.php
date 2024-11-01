<?php

function get_posts_by_attachment_id( $attachment_id ){
    
    $count = 0;
    $used_as_thumbnail = array();
    $posts = array();

    if( wp_attachment_is_image( $attachment_id ) ){
        
        $args = array(
            'meta_key'          => '_thumbnail_id',
            'meta_value'        => $attachment_id,
            'post_type'         => 'any',
            'fields'            => 'ids',
            'no_found_rows'     => true,
            'posts_per_page'    => -1,
        );
        
        $thumbnail_query   = new \WP_Query( $args );
        $used_as_thumbnail = $thumbnail_query->posts;
        $count = 1;
    }
    
    $attachment_urls = array( wp_get_attachment_url( $attachment_id ) );
    
    $i = 0;

    if( wp_attachment_is_image( $attachment_id ) ){
        
        foreach( get_intermediate_image_sizes() as $size ){
            
            $intermediate = image_get_intermediate_size( $attachment_id, $size );
            
            if( $intermediate ){
                
                $attachment_urls[] = $intermediate['url'];
            }
        }
    }

    $used_in_content = array();

    foreach( $attachment_urls as $attachment_url ){
        
        $content_query = new \WP_Query(array(
            's'                 => $attachment_url,
            'post_type'         => 'any',
            'fields'            => 'ids',
            'no_found_rows'     => true,
            'posts_per_page'    => -1,
        ));

        $used_in_content = array_merge( $used_in_content, $content_query->posts );
        
        $count = count( $used_in_content ) + $count;
    }
    
    $used_in_content = array_unique( $used_in_content );
    
    if( ! empty( $used_as_thumbnail ) || ! empty( $used_in_content ) ){
        
        $posts = array(
            'thumbnail' => $used_as_thumbnail,
            'content'   => $used_in_content,
            'count'     => $count,
        );
    }

    return $posts;
}

function get_posts_using_attachment( $attachment_id, $context ){

    $post_ids = get_posts_by_attachment_id( $attachment_id );


    $count = count($post_ids["content"]);
    $posts = array();
    
    if( is_array( $post_ids ) ){
        
        $posts = array_merge( $post_ids['thumbnail'], $post_ids['content'] );
    }
    $posts = array_unique($posts);

    switch( $context ){
        
        case 'column':
        
            $item_format   = '<strong>%1$s</strong>, %2$s %3$s<br />';
            $output_format = '%s';
        break;
        
        case 'details':
        default:
            $item_format   = '%1$s %3$s<br />';
            $output_format = '<div style="padding-top: 8px">%s</div>';
        break;
    }


    $output = '';

    foreach( $posts as $post_id ){
        
        $post = get_post( $post_id );
        
        if( ! $post ){
            continue;
        }

        $post_title = _draft_or_post_title( $post );
        $post_type  = get_post_type_object( $post->post_type );

        if( $post_type && $post_type->show_ui && current_user_can('edit_post', $post_id ) ){
            
            $link = sprintf('<a href="%s">%s</a>', get_edit_post_link( $post_id ), $post_title );
            
        } else {
            
            $link = $post_title;
        }

        if( in_array( $post_id, $post_ids['thumbnail'] ) && in_array( $post_id, $post_ids['content'] ) ){
            
            $usage_context = __('(as Featured Image and in content)', 'find-posts-using-attachment');
            
        } elseif( in_array( $post_id, $post_ids['thumbnail'] ) ){
            
            $usage_context = __('(as Featured Image)', 'find-posts-using-attachment');
            
        } else {
            
            $usage_context = __('(in content)', 'find-posts-using-attachment');
        }

        $output .= sprintf($item_format, $link, get_the_time( __( 'Y/m/d', 'find-posts-using-attachment' ) ), $usage_context);
    }

    if( ! $output ){
        
        $output = ''; // __( '(Unused)', 'find-posts-using-attachment' );
    }

    $output = sprintf( $output_format, $output );

    return $output;
}


function alt_custom_search_media_by_used( $search, $query ){
    
    global $wpdb;
    
    if( is_admin() && ( $query->query['post_type'] == 'attachment' ) && ! empty( $query->query['s'] ) ){
        
        $sql = "
            OR EXISTS (
                SELECT      *
                FROM        {$wpdb->postmeta}
                WHERE       post_id  = {$wpdb->posts}.ID
                    AND     meta_key = 'using_attachment'
                    AND     meta_value LIKE %s
            )
        ";
        $like   = '%' . $wpdb->esc_like( $query->query['s'] ) . '%';
        $search = preg_replace("#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#", $wpdb->prepare( $sql, $like ), $search );
    }
    
    return $search;
}
add_filter( 'posts_search', 'alt_custom_search_media_by_used', 10, 2 );

