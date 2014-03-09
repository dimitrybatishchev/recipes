angular.module('recipes.resources', []).
    factory('Users', function ($resource) {
        var Users = $resource('http://localhost/recipes/web/app_dev.php/api/users/:action:id/:actionParam/:recipeId',
            {
                id: '@id',
                action: "@action",
                actionParam: "@actionParam",
                recipeId: "@recipeId"
            },
            {
                update: { method: 'PUT'},
                createdRecipes: {
                    method: "GET",
                    params: {
                        actionParam: "created-recipes"
                    },
                    isArray: true
                },
                favoriteRecipes:{
                    method: "GET",
                    params:{
                        actionParam: "favorites"
                    },
                    isArray: true
                },
                addToFavorite: {
                    method: "PUT",
                    params: {
                        actionParam: "favorites"
                    }
                },
                removeFromFavorite: {
                    method: "DELETE",
                    params: {
                        actionParam: "favorites"
                    }
                }
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
                        addComment: {
                            method: "POST",
                            params: {
                                actionParam: "comments"
                            }
                        }
                    });
        angular.extend(Recipes.prototype, {

        });
        return Recipes;
    }).
    factory('Categories', function ($resource) {
        var Categories = $resource('http://localhost/recipes/web/app_dev.php/api/categories/:action:id/:actionParam',
                        {
                            id: '@id',
                            action: "@action",
                            actionParam: "@actionParam"
                        },
                        {
                            update: { method: 'PUT'}
                        });
            angular.extend(Categories.prototype, {

            });
        return Categories;
    }).
    factory('Cuisines', function ($resource) {
        var Cuisines = $resource('http://localhost/recipes/web/app_dev.php/api/cuisines/:id',
            {
                id: '@id'
            },
            {
                update: { method: 'PUT'}

            });
            angular.extend(Cuisines.prototype, {

            });
        return Cuisines;
    }).
    factory('MeasureUnits', function ($resource) {
        var MeasureUnits = $resource('http://localhost/recipes/web/app_dev.php/api/measureunits/:id',
            {id: '@id'},
            {
                update: { method: 'PUT'}
            });
            angular.extend(MeasureUnits.prototype, {

            });
        return MeasureUnits;
    });