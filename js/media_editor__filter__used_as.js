jQuery( document ).ready(function($){
    
	$('#filter__used_as').change(function(){
        
		var used_as = $(this).val();
        
        
		var url = uaa_object.set_url_var( 'used_as', used_as );
        
		if( used_as == 'show_all' || ! used_as ){
            
			url = uaa_object.del_url_var( 'used_as', url );
		}
        
		window.location.replace( url );
	});
    
});
