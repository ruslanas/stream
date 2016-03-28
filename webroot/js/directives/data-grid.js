angular.module('DataGrid', [
]).directive('theGrid', ['$compile', function($compile) {
	return {
		scope: {
			data: '=',
			columns: '=',
			action: '=',
			selected: '='
		},
		link: function(scope, element, attr) {
			scope.data.$promise.then(function(data) {
				var tpl = '<table class="table table-condensed table-bordered">'
					+'<thead><tr><th ng-repeat="col in columns">{{col}}</th></tr></thead><tbody>'
					+'<tr ng-class="selected.id === item.id ? \'success\':\'\'" ng-click="action(item)" ng-repeat="item in data">'
					+'<td ng-repeat="col in columns">{{item[col]}}</td></tr>'
					+'</tbody></table>';
				element.append($compile(tpl)(scope));
			});
		}
	}
}]);
