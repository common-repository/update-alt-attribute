<?php

namespace MauiMarketing\UpdateAltAtribute\Stats;

use MauiMarketing\UpdateAltAtribute\Media;
use MauiMarketing\UpdateAltAtribute\Logs;

function get_post_type_stats(){
    
    global $wpdb;
    
    $query = "
        
        SELECT      CONCAT( post.post_type, 's' ) AS 'type',
                    COUNT( post.ID ) AS 'number',
                    SUM(
                        COALESCE( featured_image.meta_value, '') != ''
                    OR
                        COALESCE( content_image.meta_value,  '') != ''
                    ) AS 'with_image',
                    SUM(
                        COALESCE( featured_image.meta_value, '') = ''
                    AND
                        COALESCE( content_image.meta_value,  '') = ''
                    ) AS 'without_image'
                    
        
        FROM        $wpdb->posts AS post
        
        LEFT JOIN   $wpdb->postmeta AS featured_image
            ON      post.ID = featured_image.post_id
            AND     featured_image.meta_key = '_thumbnail_id'
        
        LEFT JOIN   $wpdb->postmeta AS content_image
            ON      post.ID = content_image.post_id
            AND     content_image.meta_key = 'uaa_image_0'
        
        WHERE       post.post_type      IN ( 'post', 'page', 'product' )
            AND     COALESCE( post.post_content, '' ) != ''
        
        GROUP BY    post.post_type
    ";

    // Logs\debug_log( $query, "get_post_type_stats-query" );
    
    $counts = $wpdb->get_results( $query, ARRAY_A );
    
    $stats = [
        "total"         => [],
        "with_image"    => [],
        "without_image" => [],
    ];
    
    foreach( $counts as $count ){
        
        $type = ucwords( $count["type"] );
        
        $stats["total"][ $type ]         = $count["number"];
        $stats["with_image"][ $type ]    = $count["with_image"];
        $stats["without_image"][ $type ] = $count["without_image"];
        
    }
    
    Logs\debug_log( $stats, "get_post_type_stats-stats" );
    
    return $stats;
}

function get_media_stats(){
    
    global $wpdb;

    $condition = Media\_get_media_faulty_images_condition();

    $sum_query = sprintf(
        "
            SUM(
                %s
            )
        ",
        $condition
    );
    
    /* Example of the full query
    $sum_query = "
        SUM(
            IF(
                TRIM( COALESCE( attachment.post_excerpt,  '' ) ) = '',
                1,
                IF(
                    TRIM( COALESCE( attachment.post_content,  '' ) ) = '',
                    1,
                    IF(
                        TRIM( COALESCE( attachment.post_title,    '' ) ) = '',
                        1,
                        IF(
                            TRIM( COALESCE( alt_attribute.meta_value, '' ) ) = '',
                            1,
                            0
                        )
                    )
                )
            )
        )
    ";
     */

    $query = "
        
        SELECT      COUNT( attachment.ID ) AS total,
                    SUM( IF( TRIM( COALESCE( alt_attribute.meta_value, '' ) ) = '', 1, 0 ) ) AS no_alt,
                    SUM( IF( TRIM( COALESCE( attachment.post_title,    '' ) ) = '', 1, 0 ) ) AS no_title,
                    SUM( IF( TRIM( COALESCE( attachment.post_content,  '' ) ) = '', 1, 0 ) ) AS no_desc,
                    SUM( IF( TRIM( COALESCE( attachment.post_excerpt,  '' ) ) = '', 1, 0 ) ) AS no_caption,
                    $sum_query AS faulty_images
        
        FROM        $wpdb->posts AS attachment
        
        LEFT JOIN   $wpdb->postmeta AS alt_attribute
            ON      alt_attribute.post_id  = attachment.ID
            AND     alt_attribute.meta_key = '_wp_attachment_image_alt'
        
        WHERE       attachment.post_type = 'attachment'
            AND     attachment.post_mime_type LIKE 'image%'
        
    ";

    $stats = $wpdb->get_results( $query, ARRAY_A )[0];
    
    return $stats;
}

