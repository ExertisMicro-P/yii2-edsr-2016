//define(['jquery', 'knockout', './router', 'bootstrap', 'knockout-projections', 'postbox', 'orderpage'], function($, ko, router) {
define(['./router', 'bootstrap', 'orderpage'], function(router) {
    var self = this ;

    // Components can be packaged as AMD modules, such as the following:
    ko.components.register('nav-bar', { require: 'components/nav-bar/nav-bar' });
    ko.components.register('home-page', { require: 'components/home-page/home' });
    ko.components.register('login-page', { require: 'components/login-page/login' });

    // ... or for template-only components, you can just point to a .html file directly:
    ko.components.register('about-page', {
        template: { require: 'text!components/about-page/about.html' }
    });

    ko.components.register('stock-header', { require: '/yiicomp/stockroom/header' });
    ko.components.register('stock-selected', { require: '/yiicomp/stockroom/selected' });
    ko.components.register('stock-basket', { require: 'components/basket/basket' });

    ko.components.register('shop-list', { require: 'components/shop-list/shop-list' });



    // [Scaffolded component registrations will be inserted here. To retain this feature, don't remove this comment.]

    self.loggedIn = ko.observable(navSettings.loggedIn).publishOn('edsr.loggedin');

    if (!self.loggedIn()) {
        if (document.location.pathname !== "/gauth/resend" &&
            document.location.pathname !== "/gauth/set-password" &&
            document.location.pathname !== '/site/help') {
            document.location.href = '#login';
        }
    }

    self.useGauthify = ko.observable(navSettings.useGauthify).publishOn('edsr.usegauthify');
    self.maintenancemode = ko.observable(navSettings.maintenancemode).publishOn('edsr.maintenancemode');

    // Start the application
    ko.applyBindings({ route: router.currentRoute });
});
