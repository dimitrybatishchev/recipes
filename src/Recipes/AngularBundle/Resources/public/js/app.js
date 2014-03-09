'use strict';

// Declare app level module which depends on filters, and services
angular.module('recipes', ['recipes.controllers', 'recipes.directives', 'recipes.resources', 'recipes.services', 'recipes.filters', 'ngResource', 'ngSanitize', 'infinite-scroll']).
    config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/', {
            templateUrl: '/recipes/web/bundles/angular/partials/index.html',
            controller: 'IndexCtrl'
        });
        $routeProvider.when('/recipe', {
            templateUrl: '/recipes/web/bundles/angular/partials/list.html',
            controller: 'RecipeListCtrl',
            resolve: {
                user: function(UserVK){
                    return UserVK.getUser();
                }
            }
        });
        $routeProvider.when('/recipe/:id', {
            templateUrl: '/recipes/web/bundles/angular/partials/detail.html',
            controller: 'RecipeDetailCtrl',
            resolve: {
                recipe: function(Recipes, $route){
                    return Recipes.get({id: $route.current.params.id});
                },
                user: function(UserVK){
                    return UserVK.getUser();
                }
            }
        });
        $routeProvider.when('/create', {
            templateUrl: '/recipes/web/bundles/angular/partials/create.html',
            controller: 'CreateCtrl',
            access: {
                isFree: false
            },
            resolve: {
                user: function(UserVK){
                    return UserVK.getUser();
                },
                measureUnits: function(MeasureUnits){
                    return MeasureUnits.query();
                }
            }
        });
        $routeProvider.when('/recipe/:id/edit', {
            templateUrl: '/recipes/web/bundles/angular/partials/edit.html',
            controller: 'RecipeEditCtrl',
            resolve: {
                user: function(UserVK){
                    return UserVK.getUser();
                },
                measureUnits: function(MeasureUnits){
                    return MeasureUnits.query();
                }
            }
        });
        $routeProvider.when('/search', {
            templateUrl: '/recipes/web/bundles/angular/partials/search.html',
            controller: 'SearchCtrl',
            resolve: {
                categories: function(Categories){
                    return Categories.query();
                },
                cuisines: function(Cuisines){
                    return Cuisines.query();
                }
            }
        });
        $routeProvider.when('/favorites', {
            templateUrl: '/recipes/web/bundles/angular/partials/favorites.html',
            controller: 'FavoritesCtrl',
            resolve: {
                user: function(UserVK){
                    return UserVK.getUser();
                }
            }
        });
        $routeProvider.when('/404', {templateUrl: '/recipes/web/bundles/angular/partials/404.html', controller: 'NotFoundCtrl'});
        $routeProvider.when('/user/:id', {
            templateUrl: '/recipes/web/bundles/angular/partials/profile.html',
            controller: 'ProfileCtrl',
            resolve: {
                user: function(Users, $route){
                    return Users.get({id: $route.current.params.id});
                }
            }
        });
        $routeProvider.otherwise({
            redirectTo: '/404'
        });
    }]).
    run(['$rootScope', '$window', function($rootScope, $window){

    }]);