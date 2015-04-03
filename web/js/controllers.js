(function(){
    'use strict';

    var app = angular.module('controllers', []);

    app.controller('moviesCtrl', ['dataFactory' , function(dataFactory){

        this.movie = [];

        var self = this;
        var promise  = dataFactory.getMovies();
        promise.then(function(result){
            console.log(result);
            self.movie = result;

        });

    }]);

})();