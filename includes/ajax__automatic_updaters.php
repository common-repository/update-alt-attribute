<?php

namespace MauiMarketing\UpdateAltAtribute\Ajax;

use MauiMarketing\UpdateAltAtribute\Core;
use MauiMarketing\UpdateAltAtribute\Stats;
use MauiMarketing\UpdateAltAtribute\Media;
use MauiMarketing\UpdateAltAtribute\Attachments;

function process_media_images(){
    
    $data = $_POST;
    if( isset($_POST["dry_run"]) && isset( $_POST["images_processed"] ) ){
        $dry_run = $_POST["dry_run"] === "yes";
    
        $return = [
            "message"           => "Success",
            "images_processed"  => (int) $_POST["images_processed"],
            "processed"         => [],
        ];
        
        
        
        $attachment_ids = Media\get_media_faulty_images_ids( $dry_run );
        // $attachment_ids = [];
        $return["attachment_ids"] = $attachment_ids;
        
        if( empty( $attachment_ids ) ){
            $return["message"] = "Sorry, no images for processing were found.";
            wp_send_json_error( $return );
        }
        
        
        $processed_attachments = 0;
        
        foreach( $attachment_ids as $attachment_id ){
            
            $attachment = get_post( $attachment_id );
            $args       = Attachments\prepare_post_update_arguments( $attachment_id );
            
            
            $alt = $args["meta_input"]["_wp_attachment_image_alt"];
            unset( $args["meta_input"] );
            
            $return["processed"][ $attachment_id ] = [ "text" => $args["new_alt"] ];
            unset( $args["new_alt"] );
            
            
            $args["post_title"]   = Core\settings()->load("update_title")       === "yes" ? $args["post_title"]   : $attachment->post_title;
            $args["post_content"] = Core\settings()->load("update_description") === "yes" ? $args["post_content"] : $attachment->post_content;
            $args["post_excerpt"] = Core\settings()->load("update_caption")     === "yes" ? $args["post_excerpt"] : $attachment->post_excerpt;
            
            $return["processed"][ $attachment_id ]["updated_alt"]         = $alt                  != $attachment->_wp_attachment_image_alt;
            $return["processed"][ $attachment_id ]["updated_title"]       = $args["post_title"]   != $attachment->post_title;
            $return["processed"][ $attachment_id ]["updated_description"] = $args["post_content"] != $attachment->post_content;
            $return["processed"][ $attachment_id ]["updated_caption"]     = $args["post_excerpt"] != $attachment->post_excerpt;
            
            if( ! $dry_run ){
                
                remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
                $update = wp_update_post( $args, false, false );
                add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
            }
            
            
            if( $dry_run || $update ){
                
                if( ! $dry_run ){
                    
                    update_post_meta( $attachment_id, "_wp_attachment_image_alt", $alt );
                }
                
                $processed_attachments++;
                
                $return["processed"][ $attachment_id ]["file_name"] = Attachments\get_attachment_file_basename( $attachment_id );
                
            } else {
                
                unset( $return["processed"][ $attachment_id ] );
                
            }
            
        }
        
        if( $processed_attachments === 0 ){
            $return["message"] = "For some reason, no images were processed.";
            wp_send_json_error( $return );
        }
        
        $return["images_processed"] += $processed_attachments;
        $return["stats"]             = Stats\get_media_stats();
        $return["stats_html"]        = Media\get_media_stats_html( $return["stats"] );
        
        wp_send_json_success( $return );
    }
    
}
add_action( 'wp_ajax_'        . 'uaa_' . 'process_media_images', __NAMESPACE__ . '\\' . 'process_media_images' );
add_action( 'wp_ajax_nopriv_' . 'uaa_' . 'process_media_images', __NAMESPACE__ . '\\' . 'process_media_images' );

