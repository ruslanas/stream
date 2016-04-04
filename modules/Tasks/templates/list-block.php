<section ng-app="tasks" ng-controller="TasksController as tasks">

    <div class="panel panel-info" ng-repeat="task in tasks.tasks">
        <div class="panel-heading">
            <strong><a href="/tasks/edit/{{task.id}}">{{task.title}}</a></strong>
        </div>
        <div class="panel-body">
            {{task.description}}
            <br/><button class="btn btn-small btn-danger" ng-click="tasks.delete(task)">Close</button>
        </div>
    </div>

</section>
