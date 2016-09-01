
$(document).ready(function(){
    
    
   $('._sendResetPassword').on('click', function(){
       
       spinner('start', '._sendResetPassword');
       
      
       var userId = $('input[name="userId"]').val();
       var email = $('input[name="newEmail"]').val();
       
       if(email == ""){
           $('input[name="newEmail"]').addClass('alert-danger');
           displayMessage('error', 'reset', 'Enter an email address.');
           spinner("stop", "._sendResetPassword");
       } else {
           
            $.post('/gauser/ajax-reset-password', {id:userId, email:email}, function(result){
                
                var result = $.parseJSON(result);
                spinner("stop", "._sendResetPassword");
                
                if(result.status !== 200) {
                    $('input[name="newEmail"]').removeClass('alert-success');
                    $('input[name="newEmail"]').addClass('alert-danger');
                    displayMessage('error', 'reset', result.message);
                } else {
                    $('input[name="newEmail"]').removeClass('alert-danger');
                    $('input[name="newEmail"]').addClass('alert-success');
                    displayMessage('success', 'reset', result.message);
                }

            });

       }
       
       
   });
    
    
   $('._resendInvitation').on('click', function(){
       
       spinner('start', '._resendInvitation');
       
      
       var userId = $('input[name="userId"]').val();
       var email = $('input[name="newEmailResend"]').val();
       
       if(email == ""){
           $('input[name="newEmailResend"]').addClass('alert-danger');
           displayMessage('error', 'resend', 'Enter an email address.');
           spinner("stop", "._resendInvitation");
       } else {
           
            $.post('/gauser/ajax-resend-invitation', {id:userId, email:email}, function(result){
                
                var result = $.parseJSON(result);
                spinner("stop", "._resendInvitation");
                
                if(result.status !== 200) {
                    $('input[name="newEmailResend"]').removeClass('alert-success');
                    $('input[name="newEmailResend"]').addClass('alert-danger');
                    displayMessage('error', 'resend', result.message);
                } else {
                    $('input[name="newEmailResend"]').removeClass('alert-danger');
                    $('input[name="newEmailResend"]').addClass('alert-success');
                    displayMessage('success', 'resend', result.message);
                }

            });

       }
       
       
   });
   
   
   function spinner(type, btn){
       
       var btn = $(btn);
       
       switch (type) {
           
           case "start":
               btn.attr('disabled', 'disabled');
               btn.addClass('active');
               btn.html('<div class="loader"></div> Please wait...');
               break;
               
           case "stop":
               btn.removeAttr('disabled');
               btn.html('Send');
               break;
           
       }
       
   }
   
   
   function displayMessage(type, form, msg){
       
       var message = $('.message-'+form);
       
       switch (type){
           
           case "error":
               message.css('color', '#a94442');
               message.html(msg);
               break;
           
           case "success":
               message.css('color', '#3c763d');
               message.html(msg);
               break;
           
       }
       
   }
    
    
});