var setActive = angular.module('pc.photoGallery',['akoenig.deckgrid','pc.viewPhoto']);

setActive.directive('photoGallery', function ($http,$q,$timeout) {
    return {
        restrict: 'E',
        scope: {},
        templateUrl : '/tpl/directives/photo-gallery.html',
        compile: function compile(tElement, tAttrs, transclude) {
            return {
                pre: function preLink(scope, iElement, iAttrs, controller) {
                    scope.response = {};
                    scope.currentPhoto = null;

                    var getPhotos = function(page)
                    {
                        var defer = $q.defer();
                        var url = '/getAllPhotos';

                        if(page)
                        {
                            url += '?page=' + page;
                        }

                        $http.get(url).then(
                            function(response)
                            {
                                scope.response = response.data;
                                defer.resolve(scope.response);
                            },
                            function(error){}
                        );

                        return defer.promise;
                    }

                    scope.nextPage = function(select_first)
                    {
                        if(scope.response.current_page)
                        {
                            if(scope.response.current_page == scope.response.last_page)
                            {
                                scope.response.current_page = 0;
                            }
                        }

                        getPhotos(scope.response.current_page + 1).then(
                            function(data){
                                if(select_first)
                                {
                                    scope.currentPhoto = data.data[0];
                                }
                            }
                        );
                    }

                    scope.previousPage = function(select_last)
                    {
                        if(scope.response.current_page)
                        {
                            if(scope.response.current_page == 1)
                            {
                                scope.response.current_page = scope.response.last_page + 1;
                            }
                        }

                        getPhotos(scope.response.current_page - 1).then(
                            function(data){
                                if(select_last)
                                {
                                    scope.currentPhoto = data.data[data.data.length - 1];
                                }
                            }
                        );
                    }

                    scope.previewPhoto = function(photo)
                    {
                        scope.currentPhoto = photo;
                    }

                    scope.hideCurrentPhoto = function()
                    {
                        scope.currentPhoto = null;
                    }

                    scope.nextPhoto = function()
                    {
                        var id = scope.response.data.indexOf(scope.currentPhoto);
                        var next = id + 1;
                        if(scope.response.data[next])
                        {
                            scope.currentPhoto = scope.response.data[next];
                        }
                        else
                        {
                            if(next == scope.response.data.length)
                            {
                                scope.nextPage(true);
                            }
                        }
                    }

                    scope.previousPhoto = function()
                    {
                        var id = scope.response.data.indexOf(scope.currentPhoto);
                        var previous = id - 1;
                        if(scope.response.data[previous])
                        {
                            scope.currentPhoto = scope.response.data[previous];
                        }
                        else
                        {
                            if(previous == -1)
                            {
                                scope.previousPage(true);
                            }
                        }
                    }

                    getPhotos();
                },
                post: function postLink(scope, element, iAttrs, controller) {

                }
            }
        },
        controller : function($scope){
        }
    };
});