(function(){
    'use strict';
    var app = angular.module('movieTime', ['controllers', 'services']);

    app.run(['dataFactory',function(dataFactory){

        dataFactory.getMovies();

    }]);

})();

