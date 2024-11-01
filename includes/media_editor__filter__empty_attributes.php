<?php

namespace MauiMarketing\UpdateAltAtribute\MediaEditor;

use MauiMarketing\UpdateAltAtribute\Logs;

// return;

function load_scripts__empty_attributes(){
    
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
    
	if( $page == 'uaa_media_editor' ){
        
		wp_enqueue_script( 'uaa-media_editor-filter-empty_attributes', UAA_PLUGIN_URL . 'js/media_editor__filter__empty_attributes.js', [ 'uaa-media_editor' ] );
        
	}
    
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\' . 'load_scripts__empty_attributes' );

function display_filter__empty_attributes(){
    $echo = 'MauiMarketing\UpdateAltAtribute\Core\fnc';
    
    $empty_attributes = isset( $_GET['empty_attributes'] ) ? $_GET['empty_attributes'] : "any";
    
    $html = <<<EOF
        <select id="filter__empty_attributes" name="filter__empty_attributes">
            <option value="show_all"            {$echo( $empty_attributes == "show_all"          ? 'selected' : '' )}>Filter off: Populated Attributes</option>
            <option value="empty_title"         {$echo( $empty_attributes == "empty_title"       ? 'selected' : '' )}>Empty Title</option>
            <option value="empty_alt"           {$echo( $empty_attributes == "empty_alt"         ? 'selected' : '' )}>Empty Alt</option>
            <option value="empty_description"   {$echo( $empty_attributes == "empty_description" ? 'selected' : '' )}>Empty Description</option>
            <option value="empty_caption"       {$echo( $empty_attributes == "empty_caption"     ? 'selected' : '' )}>Empty Caption</option>
            <option value="empty_any"           {$echo( $empty_attributes == "empty_any"         ? 'selected' : '' )}>Any empty Attribute</option>
            <option value="all_correct"         {$echo( $empty_attributes == "all_correct"       ? 'selected' : '' )}>All Attributes populated</option>
        </select>
EOF;
    
    
    echo $html;
}
add_action( 'uaa_media_editor_filters', __NAMESPACE__ . '\\' . 'display_filter__empty_attributes' );

function alter_wp_query__empty_attributes( $args ){
    
    $valid_filters = [
        "empty_title",
        "empty_alt",
        "empty_description",
        "empty_caption",
        "empty_any",
        "all_correct",
    ];
    
    if( empty( $_GET["empty_attributes"] ) || ! in_array( $_GET["empty_attributes"], $valid_filters ) ){
        return $args;
    }
    
    // Logs\debug_log( $args, "filter_by_empty_attributes-args" );
    
    if( empty( $args["meta_query"] ) ){
        $args["meta_query"] = [];
    }
    
    if( $_GET["empty_attributes"] == "empty_alt" ){
        
        $args["meta_query"][] = [
            "relation" => "OR",
            [
                "key"       => "_wp_attachment_image_alt",
                "value"     => "",
                "compare"   => "=",
            ],
            [
                "key"       => "_wp_attachment_image_alt",
                "compare"   => 'NOT EXISTS',
            ],
        ];
    }
    
    if( $_GET["empty_attributes"] == "all_correct" ){
        
        $args["meta_query"][] = [
            "relation" => "AND",
            [
                "key"       => "_wp_attachment_image_alt",
                "value"     => "",
                "compare"   => "!=",
            ],
            [
                "key"       => "_wp_attachment_image_alt",
                "compare"   => 'EXISTS',
            ],
        ];
    }
    
    if( in_array( $_GET["empty_attributes"], [ "empty_title", "empty_description", "empty_caption", "empty_any" ] ) ){
        
        // this is necessary in order to get the JOIN in the 'posts_where' hook
        $args["meta_query"][] = [
            "relation" => "OR",
            [
                "key"       => "_wp_attachment_image_alt",
                "compare"   => 'EXISTS',
            ],
            [
                "key"       => "_wp_attachment_image_alt",
                "compare"   => 'NOT EXISTS',
            ],
        ];
    }
    
    // Logs\debug_log( $args, "filter_by_empty_attributes-args" );
    
    return $args;
}
add_filter( 'uaa_media_editor_args', __NAMESPACE__ . '\\' . 'alter_wp_query__empty_attributes' );

function alter_sql_query__empty_attributes( $where = '', $query ){
    
    $valid_filters = [
        "empty_title",
        "empty_alt",
        "empty_description",
        "empty_caption",
        "empty_any",
        "all_correct",
    ];
    
    if( ! is_admin() || empty( $_GET["empty_attributes"] ) || ! in_array( $_GET["empty_attributes"], $valid_filters ) ){
        return $where;
    }
    
    global $wpdb;
    
    if( $_GET["empty_attributes"] == "empty_title" ){
        
        $where .= " AND TRIM( COALESCE( $wpdb->posts.post_title, '' ) ) = '' ";
    }
    
    if( $_GET["empty_attributes"] == "empty_description" ){
        
        $where .= " AND TRIM( COALESCE( $wpdb->posts.post_content, '' ) ) = '' ";
    }
    
    if( $_GET["empty_attributes"] == "empty_caption" ){
        
        $where .= " AND TRIM( COALESCE( $wpdb->posts.post_excerpt, '' ) ) = '' ";
    }
    
    if( $_GET["empty_attributes"] == "empty_any" ){
        
        $where .= "
            AND (
                    TRIM( COALESCE( $wpdb->posts.post_title,   '' ) ) = ''
                OR  TRIM( COALESCE( $wpdb->posts.post_content, '' ) ) = ''
                OR  TRIM( COALESCE( $wpdb->posts.post_excerpt, '' ) ) = ''
                OR  (
                        $wpdb->postmeta.meta_key   = '_wp_attachment_image_alt' AND
                        $wpdb->postmeta.meta_value = ''
                    )
                OR  NOT EXISTS (
                        SELECT * FROM $wpdb->postmeta
                        WHERE $wpdb->postmeta.meta_key = '_wp_attachment_image_alt'
                          AND $wpdb->postmeta.post_id  = $wpdb->posts.ID
                    )
            )
        ";
    }
    
    if( $_GET["empty_attributes"] == "all_correct" ){
        
        $where .= "
            AND (
                    TRIM( COALESCE( $wpdb->posts.post_title,   '' ) ) <> ''
                AND TRIM( COALESCE( $wpdb->posts.post_content, '' ) ) <> ''
                AND TRIM( COALESCE( $wpdb->posts.post_excerpt, '' ) ) <> ''
            )
        ";
    }
    
    // Logs\debug_log( $where, "alter_sql_query__empty_attribute-where" );
    
    return $where;
}
add_filter( 'posts_where', __NAMESPACE__ . '\\' . 'alter_sql_query__empty_attributes', 10, 2 );
