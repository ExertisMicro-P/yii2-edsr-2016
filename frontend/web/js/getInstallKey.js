$(document).ready(function(){
   
    $('._showKey').on('click', function(){
        
        var id = $(this).attr('data-id');
        var btn = $(this);
        btn.fadeOut();
        
        $.get('/orders/ajax-get-install-key?id='+id, function(result){
           
            $('.showKey-'+id+'').html(result);
            
        });
        
        
    });
    
});