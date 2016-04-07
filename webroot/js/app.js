angular.module('stream', [

    'ngResource',
    'ngRoute',
    'ngAnimate',

    'ui.bootstrap',

    'DataGrid',

    'client',
    'messages',
    'tasks',
    'users'

]).controller('AppController', [
    '$scope', '$location', '$rootScope', function($scope, $location, $rootScope) {

    this.currentMenuItem = '/';

    this.menuItems = [
        // {title: 'Posts', path: '/', authorize: true},
        {title: 'Tasks', path: '/tasks', authorize: true},
        {title: 'Register', path: '/register', authorize: false},
        {title: 'Sign In', path: '/login', authorize: false},
        {title: 'Sign Out', path: '/logout', authorize: true}
    ];

    var self = this;

    $rootScope.setError = function(err) {
        $rootScope.error = err;
    }

    $scope.$on('$routeChangeSuccess', function(evt, curr, prev) {
        self.currentMenuItem = $location.path();
    });

}]).controller('HomeController', [

    '$rootScope', '$location', function($rootScope, $location) {

    if($rootScope.authorized) {
        $location.url('/tasks');
    }

    this.register = function() {
        $location.url('/register');
    }

    this.login = function() {
        $location.url('/login');
    }

}]).config([

    '$locationProvider', '$routeProvider',

    function($locationProvider, $routeProvider) {

    $locationProvider.html5Mode(true);
    $routeProvider.when('/', {
        templateUrl: 'partials/home.html',
        controller: 'HomeController',
        controllerAs: 'section'
    });

}]).run([

    '$rootScope', 'User', '$location', function($rootScope, User, $location) {

    User.login(function(res) {

        $rootScope.authorized = true;
        $rootScope.user = res;

    }, function(res) { });

}]);
