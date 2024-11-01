<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

use MauiMarketing\UpdateAltAtribute\Logs;

// return;

function load_scripts__faulty_names(){
    
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
    
	if( $page == 'uaa_media_editor' ){
        
		wp_enqueue_script( 'uaa-media_editor-filter-faulty_names', UAA_PLUGIN_URL . 'js/media_editor__filter__faulty_names.js', [ 'uaa-media_editor' ] );
        
	}
    
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\' . 'load_scripts__faulty_names' );

function display_filter__faulty_names(){
    $echo = 'MauiMarketing\UpdateAltAtribute\Core\fnc';
    
    $faulty_names = isset( $_GET['faulty_names'] ) ? $_GET['faulty_names'] : "any";
    
    $html = <<<EOF
        <select id="filter__faulty_names" name="filter__faulty_names">
            <option value="show_all"            {$echo( $faulty_names == "show_all"          ? 'selected' : '' )}>Filter off: Poor Attributes</option>
            <option value="without_letters"     {$echo( $faulty_names == "without_letters"   ? 'selected' : '' )}>Any Attribute without Letters</option>
            <option value="repeating"           {$echo( $faulty_names == "repeating"         ? 'selected' : '' )}>Repeating Letters</option>
            <option value="with_symbols"        {$echo( $faulty_names == "with_symbols"      ? 'selected' : '' )}>Any Attribute with Symbols</option>
            <option value="all_poor"            {$echo( $faulty_names == "all_poor"          ? 'selected' : '' )}>Any poorly populated Attributes</option>
            <option value="all_correct"         {$echo( $faulty_names == "all_correct"       ? 'selected' : '' )}>All populated Attributes correct</option>
        </select>
EOF;
    
    
    echo $html;
}
add_action( 'uaa_media_editor_filters', __NAMESPACE__ . '\\' . 'display_filter__faulty_names' );

function alter_wp_query__faulty_names( $args ){
    
    $valid_filters = [
        "without_letters",
        "repeating",
        "with_symbols",
        "all_poor",
        "all_correct",
    ];
    
    if( empty( $_GET["faulty_names"] ) || ! in_array( $_GET["faulty_names"], $valid_filters ) ){
        return $args;
    }
    
    global $wpdb;
    
    if( $_GET["faulty_names"] == "without_letters" ){
        
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
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  NOT RLIKE '[[:alpha:]]' )
                        )
        ";
        
        // Logs\debug_log( $query, "alter_wp_query__faulty_names-without_letters-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["faulty_names"] == "repeating" ){
        
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
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        )
        ";
        
        // Logs\debug_log( $query, "alter_wp_query__faulty_names-with_symbols-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["faulty_names"] == "with_symbols" ){
        
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
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  RLIKE '[[:punct:]]' )
                        )
        ";
        
        // Logs\debug_log( $query, "alter_wp_query__faulty_names-with_symbols-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["faulty_names"] == "all_poor" ){
        
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
        ";
        
        // Logs\debug_log( $query, "alter_wp_query__faulty_names-with_symbols-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( $_GET["faulty_names"] == "all_correct" ){
        
        $query = "
            SELECT      image.ID
            
            FROM        $wpdb->posts AS image
            
            LEFT JOIN   $wpdb->postmeta AS image_meta
                ON      image.ID              = image_meta.post_id
                AND     image_meta.meta_key   = '_wp_attachment_image_alt'
                
            WHERE       image.post_type       = 'attachment'
                AND     image.post_status     = 'inherit'
                AND     image.post_mime_type  LIKE 'image%'
                AND NOT (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     NOT RLIKE '[[:alpha:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  NOT RLIKE '[[:alpha:]]' )
                        )
                AND NOT (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     RLIKE '[[:punct:]]' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  RLIKE '[[:punct:]]' )
                        )
                AND NOT (
                            ( TRIM( COALESCE( image.post_title,         '' ) ) != '' AND image.post_title       REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_content,       '' ) ) != '' AND image.post_content     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image.post_excerpt,       '' ) ) != '' AND image.post_excerpt     REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        OR
                            ( TRIM( COALESCE( image_meta.meta_value,    '' ) ) != '' AND image_meta.meta_value  REGEXP  '[a]{3}|[b]{3}|[c]{3}|[d]{3}|[e]{3}|[f]{3}|[g]{3}|[h]{3}|[i]{3}|[j]{3}|[k]{3}|[l]{3}|[m]{3}|[n]{3}|[o]{3}|[p]{3}|[q]{3}|[r]{3}|[s]{3}|[t]{3}|[u]{3}|[v]{3}|[w]{3}|[x]{3}|[y]{3}|[z]{3}' )
                        )
        ";
        
        // Logs\debug_log( $query, "alter_wp_query__faulty_names-with_symbols-query" );
        
        $image_ids = $wpdb->get_col( $query );
        
    }
    
    if( empty( $image_ids ) ){
        
        $image_ids = [-1, -2, -3 ];
    }
    
    
    $args["post__in"] = empty( $args["post__in"] ) ? $image_ids : array_values( array_unique( array_intersect( $args["post__in"], $image_ids ) ) );
    
    // Logs\debug_log( $args, "alter_wp_query__faulty_names-args" );
    
    return $args;
}
add_filter( 'uaa_media_editor_args', __NAMESPACE__ . '\\' . 'alter_wp_query__faulty_names' );
