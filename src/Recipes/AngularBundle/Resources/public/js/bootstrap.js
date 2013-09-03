// to go ahead and bootstrap when the DOM is loaded
define(['angular', 'domReady'], function(angular, domReady) {
    domReady(function() {
        angular.bootstrap(document, ['MyApp']);
    });
});
