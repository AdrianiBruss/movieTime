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


        return {
            getMovies:function(){
                return getMovies();
            }
        }


    }]);

})();