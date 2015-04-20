(function(){
    'use strict';

    var app = angular.module('controllers', []);

    app.controller('moviesCtrl', ['dataFactory', '$scope' , function(dataFactory, $scope){

        this.movie = [];

        var self = this;
        var promise  = dataFactory.getMovies();
        promise.then(function(result){
            self.movie = result;

        });

        this.like = function(like){

            (this.movie[like].like) ? this.movie[like].like = false : this.movie[like].like = true;
        };

        this.removeTorrent = function(index, parent){

            dataFactory.deleteTorrent(this.movie[parent].torrents[index].id);
            this.movie[parent].torrents.splice(index, 1);

        };

        this.closeTorrent = false;
    }]);

})();