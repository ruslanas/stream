angular.module('users', [
    'ngResource',
    'ngRoute',
]).controller('LoginController',
    
    ['User', '$rootScope', '$location', function(User, $rootScope, $location) {

    this.user = {};
    
    var self = this;

    if($rootScope.authorized) {
        User.logout(function() {
            $rootScope.authorized = false;
        });
    }

    this.login = function($event) {

        $event.preventDefault();

        var user = new User(self.user);
        
        user.$login(function(res) {
            
            $rootScope.authorized = true;
            $rootScope.user = res;
            $location.url('/tasks');

        }, function(res) {
            alert(res.data);
        });

        return false;
    }

}]).factory('User', ['$resource', function($resource) {

    return $resource('/users/login.json', {id: "@id"}, {
        login: {
            method: 'POST'
        },
        logout: {
            method: 'DELETE'
        }
    });

}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'LoginController',
        controllerAs: 'section'
    }).when('/logout', {
        templateUrl: 'partials/login.html',
        controller: 'LoginController',
        controllerAs: 'section'
    });
}]);
