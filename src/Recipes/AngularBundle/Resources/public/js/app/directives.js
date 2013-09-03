'use strict';

/* Directives */


angular.module('recipes.directives', [])
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
        /*
        return {


            restrict: 'A',
            replace: true,
            transclude: true,
            scope: { title:'@ingredientsDecline' },
            //template: '<input type="text" value="{{title}}"style="width: 90%"/>',
            link: function(scope, element, attrs){
              console.log(scope.title);
            }

            ,
            compile: function compile(templateElement, templateAttrs) {
                templateElement.prepend("nfhfnfnf");
                return {
                    pre: function ($scope, element, attrs) {
                        console.log($scope);
                    },
                    post: function(scope, element, attrs) {

                    }
                }
            }

        };

        return {
            restrict: 'A', // стиль декларирования
            scope: {
                twoWayBind: "=myTwoWayBind"
            },
            compile: function compile(templateElement, templateAttrs) {

            }
        }; */
    })
    .directive('vkLogin', ['UserService', '$http', function(User, $http){
        return {
            restrict: 'A', // стиль декларирования
            scope: true, // новый scope будет создан для этой директивы
            controller: function($scope, $attrs){
                VK.init({
                    apiId: 3838127
                });
                VK.Auth.getLoginStatus(loginInVK, true);
                function loginInVK(response){
                    var that = this;
                    $scope.User = User;
                    if (response.status === 'connected') {
                        $scope.User.uid = response.session.mid;
                        $scope.User.sid = response.session.sid;
                        $scope.User.firstname = response.session.user.first_name;
                        $scope.User.lastname = response.session.user.last_name;
                        VK.api("users.get", {"uids": response.session.mid, "fields":"photo"}, function(data) {
                            $scope.User.isLogged = true;
                            $scope.User.avatar = data.response[0].photo;
                            console.log('post data');
                            $http.post('http://localhost/recipes/web/app_dev.php/api/users/login', {
                                    id: $scope.User.uid,
                                    firstname: $scope.User.firstname,
                                    lastname: $scope.User.lastname,
                                    avatar: $scope.User.avatar
                                }).success(function(response){
                                        // ...
                                });
                            // Вставить данные пользователя
                        });
                    }
                };
                $scope.login = function(){
                    console.log('login');
                    VK.Auth.login(loginInVK, 1027);
                    return false;
                }
            },
            link: function(scope, element, attrs){
                /*Задаем функцию, которая будет вызываться при изменении переменной user
                $scope.$watch(attrs.vkLogin,function(value){
                        element.text(value+attrs.habra);
                    }
                );
                */
            },
            compile: function compile(templateElement, templateAttrs) {
                templateElement.html("" +
                    "<a href='' ng-hide='User.isLogged' ng-click='login()'>Войти</a>" +
                    "<a ng-show='User.isLogged' class='user dropdown-toggle' data-toggle='dropdown'>" +
                        "<img class='img-circle' src='{{User.avatar}}'>" +
                        "<span>{{User.firstname}} {{User.lastname}}</span>" + "<b class='caret'></b>" +
                    "</a>" +
                    "<ul class='dropdown-menu' role='menu'><li><a href='#'>Профиль</a></li><li><a href='#'>Избранное</a></li><li class='divider'></li><li><a href='#'>Выйти</a></li></ul>" );
            }
        }
    }])
    .directive('checkUser', ['$rootScope', '$location', 'userService', function ($root, $location, userSrv) {
        return {
            link: function (scope, elem, attrs, ctrl) {
                $root.$on('$routeChangeStart', function(event, currRoute, prevRoute){
                    console.log('route change');
                    console.log(prevRoute.access.isFree);
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
    .directive('autocomplete', ['$http', 'Category', function($http, Category) {
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
                console.log('fu - ' + el);

                // валидация
                ngModel.$render = function () {
                    console.log(el);
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