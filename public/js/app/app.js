var app = angular.module('photoContestApp',['ngRoute','ngCookies','ngStorage','pascalprecht.translate','pc.photoDialog','pc.loading']);

app.config(['$routeProvider', function ($routeProvider) {
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
        resolve: {}
    });

    $routeProvider.when('/friends-photos', {
        templateUrl: '/tpl/friends-photos.html',
        resolve: {}
    });

    $routeProvider.otherwise({redirectTo: '/'});
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
