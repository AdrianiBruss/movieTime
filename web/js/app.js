(function(){
    'use strict';
    var app = angular.module('movieTime', ['controllers', 'services', 'directives']);

    app.run(['dataFactory', 'localService',function(dataFactory, localService){

        dataFactory.getMovies();

        localService.getLocalStorage();

    }]);

})();

