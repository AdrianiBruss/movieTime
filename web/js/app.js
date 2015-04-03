(function(){
    'use strict';
    var app = angular.module('movieTime', ['controllers', 'services', 'directives', 'filters']);

    app.run(['dataFactory',function(dataFactory){

        dataFactory.getMovies();

    }]);

})();

