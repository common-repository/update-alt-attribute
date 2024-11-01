jQuery( document ).ready(function($){
    
	$('#filter__faulty_names').change(function(){
        
		var faulty_names = $(this).val();
        
        
		var url = uaa_object.set_url_var( 'faulty_names', faulty_names );
        
		if( faulty_names == 'show_all' || ! faulty_names ){
            
			url = uaa_object.del_url_var( 'faulty_names', url );
		}

		window.location.replace( url );
	});
    
});
