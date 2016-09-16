define(["text!./login.html", 'utils'], function(homeTemplate) {

    function HomeViewModel(params) {
        var self = this ;

        self.route = params.route ;

        self.loggingIn  = ko.observable(false) ;
        self.loggedIn   = ko.observable(false).syncWith('edsr.loggedin', true) ;
        self.passwordReset   = ko.observable(false).syncWith('edsr.passwordreset', true) ;
        self.useGauthify = ko.observable(false).syncWith('edsr.usegauthify', true) ; // RCH
        self.displayName= ko.observable(false).publishOn('edsr.displayName') ;
        self.maintenancemode = ko.observable(false).syncWith('edsr.maintenancemode', true) ; // RCH

        self.userName     = ko.observable('') ; //.syncWith('user.name', true);
        self.password     = ko.observable('');
        self.errmsg       = ko.observable();
        self.userNameOk   = ko.observable(false);
        self.passwordOk   = ko.observable(false);

        self.gauthCheck   = ko.observable(false) ;
        self.gauthCode    = ko.observable('') ;
        self.gauthOk      = ko.observable(false);
        self.gauthing     = ko.observable(false);
        self.authmsg      = ko.observable();

        self.resetting    = ko.observable(false);
        self.pwrerrmsg    = ko.observable() ;

        /**
         * LOGIN
         * =====
         */
        self.login = function () {
            $('main-menu .container').removeClass('active') ;
            if (!self.loggedIn()) {
                self.gauthOk(false) ;
                self.gauthCheck(false) ;
                self.gauthCode ('') ;

                $("#vpb_pop_up_background").css({
                    "opacity": "0.8"
                })
                    .fadeIn("slow");

                $("#vpb_login_pop_up_box").fadeIn('fast');
                window.scroll(0, 0);
            } else {
                document.location.href = '/' ;
                //self.loggedIn(false) ;
            }
        } ;


        /**
         * VALID USER
         * ==========
         * Called each time the user types a character into the username box,
         * this makes an ajax call to check the entered name is recognised,
         */
        self.validUser = ko.computed({

            read : function () {
                //var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                var emailRegex = /^(([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)|([A-Za-z_0-9!"£$%\^&*(){}@~#?]+)/;
                emailRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/ ;
                                // `        Just to reset syntax highlighting

                if ($.trim(self.userName()).length > 0) {
                    self.userNameOk(emailRegex.test(self.userName())) ;

                    //$.get('/checkuser', {user: $.trim(self.userName())})
                    //    .done(function (result) {
                    //        self.userNameOk(result == 'ok')
                    //    })
                }
                return (self.userName() != '' && self.userNameOk());
            },
            write: function (value) {

            }
        }) ;

        /**
         * VALID PWORD
         * ===========
         * Checks if the password matches the minimum requirements and sets,
         * then displays, an error message if not
         */
        self.validPword = ko.computed( {
            read: function () {
                var msg = self.errmsg() ;
                var pword = self.password() ;
                var err = self.passwordOk() ;

                if (pword != self.oldpw) {
                    if (pword.length == 0) {
                        msg = '' ;
                        err = true ;

                    } else if (pword.length < 6) {
                        msg = 'Your password must be at least 8 characters long';
                        err = true ;

                    } else {
                        msg = '';
                        err = false ;
                    }
                    self.oldpw = pword;
                    self.errmsg(msg);
                    self.passwordOk(!err) ;
                }
                return !err ;
            },
            write: function (value) {

            }
        }) ;

        /**
         * VALID AUTH CODE
         * ===============
         * Called each time the user types a character into the gauth  box,
         * this just checks the length
         */
        self.validAuth = ko.computed({
            read : function () {
                var code = $.trim(self.gauthCode()) ;

                if (self.useGauthify()) {
                    var isOk = code.length == 6 && code.match(/\d{6}/) ;
                    self.gauthOk(isOk) ;

                    if (!isOk) {
                        //self.authmsg('Invalid code') ;

                    } else {
                        //self.authmsg('') ;
                    }
                } else {
                    // we're not using Gauthify, so just say we're happy
                    self.gauthOk(true) ;
                }
                return code;
            },
            write: function (value) {

            }
        }) ;



        /**
         * VALID DETAILS
         * =============
         * Checks that both the username and password are valid
         */
        self.validDetails = ko.computed( {
            read: function () {
                return self.validUser() && self.validPword() ;
            }
        })

        /**
         * VALID PASSWORD RESET DETAILS
         * ============================
         * Checks that both the username (Email)
         */
        self.validPasswordResetDetails = ko.computed( {
            read: function () {
                return self.validUser() ;
            }
        })

        self.hide_popup = function () {
            if (self.loggedIn(true)) {
                $("#vpb_signup_pop_up_box").hide();
                $("#vpb_login_pop_up_box").fadeOut('slow');
                $("#vpb_pop_up_background").fadeOut("slow");
                document.location.href = '#';
            }
        } ;


        self.doLogin = function () {
            if (self.validDetails()) {
                self.loggingIn (true) ;

                var form = $(arguments[1].target).parents('form');
                var csrf = $('<input name="'+ yii.getCsrfParam() + '">').val(yii.getCsrfToken());

                form.append(csrf);

                var action = form.attr('action');
                var data = form.serialize();

                csrf.remove();

                $.post(action, data, function () {}, 'json')
                    .done(function (result) {
                        self.loggingIn (false) ;
                        if (result['result'] == 'ok') {

                            self.errmsg('Logged in successfully as ' + result['name']);
                            self.loggedIn(true);
                            self.displayName(result['name']);

                            self.gauthCheck('true');

                            setTimeout(function () {
                                self.hide_popup();
                                document.location.href = '/';
                            }, 1000);

                        } else if (result['result'] == 'internal') {
                            self.errmsg('You ' + result['name'] + ' are an internal user.<br />Please log into the admin side');
                            setTimeout(function () {
                                self.hide_popup();
                                document.location.href = result['dest'];
                            }, 1000);

                        } else {
                            self.errmsg('Your details were not recognised');
                        }
                    })
                    .fail(function (xhr) { //, textStatus, errorThrown) {
                        self.loggingIn (false) ;
                        if (xhr.status == 400) {
                            alert('Your session has expired and you will be redirected to the home page');
                            location.reload();

                        } else {
                            alert(xhr.responseText);
                        }
                    })
            }
        } ;

        /**
         * SHOW RESET PASSWORD
         * ===================
         * This toggles between showing the login box and the reset password one
         */
        self.showResetPassword = function () {
            self.resetting(!self.resetting()) ;
            return ;
        }


        /**
         * DO RESET PASSWORD
         * =================
         * Processes the reset password command. It should only be possible to
         * call it after an email address has been entered, but we check here
         * to be safe.
         */
        self.doResetPassword = function () {


            if (self.validPasswordResetDetails()) {
                //self.loggingIn (true) ;

                var form = $(arguments[1].target).parents('form');
                var csrf = $('<input name="'+ yii.getCsrfParam() + '">').val(yii.getCsrfToken());
                var rsf = $('<input name="rsf" type="hidden">').val(1); // reset password flag

                form.append(csrf);
                form.append(rsf);

                var action = form.attr('action');
                var data = form.serialize();

                csrf.remove();

                $.post(action, data, function () {}, 'json')
                    .done(function (result) {
                        self.loggingIn (false) ;
                        if (result['result'] == 'ok') {

                            self.pwrerrmsg('An email has been sent to help you reset your password.');
                            self.passwordReset(true) ; // RCH not really logged in but this might display the message better
                            //self.displayName(result['name']) ;

                            self.showResetPassword() ;

                            //self.gauthCheck('true') ;

                            setTimeout(function () {
                                self.passwordReset(false)
                                //self.hide_popup() ;
                                //document.location.href = '/' ;
                            }, 5000);

                        } else {
                            self.pwrerrmsg('Your details were not recognised');
                        }
                    })
                    .fail(function (xhr) { //, textStatus, errorThrown) {
                        self.loggingIn (false) ;
                        if (xhr.status == 400) {
                            alert('Your session has expired and you will be redirected to the home page');
                            location.reload();

                        } else {
                            alert(xhr.responseText);
                        }
                    })
            }
        } ;  // doResetPassword



        /**
         * DO AUTH
         * =======
         */
        self.doAuth = function () {
            if (self.validAuth()) {
                self.gauthing(true) ;

                var form = $(arguments[1].target).parents('form');
                var csrf = $('<input name="'+ yii.getCsrfParam() + '">').val(yii.getCsrfToken());

                form.append(csrf);

                var action = form.attr('action');
                var data = form.serialize();

                csrf.remove();

                $.post(action, data)
                    .done(function (result) {
                        if (result == 'ok') {
                            self.authmsg('Logged in successfully');
                            //self.loggedIn(true) ;

                            self.gauthCheck('true') ;

                            setTimeout(function () {
                                self.hide_popup() ;
                                location.href = '#member' ;
                            }, 1000);

                        } else {
                            self.gauthing(false) ;
                            self.gauthCheck('true') ;
                            self.authmsg('Your details were not recognised');
                        }
                    })
                    .fail(function (xhr) { //, textStatus, errorThrown) {
                        if (xhr.status == 400) {
                            alert('Your session has expired and you will be redirected to the home page');
                            location.reload();

                        } else {
                            alert(xhr.responseText);
                        }
                    })
            }
        }

        // RCH 20160309
        // Need to not call this if in maintenance mode
        if (!self.maintenancemode()) {
           self.login();
        }
    }

    return { viewModel: HomeViewModel, template: homeTemplate };

});
