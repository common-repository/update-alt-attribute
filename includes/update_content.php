<?php

// this function is hooked to a Cron job that gets scheduled on Update Post Content button
// runs every hour
function update_alt_all_content_func(){
    
   global $wpdb;
    
   if( ! get_option('update_alt') ){
       update_option('update_alt', 0 );
   }
   
   $get_post    = get_option('custom_post_update_alt');
   $offset      = get_option('update_alt');
   $limit       = 20;
   $limit       = $offset * $limit;
   $data_query  = $wpdb->get_results("SELECT ID, post_content, post_title FROM $wpdb->posts WHERE post_type IN ('".implode("','",$get_post)."')  and post_status='publish' LIMIT 20 OFFSET ".$limit."");
   $txt         = array();
   $check_alt   = false;
   
   if( count( $data_query ) > 0 ){
       
        $num = 1;
       
		foreach( $data_query as $post ){
            
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
                    
					if( $width == '' || $height == '' ){
                        
						$img_size = getimagesize( $url );
                        
						if( $width == ''  ) $width  = $img_size[0];
						if( $height == '' ) $height = $img_size[1];
					}
                    
					$class              = $class[1][0];
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
                    
					if( empty( $alt_current ) || empty( $width ) || empty( $height ) ){
                        
						$check_alt    = true;
						$post_content = replace_image($url,$alt,$width,$height,$class,$post_content);
                        
						$txt[] = $num." \t ".$file." \t\t\t ".$alt." \t\t\t ".$link_post." \n";
                        
						$num++;
					}
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
		}
		if( $check_alt ){
            
			$date       = time();
			$file_path  = UAA_PLUGIN_DIR . "logs/altcontent/listalt-" . $date . ".txt";
		    $openfile   = fopen($file_path, "w") or die("Unable to open file!");
		    $record     = "# \t File or Page Name \t\t\t Name AlT \t\t\t Link Post \n";
            
		    fwrite($openfile, $record);
            
			foreach($txt as $rec){
				fwrite($openfile, $rec);
			}
            
			fclose($openfile);
            
			$link_file = UAA_PLUGIN_URL.'logs/altcontent/listalt-'.$date.'.txt';
		}
        
		update_option( 'update_alt', $offset + 1 );
        
	} else {
        
		update_option( 'update_alt', 0 );
    }
    
    wp_clear_scheduled_hook('mm_update_alt_post');
    $timeNext = time()+(5*60);
	// wp_schedule_event($timeNext,'hourly','mm_update_alt_post');
    
	die();
}
// add_action(                     'mm_update_alt_post',     'update_alt_all_content_func' );

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
