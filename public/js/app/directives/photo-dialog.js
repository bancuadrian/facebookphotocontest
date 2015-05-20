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
                    scope.current_album = null;

                    scope.photos = [];
                    scope.photos_paging = {};
                    scope.gotPhotos = false;
                    scope.facebookPhoto = '';
                    scope.showPhotoPreview = false;
                    scope.currentPreviewImage = null;

                    scope.action = '';

                    scope.photosAccessError = false;
                    scope.anyError = false;

                    scope.setAction = function(action){
                        if(action == 'UPLOAD_FACEBOOK')
                        {
                            scope.getAlbums();
                            return;
                        }

                        scope.action = action;
                    }

                    scope.getAlbums = function(paging)
                    {
                        scope.photosAccessError = false;
                        scope.anyError = false;

                        $http.post('/getAlbums',{direction:paging,albums:scope.albums}).then(
                            function(response)
                            {
                                $timeout(function(){
                                    delete(scope.albums);
                                    scope.action = 'UPLOAD_FACEBOOK';
                                    scope.albums = response.data;
                                    scope.$apply();
                                });
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

                        $http.post('/getPhotosForAlbum',{album_id:album.id,direction:paging,paging:scope.photos_paging}).then(
                            function(response)
                            {
                                $timeout(function(){
                                    scope.photos = response.data.data;
                                    scope.photos_paging = response.data.paging;
                                    scope.current_album = album;
                                    scope.gotPhotos = true;
                                });
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

                    scope.backToAlbums = function()
                    {
                        scope.gotPhotos = false;
                        scope.photos = [];
                        scope.photos_paging = {};
                        scope.current_album = null;
                    }

                    scope.nextPage = function()
                    {
                        scope.getAlbums('next');
                    }

                    scope.previousPage = function()
                    {
                        scope.getAlbums('previous');
                    }

                    scope.nextPagePhotos = function()
                    {
                        scope.getPhotosForAlbum(scope.current_album,'next');
                    }

                    scope.previousPagePhotos = function()
                    {
                        scope.getPhotosForAlbum(scope.current_album,'previous');
                    }

                    scope.previewPhoto = function(photo)
                    {
                        $http.post('/getImageBase64',{image:photo.images[0]}).then(
                            function(response)
                            {
                                $timeout(function() {
                                    scope.facebookPhoto = response.data.base64;
                                    scope.showPhotoPreview = true;
                                    scope.currentPreviewImage = photo;
                                });
                            },
                            function(error)
                            {
                                console.log(error);
                            }
                        );
                    }

                    scope.backToPhotos = function()
                    {
                        scope.facebookPhoto = '';
                        scope.showPhotoPreview = false;
                        scope.currentPreviewImage = null;
                    }

                    scope.savePhoto = function(photo){
                        var request = {
                            image_base64 : angular.element( document.querySelector( '#photoUpload' )).attr('src'),
                            filename : photo.uniqueIdentifier + '.' + photo.getExtension()
                        };

                        $http.post('/savePhoto',request).then(
                            function(response){
                                scope.$emit('photo_saved',response.data);
                            },
                            function(error){

                            }
                        );
                    }

                    scope.savePhotoFacebook = function(){
                        var request = {
                            image_base64 : scope.facebookPhoto,
                            filename : scope.currentPreviewImage.images[0].source
                        };

                        $http.post('/savePhoto',request).then(
                            function(response){
                                scope.$emit('photo_saved',response.data);
                            },
                            function(error){
                                console.log(error);
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