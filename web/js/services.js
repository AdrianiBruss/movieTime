(function(){
    'use strict';

    var app = angular.module('services', []);

    app.factory('dataFactory', ['$http', '$q', function($http, $q){

        function getMovies(){

            var deffered = $q.defer();

            $http.get('app_dev.php/')
                .success(function(result){

                    deffered.resolve(result);
                })
                .error(function(err){
                    console.log(err)
                });

            return deffered.promise;
        }


        function deleteTorrent(id){
            // implements method to delete a torrent

            console.log(id);
            $http.get('app_dev.php/removeTorrent/'+id+'')
                .success(function(result){
                    console.log(result);
                });
        }


        return {
            getMovies:function(){
                return getMovies();
            },
            deleteTorrent:function(id){
                return deleteTorrent(id);
            }
        }


    }]);


})();