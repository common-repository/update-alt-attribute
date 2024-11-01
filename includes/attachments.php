<?php

namespace MauiMarketing\UpdateAltAtribute\Attachments;

use MauiMarketing\UpdateAltAtribute\Core;
use MauiMarketing\UpdateAltAtribute\Logs;


function clean_alt_string( $string, $attachment_id, $file_name ){
    
    if( "yes" === Core\settings()->load("replace_hyphen") ){
        
        $string = str_replace( '-', " ", $string );
    }
    
    if( "yes" === Core\settings()->load("replace_underscore") ){
        
        $string = str_replace( '_', " ", $string );
    }
    
    if( "yes" === Core\settings()->load("remove_numbers") ){
        
        $string = preg_replace( '/[0-9]+/', '', $string );
    }
    
    if( "yes" === Core\settings()->load("capitalize_words") ){
        
        $string = ucwords( $string );
    }
    
    if( "yes" === Core\settings()->load("remove_comma") ){
        
        $string = str_replace( ',', "", $string );
    }
    
    if( "yes" === Core\settings()->load("remove_period") ){
        
        $string = str_replace( '.', "", $string );
    }
    if( "" !== Core\settings()->load("exclude_words") ){
        $exclude_words = Core\settings()->load("exclude_words");
        $exclude_words_arr = preg_split('/\s*,\s*/', $exclude_words);
        $pattern = '/\b(' . implode('|', $exclude_words_arr) . ')\b/i';
        $string = preg_replace($pattern, '', $string);
    }
    $string = apply_filters( "uaa_clean_alt_string", $string, $attachment_id, $file_name );
    
    return $string;
}

function generate_image_text_attributes( $attachment_id ){
    
    $attachment = get_post( $attachment_id );
    
    $attachment_text = [
        "alt"           => $attachment->_wp_attachment_image_alt,
        "title"         => $attachment->post_title,
        "description"   => $attachment->post_content,
        "caption"       => $attachment->post_excerpt,
        "new_alt"       => "",
    ];
    
    
    if( count( array_filter( $attachment_text ) ) != 5 ){
        
		$file_name   = get_attachment_file_basename( $attachment_id );
        
        // TODO loosen up the criterium because we have some of these in the Settings?
		$has_special_characters = preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\+=\{\}\[\]\\\|;:"]/', $file_name );
        
		if( is_numeric( $file_name ) || $has_special_characters ){
            
            $new_alt = empty( $attachment->post_parent ) ? $file_name : get_the_title( $attachment->post_parent );
            
		} else {
            
            $new_alt = clean_alt_string( $file_name, $attachment_id, $file_name );
		}
        
        if( empty( trim( $new_alt ) ) ){
            $new_alt = $file_name;
        }
        
        $attachment_text = [
            "alt"           => empty( $attachment_text["alt"]         ) ? $new_alt : $attachment_text["alt"],
            "title"         => empty( $attachment_text["title"]       ) ? $new_alt : $attachment_text["title"],
            "description"   => empty( $attachment_text["description"] ) ? $new_alt : $attachment_text["description"],
            "caption"       => empty( $attachment_text["caption"]     ) ? $new_alt : $attachment_text["caption"],
            "new_alt"       => $new_alt,
        ];
        
    }
    
    return $attachment_text;
}

function prepare_post_update_arguments( $attachment_id ){
    
    $text_attributes = generate_image_text_attributes( $attachment_id );
    
    $args = array(
        'ID'            => $attachment_id,
        'post_title'    => $text_attributes["title"],
        'post_content'  => $text_attributes["description"],
        'post_excerpt'  => $text_attributes["caption"],
        'meta_input'    => [
            '_wp_attachment_image_alt' => $text_attributes["alt"],
        ],
        
        'new_alt'  => $text_attributes["new_alt"],
    );
    
    return $args;
}

function get_attachment_file_basename( $attachment_id ){
    
	$src         = wp_get_attachment_image_src( $attachment_id, '_thumbnail_id', true );
    
	$file_name   = basename( esc_url( $src[0] ) );
	$file_name   = explode( '.', $file_name );
	$file_name   = $file_name[0];
    
    return $file_name;
}

function get_post_ids_with_featured_image( $attachment_id, $in = [ "post", "page", "product" ] ){
    
    $in_clause = empty( $in ) ? "" : "AND     post.post_type IN ( '" . implode( "','", $in ) . "' )";
    
    global $wpdb;
    
    $query = "
        SELECT      DISTINCT( meta.post_id )
        
        FROM        $wpdb->postmeta AS meta
        
        LEFT JOIN   $wpdb->posts    AS post
            ON      meta.post_id    = post.ID
        
        WHERE       meta.meta_key   = '_thumbnail_id'
            AND     meta.meta_value = $attachment_id
            $in_clause
    ";
    
    $post_ids = $wpdb->get_col( $query );
    
    return $post_ids;
}

