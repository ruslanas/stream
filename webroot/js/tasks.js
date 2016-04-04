var app = angular.module('tasks', [
    'ngResource',
    'ngRoute',
    'ui.bootstrap'
]);

app.controller('TasksController', ['Task', function(Task) {
    
    this.tasks = Task.query();
    var self = this;
    
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

}]);
