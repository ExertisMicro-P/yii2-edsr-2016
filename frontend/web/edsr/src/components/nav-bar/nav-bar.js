//define(['knockout', 'text!./nav-bar.html', 'postbox'], function(ko, template) {
define(['text!./nav-bar.html'], function(template) {

    function NavBarViewModel(params) {
        var self = this ;

    // This viewmodel doesn't do anything except pass through the 'route' parameter to the view.
    // You could remove this viewmodel entirely, and define 'nav-bar' as a template-only component.
    // But in most apps, you'll want some viewmodel logic to determine what navigation options appear.

      //self.route = ko.observable(params.route);
        self.route = params.route;

        self.navOptions = ko.observable(navSettings.options) ;
        self.loggedIn   = ko.observable().subscribeTo('edsr.loggedin', true) ;
        self.useGauthify  = ko.observable().subscribeTo('edsr.usegauthify', true) ;
        self.username   = ko.observable().subscribeTo('edsr.displayName', true) ;

        for (var ind=0; ind < navSettings.nav.length; ind++) {
            navSettings.nav[ind].cssclass = ko.observable(navSettings.nav[ind].cssclass || '')  ;
        }
        self.navItems   = ko.observableArray(navSettings.nav) ;
        ko.postbox.subscribe('edsr.displayName', function (newValue) {
            var items = self.navItems() ;

            if (self.loggedIn()) {
                for (var ind = 0; ind < items.length; ind++) {
                    var link = items[ind];
                    if (link.url[0] == "#login") {
                        items[ind].label = 'Logout (' + newValue + ')';
                        items[ind].url[0] = '/site/logout';

                        // ---------------------------------------------------
                        // As the items aren't observables, this forces a refresh
                        // ---------------------------------------------------
                        var item = self.navItems.splice(ind, 1) ;
                        self.navItems.splice(ind, 0, item[0]) ;
                        console.log('logout set')
                        break;
                    }
                }
            }

        })


        self.process = function(model, event) {
            if (model.url[2]) {
                switch (model.url[2]) {
                    case 'login':
                        self.login();
                        break;

                    case 'signup':
                        //self.signup() ;
                        break;

                    default:
                }
            }

        }

    }

    NavBarViewModel.chkLink = ko.pureComputed(function() {
        console.log(this) ;
        return this.route().page ;
    }, NavBarViewModel) ;

  return { viewModel: NavBarViewModel, template: template };
});
