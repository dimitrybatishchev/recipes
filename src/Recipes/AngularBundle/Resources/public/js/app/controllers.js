'use strict';

angular.module('recipes.controllers', [])
    .controller('NavbarController', ['$scope', '$location', function($scope, $location) {
        $scope.user = {};
        $scope.routeIs = function(routeName) {
            return $location.path() === routeName;
        };
    }])
    .controller('FavoritesCtrl', ['$scope', 'Recipes', 'Users', 'user', function($scope, Recipes, Users, user) {
        $scope.user = user;

        $scope.recipes = Users.favoriteRecipes({
            id: $scope.user.uid
        });

        $scope.addToFavorite = function(index){
            if (!$scope.recipes[index].favorite){
                Recipes.addToFavorite({ id: $scope.recipes[index].id, userId: $scope.user.uid });
            } else {
                Recipes.removeFromFavorite({ id: $scope.recipes[index].id, userId: $scope.user.uid });
            }
            $scope.recipes[index].favorite = !$scope.recipes[index].favorite;
        };
    }])
    .controller('IndexCtrl', ['$scope', 'Recipes', function($scope, Recipes) {
        $scope.lastRecipes = Recipes.query({
            count: 3,
            orderBy: 'DESC'
        });
    }])
    .controller('NotFoundCtrl', ['$scope', function($scope) {

    }])
    .controller('ProfileCtrl', ['$scope', '$routeParams', 'Recipes', 'Users', 'user', function($scope, $routeParams, Recipes, Users, user){
        $scope.user = user;

        $scope.recipes = Users.createdRecipes({
            id: $routeParams.id
        });
    }])
    .controller('RecipeListCtrl', ['$scope', '$http', 'Recipes', 'Categories', 'Cuisines', 'user', 'Users', function($scope, $http, Recipes, Categories, Cuisines, user, Users){
        $scope.user = user;
        // $scope.userIsLogged = user.isLogged;

        $scope.recipes = [];
        $scope.page = 0;

        $scope.categories = Categories.query();
        $scope.cuisines = Cuisines.query();

        $scope.categoryId = '';
        $scope.cuisineId = '';

        $scope.chooseCategory = function (categoryId) {
            $scope.categoryId = categoryId;
            $scope.recipes = Recipes.query({category: $scope.categoryId, cuisine: $scope.cuisineId, page: 0, user: $scope.user.uid});
            $scope.page = 1;
        };

        $scope.chooseCuisine = function (cuisineId) {
            $scope.cuisineId = cuisineId;
            $scope.recipes = Recipes.query({category: $scope.categoryId, cuisine: $scope.cuisineId, page: 0, user: $scope.user.uid});
            $scope.page = 1;
        };

        $scope.addToFavorite = function(index) {
            if (!$scope.recipes[index].favorite){
                Users.addToFavorite({ recipeId: $scope.recipes[index].id, id: $scope.user.uid })
            } else {
                Users.removeFromFavorite({ recipeId: $scope.recipes[index].id, id: $scope.user.uid });
            }
            $scope.recipes[index].favorite = !$scope.recipes[index].favorite;
        };

        var busy = false;
        $scope.nextPage = function(){
            if (busy) return;

            busy = true;
            Recipes.query(
                {
                    category: $scope.categoryId,
                    cuisine: $scope.cuisineId,
                    page: $scope.page,
                    user: $scope.user.uid,
                    count: 5,
                    orderBy: "ASC"
                }, function(data){
                    $scope.recipes = $scope.recipes.concat(data);
                    $scope.page++;
                    busy = false;
                }
            );
        };
    }])
    .controller('RecipeDetailCtrl', ['$scope', '$http', '$routeParams', 'Recipes', 'recipe', 'user', function($scope, $http, $routeParams, Recipes, recipe, user) {
        $scope.user = user;
        $scope.recipe = recipe;

        $scope.addComment = function(){
            Recipes.addComment({
                id: $scope.recipe.id,
                userId: $scope.user.uid,
                text: $scope.comment.text
            });
            $scope.recipe.comments.push({
                creator: $scope.user,
                text: $scope.comment.text
            });
            $scope.comment.text = '';
        };
    }])
    .controller('SearchCtrl', ['$scope', '$http', 'Recipes', 'categories', 'cuisines', function($scope, $http, Recipes, categories, cuisines) {
        $scope.categories = categories;
        $scope.cuisines = cuisines;

        $scope.searchData = {
            selectedCategory: null,
            selectedCuisine:  null,
            selectedIngredients: []
        };

        $scope.addIngredient = function(){
            $scope.searchData.selectedIngredients.push($scope.ingredient);
            $scope.ingredient = null;
        };

        $scope.searchRecipes = function(){
            console.log($scope.searchData.selectedIngredients);
            Recipes.query({
                category: $scope.searchData.selectedCategory,
                cuisine: $scope.searchData.selectedCuisine,
                ingredients: $scope.searchData.selectedIngredients.join(',')
            }, function(data){
                if (data.length == 0){
                    $scope.error = true;
                } else {
                    $scope.error = false;
                    $scope.recipes = data;
                    var listHeadTag = $(".search-results h4");
                    $('html,body').animate({scrollTop: listHeadTag.offset().top - 74},'slow');
                }
            });
        };
    }])
    .controller('RecipeEditCtrl', ['$scope', '$http', '$location', 'measureUnits', 'user', 'Recipes', '$routeParams', function($scope, $http, $location, measureUnits, user, Recipes, $routeParams) {
        $scope.measureUnits = measureUnits;
        $scope.user = user;

        $scope.recipe = Recipes.get({id: $routeParams.id}, function(data){
            $scope.recipe = data;
            $scope.editedRecipe = {
                category: $scope.recipe.category.name,
                id: $scope.recipe.id,
                description: $scope.recipe.description,
                name: $scope.recipe.name,
                ingredients: []
            };
            if ($scope.recipe.cuisine){
                $scope.editedRecipe.cuisine = $scope.recipe.cuisine.name;
            };
            $scope.recipe.recipeIngredient;
            for (var i = 0; i < $scope.recipe.recipeIngredient.length; i++){
                $scope.editedRecipe.ingredients.push({
                    count: $scope.recipe.recipeIngredient[i].count,
                    name: $scope.recipe.recipeIngredient[i].ingredient.name,
                    measureUnit: $scope.recipe.recipeIngredient[i].measure_unit.id
                });
            }
        });
        $scope.addIngredient = function(){
            $scope.editedRecipe.ingredients.push({});
        };
        $scope.deleteIngredient = function(idx){
            $scope.editedRecipe.ingredients.splice(idx, 1);
        };

        $scope.files = [];

        $scope.save = function() {
            $scope.editedRecipe.creator = User.uid;
            $http({
                method: 'POST',
                url: "http://localhost/recipes/web/app_dev.php/api/recipes/" + $scope.editedRecipe.id,
                headers: { 'Content-Type': false },
                transformRequest: function (data) {
                    var formData = new FormData();
                    formData.append("model", angular.toJson(data.model));
                    if (data.files[0]){
                        formData.append("image", data.files[0]);
                    }
                    return formData;
                },
                data: { model: $scope.editedRecipe, files: $scope.files }
            }).
            success(function (data, status, headers, config) {
                $location.path("/recipe/" + data.id);
            }).
            error(function (data, status, headers, config) {
                alert("failed!");
            });
        };

        $scope.$on("fileSelected", function (event, args) {
            $scope.$apply(function () {
                $scope.files.push(args.file);
            });
        });
    }])
    .controller('CreateCtrl', ['$scope', '$http', '$location', 'measureUnits', 'user', function($scope, $http, $location, measureUnits, user) {
        $scope.user = user;
        $scope.measureUnits = measureUnits;

        $scope.recipe = {
            data: {
                ingredients: [{}, {}, {}]
            },
            files: []
        };

        $scope.addIngredient = function(){
            $scope.recipe.data.ingredients.push({});
        };
        $scope.deleteIngredient = function(idx){
            $scope.recipe.data.ingredients.splice(idx, 1);
        };

        $scope.save = function() {
            if ($scope.recipe.data.ingredients.length == 0){
                return;
            }
            $scope.recipe.data.creator = $scope.user.uid;
            $http({
                method: 'POST',
                url: "http://localhost/recipes/web/app_dev.php/api/recipes/",
                headers: { 'Content-Type': false },
                transformRequest: function (data) {
                    var formData = new FormData();
                    formData.append("model", angular.toJson(data.model));
                    formData.append("image", data.files[0]);
                    return formData;
                },
                data: { model: $scope.recipe.data, files: $scope.recipe.files }
            }).
                success(function (data, status, headers, config) {
                    $location.path("/recipe/" + data.id);
                }).
                error(function (data, status, headers, config) {
                    alert("failed!");
                });
        };

        $scope.$on("fileSelected", function (event, args) {
            $scope.$apply(function () {
                $scope.recipes.files.push(args.file);
            });
        });
    }]);




