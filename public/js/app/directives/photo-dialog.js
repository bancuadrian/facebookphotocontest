var setActive = angular.module('pc.photoDialog',[]);

setActive.directive('photoDialog', function ($route) {
    return {
        restrict: 'E',
        scope: {},
        templateUrl : '/tpl/directives/photo-dialog.html',
        link: function(scope, element, attrs){

        },
        compile: function compile(tElement, tAttrs, transclude) {
            return {
                pre: function preLink(scope, iElement, iAttrs, controller) {
                    console.log('here');
                },
                post: function postLink(scope, element, iAttrs, controller) {

                }
            }
        },
        controller : function($scope){
        }
    };
});