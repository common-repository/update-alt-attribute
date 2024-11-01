jQuery( document ).ready(function($){
    
    $('#media_editor tr > [data-col_type="' + $('.control_column_switch:not(.active)').attr('data-columns') + '"]').addClass('horizontally_collapsed');
    $('#media_editor').slideDown();
    
    // save all selected images
	$('#media_editor_control').on("click", '.control_column_switch:not(.active)', function(){
        
        let switch_to   = $(this).attr('data-columns');
        let switch_from = $('.control_column_switch.active').attr('data-columns');
        
        $('.control_column_switch').removeClass('active');
        $(this).addClass('active')
        
        $('#media_editor tr > [data-col_type="' + switch_from + '"]').addClass('horizontally_collapsed');
        $('#media_editor tr > [data-col_type="' + switch_to   + '"]').removeClass('horizontally_collapsed');
        
        let new_url = switch_to == 'attributes' ? uaa_object.del_url_var( 'columns' ) : uaa_object.set_url_var( 'columns', switch_to );
        history.replaceState( {}, null, new_url );
        
	});
    
    
    // display images on thumbnail hover
    $('body').on('mouseenter', '#media_editor .attachment_image a', function(){
        
        let img  = $(this).attr('data-file');
        let size = $(this).attr('data-size');
        
        // console.log( "enter:", img );
        
        if( $(this).find('#img_preview').length < 1 ){
            
            $(this).append('<div id="img_preview">');
        }
        
        $(this).find('#img_preview').append('<img src="' + img + '"/>' );
        
        $(this).find('#img_preview').append('<div id="img_info">' + size + '</div>');
        
    });
    
    // remove display image on thumbnail hover end
    $('body').on('mouseleave', '#media_editor .attachment_image a', function(){
        
        // console.log( "leave" );
        
        $(this).find('#img_preview').remove();
        
    });
    
    
    // display images being used, on hover
    $('body').on('mouseenter', '.image_used__section', function(){
        
        $(this).find('.image_used__post').slideDown(300);
    });
    
    // display images being used, on hover end
    $('body').on('mouseleave', '.image_used__section', function(){
        
        $(this).find('.image_used__post').hide();
    });
    
    
    // save single image
	$('body').on('click', '.btn_save', function(){
        
		var post_id = $(this).attr('data-id');
        
		$(this).attr("disabled", true).hide().after('<img src="' + uaa_object.loader + '" id="save_item_' + post_id + '"/>');
        
		var data = { 
			'action'        : 'uaa_save_single_image',
			'post_id'       : post_id,
            'title'         : $('#edit-' + post_id + ' input[name="title"]').val(),
            'alt'           : $('#edit-' + post_id + ' input[name="alt"]').val(),
            'description'   : $('#edit-' + post_id + ' input[name="description"]').val(),
            'caption'       : $('#edit-' + post_id + ' input[name="caption"]').val()
		}; 
		$.post( uaa_object.ajax_url, data, function( response ){
            
            $('#save_item_' + post_id ).remove();
            
			$('.btn_save').removeAttr("disabled").show();
			$('.save_item_' + post_id ).remove();
		}, "json");
	});
    
    // save all selected images
	$('body').on('click', '.btn_save_all', function(){
        
        if( $('#media_editor tbody tr th input[name="check_item"]:checked').length < 1 ){
            
			$('.lb_save').html('Error: No item selected!');
            return;
        }
        
		var image_ids = {};
        
		$('.load_ajax').show(); 
        
		$(this).hide();
        
		$('.lb_save').html('');
        
		$('#media_editor tbody tr th input[name="check_item"]').each(function(){
            
			if( $(this).is(':checked') ){
                
				var id = $(this).attr('data-id');  
                
				image_ids[ id ] = {
                    'title'         : $('#edit-' + id + ' input[name="title"]').val(),
                    'alt'           : $('#edit-' + id + ' input[name="alt"]').val(),
                    'description'   : $('#edit-' + id + ' input[name="description"]').val(),
                    'caption'       : $('#edit-' + id + ' input[name="caption"]').val()
                };
			}
		});
        
		var data = { 
			'action'    : 'uaa_save_all_images',
			'data_save' : image_ids,
		}; 
        
		$.post( uaa_object.ajax_url, data, function( response ){  
			$('.load_ajax').hide();
			$('.btn_save_all').show();
		}, "json");
        
	});
    
    
    // Number of images per page
	$('#select_per_page').change(function(){
        
		var per_page = $(this).val();
        
		if( per_page == 'custom' ){
            
			$("#per_page_custom").show();
			$('#per_page_custom').focus();
            
			return false;
		}
        
        $("#per_page_custom").hide();
        
		var url = uaa_object.set_url_var( 'per_page', per_page );
            url = uaa_object.del_url_var( 'custom', url );
            url = uaa_object.del_url_var( 'paged', url );
        
		window.location.replace( url );
	});
    
    // Custom number of images per page
	$("#per_page_custom").keydown(function(e){
        if( e.keyCode == 13 ){
            
            var defaults = [ 20, 40, 100, 200 ];
            
            var per_page = $('#per_page_custom').val();
            
            var url = uaa_object.set_url_var( 'per_page', per_page );
                url = defaults.includes( parseInt( per_page ) ) ? uaa_object.del_url_var( 'custom', url ) : uaa_object.set_url_var( 'custom', 'true', url );
                url = uaa_object.del_url_var( 'paged', url );
            
            window.location.replace( url );
        }
	});

    // Custom page number
	$("#current-page-selector").keydown(function(e){
        if( e.keyCode == 13 ){
            
            var page = $(this).val();
            
            var url = uaa_object.set_url_var( 'paged', page );
            
            window.location.replace( url );
        }
	});
    
    
    // The search(es)
	$('body').on('keydown', '#search_attributes,#search_titles,#search_categories,#search_tags', function(e){
        if( e.keyCode == 13 ){
            
			let search_value = $(this).val();
			let search_type  = $(this).attr('id');
            
            let url = search_value ? uaa_object.set_url_var( search_type, search_value ) : uaa_object.del_url_var( search_type );
            
            window.location.replace( url );
        }
	});
    
    
    // URL manipulation functions
    uaa_object.get_url_var = function( var_name, starting_url = window.location.href ){
        
        var url = new window.URL( starting_url );
        
        return url.searchParams.get( var_name );
    }
    uaa_object.set_url_var = function( var_name, var_value, starting_url = window.location.href ){
    
        var url = new window.URL( starting_url );
        
        url.searchParams.set( var_name, var_value );
        
        return url.toString();
    }
    uaa_object.del_url_var = function( var_name, starting_url = window.location.href ){
        
        var url = new window.URL( starting_url );
        
        url.searchParams.delete( var_name );
        
        return url.toString();
    }
    uaa_object.replace_url = function( new_url ){
        
        var url = new window.URL( starting_url );
        
        url.searchParams.delete( var_name );
        
        return url.toString();
    }

});
