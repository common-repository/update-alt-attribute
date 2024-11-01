jQuery( document ).ready(function($) {
    
    String.prototype.filename = function( extension ){
        var s = this.replace(/\\/g, '/');
            s = s.substring(s.lastIndexOf('/')+ 1);
        return extension ? s.replace(/[?#].+$/, '') : s.split('.')[0];
    }
    
    $("img").each(function(){ 
    
        var alt = $(this).attr('alt');
        
        if( alt === "" || alt === undefined ){
            
            var namefile = $(this).attr('src').filename();
            var rep      = alt === "" ? namefile.replace(/[_#?%*$@!=]/g,'-') : namefile.replace(/[_#?%*$@!=]/g,'-');
            var temp     = rep.split('-');
            var alt      = '';
            
            for( i = 0; i < temp.length; i++ ){ 
            
                alt =  alt + ' ' + temp[ i ];
            }
            
            $(this).attr('alt', alt );
            
        }
        
    });
});



