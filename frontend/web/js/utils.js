// -------------------------------------------------------------------
// Shared items
// -------------------------------------------------------------------

define([],
    function() {

        ko.bindingHandlers.hidden = {
            update: function(element, valueAccessor) {
                ko.bindingHandlers.visible.update(element, function() { return !ko.utils.unwrapObservable(valueAccessor()); });
            }
        };

        ko.bindingHandlers.slideVisible = {
            init: function(element, valueAccessor, allBindings) {
                $(element).hide() ;
            },
            update: function(element, valueAccessor, allBindings) {
                // First get the latest data that we're bound to
                var value = valueAccessor();

                // Next, whether or not the supplied model property is observable, get its current value
                var valueUnwrapped = ko.unwrap(value);

                // Grab some more data from another binding property
                var duration = allBindings.get('slideDuration') || 400; // 400ms is default duration unless otherwise specified

                var deleteOnClose = allBindings.get('deleteOnClose') || false ;

                // Now manipulate the DOM element
                if (valueUnwrapped == true)
                    $(element).slideDown(duration); // Make the element visible

                else if ($(element).is(':visible')) {                              // Make the element invisible
                    $(element).slideUp(duration, function () {
                        if (deleteOnClose === true) {
                            $(this).remove() ;

                        } else if (deleteOnClose) {
                            $(this).parents(deleteOnClose).remove() ;
                        }
                    });
                }
            }
        };

        /**
         * FADE VISIBLE
         * ============
         * Custom Knockout binding (copied from knockout.js) that makes elements
         * shown/hidden via jQuery's fadeIn()/fadeOut() methods
         *
         * @type {{init: Function, update: Function}}
         */
        ko.bindingHandlers.fadeVisible = {
            init  : function (element, valueAccessor) {
                // Initially set the element to be instantly visible/hidden depending on the value
                var value = valueAccessor();
                $(element).toggle(ko.unwrap(value)); // Use "unwrapObservable" so we can handle values that may or may not be observable
            },
            update: function (element, valueAccessor) {
                // Whenever the value subsequently changes, slowly fade the element in or out
                var value = valueAccessor();
                ko.unwrap(value) ? $(element).fadeIn() : $(element).fadeOut();
            }
        };


        ko.bindingHandlers.numeric = {
            init: function (element, valueAccessor) {
                $(element).on("keydown", function (event) {

                    // Allow: backspace, delete, tab, escape, and enter
                    if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||

                        // Allow: Ctrl+A
                        (event.keyCode == 65 && event.ctrlKey === true) ||

                        //// Allow: . ,
                        //(event.keyCode == 188 || event.keyCode == 190 || event.keyCode == 110) ||

                        // Allow: home, end, left, right
                        (event.keyCode >= 35 && event.keyCode <= 39)) {
                        // let it happen, don't do anything
                        return;
                    }
                    else {
                        // Ensure that it is a number and stop the keypress
                        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                            event.preventDefault();
                        }
                    }
                });
            }
        };
        ko.bindingHandlers.clickToEdit = {
            init: function(element, valueAccessor) {
                var observable = valueAccessor(),
                    link = document.createElement("span"),
                    input = document.createElement("input");

                element.appendChild(link);
                element.appendChild(input);

                observable.editing = ko.observable(false);

                ko.applyBindingsToNode(link, {
                    text: observable,
                    hidden: observable.editing,
                    click: observable.editing.bind(null, true)
                });

                ko.applyBindingsToNode(input, {
                    value: observable,
                    visible: observable.editing,
                    hasfocus: observable.editing,
                    event: {
                        keyup: function(data, event) {
                            //if user hits enter, set editing to false, which makes field lose focus
                            if (event.keyCode === 13) {
                                observable.editing(false);
                                return false;
                            }
                            //if user hits escape, push the current observable value back to the field, then set editing to false
                            else if (event.keyCode === 27) {
                                observable.valueHasMutated();
                                observable.editing(false);
                                return false;
                            }

                        }
                    }
                });
            }
        };

        Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
            var self = this;
            var sign, iVal, jVal;

            decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
            decSeparator  = decSeparator == undefined ? "." : decSeparator;
            thouSeparator = thouSeparator == undefined ? "," : thouSeparator;

            sign = self < 0 ? "-" : "";

            iVal = parseInt(self = Math.abs(+self || 0).toFixed(decPlaces)) + "",

            jVal = (jVal = iVal.length) > 3 ? jVal % 3 : 0;

            return sign + (jVal ? iVal.substr(0, jVal) + thouSeparator : "") +
                iVal.substr(jVal).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) +
                (decPlaces ? decSeparator + Math.abs(self - iVal).toFixed(decPlaces).slice(2) : "");
        } ;


        $.fn.showMessageBelow = function(text, cssClass, after, leaveOthers) {
            $('.smb-msg').remove() ;
            if (!cssClass) {
                cssClass = 'danger';
            }

            cssClass = 'alert alert-' + cssClass +' fade in' ;

            if (!leaveOthers) {
                $(this).find('.smbmsg').remove() ;
            }

            div = $('<div class="' + cssClass + ' smbmsg" />').text(text) ;

            if (after) {
                $(this).after(div) ;
            } else {
                $(this).append(div) ;
            }
            div.fadeOut(10000, function() {
                div.remove() ;
            })
        }

    }


) ;
