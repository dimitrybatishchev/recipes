'use strict';

angular.module('recipes.services', ['ngResource']).
    factory('UserVK', ['$http', '$rootScope', '$q', '$timeout', function($http, $rootScope, $q, $timeout) {

        var user = {
            active: false,
            uid: 0,
            sid: 0,
            firstname: '',
            lastname: '',
            isLogged: false
        };
        var deferred = $q.defer(); //todo:

        var loginInVK = function(response){
            var that = this;

            if (response.status === 'connected') {
                user.uid = response.session.mid;
                user.sid = response.session.sid;
                user.firstname = response.session.user.first_name;
                user.lastname = response.session.user.last_name;
                user.active = true;
                VK.api("users.get", {"uids": response.session.mid, "fields":"photo"}, function(data) {
                    user.isLogged = true;
                    user.avatar = data.response[0].photo;
                    $http.post('http://localhost/recipes/web/app_dev.php/api/users/login', {
                        id: user.uid,
                        firstname: user.firstname,
                        lastname: user.lastname,
                        avatar: user.avatar
                    }).success(function(response){
                        deferred.resolve(user);
                        if(!$rootScope.$$phase){
                            $rootScope.$apply();
                        }
                    });
                });
            } else {
                deferred.resolve(user);
                if(!$rootScope.$$phase){
                    $rootScope.$apply();
                }
            }
        }

        return {
            getUser: function(){
                if (user.active){
                    return $q.when(user);
                }
                VK.Auth.getLoginStatus(loginInVK, true);
                var promise = deferred.promise;
                return promise;
            },
            login: function(){
                if (user.active){
                    return $q.when(user);
                }
                VK.Auth.login(loginInVK, 1027);
                deferred = $q.defer();
                var promise = deferred.promise;
                return promise;
            }
        };
    }]);