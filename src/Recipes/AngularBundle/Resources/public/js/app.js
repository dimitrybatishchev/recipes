'use strict';

// Declare app level module which depends on filters, and services
angular.module('recipes', ['recipes.controllers', 'recipes.directives', 'recipes.resources', 'recipes.services', 'recipes.filters', 'ngResource', 'ngSanitize', 'infinite-scroll']).
    config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/', {templateUrl: '/recipes/web/bundles/angular/partials/index.html', controller: 'IndexCtrl'});
        $routeProvider.when('/recipe', {templateUrl: '/recipes/web/bundles/angular/partials/list.html', controller: 'RecipeListCtrl'});
        $routeProvider.when('/recipe/:id', {templateUrl: '/recipes/web/bundles/angular/partials/detail.html', controller: 'RecipeDetailCtrl'});
        $routeProvider.when('/search', {templateUrl: '/recipes/web/bundles/angular/partials/search.html', controller: 'SearchCtrl'});
        $routeProvider.when('/create', {templateUrl: '/recipes/web/bundles/angular/partials/create.html', controller: 'CreateCtrl', access: { isFree: false }});
        $routeProvider.when('/admin/', {templateUrl: '/recipes/web/bundles/angular/partials/admin.html', controller: 'AdminCtrl'});
        $routeProvider.when('/admin/category/', {templateUrl: '/recipes/web/bundles/angular/partials/category/list.html', controller: 'CategoryListCtrl'});
        $routeProvider.when('/admin/category/edit/:id', {templateUrl: '/recipes/web/bundles/angular/partials/category/edit.html', controller: 'CategoryEditCtrl'});
        $routeProvider.when('/admin/category/create', {templateUrl: '/recipes/web/bundles/angular/partials/category/create.html', controller: 'CategoryCreateCtrl'});
        $routeProvider.when('/admin/cuisine/', {templateUrl: '/recipes/web/bundles/angular/partials/cuisine/list.html', controller: 'CuisineListCtrl'});
        $routeProvider.when('/admin/cuisine/edit/:id', {templateUrl: '/recipes/web/bundles/angular/partials/cuisine/edit.html', controller: 'CuisineEditCtrl'});
        $routeProvider.when('/admin/cuisine/create', {templateUrl: '/recipes/web/bundles/angular/partials/cuisine/create.html', controller: 'CuisineCreateCtrl'});
        $routeProvider.when('/view1', {templateUrl: '/recipes/web/bundles/angular/partials/partial1.html', controller: 'MyCtrl1'});
        $routeProvider.when('/view2', {templateUrl: '/recipes/web/bundles/angular/partials/partial2.html', controller: 'MyCtrl2'});
        $routeProvider.otherwise({redirectTo: '/'});
    }]).
    run(['$rootScope', '$window', 'UserService', function($rootScope, $window, User){

    }]);