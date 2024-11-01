<?php

namespace MauiMarketing\UpdateAltAtribute\Media;

use MauiMarketing\UpdateAltAtribute\Core;
use MauiMarketing\UpdateAltAtribute\Stats;

function get_media_stats_html( $stats = false ){

    $stats    = $stats === false ? Stats\get_media_stats() : $stats;

    $success  = '<span class="dashicons dashicons-yes success"></span> ';
    $process  = '<span class="dashicons dashicons-arrow-right process"></span> ';
    $disabled = '<span class="dashicons dashicons-warning disabled" title="You can enable this in the Settings"></span> ';
    $empty    = '<span></span> ';

    $stats_output = [
        '<b>total found in the Media Folder</b>' => '<span></span>' . '<b>' . $stats['total'] . ' ' . _n( 'image', 'images', $stats['total'] ) . '</b>',
    ];

    if( $stats['no_alt'] > 0 ){
        $n    = ' ' . _n( 'image', 'images', $stats['no_alt'] );
        $stats_output[ 'without the Alt attribute' ]                    = $process . $stats['no_alt']      . $n;
    } else {
        $stats_output[ '<b>have their Alt attributes!</b>' ]            = $empty   . '<b>All images</b>';
    }

    if( $stats['no_title'] > 0 ){
        $n    = ' ' . _n( 'image', 'images', $stats['no_title'] );
        $key  = 'without the Title';
        $key .= Core\settings()->load("update_title") !== "yes"         ? ", won't be processed" : "";
        $icon = Core\settings()->load("update_title") === "yes"         ? $process : $disabled;
        $stats_output[ $key ]                                           = $icon    . $stats['no_title']    . $n;
    } else {
        $stats_output[ '<b>have their Titles!</b>' ]                    = $empty   . '<b>All images</b>';
    }

    if( $stats['no_desc'] > 0 ){
        $n    = ' ' . _n( 'image', 'images', $stats['no_desc'] );
        $key  = 'without the Description';
        $key .= Core\settings()->load("update_description") !== "yes"   ? ", won't be processed" : "";
        $icon = Core\settings()->load("update_description") === "yes"   ? $process : $disabled;
        $stats_output[ $key ]                                           = $icon    . $stats['no_desc']     . $n;
    } else {
        $stats_output[ '<b>have their Descriptions!</b>' ]              = $empty   . '<b>All images</b>';
    }

    if( $stats['no_caption'] > 0 ){
        $n    = ' ' . _n( 'image', 'images', $stats['no_caption'] );
        $key  = 'without the Caption';
        $key .= Core\settings()->load("update_caption") !== "yes"       ? ", won't be processed" : "";
        $icon = Core\settings()->load("update_caption") === "yes"       ? $process : $disabled;
        $stats_output[ $key ]                                           = $icon    . $stats['no_caption']  . $n;
    } else {
        $stats_output[ '<b>have their Captions!</b>' ]                  = $empty   . '<b>All images</b>';
    }

    if( $stats['faulty_images'] > 0 ){
        $stats_output[ '<b>will be processed</b>' ]                     = $process . '<b><span id="faulty_images">' . $stats['faulty_images']  . '</span> ' . _n( 'image', 'images', $stats['faulty_images'] ) . '</b>';
    } else {
        $stats_output[ '<b>All your images are processed!</b>' ]        = $success . '<b>Congratulations!</b>';
    }
    
    $html = '';
    
    foreach( $stats_output as $description => $stat ){
    
        $html .= '<div class="tab_info_content_row">';
        $html .=    '<span class="image_count">' . $stat . '</span> ' . $description;
        $html .= '</div>';
        
    }
    
    return $html;
}

function get_media_faulty_images_ids( $no_limit = false ){
    
    global $wpdb;
    
    if( $no_limit ){
        
        $limit = "";
        
    } else {
        
        $limit = "LIMIT       " . Core\settings()->load("process_step");
    }
    
    $condition = _get_media_faulty_images_condition();

    $query = "
        
        SELECT      DISTINCT( attachment.ID )
        
        FROM        $wpdb->posts AS attachment
        
        LEFT JOIN   $wpdb->postmeta AS alt_attribute
            ON      alt_attribute.post_id  = attachment.ID
            AND     alt_attribute.meta_key = '_wp_attachment_image_alt'
        
        WHERE       attachment.post_type = 'attachment'
            AND     attachment.post_mime_type LIKE 'image%'
            AND     $condition = 1
        
        $limit
    ";

    $ids = $wpdb->get_col( $query );
    
    return $ids;
}

function _get_media_faulty_images_condition(){
    
    $condition = "
        IF(
            TRIM( COALESCE( alt_attribute.meta_value, '' ) ) = '',
            1,
            0
        )
    ";
    
    if( Core\settings()->load("update_title") === "yes" ){
        
        $condition = sprintf(
            "
                IF(
                    TRIM( COALESCE( attachment.post_title,    '' ) ) = '',
                    1,
                    %s
                )
            ",
            $condition
        );
        
    }
    
    if( Core\settings()->load("update_description") === "yes" ){
        
        $condition = sprintf(
            "
                IF(
                    TRIM( COALESCE( attachment.post_content,  '' ) ) = '',
                    1,
                    %s
                )
            ",
            $condition
        );
        
    }
    
    if( Core\settings()->load("update_caption") === "yes" ){
        
        $condition = sprintf(
            "
                IF(
                    TRIM( COALESCE( attachment.post_excerpt,  '' ) ) = '',
                    1,
                    %s
                )
            ",
            $condition
        );
        
    }
    
    /* Example of the full condition
    $condition = "
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
    ";
     */
    
    return $condition;
}
