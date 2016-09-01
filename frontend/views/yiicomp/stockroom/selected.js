define(["text!./selected?t=h", "utils"],
    function (stockTemplate, utils  ) {

        function SelectedItem(item) {            
            var self = this;
            
            //self.productId = ko.observable(item.productId);
            //self.pid       = item.productId.split('-')[1];         // stock item id
            self.stockItem   = ko.observable(item.stockitem_id);
            self.photo       = ko.observable(item.photo);
            self.partcode    = ko.observable(item.partcode);
            self.po          = ko.observable(item.po || '');
            self.description = ko.observable(item.description);
            self.available   = ko.observable(item.available);
            self.quantity    = ko.observable(item.quantity ? item.quantity : 1);
            self.inError     = ko.observable(false);
            self.key         = ko.observable();
            self.updateTimer = null;

            self.removing = ko.observable(true);

            self.badQuant = ko.computed(function () {
                var quant    = parseInt(self.quantity());
                var inError  = self.inError();
                var newError = !/^\+?\d+$/.test(quant) || (quant > 0 && quant > parseInt(self.available()));

                if (newError != inError) {
                    self.inError(newError);
                    ko.postbox.publish('product.errorChange', newError);
                }
                return newError;
            });

            self.removeItem = function (which, event) {
                var item = {
                    stockitem_id : which.stockItem()
                }
                self.removing(false);
                setTimeout(function () {
                    ko.postbox.publish('product.removed', item);
                }, 400)

            };

        }

        function StockViewModel(params) {
            var self = this;

            self.showingBasket = ko.observable().syncWith('product.showBasket', true, true, function (newValue) {
                var basket = $('#basket');

                if (newValue == true) {
                    self.showingEForm(false);
                    self.showingDForm(false);
                    basket.fadeIn();
                    self.getKeys();

                } else if (basket.is(':visible')) {
                    basket.fadeOut();
                    self.errormsg('');
                    self.onumber('');
                }
            });

            self.showingEForm = ko.observable(false);
            self.showingDForm = ko.observable(false);
            self.emailOk      = ko.observable();
            self.email        = ko.observable();
            self.recipient    = ko.observable();
            self.onumber      = ko.observable();
            self.printing     = ko.observable(false);

            self.badonumber = ko.computed(function () {
                return $.trim(self.onumber()).length == 0;
            })
            self.badrname   = ko.computed(function () {
                return $.trim(self.recipient()).length == 0;
            })

            self.canEmail         = ko.computed(function () {
                return !self.badonumber() && !self.badrname() && self.emailOk();
            })
            self.currentStockroom = ko.observable().subscribeTo('product.stockroom', true);
            self.errormsg         = ko.observable('');

            /**
             * BACK TO PRODUCTS
             * ================
             */
            self.backToProducts = function () {
                var number = self.selected().length;

                self.selected.remove(function (item) {
                    if (item.quantity() == 0) {
                        var row = $('tr[data-key=' + item.stockItem() + ']');
                        $('input[name="selection[]"]', row).click();         // Deselect the row
                    }
                    return item.quantity() == 0;
                });

                // -----------------------------------------------------------
                // If we removed any, save the details in case the page is reloaded
                // -----------------------------------------------------------
                if (number != self.selected().length) {
                    self.selectedCount(self.selected().length);

                }
                updateSelected();
                self.showingBasket(false);
            };


            self.selected      = ko.observableArray();
            self.selectedCount = ko.observable(0).publishOn('product.selectedCount');

            ko.postbox.subscribe('product.errorChange', function (newValue) {
                var inerror = false;
                var items   = self.selected();
                for (var ind = 0; ind < items.length; ind++) {

                    if (items[ind].inError()) {
                        inerror = true;
                        break;
                    }
                }
                if (!inerror) {
                    self.errormsg('');
                }
                return !inerror;
            }, true);

            /**
             * PRODUCT SELECTED
             * ================
             */
            ko.postbox.subscribe('product.selected', function (newValue) {
                
                $('#bcount').prop('disabled', true);
                
                $.get('/yiicomp/stockroom/getkeylimit', function(keyLimit){

                    $.get('/yiicomp/stockroom/countdelivery', function(delivery){
                        var shouldAdd = true;
                        
                        $('#bcount').prop('disabled', false);
                        //console.log(parseInt(delivery) + ' > ' + parseInt(keyLimit));
                       if(parseInt(delivery) > parseInt(keyLimit)){
                            $('.errorMsg').html('<b>You\'re only allowed to pick '+keyLimit+' products.</b>');
                            $('.errorMsg').slideDown();
                            shouldAdd = false;                       

                            setTimeout(function(){
                                $('.errorMsg').slideUp();
                            }, 5000);

                       } else {
                

                        for (var ind = 0; ind < self.selected().length; ind++) {
                            if(self.selected().length >= keyLimit){
                                shouldAdd = false;
                            }
                            if (self.selected()[ind].stockItem() == newValue.stockitem_id) {

                                self.selected()[ind].photo(newValue.photo);
                                self.selected()[ind].description(newValue.description);
                                // ---------------------------------------------------
                                // This is a work around, not a solution, to a problem
                                // during page reload. In that case this is called from
                                // stockroom.js@reloadItems / stockroom.js/flagCBState
                                // but with no quantity provided. In this loop we're
                                // always modifying an existing item, so leave it as is
                                // ---------------------------------------------------
                                if (newValue.quantity) {
                                    self.selected()[ind].quantity(newValue.quantity);
                                }
                                self.selected()[ind].available(newValue.available);
                                shouldAdd = false;
                                break;
                            }
                        }
                        if (shouldAdd) {
                            self.selected.push(new SelectedItem(newValue));
                            self.selectedCount(self.selected().length);

                            // -------------------------------------------------------
                            // Save the details in case the page is reloaded
                            // -------------------------------------------------------
                            updateSelected();
                        }
                       }

                    });
                });
            }, true);

            /**
             * PRODUCT REMOVED
             * ===============
             */
            ko.postbox.subscribe('product.removed', function (theValue) {

                self.selected.remove(function (item) {
                    if (item.stockItem() == theValue.stockitem_id) {
                        var row  = $('tr[data-key=' + item.stockItem() + ']');
                        var cbox = $('input[name="selection[]"]', row);

                        // ---------------------------------------------------
                        // If we're already removing the item, don't emulate
                        // a click as that would produce a loop.
                        // ---------------------------------------------------
                        if (cbox.data('removing') !== true) {
                            $('input[name="selection[]"]', row).data('removing', true).click();         // Deselect the row
                        }
                        cbox.removeData('removing');
                        return true;
                    }
                    return false;
                });

                self.selectedCount(self.selected().length);

                // -------------------------------------------------------
                // Save the details in case the page is reloaded
                // -------------------------------------------------------
                updateSelected();
            });

            this.totalProducts = ko.computed(function () {
                var total = 0;
                for (var ind = 0; ind < self.selected().length; ind++) {
                    total += parseInt(self.selected()[ind].quantity());
                }
                return total;
            }, this);

            self.viewKeys = function () {
                $('#viewKeys').expose();

            };

            /**
             * GET KEYS
             * ========
             */
            self.getKeys = function () {
                var needed     = [];
                var neededKeys = [];

                for (var ind = 0; ind < self.selected().length; ind++) {
                    if (!self.selected()[ind].key()) {
                        needed.push(self.selected()[ind]);
                        neededKeys.push(self.selected()[ind].stockItem());
                    }
                }
                if (needed.length) {
                    $.post('/yiicomp/stockroom/fetchkeys', {pid: neededKeys}, null, 'json')
                        .done(function (response) {

                            var keys = $.parseJSON(response).keys;
                            $.each(keys, function (pid, key) {
                                for (var ind = 0; ind < needed.length; ind++) {
                                    if (pid == needed[ind].stockItem()) {
                                        needed[ind].key(key);
                                        break;
                                    }
                                }
                            })


                        })

                }
            }

            function itemsInError() {
                var inerror = $('.valerr').parents('.inb');
                if (inerror.length) {
                    inerror.add('#basketmsg').expose({
                        color  : false,
                        onClose: function (els) {
                            self.showingEForm(false);
                        }
                    });
                    return true;
                }
                return false;
            }

            self.emailKeys = function () {
                var showing = self.showingEForm();

                if (showing) {
                    $.unexpose();

                } else if (!itemsInError()) {
                    self.showingEForm(!showing);

                    $('#emailKeys').expose({
                        closeOnClick: false,
                        color       : false,
                        onClose     : function (els) {
                            self.showingEForm(false);
                        }
                    });
                }
            };

            /**
             * DELIVER KEYS
             * ===========
             * This will display a form to allow the user to mark the selected
             * keys as manually delivered to their customers.
             *
             */
            self.deliverKeys = function () {
                var showing = self.showingDForm();

                if (showing) {
                    $.unexpose();

                } else if (!itemsInError()) {
                    self.showingDForm(!showing);

                    $('#deliverKeys').expose({
                        closeOnClick: false,
                        color       : false,
                        onClose     : function (els) {
                            self.showingDForm(false);
                        }
                    });
                }

            };

            /**
             * PRINT KEYS
             * ==========
             * This will initiate the creation of a formatted PDF file which
             * lists one key per A4 sheet in (one of a set of) predefined layout.
             */
            self.printKeys = function () {
                self.printing(true);

                var stockItems = gatherStockItemIds();

                var newTab = window.open(null, 'keypdf');

                if (!newTab ) {
                    alert('Sorry, but it seems the browser has blocked our attempt to open the PDF in a new window.') ;

                } else {
                    var form = $('<form id="pdfrequest" style="display:none" action="' + document.location.origin + '/printkeys" method="post">')
                        .append($('<input name="pdfkeys" type="hidden">').val(stockItems))
                        .append($('<div>').text(stockItems))
                        .append($('<input type=submit>'));

                    $(newTab.document.body).html(form);
                    form.submit();

                    setItemStatus(self.onumber());
                    clearSelected();

                    setTimeout(function () {
                        self.selected.removeAll();
                        self.backToProducts();
                    }, 1500);

                }
                self.printing(false);
            };

            /**
             * VALID EMAIL
             * ===========
             */
            self.validEmail = ko.computed({
                read : function () {
                    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if ($.trim(self.email()).length > 0) {
                        self.emailOk(emailRegex.test(self.email()));
                    }
                    return false;
                },
                write: function (value) {

                }
            });

            /**
             * GATHER STOCK ITEM DETAILS
             * =========================
             * @returns {Array}
             */
            function gatherStockItemDetails() {
                var selItems = self.selected();
                var items    = [];

                if (selItems.length > 0) {
                    for (var ind = 0; ind < selItems.length; ind++) {
                        items.push({
                            productCode: selItems[ind].partcode(),
                            quantity   : selItems[ind].quantity(),
                            pid        : selItems[ind].stockItem(),
                        });
                    }
                }
                return items;
            }

            /**
             * GATHER STOCK ITEM IDS
             * =====================
             * @returns {Array}
             */
            function gatherStockItemIds() {
                var selItems = self.selected();
                var items    = [];

                if (selItems.length > 0) {
                    for (var ind = 0; ind < selItems.length; ind++) {
                        items.push(selItems[ind].stockItem());
                    }
                }
                return items;
            }

            /**
             * DELIVER ALL
             * ===========
             * This should process all selected items, passing their details to the
             * server to flag them as used, deselect them and then hide the rows so
             * that they cannot be selected again.
             *
             * @param form
             */
            self.deliverAll = function (form) {
                var stockItems = gatherStockItemIds();

                var data = {
                    stockroom: self.currentStockroom().id,
                    onumber  : self.onumber(),
                    items    : stockItems
                };

                $.post('/yiicomp/stockroom/markkeysdelivered', data, 'json')
                    .done(function (response) {
                        self.errormsg('Your selected items have been marked delivered');
                        $.unexpose();

                        setItemStatus(self.onumber());
                        clearSelected();

                        setTimeout(function () {
                            self.selected.removeAll();
                            self.backToProducts();
                        }, 1500);

                    })
                    .fail(function (response) {
                        var data = response.responseJSON;
                        data     = $.parseJSON(data);              // Shouldn't need this https://github.com/jquery/jquery/commit/eebc77849cebd9a69025996939f930cbf9b1bae1

                        self.errormsg(data);
                        $.unexpose();

                    });

            }

            /**
             * SET ITEM STATUS
             * ===============
             * @returns {Array}
             */
            function setItemStatus(status) {
                var selItems = self.selected();
                var items    = [];

                if (selItems.length > 0) {
                    for (var ind = 0; ind < selItems.length; ind++) {
                        selItems[ind].status = status;
                    }
                }
                return items;
            }

            /**
             * CLEAR SELECTED
             * ==============
             * This only works for the grouped view
             */
            function clearSelected() {
                // ---------------------------------------------------
                // Set all required counts to the new value
                // ---------------------------------------------------
                for (var ind = 0; ind < self.selected().length; ind++) {
                    var row = $('tr[data-key=' + self.selected()[ind].stockItem() + ']');
                    var cnt = $('td', row).length;

                    if (row && cnt == 7) {


                        var available = parseInt($('> td:nth-child(5)', row).text());
                        var quantity  = self.selected()[ind].quantity();

                        self.selected()[ind].quantity(0);

                        $('> td:nth-child(5)', row).text(available - quantity);

                        // --------------------------------------------------
                        // If the non-grouped display, getting here means the
                        // items have been delivered, so we want to remove
                        // them from the original table. To play safe also
                        // update the status, which is checked prior to display.
                        // --------------------------------------------------
                    } else if (cnt == 9) {
                        $('td:nth-child(8)', row).text(self.selected()[ind].status);
                        row.css('background-color', '#F9DCA9');
                        row.fadeOut('slow', function () {
                            row.remove();
                        });
                    }
                    $('> td:nth-child(7) input', row).click();         // Deselect the row
                }
                self.selectedCount(0);

                // -------------------------------------------------------
                // Save the details in case the page is reloaded, then
                // display the page for a short while before fading it out
                // and returning to the main grid
                // -------------------------------------------------------
                updateSelected(true);
            }

            /**
             * UPDATE SELECTED
             * ===============
             * Updates the local record of selected items then starts a timer
             * which will trigger a corresponding update on the server. This is
             * delayed to ensure that a select/clear all action isn't held up
             * by numerous $.post requests
             *
             * @param clearAll
             */
            function updateSelected(clearAll) {
                //if (clearAll === true) {
                //    localStorage.removeItem("product.selections");
                //
                //} else {
                //    localStorage.setItem("product.selections", ko.toJSON(self.selected()));
                //}
                
                $.get('/yiicomp/stockroom/getkeylimit', function(keyLimit){
                   
                    $.get('/yiicomp/stockroom/countdelivery', function(delivery){
                       
                        if(parseInt(delivery) > parseInt(keyLimit)){
                            
                            $('.errorMsg').html('<b>You\'re only allowed to pick '+keyLimit+' products.</b>');
                            $('.errorMsg').slideDown();
                            return false;

                            setTimeout(function(){
                                $('.errorMsg').slideUp();
                            }, 5000);
                            
                        } else {
                            
                            if (self.updateTimer) {
                                clearTimeout(self.updateTimer);
                            }
                            
                            self.updateTimer = setTimeout(function () {
                            self.updateTimer = null;

                            $.post('/yiicomp/stockroom/checkselected', {pids: ko.toJSON(self.selected)}, 'json')
                                .done(function (response) {
                                    response = $.parseJSON(response);
                                    if (response.status) {
                                        if (response.allocatedAlready.keys.length > 0) {
                                            self.selected.remove(function (item) {
                                                return $.inArray(item.stockItem(), response.allocatedAlready.keys) >= 0;
                                            });
                                        }
                                        ko.postbox.publish('items.allocated', response.allocatedAlready.keys);
                                    }

                                })
                                .fail(function (xhr) {

                                });

                            }, 100);
                            
                        }
                        
                    });
                    
                });
                
            }

            /**
             * EMAIL TO RECIPIENT
             * ==================
             *
             * @param form
             */
            self.emailToRecipient = function (form) {
                var stockItems = gatherStockItemDetails();

                var data = {
                    stockroom: self.currentStockroom().id,
                    email    : self.email(),
                    recipient: self.recipient(),
                    onumber  : self.onumber(),
                    items    : stockItems
                };

                $.post('/yiicomp/stockroom/emailkeys', data, 'json')
                    .done(function (response) {
                        self.errormsg('Your order details have been emailed');
                        $.unexpose();

                        clearSelected();

                        setTimeout(function () {
                            self.selected.removeAll();
                            self.backToProducts();
                        }, 1500);

                    })
                    .fail(function (response) {
                        var data = response.responseJSON;
                        data     = $.parseJSON(data);              // Shouldn't need this https://github.com/jquery/jquery/commit/eebc77849cebd9a69025996939f930cbf9b1bae1

                        if (data.insufficient) {
                            flagInsufficientquantitities(data.insufficient);
                            self.errormsg('Please review the highlighted products as the available quantities have changed.');
                            $.unexpose();

                        } else if (data.recipient && data.recipient.orderNumber) {
                            self.errormsg(data.recipient.orderNumber);
                            $.unexpose();

                        } else {
                            self.errormsg('Unrecognised error');
                            $.unexpose();
                        }

                    });

            };

            /**
             * FLAG INSUFFICENT QUANTITIES
             * ===========================
             * @param data
             */
            function flagInsufficientquantitities(data) {
                var selected = self.selected();

                $.each(data, function (productCode, quantity) {
                    for (var pind = 0; pind < selected.length; pind++) {
                        var item = selected[pind];
                        if (item.partcode() == productCode) {
                            item.inError(true);
                            item.available(quantity);
                            var row = $('tr[data-key=' + item.stockItem() + ']');
                            $('> td:nth-child(5)', row).text(quantity);
                            break;
                        }
                    }
                });
            }

            /**
             * RELOAD ITEMS
             * ============
             * All data initialised, so now reload any previously selected items
             */
            ko.postbox.subscribe('selection.reloaded', function (selected) {

                //var selected = localStorage.getItem('product.selections');
                //if (typeof selected === 'string' && selected.length > 0) {
                //    selected = $.parseJSON(selected);

                if (selected) {
                    $.each(selected, function (key, item) {
                        //Don't need this line as it pushes x amount of products and the next line will load it which causes the problem. 05042016
                        //self.selected.push(new SelectedItem(item));
                    });
                    self.selectedCount(self.selected().length);

                }
            });
        }

        return {viewModel: StockViewModel, template: stockTemplate};


    }
);
