define(["text!./signup.html"], function(signupTemplate) {

    function SignupViewModel(params) {
        var self = this ;

        self.route = params.route ;

        self.loggedIn = ko.observable() ; //.syncWith('user.loggedIn', true);
    }

    return { viewModel: SignupViewModel, template: signupTemplate };

});
