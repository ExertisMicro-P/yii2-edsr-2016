define(["text!./basket.html"], function (Template) {    
    
    /**
     * ITEM
     * ====
     * Simple object to store details of an individual product
     * @param itemDetails
     * @constructor
     */
    function Item(itemDetails) {       
        var self = this;

        if (itemDetails.item) {
            self.item = itemDetails.item;

        } else {
            self.item = itemDetails;
        }
        self.cost     = parseFloat(self.item.cost) ;
        self.quantity = ko.observable(itemDetails.quantity).publishOn('product.order.change', true, function (newValue, oldValue) {
            return false ;           // Always publish
        });
        self.active   = ko.observable(true);
        self.creditBalance = ko.observable().subscribeTo('credit.balance') ;
        self.isRetailView = ko.observable().subscribeTo('is.retailview') ; // RCh 20151005

        self.inError = ko.observable(false) ;     
        self.maxLimitReached = ko.observable(false) ;        

        /**
         * NEW QUANTITY
         * ============
         */
        self.newQuantity = ko.computed( {
            read:   function () {
                return self.quantity() ;
            },
            write: function (quantity) {
                var qChange ;
                var pChange ;
                var price = self.item.price ;
                var inQuantity
                var cBalance = self.creditBalance() + (self.quantity() * self.cost) ;
                inQuantity = quantity = parseInt(quantity) ;
                
                // RCH 20160322
                // Introduce a hard upper limit so that people do not buy 1000s of Xbox gift card etc
                //
                                
                if (!isNaN(quantity)) {
                    while (quantity && cBalance < (quantity * self.cost)) {
                        quantity--;
                    }
                    // -------------------------------------------------------
                    // Can now set the new quantity, which triggers the
                    // publishOn event, which then sends the details to the server
                    // -------------------------------------------------------
                    self.quantity(quantity);
                    self.inError(quantity != inQuantity);
                    
                    $('#checkoutBtn').html('Please wait...');                    
                    $('#checkoutBtn').prop('disabled', true);
                                            
                    $.get('/orders/getkeylimit', function(keyLimit){
                        
                        $.get('/orders/countbasket', function(basket){
                            

                            $('#checkoutBtn').html('Checkout <i class="glyphicon glyphicon-chevron-right"></i>');                    
                            $('#checkoutBtn').prop('disabled', false);
                    
                            if(parseInt(basket) > parseInt(keyLimit)){
                                
                                var takeOff = basket - keyLimit;
                                var newQty = quantity - takeOff;
                                
                                self.maxLimitReached(true);
                                self.quantity(newQty);
                                ko.postbox.publish('product.quantity.' + self.item.partcode, {quantity: newQty, cost: newQty * self.cost});
                            } else {
                                self.maxLimitReached(false);
                                ko.postbox.publish('product.quantity.' + self.item.partcode, {quantity: quantity, cost: quantity * self.cost});
                            }
                        });
                        
                    });
                    

                }                
            }
        }) ;
        
        /**
         * SUB TOTAL
         * ========
         */
        self.subtotal = ko.computed(function () {
            var subtotal = parseInt(self.quantity()) * self.item.cost;

            return subtotal;
        }, this, false);

        /**
         * ADD ONE
         * ======
         */
        self.addOne = function () {
            self.quantity(parseInt(self.quantity()) + 1);
            self.active(true);
        };

        /**
         * REMOVE ONE
         * ==========
         */
        self.removeOne = function () {
            if (self.quantity() > 0) {
                self.quantity(parseInt(self.quantity()) - 1);
            }
        };

        /**
         * TO JSON
         * =======
         * This is used to format the data ready for sending to the server.
         * It only removes the functions/computeds to allow the full item
         * to be rebuilt after re-reading from the server.
         *
         * @returns {{partcode: (*|jQuery|partcode), quantity: *}}
         */
        self.toJSON = function () {
            var data = ko.toJS(self);
            delete data.active;
            delete data.addOne;
            delete data.removeOne;
            delete data.subtotal;

            //
            //var data = {
            //    partcode: self.item.partcode,
            //    quantity: self.quantity()
            //} ;
            return data;
        };
    }

    function ViewModel(params) {
        var model = this;

        model.canBuy        = ko.observable(true).subscribeTo('can.buy', true);
        model.isRetailView  = ko.observable(true).subscribeTo('is.retailview'); // RCH 20151002
        model.showingBasket = ko.observable(false);
        model.items         = ko.observableArray();
        model.reloading     = true;
        model.creditBalance = ko.observable(0).publishOn('credit.balance') ;
        model.creditLimit   = ko.observable(0);

        /**
         * BASKET COUNT
         * ============
         * Loops to count the current product count. Being a compute
         * this is cached and the code only called it the items change.
         */
        model.basketCount = ko.computed(function () {
            var total = 0;
            for (var ind = 0; ind < model.items().length; ind++) {
                total += parseInt(model.items()[ind].quantity());
            }
            
            $.get('/orders/getkeylimit', function(keyLimit){
                if(total > keyLimit) {
                    total = keyLimit;
                    return total;
                }
            });
            
            return total;
        })

        /**
         * SHOW BASKET
         * ===========
         */
        model.showBasket = function () {
            if (model.showingBasket()) {
                var removed = model.items.remove(function (item) {
                    return item.quantity() <= 0;
                });

                delete removed;
            }

            model.showingBasket(!model.showingBasket());
        };

        /**
         * TOTAL COST
         * ==========
         * Iterates over the products to determine the total cost.
         */
        model.totalCost = ko.computed(function () {
            var cost = 0;
            for (var ind = 0; ind < model.items().length; ind++) {
                cost += model.items()[ind].quantity() * model.items()[ind].item.cost;
            }
            return cost;
        });

        /**
         * REMOVE ALL
         * ==========
         * @param which
         * @param event
         */
        model.removeAll = function (which, event) {
            which.quantity(0);
            which.active(false);
        }

        /**
         * UPDATE CREDIT
         * =============
         * @param credit
         */
        model.updateCredit = function (credit) {
            model.creditBalance(credit.balance);
            model.creditLimit(credit.limit);

            var balance = parseFloat(credit.balance) ;
            $('.buymore').each (function (index, element) {
                var price = $(element).data('price') ;
                if (price > balance) {
                    $(element).prop('disabled', true).parents('td').addClass('disabled') ;
                } else {
                    $(element).prop('disabled', false).parents('td').removeClass('disabled') ; ;
                }
            }) ;

            var numBalance = Number(balance) ;
            $('.cbalance').text(numBalance.formatMoney(2)) ;
        } ;

        /**
         * GET BOOTSTRAP TEMPLATE
         * ======================
         * This returns the outline html for a bootstrap modal, with no buttons
         * The visible content needs to be inserted into the modal-body div
         */
        function getBootstrapTemplate() {
            $modal =
                $('<div class="modal fade">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                            //'<div class="modal-header">' +
                            //    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                            //    '<h4 class="modal-title"></h4>' +
                            //'</div>' +
                            '<div class="modal-body">Loading Product Details...' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>') ;
            return $modal ;
        }        

        /**
         * VIEW DETAILS
         * ============
         * Loads the detail view for the passed product into the modal_body
         * then displays the modal agaisnt a fixed backdrop
         */
        ko.postbox.subscribe('view.details', function (itemId) {

            var $modal = getBootstrapTemplate() ;
            $('.modal-body', $modal).load('/shop/detail?product=' + itemId.split('-')[1]);

            $('body').append($modal);
            $modal.modal({backdrop: 'static', keyboard: false});
        }) ;

            /**
         * SUBSCRIBE TO BUY MORE
         * =====================
         */
        ko.postbox.subscribe('buy.more', function (itemId) {
            
            $.get('/orders/getkeylimit', function(keyLimit){        
                $.get('/orders/countbasket', function(basket){
                    var shouldAdd = true;
                    if(parseInt(basket) >= parseInt(keyLimit)){
                        $('.errorMsg').html("<b>You can only have "+keyLimit+" products in your basket.</b>");
                        $('.errorMsg').slideDown();
                        
                        setTimeout(function(){
                            $('.errorMsg').slideUp();
                        }, 5000);
                        
                        shouldAdd = false;
                    } else {

                        var row      = $('#' + itemId);
                        var cbColumn = row.children().length;
                        var partcode = $('> td:nth-child(' + (cbColumn == 7 ? 3 : 2) + ')', row).text();
                        var found    = false;

                        // ---------------------------------------------------------------
                        // First thing - highlight the partcode while we process it (sort of)
                        // ---------------------------------------------------------------
                        $("td:eq(1)", row).addClass("highlightflash") ;
                        setTimeout(function () {
                            $("td:eq(1)", row).removeClass("highlightflash") ;
                        }, 750) ;


                        // ---------------------------------------------------------------
                        // Check if already ordered and increment the count if it is
                        // ---------------------------------------------------------------
                        for (var ind = 0; ind < model.items().length; ind++) {
                            if (partcode === model.items()[ind].item.partcode) {
                                model.items()[ind].addOne();
                                found = true;
                                break;
                            }
                        }

                        if (!found) {
                            var itemDetails = {
                                productId  : row.attr('id'),
                                stockItem  : row.attr('data-key'),
                                product_id : row.attr('data-digId'),
                                photo      : $('> td:nth-child(' + (cbColumn == 7 ? 2 : 1) + ') >img', row).attr('src'),
                                partcode   : partcode,
                                description: $('> td:nth-child(' + (cbColumn == 7 ? 4 : 3) + ')', row).text(),
                                cost       : parseFloat($('input.price', row).val()),
                                quantity   : 1,
                            };

                            // -----------------------------------------------------------
                            // Create the new object, add it to the array, then publish it
                            // We can't use the publishOn event in the item as at that point
                            // it isn't in the array
                            // -----------------------------------------------------------
                            
                            if(shouldAdd){
                                var item = new Item(itemDetails);
                                model.items.push(item);
                                ko.postbox.publish('product.order.change') ;
                            }
                        }
                    
                    }
            
                });

            });
            
        });

        /**
         * UPDATE STORAGE
         * ==============
         * Activated when an item quantity (not it's computed newQuantity)
         * is modified, this is responsible for updating the server.
         */
        ko.postbox.subscribe('product.order.change', function () {
            if (!model.reloading) {
                var data = ko.toJSON(model.items());
                //localStorage.setItem("product.buy.more", data);
                
                $.post('/orders/flaggedtobuy', {items: data}, 'json')
                    .done(function (data) {
                        data            = $.parseJSON(data);
                        model.updateCredit(data['credit']);
                    })
            }
        });

        ko.postbox.subscribe('product.quantity.change', function (what) {
            var pcode = what.pcode;
            for (var ind = 0; ind < model.items().length; ind++) {
                if (pcode === model.items()[ind].item.partcode) {
                    model.items()[ind].newQuantity(what.quant);
                }
            }
        });

        /**
         * RELOAD ITEMS
         * ============
         */
        model.reloadItems = function () {

            $.get('/orders/flaggedtobuy')
                .done(function (data) {                    
                    data            = $.parseJSON(data);
                    $.each(data['selected'], function (key, item) {
                        model.items.push(new Item(item));
                    });
                    model.updateCredit(data['credit']);
                    model.reloading = false;
                });
        }

        // ---------------------------------------------------------------
        // All data initialised, so now reload any previously selected items
        // ---------------------------------------------------------------
        model.reloadItems();

        model.route = params.route;
    }

    return {viewModel: ViewModel, template: Template};
    

});
