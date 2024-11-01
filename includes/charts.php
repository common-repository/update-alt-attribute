<?php

namespace MauiMarketing\UpdateAltAtribute\Charts;

use MauiMarketing\UpdateAltAtribute\Stats;
use MauiMarketing\UpdateAltAtribute\Logs;

function get_dashboard_chart_data(){
    
    $data = [
        "media_stats"           => get_media_populated_attributes_data(),
        "poor_attributes_stats" => get_poor_attributes_data(),
        "post_type_stats"       => get_post_data(),
        "used_images_stats"     => get_used_images_data(),
    ];
    
    // Logs\debug_log( $data, "get_dashboard_chart_data-data" );
    
    return $data;
}

function get_media_populated_attributes_data(){
    
    $data = [
        "data"   => [],
        "color"  => [],
        "labels" => [],
    ];
    
    $media_stats = Stats\get_media_stats();
    
    // Logs\debug_log( $media_stats, "get_media_populated_attributes_stats-media_stats" );
    
    $data["data"][]   = $media_stats["total"] - $media_stats["faulty_images"];
    $data["color"][]  = empty( $media_stats["faulty_images"] ) ? 'rgb(75, 192, 192)' : 'rgb(201, 203, 207)';
    $data["labels"][] = 'All attributes populated';
    
    if( ! empty( $media_stats["no_alt"] ) ){
        $data["data"][]   = $media_stats["no_alt"];
        $data["color"][]  = 'rgb(255, 99, 132)';
        $data["labels"][] = 'Empty Alt';
    }
    
    if( ! empty( $media_stats["no_title"] ) ){
        $data["data"][]   = $media_stats["no_title"];
        $data["color"][]  = 'rgb(54, 162, 235)';
        $data["labels"][] = 'Empty Title';
    }
    
    if( ! empty( $media_stats["no_desc"] ) ){
        $data["data"][]   = $media_stats["no_desc"];
        $data["color"][]  = 'rgb(255, 205, 86)';
        $data["labels"][] = 'Empty Description';
    }
    
    if( ! empty( $media_stats["no_caption"] ) ){
        $data["data"][]   = $media_stats["no_caption"];
        $data["color"][]  = 'rgb(75, 192, 192)';
        $data["labels"][] = 'Empty Caption';
    }
    
    // Logs\debug_log( $data, "get_media_populated_attributes_stats-data" );
    
    return $data;
}

function get_poor_attributes_data(){
    
    $data = [
        "data"   => [],
        "color"  => [],
        "labels" => [],
    ];
    
    $poor_attributes_stats = Stats\get_poor_attributes_stats();
    
    // Logs\debug_log( $poor_attributes_stats, "get_poor_attributes_data-poor_attributes_stats" );
    
    $data["data"][]   = $poor_attributes_stats["total_with_attribute"] - $poor_attributes_stats["faulty_images"];
    $data["color"][]  = empty( $poor_attributes_stats["faulty_images"] ) ? 'rgb(75, 192, 192)' : 'rgb(201, 203, 207)';
    $data["labels"][] = 'Correctly populated';
    
    if( ! empty( $poor_attributes_stats["without_letters"] ) ){
        $data["data"][]   = $poor_attributes_stats["without_letters"];
        $data["color"][]  = 'rgb(255, 99, 132)';
        $data["labels"][] = 'Without Letters';
    }
    
    if( ! empty( $poor_attributes_stats["repeating"] ) ){
        $data["data"][]   = $poor_attributes_stats["repeating"];
        $data["color"][]  = 'rgb(54, 162, 235)';
        $data["labels"][] = 'Repeating letters';
    }
    
    if( ! empty( $poor_attributes_stats["with_symbols"] ) ){
        $data["data"][]   = $poor_attributes_stats["with_symbols"];
        $data["color"][]  = 'rgb(255, 205, 86)';
        $data["labels"][] = 'With Symbols';
    }
    
    // Logs\debug_log( $data, "get_poor_attributes_data-data" );
    
    return $data;
}

