var app = angular.module('stream', [
    
    'ngResource',
    'ngRoute',
    'ngAnimate',
    'DataGrid',
    'ui.bootstrap',
    
    'client',
    'messages',
    'tasks',
    'users'

]).controller('AppController', ['$scope', '$location', function($scope, $location) {
    
    this.currentMenuItem = '/';
    
    this.menuItems = [
        {title: 'Posts', path: '/', authorize: true},
        {title: 'Client', path: '/clients', authorize: true},
        {title: 'Tasks', path: '/tasks', authorize: true},
        {title: 'Register', path: '/register', authorize: false},
        {title: 'Sign In', path: '/login', authorize: false},
        {title: 'Sign Out', path: '/logout', authorize: true}
    ];
    
    var self = this;

    $scope.$on('$routeChangeSuccess', function(evt, curr, prev) {
        self.currentMenuItem = $location.path();
    });

}]).config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
}]);
