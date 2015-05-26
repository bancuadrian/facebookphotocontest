var setActive = angular.module('pc.viewPhoto',[]);

setActive.directive('viewPhoto', function ($http,flowFactory,$timeout) {
    return {
        restrict: 'E',
        scope: {
            photo : '=',
            closeAction : '&onClose',
            next : '&next',
            prev : '&prev',
            vote : '&vote'
        },
        templateUrl : '/tpl/directives/view-photo.html',
        link: function(scope, element, attrs){

        },
        compile: function compile(tElement, tAttrs, transclude) {
            return {
                pre: function preLink(scope, iElement, iAttrs, controller) {

                },
                post: function postLink(scope, element, iAttrs, controller) {

                }
            }
        },
        controller : function($scope){
            $scope.share = function(){
                var obj = {
                    method: 'feed',
                    link: app_url + '/#/gallery?view_photo='+$scope.photo.id,
                    name: contest_name,
                    description: "Help me win votes",
                    picture: $scope.photo.path
                };

                FB.ui(obj, function(response){
                });
            }
        }
    };
});