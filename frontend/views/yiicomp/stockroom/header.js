/**
 * HEADER
 * ======
 * This module handles the display, and later editing, of the stockroom name.
 *
 */
define(["text!./header?t=h", "utils"], function(stockTemplate, utils) {


    function stockroom (item) {
        var self = this ;
        self.id = ko.observable(item.id) ;
        self.name = ko.observable(item.name ? item.name : '-- Un-named --') ;
        self.accountlogo = ko.observable(item.accountlogo) ;
    }

    function StockViewModel(params) {
        var self = this ;

        // -------------------------------------------------------------------
        // This can be toggled either here or elsewhere using the event.
        // This basket refers to the main display, not the shopping basket
        // -------------------------------------------------------------------
        self.showingBasket = ko.observable(false).syncWith('product.showBasket', true, true, function (newValue) {
            if (newValue) {
                $('#stockroom-index').fadeOut('slow') ;
            } else {
                $('#stockroom-index').fadeIn('slow') ;
            }
        }) ;
        
        self.route = params.route ;
        self.currentStockroom = ko.observable(new stockroom({id: 0, name: ''})).publishOn('product.stockroom') ;
        self.currentStockroomName = ko.observable(self.currentStockroom().name()) ;
        self.currentStockroomAccountLogo = ko.observable(self.currentStockroom().accountlogo()) ;
        
        self.stockroomNames = ko.observableArray() ;

        self.selectedCount = ko.observable(0).subscribeTo('product.selectedCount', true) ;

        self.canBuy      = ko.observable(false).publishOn('can.buy');
        self.isRetailView      = ko.observable(false).publishOn('is.retailview');

        /**
         * TOGGLE BASKET
         * =============
         * Shows or hides the selected basket items
         */
        self.toggleBasket = function () {
            if (self.selectedCount() > 0 || self.showingBasket()) {
                self.showingBasket(!self.showingBasket());
            }
        }

        $.get('/yiicomp/stockroom/stockroom?t=d', 'json')
            .done(function (data) {
                var names = [];
        
                if (data.stockrooms) {

                    for (var ind = 0; ind < data.stockrooms.length; ind++) {
                        names[names.length] = new stockroom(data.stockrooms[ind]);
                    }
                }
                if (names.length == 0) {
                    names[0] = new stockroom({id: 0, name: '', accountlogo: ''}) ;
                }
                
                //console.log(document.title);

                self.canBuy (data.accountStatus == 'T') ;
                
                self.stockroomNames (names) ;
                self.currentStockroom(self.stockroomNames()[0]) ;
                
                if (window.location.pathname == '/'){
                    self.currentStockroomName (self.currentStockroom().name()) ;
                } else {
                    self.currentStockroomName (document.title) ;
                }
                
                self.currentStockroomAccountLogo (self.currentStockroom().accountlogo()) ;
            })


    }

    return { viewModel: StockViewModel, template: stockTemplate };

});
