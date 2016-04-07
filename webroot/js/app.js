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
        {title: 'Posts', path: '/', authorize: true},
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

}]).config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
}]).run([

    '$rootScope', 'User', '$location', function($rootScope, User, $location) {

    User.login(function(res) {

        $rootScope.authorized = true;
        $rootScope.user = res;

    }, function(res) {
        $location.url('/login');
    });
}]);
