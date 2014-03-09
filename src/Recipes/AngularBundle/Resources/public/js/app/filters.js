'use strict';

/* Filters */

angular.module('recipes.filters', []).
    filter('newlines', function(){
        return function(input, uppercase){
            input = String(input).replace(/\n\n/g, '<br/>');
            return input;
        }
    });
    /**
    filter('interpolate', ['version', function(version) {
        return function(text) {
            return String(text).replace(/\%VERSION\%/mg, version);
        }
    }]);*/