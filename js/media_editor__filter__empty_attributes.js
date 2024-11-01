jQuery( document ).ready(function($){
    
	$('#filter__empty_attributes').change(function(){
        
		var empty_attributes = $(this).val();
        
        
		var url = uaa_object.set_url_var( 'empty_attributes', empty_attributes );
        
		if( empty_attributes == 'show_all' || ! empty_attributes ){
            
			url = uaa_object.del_url_var( 'empty_attributes', url );
		}
        
		window.location.replace( url );
	});
    
});
