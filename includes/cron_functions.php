<?php

namespace MauiMarketing\UpdateAltAtribute\Cron;

use MauiMarketing\UpdateAltAtribute\Logs;

// this function is hooked to a Cron job that gets scheduled on plugin activation
// it runs every 5 minutes
function using_attachment_field_gallery_save(){


    $attachments = new \WP_Query([
        'post_type'         => 'attachment',
        'post_mime_type'    => 'image',
        'meta_key'          => 'cron_status',
        'meta_value'        => '0',
        'posts_per_page'    => 1,
        'post_status'       => 'inherit',
        'orderby'           => 'date',
        'order'             => 'DESC',
    ]);
    
    if( $attachments->have_posts() ){
        
        while( $attachments->have_posts() ){
            $attachments->the_post();
        
            $post_ids = get_posts_by_attachment_id( get_the_ID() );
            
            $posts = array();
            
            if( is_array( $post_ids ) && ! empty( $post_ids ) ){
                
                $posts = array_merge( $post_ids['thumbnail'], $post_ids['content'] );
            }
            $posts = array_unique( $posts );// TODO This creates notices

            switch('column-used'){// TODO This is redundant
                
                case 'column':
                    $item_format = '<strong>%1$s</strong>, %2$s %3$s<br />';
                    $output_format = '%s';
                break;
                
                case 'details':
                default:
                    $item_format = '%1$s %3$s<br />';
                    $output_format = '<div style="padding-top: 8px;">%s</div>';
                break;
            }

            $output = '';

            $count_post_using_attachments = 0;
            
            foreach( $posts as $post_id ){// TODO This creates notices
                
                $post = get_post($post_id);
                
                if( ! $post ){
                    continue;
                }
                
                $pcount     = $count_post_using_attachments + 1;
                $post_title = $pcount . '. ' . get_the_title( $post );
                $post_type  = get_post_type_object( $post->post_type );

                if( $post_type && $post_type->show_ui && current_user_can('edit_post', $post_id ) ){
                    
                    $link = sprintf('<a href="%s">%s</a>', get_edit_post_link( $post_id ), $post_title);
                    
                } else {
                    
                    $link = $post_title;
                }
                
                //overwrite
                $link = sprintf('<a href="%s">%s</a>', get_edit_post_link( $post_id ), $post_title );

                if( in_array( $post_id, $post_ids['thumbnail'] ) && in_array( $post_id, $post_ids['content'] ) ){
                    
                    $usage_context = __('(as Featured Image and in content)', 'find-posts-using-attachment');
                    
                } elseif( in_array( $post_id, $post_ids['thumbnail'] ) ){
                    
                    $usage_context = __('(as Featured Image)', 'find-posts-using-attachment');
                    
                } else {
                    
                    $usage_context = __('(in content)', 'find-posts-using-attachment');
                }

                $output .= sprintf( $item_format, $link, get_the_time( __('Y/m/d', 'find-posts-using-attachment') ), $usage_context);
                $count_post_using_attachments++;
            }

            if( ! $output ){
                $output = '';
            }

            $output = sprintf( $output_format, $output );

            update_post_meta( get_the_ID(), 'using_attachment', $output );
            update_post_meta( get_the_ID(), 'total_post_using_attachment', $count_post_using_attachments );
            update_post_meta( get_the_ID(), 'cron_status', '1' );
            
            //Log file
            $logitem = get_the_ID() . "\t" . $output . "\t\t\t" . $count_post_using_attachments . "\n";
            Logs\write_to_log_file( $logitem );
            //end Log file
        
        }
        
    } else {
        
        $attachments = new \WP_Query([
            'post_type'         => 'attachment',
            'post_mime_type'    => 'image',
            'posts_per_page'    => -1,
            'post_status'       => 'inherit',
            'orderby'           => 'date',
            'order'             => 'DESC',
        ]);
        
        while( $attachments->have_posts() ){
            $attachments->the_post();
            
            update_post_meta(get_the_ID(), 'using_attachment', '' );
            update_post_meta(get_the_ID(), 'total_post_using_attachment', 0 );
            update_post_meta(get_the_ID(), 'cron_status', '0' );
        }
        
        //Log
        Logs\write_to_log_file("-------------------");
        Logs\write_to_log_file("-------MM RESET------------");
        //End Log
    }
}
// add_action('mm_hook_posts_using_attachment', __NAMESPACE__ . '\\' . 'using_attachment_field_gallery_save', 10 );

