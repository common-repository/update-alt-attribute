<?php

// this function is hooked to a Cron job that gets scheduled in the settings,
// runs every hour
function mm_update_info_database_func(){
    
    global $wpdb;
    
    $table_lists = array('posts','options','comments','postmeta');
    
	if( ! get_option('update_info_db') ){
        update_option('update_info_db', 0 );
    }
    
    $offset     = get_option('update_info_db');
	$limit      = 5;
    $limit      = $offset * $limit;
	$prefix     = $wpdb->prefix;
	$check_data = false;
    
	foreach( $table_lists as $table ){
        
		$result_query = $wpdb->get_results("SELECT * FROM " . $prefix . $table . " LIMIT 5 OFFSET " . $limit );
        
		if( count( $result_query ) > 0 ){
            
			$check_data = true;
			$num = 1;
            
			foreach( $result_query as $result ){
                
				$content = '';
				$alt     = 'images';
                
				if( $table == 'posts' ){
                    
					$content     = $result->post_content;
					$alt         = $result->post_title;
					$new_content = getStringImage($content,$alt);
                    
					if( $new_content != '' ){
                        
						$content_builder = get_post_meta( $result->ID, '_aviaLayoutBuilderCleanData', true );
                        
						if( $content_builder != '' ){
                            
                            update_post_meta( $result->ID, '_aviaLayoutBuilderCleanData', $new_content );
						}
                        
						$result = array(
                            'ID'           => $result->ID ,
                            'post_content' => $new_content,
						);
                        
                        remove_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );
						wp_update_post($result);
                        add_action( 'save_post', 'MauiMarketing\\UpdateAltAtribute\\Posts\\index_saved_post', 10, 3 );

					  }
				}
                
				if( $table == 'options' ){
                    
					$content     = $result->option_value;
					$new_content = getStringImage( $content );
                    
					if( $new_content != '' ){
                        
						$option_name = $result->option_name;
						update_option($option_name,$new_content);
					}
				}
                
				if( $table == 'comments' ){
                    
					$content     = $result->comment_content;
					$new_content = getStringImage( $content );
                    
					if( $new_content != '' ){
                        
						$commentarr = array();
						$commentarr['comment_ID'] = $result->comment_ID;
						$commentarr['comment_content'] = $new_content;
                        
						wp_update_comment( $commentarr );
					}
				}
				if( $table == 'postmeta' ){
                    
					$content     = $result->meta_value;
					$new_content = getStringImage( $content );
                    
					if( $new_content != '' ){
                        
						$post_id = $result->post_id;
                        
						update_post_meta($post_id,$result->meta_key,$new_content);
					}
				}
			}
		}
	}
    
	if( ! $check_data ){
        
		update_option('update_info_db', 0 );
	}
    
}
// add_action(                     'mm_update_info_database', 'mm_update_info_database_func' );

// TODO this function is useful
function getStringImage( $content,$alt = 'Images' ){
    
	$post_content = '';
    
	preg_match_all('/<img[^>]+>/i', $content, $images );
    
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
            
			if( $width == '' || $height == '' ){
                
                $img_size = getimagesize($url);
                
                if( $width == ''  ) $width  = $img_size[0];
                if( $height == '' ) $height = $img_size[1];
			}
            
			$class              = $class[1][0];
			$file               = basename($url);
			$nameFile           = explode('.',$file);
			$nameFile           = trim(clean($nameFile[0]));
			$character_special  = preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\+=\{\}\[\]\\\|;:"]/', $nameFile[0]);
            
			if( is_numeric( $nameFile ) || $character_special || $nameFile == '' ){
                
                $post_name = $alt;
                $alt = trim($post_name);
                
			} else {
                
                $alt = trim(clean($nameFile));
                
			}
            
			if( empty( $alt_current ) || empty( $width ) || empty( $height ) ){
                
                $post_content = replace_image( $url, $alt, $width, $height, $class, $content );
			}
        }
        
	}
    
	return $post_content;
}
