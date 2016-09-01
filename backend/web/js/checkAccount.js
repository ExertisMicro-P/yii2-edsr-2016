$(document).ready(function(){
   
    $('#salesrepuseremailsetupform-exertis_account_number').on('blur', function(){
       
        var accountNumber = $('#salesrepuseremailsetupform-exertis_account_number').val().split(' ')[0];
        $('.rep_ids p').html('Please Wait...');
        
        $.get('/sales-rep-account-email-form/ajaxfindaccount', { accountNo: accountNumber }, function(data, status){
           
                $('#accFound').attr('value',data);
                
                if(data>0){
                    $('.field-salesrepuseremailsetupform-edi_rep').slideUp();
                    $('.rep_ids').slideUp();
                } else {
                    $('.field-salesrepuseremailsetupform-edi_rep').slideDown();
                    
        
                    $.get('/sales-rep-account-email-form/ajaxgetrepids', { accountNo: accountNumber }, function(table){

                        $('.rep_ids p').html('EDI Rep IDs');

                        $('.rep_ids').html(table);
                        $('.rep_ids').slideDown();

                    });
                    
                }
           
        });
        
    });
    
        
});

function addId(id){
    
    $('#salesrepuseremailsetupform-edi_rep').attr('value', id);
    
}