(function() {
    "use strict";

    var app = angular.module('filters', []);

    app.filter('resource', ['$sce', function($sce) {
        return function(val) {
            return $sce.trustAsResourceUrl(val);
        };
    }]);

})();