function get_poor_attributes_stats(){
    
    global $wpdb;
    
    $query = "
        SELECT      COUNT( image.ID ) AS total,
                    SUM(
                            (
                                ( TRIM( COALESCE( image.post_title,         '' ) ) != '' )
                            OR
                                ( TRIM( COALESCE( image.post_content,       '' ) ) != '' )
                            OR
                                ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' )
                            OR
                                ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' )
                            )
                    ) AS total_with_attribute,
                    SUM(
                        (
                            (
                                ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       NOT RLIKE '[[:alpha:]]' )
                            OR
                                ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     NOT RLIKE '[[:alpha:]]' )
                            OR
                                ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     NOT RLIKE '[[:alpha:]]' )
                            OR
                                ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  NOT RLIKE '[[:alpha:]]' )
                            )
                    OR      (
                                ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       RLIKE '[[:punct:]]' )
                            OR
                                ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     RLIKE '[[:punct:]]' )
                            OR
                                ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     RLIKE '[[:punct:]]' )
                            OR
                                ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  RLIKE '[[:punct:]]' )
                            )
                    OR      (
                                ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                            OR
                                ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                            OR
                                ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                            OR
                                ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                            )
                        )
                    ) AS faulty_images,
                    SUM(
                        (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  NOT RLIKE '[[:alpha:]]' )
                        )
                    ) AS without_letters,
                    SUM(
                        (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  RLIKE '[[:punct:]]' )
                        )
                    ) AS with_symbols,
                    SUM(
                        (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        )
                    ) AS repeating
        
        FROM        $wpdb->posts AS image
        
        LEFT JOIN   $wpdb->postmeta AS image_meta
            ON      image.ID              = image_meta.post_id
            AND     image_meta.meta_key   = '_wp_attachment_image_alt'
            
        WHERE       image.post_type       = 'attachment'
            AND     image.post_status     = 'inherit'
            AND     image.post_mime_type  LIKE 'image%'
    ";
    
    // Logs\debug_log( $query, "get_poor_attributes_stats-query" );
    
    $stats = $wpdb->get_row( $query, ARRAY_A );
    
    return $stats;
}

function get_used_images_stats(){
    
    global $wpdb;
    
    $query = "
        SELECT      COUNT( image.ID ) AS total,
                    SUM(
                        image.ID IN (
                            SELECT      meta_value
                            FROM        $wpdb->postmeta
                            WHERE       meta_key = '_thumbnail_id'
                        )
                    AND NOT
                        EXISTS(
                            SELECT      uaa_image.post_id
                            FROM        $wpdb->postmeta AS uaa_image
                            WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                        )
                    ) AS featured,
                    SUM(
                        image.ID NOT IN (
                            SELECT      meta_value
                            FROM        $wpdb->postmeta
                            WHERE       meta_key = '_thumbnail_id'
                        )
                    AND
                        EXISTS(
                            SELECT      uaa_image.post_id
                            FROM        $wpdb->postmeta AS uaa_image
                            WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                        )
                    ) AS in_content,
                    SUM(
                        image.ID IN (
                            SELECT      meta_value
                            FROM        $wpdb->postmeta
                            WHERE       meta_key = '_thumbnail_id'
                        )
                    AND
                        EXISTS(
                            SELECT      uaa_image.post_id
                            FROM        $wpdb->postmeta AS uaa_image
                            WHERE       uaa_image.meta_key   LIKE 'uaa_image%'
                                AND     uaa_image.meta_value LIKE CONCAT( LEFT( image.guid, CHAR_LENGTH( image.guid ) - LOCATE( '.', REVERSE( image.guid ) ) ), '%' )
                        )
                    ) AS 'both'
        FROM        $wpdb->posts AS image
        WHERE       image.post_type       = 'attachment'
            AND     image.post_status     = 'inherit'
            AND     image.post_mime_type  LIKE 'image%'
    ";
    
    // Logs\debug_log( $query, "get_used_images_stats-query" );
    
    $stats = $wpdb->get_row( $query, ARRAY_A );
    
    return $stats;
}
