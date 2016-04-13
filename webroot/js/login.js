angular.module('stream.user', [

    'ngResource',
    'ngRoute',

]).controller('LoginController',

    ['User', '$rootScope', '$location', function(User, $rootScope, $location) {

    this.user = $rootScope.user ? $rootScope.user : {};

    this.error = {};

    var self = this;

    if($rootScope.authorized) {
        User.logout(function() {
            $rootScope.authorized = false;
            $rootScope.user = false;
        });
    }

    this.register = function($event, user) {

        $event.preventDefault();

        var user = new User(user);

        user.$register(function(res) {

            // display system error
            if(res.error) {
                self.error = res.error;
                return;
            }

            $rootScope.user = res;

            $location.url('/login');

        }, function(res) {
            console.log(res);
        });

    }

    this.login = function($event) {

        $event.preventDefault();

        var user = new User(self.user);

        user.$login(function(res) {

            if(res.error) {
                self.error = res.error;
                return false;
            }

            $rootScope.authorized = true;
            $rootScope.user = res;
            $location.url('/tasks');

        }, function(res) {
            alert(res.data);
        });

        return false;
    }

}]).factory('User', ['$resource', function($resource) {

    return $resource('/users/:action.json', {action: "@action"}, {

        login: {
            method: 'POST',
            params: {
                action: 'login'
            }
        },

        search: {
            method: 'GET',
            isArray: true,
            params: {action: 'search'}
        },

        logout: {
            method: 'POST',
            params: {action: 'logout'}
        },

        register: {
            method: 'POST',
            params: {action: 'register'}
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
    }).when('/register', {
        templateUrl: 'partials/register.html',
        controller: 'LoginController',
        controllerAs: 'section'
    });
}]);
