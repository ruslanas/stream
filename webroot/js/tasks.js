angular.module('stream.tasks', [

    'ngResource',
    'ngRoute',

]).controller('TasksController', [

    'Task', '$rootScope', 'User', '$routeParams',
    function(Task, $rootScope, User, $routeParams) {

    if($routeParams.key !== undefined) {
        User.login({key: $routeParams.key}, function(res) {
            $rootScope.user = res;
            $rootScope.authorized = true;
        });
    }

    this.tasks = Task.query();
    this.tpl = {tilte: '', description: '', focus: 1};
    this.task = angular.copy(this.tpl);

    var self = this;

    this.getUsers = function($viewValue) {
        
        return User.search({email: $viewValue}).$promise.then(function(res) {
            return res;
        });

    };

    this.add = function(task) {

        task.focus = 1;
        
        if(!task.description) { task.description = task.title; }
        
        new Task(task).$save(function(res) {
            self.tasks.unshift(res);
            self.task = angular.copy(this.tpl);
        });

    };

    // delegate to admin for now
    this.delegate = function(task, email) {
        task.$delegate({email: email, focus: 0});
    };

    this.reject = function(task) {
        task.accepted = false;
        task.$save();
    };

    this.focus = function(task) {

        task.focus = (task.focus == 0) ? 1 : 0;
        task.$save();

    };

    this.complete = function(task) {
        task.completed = 1;
        task.$save();
    };

    this.delete = function(task) {
        task.$remove(function(res) {

            self.tasks = self.tasks.filter(function(el) {
                return el.id !== res.id;
            });

            $rootScope.setError('Task dismissed', 'info');

        }, function(res) {
            $rootScope.setError('Unexpected error occured', 'danger');
        });
    };

}]).factory('Task', ['$resource', function($resource) {

    return $resource('/tasks/:id.json', {id: "@id"}, {
        
        delegate: {
            method: 'POST',
            params: {action: 'delegate' }
        }
    
    });

}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/tasks', {
        templateUrl: 'partials/tasks.html',
        controller: 'TasksController',
        controllerAs: 'section'
    });
    $routeProvider.when('/tk/:key', {
        templateUrl: 'partials/tasks.html',
        controller: 'TasksController',
        controllerAs: 'section'
    });
}]);
