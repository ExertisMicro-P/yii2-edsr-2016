$(document).ready(function(){
   
    $('.price').on('blur', function(){
        
        var qty = $(this);
        
        $('.pleaseWait').html('Please wait...');
        $('input').prop('disabled', true);
    
        $.get('/checkout/getlimit', function(keyLimit){

            $.get('/checkout/getbasket', function(basket){
                
                $.get('/orders/spendinglimitreached', function(spendingLimitReached){

                    if(spendingLimitReached){
                        qty.val(1);
                        $('.pleaseWait').html('You have reached your spending limit.');
                    }
                    
                    //console.log(parseInt(basket) + " > " + parseInt(keyLimit));

                    basket = parseInt(basket);
                    keyLimit = parseInt(keyLimit);

                    if(basket > keyLimit){

                        var takeOff = basket - keyLimit;
                        var newQty = qty.val() - takeOff;
                        
                        setTimeout(function(){
                            $.get('/checkout/getitemqty?item='+qty.attr('data-partcode')+'', function(itemQty){
                                qty.val(itemQty);
                        
                                $('input').prop('disabled', false);
                            });
                        }, 300);
                        
                        $('.pleaseWait').html('You can only have '+keyLimit+' products in your basket.');

                    } else {
                        $('input').prop('disabled', false);
                        $('.pleaseWait').html('');
                    }

                });  

            });  

        });
        
    });
    
});