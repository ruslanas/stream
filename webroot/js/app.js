angular.module('stream', [

    'ngResource',
    'ngRoute',
    'ngAnimate',
    'ngSanitize',

    'ui.bootstrap',
    'wiz.markdown',

    'stream.tasks',
    'stream.user'

]).controller('AppController', [

    '$scope', '$location', '$rootScope', 'User',

    function($scope, $location, $rootScope, User) {

    User.login(function(res) {
        if(res.error) { return ; };
        $rootScope.authorized = true;
        $rootScope.user = res;
    });

    this.currentMenuItem = '/';

    this.errors = [];

    var self = this;

    this.dismiss = function($index) {
        self.errors.splice($index, 1);
    };

    this.menuItems = [
        {title: 'Tasks', path: '/tasks', authorize: true},
        {title: 'Register', path: '/register', authorize: false},
        {title: 'Sign In', path: '/login', authorize: false},
        {title: 'Sign Out', path: '/logout', authorize: true}
    ];

    $rootScope.setError = function(err, type) {

        if(typeof type === undefined) {
            type = 'error';
        }

        self.errors.push({
            type: type,
            msg: err
        });
    };

    $scope.$on('$routeChangeSuccess', function(evt, curr, prev) {
        self.currentMenuItem = $location.path();
    });

}]).controller('HomeController', [

    '$rootScope', '$location', function($rootScope, $location) {

    if($rootScope.authorized === true) {
        $location.url('/tasks');
    }

    this.register = function() {
        $location.url('/register');
    }

    this.login = function() {
        $location.url('/login');
    }

}]).config([

    '$locationProvider', '$routeProvider', '$httpProvider',

    function($locationProvider, $routeProvider, $httpProvider) {

    $locationProvider.html5Mode(true);

    $httpProvider.interceptors.push(function($q, $rootScope) {

        return {

            responseError: function(r) {
                
                if(r.status === 401) {
                    $rootScope.authorized = false;
                }

                return $q.reject(r);
            
            }
        
        }
    
    });

    $routeProvider.when('/', {
        templateUrl: 'partials/home.html',
        controller: 'HomeController',
        controllerAs: 'section'
    });

}]);
