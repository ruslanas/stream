angular.module('tasks', [

    'ngResource',
    'ngRoute',

    'ui.bootstrap'

]).controller('TasksController', ['Task', function(Task) {
    
    this.tasks = Task.query();
    this.task = {};

    var self = this;

    this.add = function(task) {

        new Task(task).$save(function(res) {
            self.tasks.unshift(res);
            self.task = {};
        });
    
    }

    this.delete = function(task) {
        task.$remove(function(res) {

            self.tasks = self.tasks.filter(function(el) {
                return el.id !== res.id;
            });
            
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
