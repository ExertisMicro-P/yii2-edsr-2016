/**
 * Ideally should be a jquery extension, but for speed it's just a normal object
 *
 * @param theTable
 */

"use strict";

function keyHandler(theTableSelector, selectedRowClass, cbColumn) {
    var self = this;

    self.tableSelector = theTableSelector;

    cbColumn = cbColumn || 7;              // Need to fix this properly

    self.table            = null;
    self.headerTable      = null;
    self.selectedRowClass = selectedRowClass;


    /**
     * INIT
     * ====
     * Handles the situation where the page loaded and the user clicked
     * checkboxes prior to the javascript loading.
     */
    self.init = function () {

        var initTimer = setInterval(function () {
            if (typeof ko == 'object') {
                removeEventHandlers();

                self.table   = $(self.tableSelector);
                var colIndex = $('> tbody > tr:eq(0)', self.table).children().length;
                //if ($('>thead > tr > th:nth-child(' + colIndex + ') > input[type=checkbox]', self.table).length == 0) {
                //    var headerTable = self.table.parent().find('table.kv-table-float') ;
                //    if (!headerTable ||
                //        $('>thead > tr > th:nth-child(' + colIndex + ') > input[type=checkbox]', headerTable).length == 0) {
                //        return;
                //    }
                //}
                clearInterval(initTimer);

                reloadItems();

                // -----------------------------------------------------------
                // Test the checkbox in the header. If set, set all rows
                // NW 2015-05-08 removed the nth-child test to make this more
                // flexible, and on the assumption there will only be 1 checkbox
                // -----------------------------------------------------------
                //if ($('>thead > tr > th:nth-child(6) > input[type=checkbox]', self.table).is(':checked')) {
                if ($('>thead > tr > th > input[type=checkbox]', self.table).is(':checked')) {
                    $('>tbody > tr:not(.selected-stock) > td:nth-child(' + cbColumn + ') > input[type=checkbox]', self.table).each(
                        function () {
                            $(this).click();
                            //var row = $(this).parents('tr:eq(0)');
                            //row.addClass('selected-stock');
                            //flagCBState($(this), 'product.selected');
                        }
                    );

                } else {
                    // -------------------------------------------------------
                    // Look for any individually checked rows
                    // -------------------------------------------------------
                    $('>tbody > tr:not(.selected-stock) > td:nth-child(' + cbColumn + ') > input[type=checkbox]:checked', self.table).each(
                        function () {
                            var row = $(this).parents('tr:eq(0)');
                            row.addClass('selected-stock');
                            flagCBState($(this), 'product.selected');
                        }
                    );
                }
                addEventHandlers();
                // -----------------------------------------------------------
                // This is a work around for an initialisation problem with
                // the kartik grid and floating header. The grid often starts
                // with the rows at the wrong height and the floating header
                // floated to the left instead of at the top. By scrolling the
                // window very slightly, the grid reflows and sorts itself.
                // -----------------------------------------------------------
                scrollTo(0, 10) ;
            }
        }, 100);
    };

    /**
     * REMOVE EVENT HANDLERS
     * =====================
     * This is called on each page refresh to remove the existing change handlers
     * on the checkboxes, in the header, floating header and body. Without this,
     * the events are triggered an extra time each time the page reloads
     */
    function removeEventHandlers() {
        if (self.table) {
            $(self.table).add('*').off('change.edsr');
            var headerTable = self.table.parent().find('table.kv-table-float');
            if (headerTable) {
                $(headerTable).add('*').off('change.edsr');
            }
        }
    }

    /**
     * ADD EVENT HANDLERS
     * ==================
     * This is called on each page load to assign the checkbox change handlers.
     * This has to be done fresh each time as the pajax call recreates the
     * complete table, so the old event are not longer viable.
     */
    function addEventHandlers() {
        var theTable = self.table;
        var colIndex = $('> tbody > tr:eq(0)', theTable).children().length;

        var headerTable = self.table.parent().find('table.kv-table-float');
        if (headerTable && headerTable.find('tr').length > 0) {
            theTable = headerTable;
        }

        /**
         * HEADER SELECTION CHECKBOX
         * =========================
         * Because the header can float, the checkbox can at any time switch
         * from being in the main table header to being in the floating one.
         * If this happens before we're called we need to swap to it.
         */

        $('>thead > tr > th:nth-child(' + colIndex + ') > input[type=checkbox]', theTable).on('change.edsr', function (event) {
            var eventName = $(this).is(':checked') ? 'product.selected' : 'product.removed';

            var cboxes = $('>tbody > tr > td:nth-child(' + colIndex + ') > input[type=checkbox]', self.table).each(function () {
                flagCBState($(this), eventName);
            });
        });

        /**
         * BODY CHECKBOXES
         * ===============
         * The postbox subscriptions are received and handled in selected.js
         */
        $('>tbody > tr > td:nth-child(' + colIndex + ') > input[type=checkbox]', self.table).on('change.edsr', function (event) {
            var row  = $(this).parents('tr:eq(0)');
            var eventName;
            var cbox = $(this);

            // ---------------------------------------------------------------
            // The product.removed code can be called from elsewhere and in
            // that case needs to trigger a click. To prevent that causing a
            // loop here, we set the 'removing' data item and check it in the
            // handler, and if set skip the click.
            // ---------------------------------------------------------------
            if (!cbox.data('removing')) {
                if (cbox.is(':checked')) {
                    eventName = 'product.selected';
                    row.addClass('selected-stock');

                } else {
                    cbox.data('removing', true);
                    eventName = 'product.removed';
                    row.removeClass('selected-stock');
                }

                flagCBState($(this), eventName);
            }
        });

        /**
         * ITEM ALLOCATED
         * ==============
         * This is called when a new list of stockitems allocated to other users
         * is provided. It starts by making all rows useable, then disables the
         * allocated rows and hides the checkbox used to select them.
         */
        ko.postbox.subscribe('items.allocated', function (newAllocations) {
            // ---------------------------------------------------------------
            // First, gather a list of all the rows which should now be selected
            // ---------------------------------------------------------------
            var rows = $();                // Empty match list
            for (var ind = 0; ind < newAllocations.length; ind++) {
                rows = rows.add('tr[data-key=' + newAllocations[ind] + ']');
            }

            // ---------------------------------------------------------------
            // Get the list of currently selected rows, then find the difference
            // from the new set
            // ---------------------------------------------------------------
            var curRows = $('tr.disabled.alloc') ; // .find('input[name="selection[]"]').show().end().removeClass('disabled alloc');
            var restoring = curRows.not(rows) ;

            // ---------------------------------------------------------------
            // Now flash and then restore the rows which are no longer selected
            // ---------------------------------------------------------------
            restoring.addClass('flash').addClass('highlightflash');
            setTimeout(function () {
                restoring.removeClass("highlightflash disabled alloc")
                    .removeAttr('title data-toggle')
                    .find('input[name="selection[]"]').show();
                setTimeout(function () {
                    rows.removeClass('flash') ;
                }, 500) ;
            }, 500) ;

            // ---------------------------------------------------------------
            // Finally, find and new rows which still need to be disabled.
            // ---------------------------------------------------------------
            curRows = $('tr.disabled.alloc')
            rows.not(curRows).addClass('flash').addClass('highlightflash');
            setTimeout(function () {
                rows.removeClass("highlightflash").addClass('disabled alloc')
                    .attr('data-toggle', 'tooltip')
                    .attr('title', 'This product has been reserved for another user in your organisation')
                    .find('input[name="selection[]"]').hide();
                setTimeout(function () {
                    rows.removeClass('flash') ;
                }, 500) ;
            }, 500);

        });
    }

    /**
     * RELOAD ITEMS
     * ============
     * Re-select any previously selected rows
     */
    function reloadItems() {
        var selected ; //  = localStorage.getItem('product.selections');
        var ind ;

        $.getJSON('/yiicomp/stockroom/checkselected')
            .done(function (response) {
                // -------------------------------------------------------
                // Flash the already allocated items, then disable them
                // -------------------------------------------------------
                if (response.allocatedAlready.keys.length > 0) {

                    var rows = $();                // Empty match list
                    for (ind = 0; ind < response.allocatedAlready.keys.length; ind++) {
                        rows = rows.add('tr[data-key=' + response.allocatedAlready.keys[ind] + ']');
                    }

                    rows.addClass('flash').addClass('highlightflash');
                    setTimeout(function () {
                        rows.removeClass("highlightflash").addClass('disabled alloc')
                            .attr('data-toggle', 'tooltip')
                            .attr('title', 'This product has been reserved for another user in your organisation')
                            .find('input[name="selection[]"]').hide();
                        setTimeout(function () {
                            rows.removeClass('flash') ;
                        }, 500) ;
                    }, 500);
                }

                if (response.allocatedBySelf.keys.length > 0) {
                    for (ind = 0; ind < response.allocatedBySelf.keys.length; ind++) {
                        var row = $('tr[data-key=' + response.allocatedBySelf.keys[ind] + ']');
                        if (row.length > 0 && !row.hasClass('selected-stock')) {
                            row.addClass('selected-stock');

                            var cbox = $('input[type=checkbox][name="selection[]"]', row);
                            cbox.prop('checked', true);
                            //flagCBState(cbox, 'product.selected');        // Now obsolete
                        }
                    } ;
                }

                ko.postbox.publish('selection.reloaded', response.allocatedBySelf.items) ;

            })
            .fail(function (xhr) {

            });


        //if (typeof selected === 'string' && selected.length > 0) {
        //    selected = $.parseJSON(selected);
        //    $.each(selected, function (key, item) {
        //        var pid = item.productId;
        //
        //        var row      = $('tr#' + pid);
        //        var quantity = item.quantity;
        //
        //        if (row.length > 0 && !row.hasClass('selected-stock') && quantity > 0) {
        //            row.addClass('selected-stock');
        //
        //            var cbox = $('input[type=checkbox][name="selection[]"]', row);
        //            cbox.prop('checked', true);
        //            flagCBState(cbox, 'product.selected');
        //        }
        //    });
        //}
    }

    /**
     * ACTION LINK HANDLING
     * ====================
     */
    $('a.keyact', self.table).on('click', function (event) {
        var cbox = $(this).parent().next().find('input');
        if (!cbox.is(':checked')) {
            cbox.click();
        }
        var action = self.getAction($(this).attr('href'));
        self.updateDisplay(action);

        return false;
    });

    /**
     * FLAG CB STATE
     * =============
     * @param cbox
     * @param eventName
     */
    function flagCBState(cbox, eventName) {
        var row = $(cbox).parents('tr:eq(0)');
        var item;

        if ((item = validateAndGatherCBoxDetails(row)) !== false) {
            ko.postbox.publish(eventName, item);
        }
    }


    function validateAndGatherCBoxDetails(row) {
        //--------------------------------------------------------------------
        // If the po starts with a '#', it's already been delivered and so
        // shouldn't be here. If it is, it's a bug, or timing issue, elsewhere
        //--------------------------------------------------------------------
        if ($('td', row).length == 9) {
            var status = $('> td:nth-child(8)', row).text();
            if (status.substr(0, 1) == '#') {
                row.fadeOut('fast', function () {
                    $(this).remove();
                });
                return false;
            }
        }

        var item = {
            productId  : row.attr('id'),
            stockitem_id  : row.attr('data-key'),
            photo      : $('> td:nth-child(' + (cbColumn == 7 ? 2 : 1) + ') >img', row).attr('src'),
            partcode   : $('> td:nth-child(' + (cbColumn == 7 ? 3 : 2) + ')', row).text(),
            po         : $('> td:nth-child(' + (cbColumn == 7 ? 5 : 4) + ')', row).text(),
            description: $('> td:nth-child(' + (cbColumn == 7 ? 4 : 3) + ')', row).text(),

            // ---------------------------------------------------------------
            // Non grouped display, (cbColumn == 9), then have a status
            // ---------------------------------------------------------------
            status: cbColumn == 9 ? $('> td:nth-child(0)', row).text() : '',

            // ---------------------------------------------------------------
            // If this is the grouped display (cbColumn == 7) we have a quantity
            // ---------------------------------------------------------------
            available: cbColumn == 7 ? parseInt($('> td:nth-child(5)', row).text()) : 1
        };

        return item;
    }

    self.updateDisplay = function (action) {
        self.hideOrShowUnselected(false);

        // -------------------------------------------------------------------
        // Add an input box to the quantity columns
        // -------------------------------------------------------------------
        $('.selected-stock>td:nth-child(6)')
            .find('.expquant').remove().end()
            .find('a.keyact').hide()
            .end()
            .append('<input type="text" class="expquant" size="2" value="1"/>')

        // -------------------------------------------------------------------
        // Exposé the selected rows (ie, placed an overlay over the others)
        // -------------------------------------------------------------------
        $('.' + self.selectedRowClass + ' > td:not(:first-child), .panel-footer').expose(
            {
                closeOnClick: false,

                // ---------------------------------------------------------------
                // Highlight the selected items. No longer using, and expose
                // remembers it.
                // ---------------------------------------------------------------
//                onLoad: function(els) {
//                    self.showForm () ;
////				$(els).css({backgroundColor: '#c7f8ff'});
//                    $(els).addClass('exposedSelected') ;
//                },

                // ---------------------------------------------------------------
                // Remove highlighting when unexposing
                // ---------------------------------------------------------------
                onClose: function (els) {
                    self.closeForm();
                }
            })

    }


    self.showForm = function () {
        $('#stockLevels-filters').each(function () {
            var divOverlay   = $('#keyoverlay');
            var bottomWidth  = $(this).css('width');
            var bottomHeight = $(this).css('height');
            var rowPos       = $(this).position();
            var bottomTop    = rowPos.top;
            var bottomLeft   = rowPos.left;

            divOverlay.appendTo($(this));

            divOverlay.css({
                position: 'absolute',
                top     : bottomTop,
                right   : '0px',
                width   : '100%',
                height  : bottomHeight
            });

            $('a.cancel', divOverlay).click(self.closeForm);
            $('a.deliver', divOverlay).click(self.deliver);
            divOverlay.delay(100).slideDown('fast');

        });
    }

    self.closeForm = function () {
        $.unexpose();
        $('#keyoverlay').slideUp();
        $('.exposedSelected').removeClass('exposedSelected');
        $('.expquant').remove();
        $('.keyact').fadeIn();
        self.hideOrShowUnselected(true);
    }

    self.deliver = function () {
        //console.log('Delivered') ;

        self.closeForm();
    }


    $('.table button.keyact').on('click', function (event) {
        var self         = $(this);
        var url          = self.attr('rel')
        var tabBlock     = self.parents(".tabs-x:eq(0)");
        var tabLink      = $('li:eq(1) a', tabBlock);
        var contentBlock = $(tabLink.attr('href'));

        contentBlock.html('<img src="/img/ajax-loader.gif" title="Please wait..." />');

        tabLink.click();       // Switch the tabs
        contentBlock.load(url);

        return false;
    })


    self.getAction = function (url) {
        var result = false;
        var qpos;

        if ((qpos = url.indexOf('?')) >= 0 &&
            url.indexOf('id=') > qpos) {
            var urlbits = url.split('?');

            var actionbits = urlbits[0].split('/');
            var action     = actionbits[actionbits.length - 1];

            var idbits = urlbits[1].split('id=');
            var id     = idbits[1].split('?')[0];

            var ids   = self.getMultiSelected(id);
            var names = self.getProductNames(ids);
            result    = [action, names];
        }

        return result;
    }


    self.getMultiSelected = function (id) {
        var selected = $('#stockLevels').yiiGridView('getSelectedRows');

//        if (selected.length == 0) {
//            $('input[value=' + id + '][type=checkbox]') ;
//            selected.push(id) ;
//        }

        return selected;
    }

    self.getProductNames = function (ids) {
        var products = [];
        var table    = $('#stockLevels');

        // -------------------------------------------------------------------
        // the first td has an embedded table, so use the sibling operator (~)
        // to find the actual td
        // -------------------------------------------------------------------
        for (var ind = 0; ind < ids.length; ind++) {
//            products[products.length] =  {ids[ind]: table.find('[data-key="' + ids[ind] + '"]:eq(0) td:eq(0) ~ td:eq(1)').text()} ;
            products[products.length] = {
                id    : ids[ind],
                'name': table.find('[data-key="' + ids[ind] + '"]:eq(0) td:eq(0) ~ td:eq(1)').text()
            };
        }

        return products;
    }

    self.hideOrShowUnselected = function (show) {
        var table = $('#stockLevels');
        var items = $('tr:gt(1):not(.' + self.selectedRowClass + ')', table);

        if (show === true) {
            items.fadeIn();
        } else {
            items.fadeOut('slowly');
        }
    }

    /**
     * KV SELECT ROW
     * is only available when the checkboxColumn has been included.
     */
    if (typeof kvSelectRow !== 'undefined') {
        /**
         * KV SELECT ROW
         * =============
         * Overload the Kartvik reload event handler so that we can reinitialise
         * the grid details related to EDSR
         *
         * @param gridId
         * @param css
         */
        kvSelectRow = function (gridId, css) {
            self.init();
        } ;
    }

    self.init();
}
