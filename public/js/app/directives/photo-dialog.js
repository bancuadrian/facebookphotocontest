var setActive = angular.module('pc.photoDialog',['flow','akoenig.deckgrid']);

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

setActive.directive('photoDialog', function ($http) {
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
                    scope.albums = [];
                    scope.action = '';
                    scope.photosAccessError = false;

                    scope.setAction = function(action){
                        scope.action = action;
                        if(action == 'UPLOAD_FACEBOOK')
                        {
                            scope.photosAccessError = false;
                            $http.get('/getAlbums').then(
                                function(response)
                                {
                                    scope.albums = response.data;
                                },
                                function(error)
                                {
                                    if(error.data.scope_required)
                                    {
                                        scope.photosAccessError = true;
                                    }
                                }
                            );
                        }
                    }

                    scope.getPhotosScope = function()
                    {
                        FB.login(function(response){
                            scope.setAction('UPLOAD_FACEBOOK');
                        },{scope: 'user_photos',auth_type:'rerequest'});
                    }

                    scope.getCover = function(album)
                    {
                        console.log(album);
                    }

                    scope.savePhoto = function(photo){
                        var request = {
                            image_base64 : angular.element( document.querySelector( '#photoUpload' )).attr('src'),
                            filename : photo.uniqueIdentifier + '.' + photo.getExtension()
                        };

                        $http.post('/savePhoto',request).then(
                            function(response){

                            },
                            function(error){

                            }
                        );
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