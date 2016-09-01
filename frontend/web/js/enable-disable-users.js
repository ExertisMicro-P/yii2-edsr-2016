$(document).ready(function(){
    
    
    //Enable Or Disable User
    $('.EnableDisableUser').on('click', function(){
       
       //Get user's ID and status
       var userId = $(this).attr('user-id');
       var status = $(this).attr('user-status');
       
       
       if (status === 'enabled'){
           
        $.post('/useradmin/enableordisable?userId='+userId+'&status='+status+'', function(result){
            alert(result);
        });

            //Change the user-status to disabled
            $(this).attr('user-status', 'disabled');

            //Change the value to Red cross
            $(this).html('<span class="glyphicon glyphicon-remove" style="color:#F51E1E !important"></span>');

       }
       else if (status === 'disabled'){
           
        $.post('/useradmin/enableordisable?userId='+userId+'&status='+status+'', function(result){
            alert(result);
        });

            //Change the user-status to enabled
            $(this).attr('user-status', 'enabled');

            //Change the value to Green tick
            $(this).html('<span class="glyphicon glyphicon-ok text-success" style="color:#3c763d !important"></span>');

       }
       
    });
    
    
    //Enable Or Disable Shop For User
    $('.EnableDisableShop').on('click', function(){
       
       //Get user's ID and status
       var userId = $(this).attr('user-id');
       var status = $(this).attr('shop-status');
       
       
       if (status === 'enabled'){
           
        $.post('/useradmin/enableordisableshop?userId='+userId+'&status='+status+'', function(result){
            alert(result);
        });

            //Change the user-status to disabled
            $(this).attr('shop-status', 'disabled');

            //Change the value to Red cross
            $(this).html('<span class="glyphicon glyphicon-remove" style="color:#F51E1E !important"></span>');

       }
       else if (status === 'disabled'){
           
        $.post('/useradmin/enableordisableshop?userId='+userId+'&status='+status+'', function(result){
            alert(result);
        });

            //Change the user-status to enabled
            $(this).attr('shop-status', 'enabled');

            //Change the value to Green tick
            $(this).html('<span class="glyphicon glyphicon-ok text-success" style="color:#3c763d !important"></span>');

       }
       
    });
    
    
});