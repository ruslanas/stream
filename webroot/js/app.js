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
]).controller('AppController', [function() {

}]).config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
}]);
