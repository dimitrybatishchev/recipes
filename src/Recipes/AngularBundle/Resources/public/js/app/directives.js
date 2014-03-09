'use strict';

/* Directives */


angular.module('recipes.directives', [])
    .directive('scrollUp', ['$rootScope', function ($root) {
        return {
            link: function (scope, elem, attrs, ctrl) {
                $(window).scroll(function(){
                    if ($(this).scrollTop() > 100) {
                        elem.fadeIn();
                    } else {
                        elem.fadeOut();
                    }
                });
                elem.click(function(){
                    $("html, body").animate({ scrollTop: 0 }, 600);
                    return false;
                });

            }
        }
    }])
    .directive('ingredientsDecline', function(){
        return function(scope, element, attrs){
            attrs.$observe('ingredientsDecline',function(){
                var word = '';
                if (parseInt(attrs.ingredientsDecline) == 0){
                    word = 'ов';
                } else if (parseInt(attrs.ingredientsDecline) == 1){
                    word = '';
                } else if(parseInt(attrs.ingredientsDecline) < 5){
                    word = 'а';
                } else if(parseInt(attrs.ingredientsDecline) > 5){
                    word = 'ов';
                }
                element.text(attrs.ingredientsDecline + ' ингредиент' + word);
            });
        }
    })
    .directive('vkLogin', ['$http', 'UserVK', function($http, UserVK){
        return {
            restrict: 'A', // стиль декларирования
            scope: true, // новый scope будет создан для этой директивы
            controller: function($scope, $attrs){
                function loginInVK(response){
                    UserVK.login();
                }
            },
            link: function(scope, element, attrs){
                scope.User = UserVK.login();
                // Задаем функцию, которая будет вызываться при изменении переменной user
                $scope.$watch(attrs.vkLogin, function(value){
                        element.text(value+attrs.habra);
                    }
                );
            },
            compile: function compile(templateElement, templateAttrs) {
                templateElement.html("" +
                    "<a href='' ng-hide='User.isLogged' ng-click='loginInVK()' class='login'><img src='/recipes/web/bundles/angular/img/vkontakte-logo.png'> Войти</a>" +
                    "<a ng-show='User.isLogged' class='user dropdown-toggle' data-toggle='dropdown'>" +
                        "<img class='img-circle' ng-src='{{User.avatar}}'>" +
                        "<span>{{User.firstname}} {{User.lastname}}</span>" + "<b class='caret'></b>" +
                    "</a>" +
                    "<ul class='dropdown-menu' role='menu'><li><a ng-href='#/profile'>Профиль</a></li><li><a ng-href='#/create'>Добавить рецепт</a></li><li class='divider'></li><li><a ng-href='#/favorites'>Избранное</a></li><li class='divider'></li><li><a ng-href='/exit'>Выйти</a></li></ul>" );
            }
        }
    }])
    .directive('login', ['UserVK', '$q', '$compile', function(UserVK, $q, $compile) {
        return {
            restrict: 'CA',
            replace: false,
            transclude: false,

            controller: function($scope, $attrs){
                function loginInVK(response){
                    UserVK.login();
                }
            },

            link: function(scope, elem, attrs) {
                scope.isLogged = UserVK.getUser();

                scope.isLogged.then(function(response){
                    scope.User = response;
                    elem.html($compile(
                        "<a ng-show='User.isLogged' class='user dropdown-toggle' data-toggle='dropdown'>" +
                            "<img class='img-circle' ng-src='{{User.avatar}}'>" +
                            "<span>{{User.firstname}} {{User.lastname}}</span>" + "<b class='caret'></b>" +
                        "</a>" +
                        "<ul ng-show='User.isLogged' class='dropdown-menu' role='menu'>" +
                            "<li>" +
                                "<a ng-href='#/user/{{User.uid}}'>Профиль</a>" +
                            "</li>" +
                            "<li>" +
                                "<a ng-href='#/create'>Добавить рецепт</a>" +
                            "</li>" +
                            "<li>" +
                                "<a ng-href='#/favorites'>Избранное</a>" +
                            "</li>" +
                            "<li class='divider'></li>" +
                            "<li>" +
                                "<a ng-href='/exit'>Выйти</a>" +
                            "</li>" +
                        "</ul>" +
                        "<a href='#' ng-hide='User.isLogged' class='login-button'>" +
                            "<img src='/recipes/web/bundles/angular/img/vkontakte-logo.png'> Войти" +
                        "</a>"
                    )(scope));
                    elem.find('.login-button').bind('click', function(e) {
                        UserVK.login().then(function(response){
                            console.log(response);
                            scope.User = response;
                        });
                        e.preventDefault();
                    });
                });
            }
        }
    }])
    .directive('checkUser', ['$rootScope', '$location', 'userService', function ($root, $location, userSrv) {
        return {
            link: function (scope, elem, attrs, ctrl) {
                $root.$on('$routeChangeStart', function(event, currRoute, prevRoute){
                    if (!prevRoute.access.isFree && !userSrv.isLogged) {
                        // reload the login route
                    }
                    /*
                     * IMPORTANT:
                     * It's not difficult to fool the previous control,
                     * so it's really IMPORTANT to repeat the control also in the backend,
                     * before sending back from the server reserved information.
                     */
                });
            }
        }
    }])
    .directive('autocomplete', ['$http', function($http) {
        return{
            require:"ngModel",
            link: function (scope, element, attrs, ngModel) {
                scope.$watch(attrs.ngModel, function (v) {
                });
                element.autocomplete({
                    minLength:3,

                    source: function (request, response) {
                        var url = "http://localhost/recipes/web/app_dev.php/api/" + attrs.autocomplete + "/search/" + request.term;
                        $http.get(url).success( function(data) {
                            response(data);
                        });
                    },
                    focus:function(event, ui) {
                        element.val(ui.item.name);
                        return false;
                    },
                    select:function(event, ui) {
                        ngModel.$setViewValue(ui.item.name);
                        scope.$apply;
                        return false;
                    },
                    change:function (event, ui) {
                        scope.$apply;
                        if (ui.item === null) {
                            //scope.myModelId.selected = null;
                        }
                    }
                })
                .keyup(function (e) {
                        if(e.which === 13) {
                            $(".ui-autocomplete").hide();
                        }
                })
                .data("autocomplete")._renderItem = function (ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.name + "</a>")
                        .appendTo(ul);
                };
            }
        }
    }])
    .directive('fileUpload', function () {
        return {
            scope: true,
            require: 'ngModel',
            link: function (scope, el, attrs, ngModel) {
                // валидация
                ngModel.$render = function () {
                    ngModel.$setViewValue(el.val());
                };

                el.bind('change', function (event) {
                    // валидация
                    scope.$apply(function () {
                        ngModel.$render();
                    });

                    // загрузка
                    var files = event.target.files;
                    for (var i = 0;i<files.length;i++) {
                        scope.$emit("fileSelected", { file: files[i] });
                    }
                });

                $(el).bootstrapFileInput();
            }
        };
    })
    .directive('blur', function () {
        return function (scope, elem, attrs) {
            elem.bind('blur', function () {
                scope.$apply(attrs.blur);
            });
        };
    });