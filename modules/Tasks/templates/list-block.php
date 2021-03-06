<section ng-app="tasks" ng-controller="TasksController as tasks">

    <div class="panel panel-info" ng-repeat="task in tasks.tasks">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8"><strong><a href="/tasks/edit/{{task.id}}">{{task.title}}</a></strong></div>
                <div class="col-md-4 text-right">
                    {{task.created}}
                    <button class="btn btn-xs btn-danger" ng-click="tasks.delete(task)">Close</button>
                </div>
            </div>
        </div>
        <div class="panel-body">{{task.description}}</div>
    </div>

</section>
