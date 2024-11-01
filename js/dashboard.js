jQuery( document ).ready(function($){
    
    $('body').on('click', '#start_indexing', function(){
        
        window.uaa_indexing = {
            posts_indexed  : [],
            posts_to_index : -1,
        };
        
        $('#result_of_media_update table tr:not(:first-of-type)').remove();
        
        $('#start_indexing_wrapper').slideUp( 250, function(){
            $('#indexing_progress').slideDown( 500 );
        });
        
        uaa_index_posts();
        
    });
    
    function uaa_index_posts(){
        
        let data = {
            action         : 'uaa_index_posts',
            posts_indexed  : window.uaa_indexing.posts_indexed,
            posts_to_index : window.uaa_indexing.posts_to_index,
        };
        
        $.post( uaa_object.ajax_url, data, function( response ){
            
            // console.log( "Ajax response:", response );
            // console.log( "Ajax data:", response.data );
            
            if( response.success === true ){
                
                window.uaa_indexing.posts_indexed  = response.data.posts_indexed;
                $('#posts_indexed').html( window.uaa_indexing.posts_indexed.length );
                
                if( window.uaa_indexing.posts_to_index < 1 ){
                    
                    window.uaa_indexing.posts_to_index = response.data.posts_to_index;
                    $('#posts_to_index').html( window.uaa_indexing.posts_to_index );
                }
                
                let percentage = window.uaa_indexing.posts_indexed.length / window.uaa_indexing.posts_to_index * 100;
                
                $('#indexing_progress_bar_filler').css( 'width', percentage + '%' );
                
                
                if( window.uaa_indexing.posts_indexed.length < window.uaa_indexing.posts_to_index ){
                    
                    uaa_index_posts();
                    
                } else {
                    
                    $('#indexing_progress').slideUp( 500, function(){
                        
                        $('#indexing_error').html( response.data.message + ' Refreshing the page, please wait...' );
                        $('#indexing_error').slideDown( 250 );
                        
                        setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 500);
                    });
                
                }
                
                
            } else {
                
                let message = response.success === false ? response.data.message : "Oops, some unknown error has occured..";
                
                $('#indexing_error').html( "<b>Error:</b> " + message );
                
                $('#indexing_progress').hide();
                $('#indexing_error').slideDown( 250 );
                
            }
            
        }, "json");
        
    }

});
