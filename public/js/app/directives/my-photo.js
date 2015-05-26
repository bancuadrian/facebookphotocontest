var setActive = angular.module('pc.myPhoto',['akoenig.deckgrid']);

setActive.config(['flowFactoryProvider', function (flowFactoryProvider) {
    flowFactoryProvider.defaults = {
        target: '/uploadImage',
        permanentErrors: [500, 501],
        maxChunkRetries: 1,
        chunkRetryInterval: 5000,
        simultaneousUploads: 1
    };
    flowFactoryProvider.on('catchAll', function (event) {
        //console.log('catchAll', arguments);
    });
}]);

setActive.directive('myPhoto', function ($http,flowFactory,$timeout) {
    return {
        restrict: 'E',
        scope: {
            photo : '='
        },
        templateUrl : '/tpl/directives/my-photo.html',
        link: function(scope, element, attrs){

        },
        compile: function compile(tElement, tAttrs, transclude) {
            return {
                pre: function preLink(scope, iElement, iAttrs, controller) {
                    scope.changePicture = function(){
                        scope.$emit('change_picture');
                    }

                    scope.removePicture = function(){
                        $http.post('/removeMyPhoto',{photo:scope.photo}).then(
                            function(response){
                                scope.$emit('change_picture');
                            }
                        );
                    }

                    scope.sharePicture = function()
                    {
                        var obj = {
                            method: 'feed',
                            link: app_url + '/#/gallery?view_photo='+scope.photo.id,
                            name: contest_name,
                            description: "Help me win votes",
                            picture: scope.photo.path
                        };

                        FB.ui(obj, function(response){
                        });
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