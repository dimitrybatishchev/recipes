// defines RequireJS config
require.config({
    paths: {
        angular: 'vendor/angular',
        jquery: 'vendor/jquery',
        domReady: 'vendor/domReady',
        twitter: 'vendor/bootstrap',
        angularResource: 'vendor/angular-resource'
    },
    shim: {
        'twitter/js/bootstrap': {
            deps: ['jquery/jquery']
        },
        angular: {
            deps: [ 'jquery/jquery',
                'twitter/js/bootstrap'],
            exports: 'angular'
        },
        angularResource: { deps:['angular'] }
    }
});
require([
    'app',
    // Note this is not Twitter Bootstrap
    // but our AngularJS bootstrap
    'bootstrap',
    'controllers/mainControllers',
    'services/searchServices'
    // Any individual controller, service, directive or filter file
    // that you add will need to be pulled in here.
    // This will have to be maintained by hand.
    ],
    function (angular, app) {
        'use strict';
        app.config(['$routeProvider',
            function($routeProvider) {
                // Define your Routes here

            }
        ]);
    }
);
