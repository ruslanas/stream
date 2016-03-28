var app = angular.module('stream', [
    'ngResource',
    'ngRoute',
    'DataGrid',
    'ui.bootstrap',
    'client',
    'messages'
]);

app.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'LoginController'
    });
}]);

app.controller('LoginController', [function() {

}]);
