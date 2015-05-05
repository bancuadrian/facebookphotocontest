var setActive = angular.module('pc.photoDialog',['flow']);

setActive.config(['flowFactoryProvider', function (flowFactoryProvider) {
    flowFactoryProvider.defaults = {
        target: '/uploadImage',
        permanentErrors: [500, 501],
        maxChunkRetries: 1,
        chunkRetryInterval: 5000,
        simultaneousUploads: 1
    };
    flowFactoryProvider.on('catchAll', function (event) {
        console.log('catchAll', arguments);
    });
}]);

setActive.directive('photoDialog', function () {
    return {
        restrict: 'E',
        scope: {},
        templateUrl : '/tpl/directives/photo-dialog.html',
        link: function(scope, element, attrs){

        },
        compile: function compile(tElement, tAttrs, transclude) {
            return {
                pre: function preLink(scope, iElement, iAttrs, controller) {
                    scope.obj = {};

                    scope.test = function(file){
                        console.log(file);
                        console.log(scope.obj);
                        scope.obj.flow.upload();
                    }
                },
                post: function postLink(scope, element, iAttrs, controller) {

                }
            }
        },
        controller : function($scope){
        }
    };
});