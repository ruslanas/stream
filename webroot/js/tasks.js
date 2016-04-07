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

    this.focus = function(task) {

        task.$save({focus: task.focus == 0 ? 1 : 0});

    }

    this.delete = function(task) {
        task.$remove(function(res) {

            self.tasks = self.tasks.filter(function(el) {
                return el.id !== res.id;
            });

            $rootScope.message = 'Task dismissed';

        }, function(res) {
            alert(res.data);
        });
    };

}]).factory('Task', ['$resource', function($resource) {

    return $resource('/tasks/:id.json', {id: "@id"});

}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/tasks', {
        templateUrl: 'partials/tasks.html',
        controller: 'TasksController',
        controllerAs: 'section'
    });
}]);
