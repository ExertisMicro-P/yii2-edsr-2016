$(document).ready(function () {
        $('#reset-form').on('beforeSubmit', function (event, jqXHR, settings) {
            var form = $(this);
            if (form.find('.has-error').not('#rerrs').length) {
                return false;
            }

            $('#doreg .row').fadeOut() ;
            $('#doreg .loader').fadeIn() ;
            $('#rerrs').fadeOut() ;

            $.ajax({
                url    : form.attr('action'),
                type   : 'post',
                data   : form.serialize(),
                success: function (data) {
                    $('#doreg .loader').fadeOut() ;

                    if (!data.ok) {
                        var msg = '' ;
                        $.each(data, function (key, value) {msg += "\n" + value}) ;

                        $('#rerrs div').html(msg) ;
                        $('#rerrs').fadeIn() ;
                        $('#doreg .row').fadeIn() ;

                    } else {
                        $('#doreg').fadeOut('slow', function () { $('#rdone').fadeIn() })

                        // do we need to skip all the gauthify stuff?
                        if (data.ga.usegauthify) {

                            $('#gaqrurl').attr('src', data.ga.qrurl) ;
                            $('#gaezurl').text(data.ga.ezurl) ;
                            $('#gaaccount').text(data.ga.account) ;
                            $('#gakey').text(data.ga.key) ;


                            var repeats = setInterval(
                                function () {
                                    var update = $.get('/gauth/reload-ga', {"username" : data.ga.account}) ;
                                    update.done(function (data) {

                                            if (!data.ok) {
                                                var msg = '' ;
                                                $.each(data, function (key, value) {msg += "\n" + value}) ;

                                                $('#rerrs div').html(msg) ;
                                                $('#rerrs').fadeIn() ;
                                                $('#doreg .row').show() ;
                                                $('#doreg').fadeIn('slow') ;
                                                $('#rdone').fadeOut('slow') ;
                                                clearInterval(repeats) ;

                                            } else {
                                                $('#gaqrurl').fadeOut('slow', function () {
                                                    $('#gaqrurl').attr('src', data.ga.qrurl);
                                                    $('#gaezurl').text(data.ga.ezurl);
                                                    $('#gaaccount').text(data.ga.account);
                                                    $('#gakey').text(data.ga.key);
                                                    $('#gaqrurl').fadeIn('slow');
                                                }) ;
                                            }
                                        }) ;
                                    update.fail(function (xhr) {
                                        $('#rerrs div').html('Unable to proceed') ;
                                        $('#rerrs').fadeIn() ;
                                        $('#doreg .row').show() ;
                                        $('#doreg').fadeIn('slow') ;
                                        $('#rdone').fadeOut('slow') ;

                                        clearInterval(repeats) ;
                                    }) ;

                                }, 540000
                            ) ;
                        } // if gauthify
                    }
                }
            });

            return false;
        });


        // see http://stackoverflow.com/questions/5986389/using-jquery-how-do-i-force-a-visitor-to-scroll-to-the-bottom-of-a-textarea-to
        // When use has read/scrolled theough the terms, enable the agrrement checkbox
        $('#terms').scroll(function () {
            
            // RCH 2016-08-19
            // for some reason this has stopped working (in Chrome?)
            // let's skip it
            /*
            if ($(this).scrollTop() == $(this)[0].scrollHeight - $(this).height()) {
                $('#agreecheckbox').removeAttr('disabled');
                $('#agreecheckbox').attr('title', 'tick to agree')
            }
            */
           // RCH 201608-19
           // thrown this in - so we enable the check box on any scroll
                $('#agreecheckbox').removeAttr('disabled');
                $('#agreecheckbox').attr('title', 'tick to agree')
           
        });

        // when user agrees to terms, enable the submit button
        $('#agreecheckbox').on('change', function(){
            if($(this).is(':checked')){
              $('#submitbtn').prop('disabled', false);
              $('#agreecheckbox').removeAttr('title')
            } else {
              $('#submitbtn').prop('disabled', true);
              $('#agreecheckbox').attr('title', 'tick to agree')
            }
          });


    }
);
