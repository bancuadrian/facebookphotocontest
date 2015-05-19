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

setActive.directive('photoDialog', function ($http,flowFactory,$timeout) {
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
                    scope.obj.flow = flowFactory.create({});
                    scope.albums = [];

                    scope.photos = [];
                    scope.photos_paging = {};
                    scope.gotPhotos = false;
                    scope.facebookPhoto = '';
                    scope.showPhotoPreview = false;

                    scope.action = '';

                    scope.photosAccessError = false;
                    scope.anyError = false;

                    scope.setAction = function(action){
                        scope.action = action;
                        if(action == 'UPLOAD_FACEBOOK')
                        {
                            scope.photosAccessError = false;
                            scope.anyError = false;

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
                                        return;
                                    }

                                    scope.anyError = true;
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

                    scope.getPhotosForAlbum = function(album,paging)
                    {
                        scope.photosAccessError = false;
                        scope.anyError = false;

                        $http.post('/getPhotosForAlbum',{album_id:album.id}).then(
                            function(response)
                            {
                                scope.photos = response.data.data;
                                scope.photos_paging = response.data.paging;
                                scope.gotPhotos = true;
                            },
                            function(error)
                            {
                                if(error.data.scope_required)
                                {
                                    scope.photosAccessError = true;
                                    return;
                                }

                                scope.anyError = true;
                            }
                        );
                    }

                    scope.nextPage = function()
                    {

                    }

                    scope.previousPage = function()
                    {

                    }

                    scope.previewPhoto = function(photo)
                    {
                        $http.post('/getImageBase64',{image:photo.images[0]}).then(
                            function(response)
                            {
                                $timeout(function() {
                                    scope.facebookPhoto = response.data.base64;
                                    scope.showPhotoPreview = true;
                                });
                            },
                            function(error)
                            {
                                console.log(error);
                            }
                        );
                    }

                    scope.backToAlbums = function()
                    {
                        scope.showPhotoPreview = false;
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