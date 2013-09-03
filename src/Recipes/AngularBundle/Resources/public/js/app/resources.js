angular.module('recipes.resources', []).
    factory('Users', function ($resource) {
        var Users = $resource('http://localhost/recipes/web/app_dev.php/api/users/:action:id/:actionParam',
            {
                id: '@id',
                action: "@action",
                actionParam: "@actionParam"
            },
            {
                update: { method: 'PUT'}
            });
        angular.extend(Users.prototype, {

        });
        return Users;
    }).
    factory('Recipes', function ($resource) {
        var Recipes = $resource('http://localhost/recipes/web/app_dev.php/api/recipes/:action:id/:actionParam',
                    {
                        id: '@id',
                        action: "@action",
                        actionParam: "@actionParam"
                    },
                    {
                        update: { method: 'PUT'},
                        getLast: {
                            method: "GET",
                            params: {
                                action: "last",
                                actionParam: "3"
                            },
                            isArray: true
                        },
                        search:{
                            method: "GET",
                            params:{
                                action: "page",
                                actionParam: 0
                            }
                        },
                        addToFavorite: {
                            method: "POST",
                            params: {
                                actionParam: "addToFavorite"
                            }
                        },
                        addComment: {
                            method: "POST",
                            params: {
                                actionParam: "addComment"
                            }
                        }
                    });
        angular.extend(Recipes.prototype, {

        });
        return Recipes;
    }).
    factory('Category', function ($resource) {
        var Category = $resource('http://localhost/recipes/web/app_dev.php/api/categories/:action:id/:actionParam',
                        {
                            id: '@id',
                            action: "@action",
                            actionParam: "@actionParam"
                        },
                        {
                            search: {
                                method: "GET",
                                params: {
                                    action: "search"
                                },
                                isArray: true
                            },
                            update: { method: 'PUT'}
                        });
            angular.extend(Category.prototype, {

            });
        return Category;
    }).
    factory('Cuisine', function ($resource) {
        var Cuisine = $resource('http://localhost/recipes/web/app_dev.php/api/cuisines/:id',
            {id: '@id'},
            {
                update: { method: 'PUT'}
                });
            angular.extend(Cuisine.prototype, {

            });
        return Cuisine;
    }).
    factory('MeasureUnit', function ($resource) {
        var Cuisine = $resource('http://localhost/recipes/web/app_dev.php/api/measureunits/:id',
            {id: '@id'},
            {
                update: { method: 'PUT'}
            });
            angular.extend(Cuisine.prototype, {

            });
        return Cuisine;
    });