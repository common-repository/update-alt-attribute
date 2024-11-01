jQuery( document ).ready(function($){
    
    $('body').on('click', '#update_media,#update_media_dry_run', function(){
        
        let dry_run = $(this).attr('id') === 'update_media_dry_run' ? "yes" : "no";
        
        window.processing_media = {
            images_processed  : 0,
            total_to_process  : $('#faulty_images').text(),
        };
        
        $('#images_to_process').html( window.processing_media.total_to_process );
        
        $('#result_of_media_update table tr:not(:first-of-type)').remove();
        
        if( dry_run !== "yes" ){
            
            $('#update_media_button_wrapper').slideUp( 250, function(){
                $('#update_media_progress').slideDown( 500 );
            });
        }
        
        process_media_images( dry_run );
        
    });
    
    function process_media_images( dry_run ){
        
        let data = {
            action            : 'uaa_process_media_images',
            images_processed  : window.processing_media.images_processed,
            dry_run           : dry_run,
        };
        
        $.post( uaa_object.ajax_url, data, function( response ){
            
            // console.log( "Ajax response:", response );
            // console.log( "Ajax data:", response.data );
            
            if( response.success === true ){
                
                window.processing_media.images_processed = response.data.images_processed;
                
                
                $('.tab_info_content').html( response.data.stats_html );
                
                $('#result_of_media_update > *').slideDown();
                
                let processed_text  = window.processing_media.images_processed + " images "; 
                    processed_text  = dry_run === "yes" ? "Dry run, " + processed_text : processed_text;
                    processed_text += dry_run === "yes" ? "would be processed:" : "were processed:";
                $('#result_of_media_update > h3').html( processed_text );
                
                
                $('#images_processed').html( window.processing_media.images_processed );
                
                
                
                let percentage = window.processing_media.images_processed / window.processing_media.total_to_process * 100;
                
                $('#update_media_progress_bar_filler').css( 'width', percentage + '%' );
                
                
                $.each( response.data.processed, function( attachment_id, attachment ){
                    
                    // console.log( "Processed attachment: ", attachment );
                    
                    let icon_yes = '<span class="dashicons dashicons-yes" style="color: gray;"></span>';
                    
                    let new_element  = '<tr id="attachment_' + attachment_id + '" style="display: none;">';
                    
                        new_element +=   '<td>' + attachment_id         + '</td>';
                        new_element +=   '<td>' + attachment.file_name  + '</td>';
                        new_element +=   '<td>' + attachment.text       + '</td>';
                        
                        new_element +=   '<td>' + ( attachment.updated_alt          ? icon_yes : "" ) + '</td>';
                        new_element +=   '<td>' + ( attachment.updated_title        ? icon_yes : "" ) + '</td>';
                        new_element +=   '<td>' + ( attachment.updated_description  ? icon_yes : "" ) + '</td>';
                        new_element +=   '<td>' + ( attachment.updated_caption      ? icon_yes : "" ) + '</td>';
                        
                        new_element += '<tr>';
                    
                    $('#result_of_media_update table').append( new_element );
                    
                    $('#result_of_media_update table tr#attachment_' + attachment_id ).slideDown();
                });
                
                
                
                if( window.processing_media.images_processed < window.processing_media.total_to_process ){
                    
                    process_media_images( dry_run );
                    
                } else {
                    
                    if( dry_run !== "yes" ){
                        
                        $('#update_media_progress').slideUp( 500, function(){
                            
                            $('#update_media_nothing_to_do').slideDown( 250 );
                        });
                    }
                
                }
                
                
            } else {
                
                let message = response.success === false ? response.data.message : "Oops, some unknown error has occured..";
                
                $('#update_media_nothing_to_do').html( "<b>Error:</b> " + message );
                
                $('#update_media_progress').hide();
                $('#update_media_nothing_to_do').slideDown( 250 );
                
            }
            
        }, "json");
        
    }

    $('body').on('click', '#update_content,#update_content_dry_run', function(event){
        event.preventDefault();

        let list_post_type = [];
        let e_post_type = $('ul.custom_post_types_checkboxes li input#post_type_selected:checked');
        list_post_type = e_post_type.map(function() {
            return $(this).val();
        }).get();

        window.processing_content = {
            content_processed  : 0,
            total_to_process   : 0,
            list_to_process    : '',
            list_post_type     : list_post_type,
        };
        
        $('#result_of_content_update table tr:not(:first-of-type)').remove();
        
        $('#update_content_progress').slideDown( 500 );
        
        process_content_images( );
        
    });

    function process_content_images( ){
        let data = {
            action            : 'uaa_process_content_images',
            content_processed : window.processing_content.content_processed,
            total_to_process  : window.processing_content.total_to_process,
            list_to_process  : window.processing_content.list_to_process,
            list_post_type  : window.processing_content.list_post_type,
        };
        
        $.post( uaa_object.ajax_url, data, function( response ){
            
            // console.log( "Ajax response:", response );
            // console.log( "Ajax data:", response.data );
            if( response.success === true ){
                
                window.processing_content.content_processed = response.data.content_processed;
                window.processing_content.total_to_process = response.data.total_to_process;
                window.processing_content.list_to_process = response.data.list_to_process;

                $('#result_of_content_update > *').slideDown();

                $('#content_to_process').html( window.processing_content.total_to_process );
                $('#content_processed').html( window.processing_content.content_processed );
                
                let percentage = window.processing_content.content_processed / window.processing_content.total_to_process * 100;
                
                $('#update_content_progress_bar_filler').css( 'width', percentage + '%' );

                $.each( response.data.processed, function( post_id, attachment ){
                    $.each( attachment, function( key, value ){

                        let icon_yes = '<span class="dashicons dashicons-yes" style="color: gray;"></span>';
                        
                        let new_element  = '<tr id="update_content_' + post_id + '" style="display: none;">';
                        
                            new_element +=   '<td>' + post_id + '</td>';
                            new_element +=   '<td>' + value.url + '</td>';
                            new_element +=   '<td>' + value.alt + '</td>';
                            new_element +=   '<td>' + ( value.update_alt ? icon_yes : "" ) + '</td>';
                            new_element += '<tr>';
                        
                        $('#result_of_content_update table').append( new_element );
                    });
                    
                    
                    $('#result_of_content_update table tr#update_content_' + post_id ).slideDown();
                });
                
                if( window.processing_content.content_processed < window.processing_content.total_to_process ){
                    process_content_images();
                }else{
                    $('#update_content_progress_bar_filler').css('background', '#008000');
                }
            } else {
                
                let message = response.success === false ? response.data.message : "Oops, some unknown error has occured..";
                
                $('#update_content_nothing_to_do').html( "<b>Error:</b> " + message );
                
                $('#update_content_progress').hide();
            }
            
        }, "json");
        
    }
});
