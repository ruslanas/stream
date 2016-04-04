angular.module('users', [
    'ngResource',
    'ngRoute',
]).controller('LoginController', ['User', function(User) {

    this.user;
    
    var self = this;

    this.login = function($event) {

        $event.preventDefault();

        var user = new User(self.user);
        
        user.$login(function(res) {
            console.log(res);
        }, function(res) {
            console.log(res);
        });

        return false;
    }

}]).factory('User', ['$resource', function($resource) {

    return $resource('/users/login.json', {id: "@id"}, {
        login: {
            method: 'POST'
        }
    });

}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'LoginController'
    });
}]);
