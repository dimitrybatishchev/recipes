'use strict';

/* Controllers */

angular.module('recipes.controllers', [])
    .controller('LoginController', function($scope, $location) {

    })
    .controller('NavbarController', ['$scope', '$location', 'UserService', function($scope, $location, UserService) {
        $scope.user = UserService;
        $scope.routeIs = function(routeName) {
            return $location.path() === routeName;
        };
    }])
    .controller('IndexCtrl', ['$scope', 'Recipes', function($scope, Recipes) {
        $scope.lastRecipes = Recipes.getLast();
    }])
    .controller('RecipeListCtrl', ['$scope', '$http', 'Recipes', 'UserService', function($scope, $http, Recipes, UserService) {
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });
        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 600);
            return false;
        });

        $scope.recipes = [];
        $scope.busy = false;
        $scope.page = 0;

        $scope.addToFavorite = function(id){
            var data = Recipes.addToFavorite({ id: id, userId: UserService.uid });
        };

        $scope.nextPage = function(){
            if ($scope.busy) return;
            $scope.busy = true;

            var data = Recipes.query(
                {
                    actionParam: $scope.page,
                    action: "page"
                }, function(data){
                    for (var i = 0; i < data.length; i++) {
                        $scope.recipes.push(data[i]);
                    }
                    $scope.page++;
                    $scope.busy = false;
                }
            );
        };
    }])
    .controller('RecipeDetailCtrl', ['$scope', '$http', '$routeParams', 'Recipes', 'UserService', function($scope, $http, $routeParams, Recipes, User) {
        $scope.recipe = Recipes.get({id: $routeParams.id});
        $scope.user = User;

        $scope.comment = {
          text: ''
        };

        $scope.addComment = function(){
            var data = Recipes.addComment({
                id: $scope.recipe.id,
                userId: User.uid,
                text: $scope.comment.text
            });
            $scope.recipe.comments.push({
                creator: User,
                text: $scope.comment.text
            });
            $scope.comment.text = '';
        };
    }])
    .controller('SearchCtrl', ['$scope', '$http', 'Category', 'Cuisine', function($scope, $http, Category, Cuisine) {
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });
        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 600);
            return false;
        });

        $scope.categories = Category.query();
        $scope.cuisines = Cuisine.query();

        $scope.searchData = {};
        $scope.searchData.selectedCategory = null;
        $scope.searchData.selectedCuisine = null;
        $scope.searchData.selectedIngredients = [];

        $scope.addIngredient = function(){
            $scope.searchData.selectedIngredients.push($scope.ingredient);
            $scope.ingredient = null;
        };
        $scope.result = null;

        $scope.searchRecipes = function(){
            console.log($scope.searchData);
            $http.post('http://localhost/recipes/web/app_dev.php/api/recipes/search/', $scope.searchData).success( function(data) {
                console.log(data);
                $scope.recipes = data;
                var listHeadTag = $(".search-results h4");
                $('html,body').animate({scrollTop: listHeadTag.offset().top - 74},'slow');
            });
        };

        $scope.recipes = [];
    }])
    .controller('CreateCtrl', ['$scope', '$http', '$location', 'MeasureUnit', 'UserService', function($scope, $http, $location, MeasureUnit, User) {

        console.log(User.isLogged);

        $scope.files = [];

        $scope.$on("fileSelected", function (event, args) {
            $scope.$apply(function () {
                $scope.files.push(args.file);
            });
        });

        //the save method
        $scope.save = function() {
            if ($scope.recipe.ingredients.length == 0){
                return;
            }
            console.log($scope.recipe.ingredients);
            $scope.recipe.creator = User.uid;
            $http({
                method: 'POST',
                url: "http://localhost/recipes/web/app_dev.php/api/recipes/",
                //IMPORTANT!!! You might think this should be set to 'multipart/form-data'
                // but this is not true because when we are sending up files the request
                // needs to include a 'boundary' parameter which identifies the boundary
                // name between parts in this multi-part request and setting the Content-type
                // manually will not set this boundary parameter. For whatever reason,
                // setting the Content-type to 'false' will force the request to automatically
                // populate the headers properly including the boundary parameter.
                headers: { 'Content-Type': false },
                //This method will allow us to change how the data is sent up to the server
                // for which we'll need to encapsulate the model data in 'FormData'
                transformRequest: function (data) {
                    var formData = new FormData();
                    //need to convert our json object to a string version of json otherwise
                    // the browser will do a 'toString()' on the object which will result
                    // in the value '[Object object]' on the server.
                    formData.append("model", angular.toJson(data.model));
                    //now add all of the assigned files
                    formData.append("image", data.files[0]);
                    return formData;
                },
                //Create an object that contains the model and files which will be transformed
                // in the above transformRequest method
                data: { model: $scope.recipe, files: $scope.files }
            }).
                success(function (data, status, headers, config) {
                    $location.path("/recipes/" + data.id);
                }).
                error(function (data, status, headers, config) {
                    alert("failed!");
                });
        };

        $scope.recipe = {
            ingredients: [{}, {}, {}]
        };
        $scope.ingredient = {

        };
        $scope.ingredients = [

        ];
        $scope.measureUnits = MeasureUnit.query();
        $scope.addIngredient = function(){
            $scope.recipe.ingredients.push({});
        };
        $scope.deleteIngredient = function(idx){
            $scope.recipe.ingredients.splice(idx, 1);
        };
    }])
    .controller('AdminCtrl', ['$scope', '$http', function($scope, $http) {

    }])
    .controller('CategoryListCtrl', ['$scope', '$http', '$resource', 'Category', function($scope, $http, $resource, Category) {
        $scope.categories = Category.query();
    }])
    .controller('CategoryCreateCtrl', ['$scope', '$http', '$location', 'Category', function($scope, $http, $location, Category) {
        $scope.category = new Category();
        $scope.saveCategory = function(){
            Category.save($scope.category,function(){
                $location.path("/admin/category");
            });
        }
    }])
    .controller('CategoryEditCtrl', ['$scope', '$http', '$routeParams', '$location', 'Category', function($scope, $http, $routeParams, $location, Category) {
        $scope.category = Category.get({id:$routeParams.id});
        $scope.saveCategory = function(){
            $scope.category.$update(function(data){
                $location.path("/admin/category");
            });
        }
        $scope.deleteCategory = function(){
            $scope.category.$delete(function(data){
                $location.path("/admin/category");
            });
        }
    }])
    .controller('CuisineListCtrl', ['$scope', '$http', 'Cuisine', function($scope, $http, Cuisine) {
        $scope.cuisines = Cuisine.query();
    }])
    .controller('CuisineCreateCtrl', ['$scope', '$http', '$location', 'Cuisine', function($scope, $http, $location, Cuisine) {
        $scope.cuisine = new Cuisine();
        $scope.saveCuisine = function(){
            Cuisine.save($scope.cuisine,function(){
                $location.path("/admin/cuisine");
            });
        }
    }])
    .controller('CuisineEditCtrl', ['$scope', '$http', '$routeParams', '$location', 'Cuisine', function($scope, $http, $routeParams, $location, Cuisine) {
        $scope.cuisine = Cuisine.get({id:$routeParams.id});
        $scope.saveCuisine = function(){
            $scope.cuisine.$update(function(data){
                $location.path("/admin/cuisine");
            });
        }
        $scope.deleteCuisine = function(){
            $scope.cuisine.$delete(function(data){
                $location.path("/admin/cuisine");
            });
        }
    }]);




