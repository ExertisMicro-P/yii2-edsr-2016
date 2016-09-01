$(document).ready(function(){
    $('._setTestUser').hide();
    
    
    $('._enableDisableShop').on('click', function(){
       
        var action  = $(this).attr('data-action');
        var account = $(this).attr('data-account');
       
        if(action === 'enable'){
            
            $.post('/account/ajax-manage-shop?accountId='+account+'&action=enable', function(result){
                
            });
            
            $(this).html('<span class="glyphicon glyphicon-remove text-danger"></span>');
            $(this).attr('data-action', 'disable');
            
        }
        
        else if(action === 'disable') {
            
            $.post('/account/ajax-manage-shop?accountId='+account+'&action=disable', function(result){
                
            });

            $(this).html('<span class="glyphicon glyphicon-ok text-success"></span>');
            $(this).attr('data-action', 'enable');
            
        }
        
    });
    
    
    $('select[name="adminuser"]').on('change', function(){
        var self = $(this);
        
        if(self.val() == ''){
            $('._setTestUser').slideUp(500);
        } else {
            $('._setTestUser').html('Set '+self.val()+' To This Account');
            $('._setTestUser').slideDown(500);
        }
        
    });
    
    $('._setTestUser').on('click', function(){
       
        var account = $(this).attr('data-account');
        var user = $('select[name="adminuser"]').val();
        
        $.post('/account/ajax-set-test-user?user='+user+'&account='+account+'', function(result){
            if(result == '<b style="color:green">'+user+' has been set to this account!</b>'){
                $('._setTestUser').slideUp(500);
                $('select[name="adminuser"]').slideUp(500);
            }
            
            $('.helper').html(result);
            
            setTimeout(function(){
                $('.helper').fadeOut(500);
            }, 3000);
            
        });
        
    });
    
    
    $('._setTestUser2').on('click', function(){
       
        var account = $(this).attr('data-account');
        
        $.post('/account/ajax-set-test-user2?account='+account+'', function(result){
            if(result == '<b style="color:green">Dominik set to this account!</b>'){
                $('._setTestUser').fadeOut(500);
            }
            
            $('.helper').html(result);
            
            setTimeout(function(){
                $('.helper').fadeOut(500);
            }, 3000);
            
        });
        
    });
    
})