<?php


/*
 *  This doesn't appear to be used anymore
 */

if (!$model) { ?>

define([], function() {

    return {template: ' '} ;
}) ;

<?php } else {
?>
define(["knockout", "text!./stockroom?t=h", "utils", "postbox"], function(ko, stockTemplate, utils, postbox) {

    function stockroom (item) {
        var self = this ;
        self.id = ko.observable(item.id) ;
        self.name = ko.observable(item.name ? item.name : '-- Un-named --') ;
        self.accountlogo = ko.observable(item.accountlogo) ;
    }

    function StockViewModel(params) {
        var self = this ;

        window.ko = ko ;
        window.postbox = postbox ;

        self.route = params.route ;
        self.currentStockroom = ko.observable(new stockroom({id: 0, name: '', accountlogo: ''})) ;
        self.currentStockroomName = ko.observable(self.currentStockroom().name()) ;
        self.currentStockroomAccountLogo = ko.observable(self.currentStockroom().accountlogo()) ;

        self.stockroomNames = ko.observableArray() ;


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

                self.stockroomNames (names) ;
                self.currentStockroom(self.stockroomNames()[0]) ;
                self.currentStockroomName (self.currentStockroom().name()) ;
                self.currentStockroomAccountLogo (self.currentStockroom().accountlogo()) ;
            })


    }
alert('Stockroom.js is being used') ;
    return { viewModel: StockViewModel, template: stockTemplate };

});
<?php }
