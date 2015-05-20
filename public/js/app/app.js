var app = angular.module('photoContestApp',
    [
        'ngRoute',
        'ngCookies',
        'ngStorage',
        'pascalprecht.translate',
        'pc.photoDialog',
        'pc.myPhoto',
        'pc.loading']
);

app.config(['$routeProvider','$httpProvider', function ($routeProvider,$httpProvider) {
    $routeProvider.when('/', {
        templateUrl: '/tpl/home.html',
        resolve: {
            test: function ($q, $route, $rootScope) {
                var defer = $q.defer();

                defer.resolve('test1');

                $rootScope.test = "test1";

                return defer.promise;
            }
        }
    });

    $routeProvider.when('/gallery', {
        templateUrl: '/tpl/gallery.html',
        resolve: {}
    });

    $routeProvider.when('/rankings', {
        templateUrl: '/tpl/rankings.html',
        resolve: {}
    });

    $routeProvider.when('/my-photo', {
        templateUrl: '/tpl/my-photo.html',
        controller: 'MyPhotoCtrl',
        resolve: {
            MyPhoto : function($q,$http){
                var defer = $q.defer();

                $http.get('/getMyPhoto').then(
                    function(response){
                        defer.resolve(response.data);
                    },
                    function(error){
                        defer.reject('Error');
                    }
                );

                return defer.promise;
            }
        }
    });

    $routeProvider.when('/friends-photos', {
        templateUrl: '/tpl/friends-photos.html',
        resolve: {}
    });

    $routeProvider.otherwise({redirectTo: '/'});

    $httpProvider.interceptors.push(function($q){
        return {
            'request':function(config){
                return config;
            },
            'response': function (response) {
                return response;
            },
            'responseError': function (rejection) {
                return $q.reject(rejection);
            }
        };
    });

    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

app.run(['$rootScope',function($rootScope){
    $rootScope.$on( "$routeChangeSuccess", function(event, next, current) {
        var currentRoute = next.originalPath;
        $rootScope.getActive = function(path){
            if(path == currentRoute) return 'active';
            return '';
        }
    });
}]);

app.config(['$translateProvider', function($translateProvider){
    // Register a loader for the static files
    // So, the module will search missing translation tables under the specified urls.
    // Those urls are [prefix][langKey][suffix].
    $translateProvider.useStaticFilesLoader({
        prefix: 'l10n/',
        suffix: '.json'
    });
    // Tell the module what language to use by default
    $translateProvider.preferredLanguage('en');
    // Tell the module to store the language in the local storage
    $translateProvider.useLocalStorage();

    $translateProvider.useSanitizeValueStrategy('escaped');
}]);

app.controller('MyPhotoCtrl',function($scope,MyPhoto){
    $scope.addPhoto = true;
    $scope.userHasPhoto = false;

    if(MyPhoto)
    {
        $scope.myPhoto = MyPhoto;
        $scope.userHasPhoto = true;
        $scope.addPhoto = false;
    }

    $scope.$on('change_picture',function(){
        $scope.addPhoto = true;
        $scope.userHasPhoto = false;
    });

    $scope.$on('photo_saved',function(event,data){
        $scope.myPhoto = data;
        $scope.addPhoto = false;
        $scope.userHasPhoto = true;
    });
});
