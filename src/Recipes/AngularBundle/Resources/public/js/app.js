'use strict';

// Declare app level module which depends on filters, and services
angular.module('recipes', ['recipes.controllers', 'recipes.directives', 'recipes.resources', 'recipes.services', 'recipes.filters', 'ngResource', 'ngSanitize', 'infinite-scroll']).
    config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/', {templateUrl: '/recipes/web/bundles/angular/partials/index.html', controller: 'IndexCtrl'});
        $routeProvider.when('/recipe', {templateUrl: '/recipes/web/bundles/angular/partials/list.html', controller: 'RecipeListCtrl'});
        $routeProvider.when('/recipe/:id', {templateUrl: '/recipes/web/bundles/angular/partials/detail.html', controller: 'RecipeDetailCtrl'});
        $routeProvider.when('/search', {templateUrl: '/recipes/web/bundles/angular/partials/search.html', controller: 'SearchCtrl'});
        $routeProvider.when('/favorites', {templateUrl: '/recipes/web/bundles/angular/partials/favorites.html', controller: 'FavoritesCtrl'});
        $routeProvider.when('/create', {templateUrl: '/recipes/web/bundles/angular/partials/create.html', controller: 'CreateCtrl', access: { isFree: false }});
        $routeProvider.otherwise({redirectTo: '/'});
    }]).
    run(['$rootScope', '$window', 'UserService', function($rootScope, $window, User){

    }]);