function get_post_ids_with_content_image( $attachment_id, $in = [ "post", "page", "product" ], $true_count = false ){
    
    $in_clause = empty( $in ) ? "" : "AND     post.post_type IN ( '" . implode( "','", $in ) . "' )";
    
    global $wpdb;
    
    $attachment_url = str_replace( [ ".jpg", ".jpeg", ".png", ".gif", ".bmp" ], "", wp_get_attachment_url( $attachment_id ) );
    
    $true_count_query = $true_count ? "" : "AND     meta_key != 'uaa_image_0'";
    
    $query = "
        SELECT      DISTINCT( meta.post_id )
        
        FROM        $wpdb->postmeta AS meta
        
        LEFT JOIN   $wpdb->posts    AS post
            ON      meta.post_id    = post.ID
        
        WHERE       meta.meta_key   LIKE 'uaa_image%'
            $true_count_query
            AND     meta.meta_value LIKE '$attachment_url%'
            $in_clause
    ";
    
    $post_ids = $wpdb->get_col( $query );
    
    return $post_ids;
}

function get_post_ids_with_first_image( $attachment_id, $in = [ "post", "page", "product" ] ){
    
    $in_clause = empty( $in ) ? "" : "AND     post.post_type IN ( '" . implode( "','", $in ) . "' )";
    
    global $wpdb;
    
    $attachment_url = str_replace( [ ".jpg", ".jpeg", ".png", ".gif", ".bmp" ], "", wp_get_attachment_url( $attachment_id ) );
    
    $query = "
        SELECT      DISTINCT( meta.post_id )
        
        FROM        $wpdb->postmeta AS meta
        
        LEFT JOIN   $wpdb->posts    AS post
            ON      meta.post_id    = post.ID
        
        WHERE       meta.meta_key   = 'uaa_image_0'
            AND     meta.meta_value LIKE '$attachment_url%'
            $in_clause
    ";
    
    $post_ids = $wpdb->get_col( $query );
    
    // Logs\debug_log( $query, "get_post_ids_with_first_image-query" );
    // Logs\debug_log( $post_ids, "get_post_ids_with_first_image-post_ids" );
    
    return $post_ids;
}

function get_image_being_used( $attachment_id, $in = [ "post", "page", "product" ] ){
    
    $used = [
        "featured"  => get_post_ids_with_featured_image( $attachment_id, $in ),
        "hero"      => get_post_ids_with_first_image(    $attachment_id, $in ),
        "content"   => get_post_ids_with_content_image(  $attachment_id, $in ),
    ];
    
    // Logs\debug_log( $used, "get_image_being_used-used: ". implode( "','", $in ) );
    
    return $used;
}

function get_image_being_used_html( $attachment_id, $in = [ "post", "page", "product" ] ){
    
    $image_used_as = get_image_being_used( $attachment_id, $in );
    
    $html = '';
    
    if( ! empty( $image_used_as["featured"] ) ){
        
        $html .= '<div class="image_used__section">';
        
        $html .= '<div class="image_used__title">As a Featured image: <span>' . count( $image_used_as["featured"] ) . '</span></div>';
        
        foreach( $image_used_as["featured"] as $post_id ){
            
            $post = get_post( $post_id );
            
            $html .= '<div class="image_used__post">';
            $html .= '- ' . '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    if( ! empty( $image_used_as["hero"] ) ){
        
        $html .= '<div class="image_used__section">';
        
        $html .= '<div class="image_used__title">As a First image: <span>' . count( $image_used_as["hero"] ) . '</span></div>';
        
        foreach( $image_used_as["hero"] as $post_id ){
            
            $post = get_post( $post_id );
            
            $html .= '<div class="image_used__post">';
            $html .= '- ' . '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    if( ! empty( $image_used_as["content"] ) ){
        
        $html .= '<div class="image_used__section">';
        
        $html .= '<div class="image_used__title">In the content: <span>' . count( $image_used_as["content"] ) . '</span></div>';
        
        foreach( $image_used_as["content"] as $post_id ){
            
            $post = get_post( $post_id );
            
            $html .= '<div class="image_used__post">';
            $html .= '- ' . '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    if( empty( $html ) ){
        $html = "-";
    }
    
    return '<div>' . $html . '</div>';
}