function get_post_data(){
    
    $data = [
        "data"   => [],
        "color"  => [],
        "labels" => [],
    ];
    
    $post_type_stats = Stats\get_post_type_stats();
    
    // Logs\debug_log( $post_type_stats, "get_post_data-post_type_stats" );
    
    if( ! empty( $post_type_stats["total"]["Pages"] ) ){
        
        if( ! empty( $post_type_stats["with_image"]["Pages"] ) ){
            
            $data["data"][]   = $post_type_stats["with_image"]["Pages"];
            $data["color"][]  = 'rgb(54, 162, 235)';
            $data["labels"][] = empty( $post_type_stats["without_image"]["Pages"] ) ? 'Pages' : 'Pages with images';
        }
            
        if( ! empty( $post_type_stats["without_image"]["Pages"] ) ){
            
            $data["data"][]   = $post_type_stats["without_image"]["Pages"];
            $data["color"][]  = 'rgba(54, 162, 235, 0.6)';
            $data["labels"][] = 'Pages without images';
        }
        
    }
    
    if( ! empty( $post_type_stats["total"]["Posts"] ) ){
        
        if( ! empty( $post_type_stats["with_image"]["Posts"] ) ){
            
            $data["data"][]   = $post_type_stats["with_image"]["Posts"];
            $data["color"][]  = 'rgb(255, 205, 86)';
            $data["labels"][] = empty( $post_type_stats["without_image"]["Posts"] ) ? 'Posts' : 'Posts with images';
        }
            
        if( ! empty( $post_type_stats["without_image"]["Posts"] ) ){
            
            $data["data"][]   = $post_type_stats["without_image"]["Posts"];
            $data["color"][]  = 'rgba(255, 205, 86, 0.6)';
            $data["labels"][] = 'Posts without images';
        }
        
    }
    
    if( ! empty( $post_type_stats["total"]["Products"] ) ){
        
        if( ! empty( $post_type_stats["with_image"]["Products"] ) ){
            
            $data["data"][]   = $post_type_stats["with_image"]["Products"];
            $data["color"][]  = 'rgb(75, 192, 192)';
            $data["labels"][] = empty( $post_type_stats["without_image"]["Products"] ) ? 'Products' : 'Products with images';
        }
            
        if( ! empty( $post_type_stats["without_image"]["Products"] ) ){
            
            $data["data"][]   = $post_type_stats["without_image"]["Products"];
            $data["color"][]  = 'rgba(75, 192, 192, 0.6)';
            $data["labels"][] = 'Products without images';
        }
        
    }
    
    // Logs\debug_log( $data, "get_post_data-data" );
    
    return $data;
}

function get_used_images_data(){
    
    $data = [
        "data"   => [],
        "color"  => [],
        "labels" => [],
    ];
    
    $used_images_stats = Stats\get_used_images_stats();
    
    // Logs\debug_log( $used_images_stats, "get_post_data-used_images_stats" );
    
    $used   = array_sum( $used_images_stats ) - $used_images_stats["total"];
    $unused = $used_images_stats["total"] - $used;
    
    if( ! empty( $unused ) ){
        $data["data"][]   = $unused;
        $data["color"][]  = 'rgb(255, 99, 132)';
        $data["labels"][] = 'Unused images';
    }
    
    if( ! empty( $used_images_stats["featured"] ) ){
        $data["data"][]   = $used_images_stats["featured"];
        $data["color"][]  = 'rgb(255, 205, 86)';
        $data["labels"][] = 'as a Featured image';
    }
    
    if( ! empty( $used_images_stats["both"] ) ){
        $data["data"][]   = $used_images_stats["both"];
        $data["color"][]  = 'rgb(75, 192, 192)';
        $data["labels"][] = 'Both';
    }
    
    if( ! empty( $used_images_stats["in_content"] ) ){
        $data["data"][]   = $used_images_stats["in_content"];
        $data["color"][]  = 'rgb(54, 162, 235)';
        $data["labels"][] = 'in the Content';
    }
    
    // Logs\debug_log( $data, "get_post_data-data" );
    
    return $data;
}
