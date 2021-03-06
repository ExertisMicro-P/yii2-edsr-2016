// require.js looks for the following global when initializing
var require = {
    baseUrl: "/edsr/src",
    paths: {
        "bootstrap":            "bower_modules/components-bootstrap/js/bootstrap.min",
        "crossroads":           "bower_modules/crossroads/dist/crossroads.min",
        "hasher":               "bower_modules/hasher/dist/js/hasher.min",
        //"jquery":               "bower_modules/jquery/dist/jquery",
        //"jquery":               "//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min",
        //"knockout":             "bower_modules/knockout/dist/knockout",
        //"knockout-projections": "bower_modules/knockout-projections/dist/knockout-projections",
        //"postbox":              "bower_modules/knockout-postbox/build/knockout-postbox.min",
        "signals":              "bower_modules/js-signals/dist/signals.min",
        "text":                 "bower_modules/requirejs-text/text",

        'orderpage':            '/js/orderpage',

        'stockroom':            "/js/stockroom",

        'utils':                "/js/utils"
    },
    shim: {
        //"bootstrap": { deps: ["jquery"] }
    }
};
