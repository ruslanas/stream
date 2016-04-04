angular.module('client', [
	'ngResource'
]).controller('ClientsController', ['Client', function(Client) {
    this.clients = Client.query();
    this.title = 'Clients';
    this.columns = ['id', 'name', 'email', 'phone', 'created', 'username'];
    this.data = {};
    var self = this;
    this.open = function(data) {
    	self.data = data;
    	// Client.get({id: id}, function(res) {
    	// 	self.data = res;
    	// });
    };
    this.save = function(client) {
    	client.$save(function(res) {
    		console.log(res);
    	});
    };

}]).factory('Client', ['$resource', function($resource) {
    return $resource('/clients/:id.json', {id: "@id"}, {
        delete: {
            method: 'DELETE'
        }
    });
}]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/clients', {
        templateUrl: 'partials/clients.html',
        controller: 'ClientsController',
        controllerAs: 'section'
    });
}]);