// update content
function process_content_images(){ 
    global $wpdb;
    if ( ! wp_verify_nonce( $_POST['update_post_content_nonce'], 'submit_update_post_content' ) ) {
        wp_die( 'Invalid nonce' );
    }

    if( isset($_POST["content_processed"]) && isset( $_POST["total_to_process"] ) && isset( $_POST["list_to_process"] ) && isset( $_POST["list_post_type"] ) ){
        $return = [
            "message"           => "Success",
            "content_processed" => (int) $_POST["content_processed"],
            "total_to_process"  => (int) $_POST["total_to_process"],
            "list_to_process"   => $_POST["list_to_process"],
            "list_post_type"   => $_POST["list_post_type"],
            "processed"         => [],
        ];
        
        if( ! get_option('update_alt') ){
            update_option('update_alt', 0 );
        }
    
        $get_post    = $return["list_post_type"];
        $offset      = get_option('update_alt');
        $txt         = array();
        $check_alt   = false;
        
        if (empty($return["list_to_process"])){
            $total_query  = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type IN ('".implode("','",$get_post)."') and post_status='publish' ORDER BY ID DESC");
            $return["list_to_process"]  = $total_query;
            $return["total_to_process"]  = count($total_query);
        }
        $data_query  = $wpdb->get_results("SELECT ID, post_content, post_title FROM $wpdb->posts WHERE post_type IN ('".implode("','",$get_post)."') and post_status='publish' and ID=".end($return["list_to_process"]));
        array_pop($return["list_to_process"]);
        if( count( $data_query ) > 0 ){
            $num = 1;
            $run = 1;
            
            $post = $data_query[0];
            $post_content = $post->post_content;
            
            preg_match_all( '/<img[^>]+>/i', $post_content, $images );
    
            if( ! empty( $images[0] ) ){
                foreach($images[0] as $img){
                    
                    preg_match_all('/alt="([^"]+)/i',   $img, $alt    );
                    preg_match_all('/src="([^"]+)/i',   $img, $src    );
                    preg_match_all('/width="([^"]+)/i', $img, $width  );
                    preg_match_all('/height="([^"]+)/i',$img, $height );
                    preg_match_all('/class="([^"]+)/i', $img, $class  );
                    
                    $alt_current = trim( $alt[1][0] );
    
                    $url    = $src[1][0];
                    $width  = $width[1][0];
                    $height = $height[1][0];
                    $class  = $class[1][0];
                    if( $width == '' || $height == '' ){
                        
                        $img_size = getimagesize( $url );
                        
                        if( $width == ''  ) $width  = $img_size[0];
                        if( $height == '' ) $height = $img_size[1];
                    }
    
                    $file               = basename($url);
                    $nameFile           = explode('.',$file);
                    $nameFile           = trim(clean($nameFile[0]));
                    $link_post          = get_permalink($post->ID);
                    $character_special  = preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\+=\{\}\[\]\\\|;:"]/', $nameFile[0]);
                    
                    if( is_numeric( $nameFile ) || $character_special || $nameFile == '' ){
                        
                        $post_name = $post->post_title;
                        $alt = trim($post_name);
                        
                    } else {
                        
                        $alt = trim(clean($nameFile));
                    }
                    
                    // Exclude words
                    $check_replace = false;
                    $exclude_words = Core\settings()->load("exclude_words");
                    $exclude_words_arr = preg_split('/\s*,\s*/', $exclude_words);
                    $pattern = '/\b(' . implode('|', $exclude_words_arr) . ')\b/i';
                    $alt_tmp = preg_replace($pattern, '', $alt_current);
                    if ($alt_tmp != $alt_current) {
                        $check_replace = true;
                    }
                    $alt_current = str_replace('  ', ' ', $alt_tmp);
                    if( $check_replace ){
    
                        $check_alt    = true;
                        $alt          = $alt_current;
                        $post_content = replace_image($url,$alt,$width,$height,$class,$post_content);
    
                        $num++;
    
                        $return["processed"][ $post->ID ]["updated_data_".$run] = array(
                            "url" => $url,
                            "alt" => $alt_current,
                            "update_alt" => true
                        );
                    }else{
                        $return["processed"][ $post->ID ]["updated_data_".$run] = array(
                            "url" => $url,
                            "alt" => $alt_current,
                            "update_alt" => false
                        );
                    }
                    $run++;
                    
                }
                
                if( $check_alt ){
                    
                    $content_builder = get_post_meta($post->ID,'_aviaLayoutBuilderCleanData',true);
                    
                    if( $content_builder != '' ){
                        
                        update_post_meta($post->ID,'_aviaLayoutBuilderCleanData',$post_content);
                    }
                    
                    $post = array(
                        'ID'           => $post->ID ,
                        'post_content' => $post_content,
                        );
                        
                    remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
                    wp_update_post($post);
                    add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
                }
                
            }
            
            // if( $check_alt ){
                
            // 	$date       = time();
            // 	$file_path  = UAA_PLUGIN_DIR . "logs/altcontent/listalt-" . $date . ".txt";
            //     $openfile   = fopen($file_path, "w") or die("Unable to open file!");
            //     $record     = "# \t File or Page Name \t\t\t Name AlT \t\t\t Link Post \n";
                
            //     fwrite($openfile, $record);
                
            // 	foreach($txt as $rec){
            // 		fwrite($openfile, $rec);
            // 	}
                
            // 	fclose($openfile);
                
            // 	$link_file = UAA_PLUGIN_URL.'logs/altcontent/listalt-'.$date.'.txt';
            // }
            
            update_option( 'update_alt', $offset + 1 );
        } else {
            update_option( 'update_alt', 0 );
        }
    
        $return["content_processed"] += 1;
    
        wp_send_json_success( $return );
    }
    
}
add_action( 'wp_ajax_'        . 'uaa_' . 'process_content_images', __NAMESPACE__ . '\\' . 'process_content_images' );
add_action( 'wp_ajax_nopriv_' . 'uaa_' . 'process_content_images', __NAMESPACE__ . '\\' . 'process_content_images' );

function clean( $string ){
    
    $string = str_replace('-'," ", $string);
    $string = str_replace('_'," ", $string);
    $string = preg_replace('/[0-9]+/','', $string);
    $result = str_replace(' x','', $string);
    
    return $result;
 }
 
 // TODO this function is useful
 function replace_image( $src, $alt, $width, $height, $class, $content ){
     
    $namefile = basename($src);
    $find     = '/<img.*src="(.*?).'.$namefile.'".*?>/';
    $string   = preg_replace( $find, "<img class=\"{$class}\" src=\"{$src}\" width=\"{$width}\" height=\"{$height}\" alt=\"{$alt}\">", $content );
    
    return $string;
 }
 