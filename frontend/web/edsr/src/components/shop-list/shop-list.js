define(["text!./shop-list.html"], function (Template) {


    function Item(row) {
        var item = this;

        item.id          = row.data('digid');
        item.photo       = row.find('.list-image img').attr('src');
        item.partcode    = row.find('.partcode').text();
        item.description = row.find('.description').text();
        item.price       = row.find('input.price').val();

        item.formattedPrice = function () {
            // RCH 20151214
            var locale = 'en';
            var options = {style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2,useGrouping: false};
            var formatter = new Intl.NumberFormat(locale, options);

            price = formatter.format(item.price);
            return '&pound;' + price;
        }

        item.addToBasket = function (which, event) {
            ko.postbox.publish('buy.more', 'prow_' + item.id + '-' + item.id) ;
        } ;
    }

    function ListModel(params) {
        var model = this;

        model.active = ko.observable(false);
        model.loaded = false ;

        /**
         * ITEMS - LOAD ITEMS
         * ==================
         * Iterates over the current table and extracts the relevant details
         * for display in the grid view.
         */
        model.items     = ko.observableArray();
        model.isRetailView = ko.observable().subscribeTo('is.retailview') ; // RCH 20151005



        model.loadItems = function () {
            model.items.removeAll();
            $('#shopgrid-container table tbody tr').each(function (index) {
                model.items.push(new Item($(this)));
            });


        }

        ko.postbox.subscribe('shop.applyBindings', function () {
            if (!$('shop-list').data('ready')) {
                $('shop-list').data('ready', true) ;
                ko.applyBindings(model, $('shop-list')[0]);
            }
        }) ;

        ko.postbox.subscribe('shop.initlist', function (newValue) {
            model.loadItems();
        })

        ko.postbox.subscribe('shop.showlist', function (newValue) {
            document.showingList = newValue;
            model.active         = newValue;

            if (newValue) {
                $('shop-list').fadeOut('slow', function () {
                    $('#shopgrid-container').removeClass('hidden').fadeIn();
                })
            } else {
                $('#shopgrid-container').fadeOut('slow', function () {
                    $(this).addClass('hidden');
                    $('shop-list').fadeIn('slow');
                });
            }
        });


        //$('#shopgrid-container').after($('shop-list'));

    }

    return {viewModel: ListModel, template: Template};

})
