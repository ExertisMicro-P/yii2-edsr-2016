define([],
    function () {
        var self = this;

        self.vModel = function () {
            var vmodel = this;

            vmodel.recipient  = ko.observable();
            vmodel.badrname   = ko.observable();
            vmodel.emailOk    = ko.observable();
            vmodel.onumber    = ko.observable();
            vmodel.badonumber = ko.observable();
            vmodel.email      = ko.observable();
            vmodel.message    = ko.observable();
            vmodel.invalid    = ko.computed(function () {
                return !vmodel.emailOk() || vmodel.recipient() == '';
            });
            vmodel.params     = {};

            vmodel.resending = ko.observable(false) ;
            vmodel.errormsg  = ko.observable('') ;
            vmodel.msgClass  = ko.observable('alert-info') ;
            vmodel.showForm = ko.observable(false);

            vmodel.closeForm = function () {
                vmodel.showForm(false);
            }

            vmodel.emailToRecipient = function (formElement) {
                var fdata = $(formElement).serializeArray();
                for (var ind=0; ind < fdata.length; ind++) {
                    vmodel.params[fdata[ind].name] = fdata[ind].value ;
                }
                vmodel.params.recipient = vmodel.recipient() ;
                vmodel.params.email = vmodel.email() ;

                vmodel.msgClass('alert-info');
                vmodel.errormsg('Sending...');
                vmodel.resending(true) ;

                $.post('/yiicomp/stockroom/reemailkey', vmodel.params, 'json')
                    .done(function (response) {
                        vmodel.msgClass('alert-success');
                        vmodel.errormsg('The order details have been resent');
                        vmodel.resending(false) ;

                        setTimeout(function () {
                            vmodel.closeForm();
                        }, 1500);
                    })

                .fail(function (response) {
                    var data = response.responseJSON;
                    data     = $.parseJSON(data);              // Shouldn't need this https://github.com/jquery/jquery/commit/eebc77849cebd9a69025996939f930cbf9b1bae1
                    vmodel.resending(false) ;
                    vmodel.msgClass('alert-danger');
                    vmodel.errormsg('Unrecognised error');
                });
            }

            vmodel.validEmail = ko.computed({
                read : function () {
                    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if ($.trim(vmodel.email()).length > 0) {
                        vmodel.emailOk(emailRegex.test(vmodel.email()));
                    }
                    return (vmodel.email() != '');
                },
                write: function (value) {

                }
            });
        } ;


        ko.postbox.subscribe('resend.emails', function (elementId) {
            var element = $('#' + elementId);
            var row     = element.parents('tr:eq(0)');

            if (!row.next().hasClass('resend')) {
                var cols    = row.find('td').length;
                var details = decodeURIComponent(element.attr('rel')).split('?')[1].split('&');
                var data    = '<div data-bind="template: { name: \'resendEmails\', data: $data}"></div>';
                var vModel  = new self.vModel(); //{showForm: true};
                var newrow = $('<tr class="resend" ><td colspan="' + cols + '">' + data + '</td></tr>');

                vModel.recipient(element.data('name'));
                vModel.email(element.data('email'));
                vModel.showForm(false);
                //vModel.params = details ;

                row.after(newrow);
                ko.applyBindings(vModel, newrow[0]);


                for (var ind = 0; ind < details.length; ind++) {
                    var bits               = details[ind].split('=');
                    vModel.params[bits[0]] = bits[1];

                    if (bits[0] == 'email') {
                        vModel.email(bits[1]) ;
                        
                    } else if (bits[0] == 'name') {
                        vModel.recipient(bits[1]) ;
                    }
                }

                vModel.showForm(true);
            }
        }) ;

    }
)
