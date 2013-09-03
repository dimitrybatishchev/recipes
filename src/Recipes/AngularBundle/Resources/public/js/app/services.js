'use strict';

/* Services */

// Demonstrate how to register services
// In this case it is a simple value service.
angular.module('recipes.services', ['ngResource']).
    factory('UserService', [function() {
        var sdo = {
            isLogged: false,
            uid: '',
            sid: '',
            firstname: '',
            lastname: '',
            avatar: ''
        };
        return sdo;
    }]);