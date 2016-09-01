$('body').on('click', '._addToStockroom', function(){
   
   var productId = $(this).attr('id');
   var productDesc = $(this).attr('product-desc');
   var productName = $(this).attr('product-name');
      
   $('#modalHeader').html('Enable Product (#'+productId+') In EDSR');
   $('#prodName').html($(this).attr('product-name'));
   $('#modalForm').click();
   
   
   $('._displayPrice').on('change', function(){
       if($(this).val() == 'FIXED'){
           $('input[name="fixed_price"]').slideDown();
       } else {
           $('input[name="fixed_price"]').slideUp();
       }
   });
   
   
   //Submitting the form
   $('._saveProduct').on('click', function(){
       
       
        if($('input[name="exertis_pc"]').val() == ''){
            $('input[name="exertis_pc"]').css('border-color', 'red');
            alert('Enter Exertis Part Code.');
            $('input[name="exertis_pc"]').focus();
        }
        
        else if($('._displayPrice').val() == 0){
            
            $('input[name="exertis_pc"]').css('border-color', 'green');
            
            $('._displayPrice').css('border-color', 'red');
            alert('Select Display price.');
            $('._displayPrice').focus();
        }
        
        else if($('._displayPrice').val() == 'FIXED' && $('input[name="fixed_price"]').val() == ''){
            
            $('._displayPrice').css('border-color', 'green');
            
            $('input[name="fixed_price"]').css('border-color', 'red');
            alert('Enter fixed price.');
            $('input[name="fixed_price"]').focus();
        }
        
        else {
            $('input[name="exertis_pc"]').css('border-color', 'green');
            $('._displayPrice').css('border-color', 'green');
            $('input[name="fixed_price"]').css('border-color', 'green');
            
            $('._saveProduct').html('Please Wait...');
            $('._saveProduct').attr('disabled', 'disabled');
            
            var data = { productid:productId, productname:productName, partcode:$('input[name="exertis_pc"]').val(), displayprice:$('._displayPrice').val(), fixedprice:$('input[name="fixed_price"]').val() };
            
            $.post('/digitalproduct/ajax-add-product-to-edsr', data, function(result){
                                
                if(result=='ok'){

                    $('input[name="exertis_pc"]').val('');
                    $('input[name="fixed_price"]').val('');
                    $('._saveProduct').html('Save');
                    $('._saveProduct').removeAttr('disabled');
                    
                    alert('Product has been enabled');
                    $('#'+productId).html('Disable Product');
                    $('#'+productId).attr('class', 'btn btn-danger _enableDisableProduct');
                    $('#'+productId).attr('data-action', 'disable');
                    $('.close').click();
                    
                }
            });
            
            
        }
       
   });
   
    
});

$('input[name="searchForPartCode"]').on('keydown', function(){
   var productName = $(this).val();
   $('#partCodes').html('Loading...');
   
       $.get('/digitalproduct/ajax-get-product-codes', { productname: productName }, function(data){
           
            $('#partCodes').html(data);
        });
    
});


$('body').on('click', '._enableDisableProduct', function(){
   
    var productId = $(this).attr('id');
    var action = $(this).attr('data-action');
    
    if(action == 'enable'){
        
        $.post('/digitalproduct/ajax-enable-product', { productid:productId }, function(result){
            if(result=='ok'){
                alert('Product has been enabled');
                $('#'+productId).attr('class', 'btn btn-danger _enableDisableProduct');
                $('#'+productId).attr('data-action', 'disable');
                $('#'+productId).html('Disable Product');
            }
        });
        
    }
    else if(action == 'disable'){
     
        $.post('/digitalproduct/ajax-disable-product', { productid:productId }, function(result){
            
            if(result=='ok'){
                alert('Product has been disabled');
                $('#'+productId).attr('class', 'btn btn-success _enableDisableProduct');
                $('#'+productId).attr('data-action', 'enable');
                $('#'+productId).html('Enable Product');
            }
        });
        
    }
    
});





function addToPartCode(partcode){
    $('input[name="exertis_pc"]').val(partcode);
}