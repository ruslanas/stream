angular.module('tasks', [

    'ngResource',
    'ngRoute',

    'ui.bootstrap'

]).controller('TasksController', [

    'Task', '$rootScope', function(Task, $rootScope) {

    this.tasks = Task.query();
    this.tpl = {tilte: '', description: '', focus: 1};
    this.task = angular.copy(this.tpl);

    var self = this;

    this.add = function(task) {

        task.focus = 1;

        new Task(task).$save(function(res) {
            self.tasks.unshift(res);
            self.task = angular.copy(this.tpl);
        });

    }

    // delegate to admin for now
    this.delegate = function(task, email) {
        task.$delegate({email: email, focus: 0});
    }

    this.focus = function(task) {

        task.focus = (task.focus == 0) ? 1 : 0;
        task.$save();

    }

    this.delete = function(task) {
        task.$remove(function(res) {

            self.tasks = self.tasks.filter(function(el) {
                return el.id !== res.id;
            });

            $rootScope.setError('Task dismissed', 'info');

        }, function(res) {
            alert(res.data);
        });
    };

}]).factory('Task', ['$resource', function($resource) {

    return $resource('/tasks/:id.json', {id: "@id"}, {
        delegate: {
            method: 'POST',
            params: {'action': 'delegate' }
        }
    });

}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/tasks', {
        templateUrl: 'partials/tasks.html',
        controller: 'TasksController',
        controllerAs: 'section'
    });
}]);
