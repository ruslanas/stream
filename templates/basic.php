<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->e($title);?></title>
        <script src="/components/angular/angular.min.js"></script>
        <script src="/components/angular-resource/angular-resource.min.js"></script>
        <script src="/js/app.js"></script>
        <link type="text/css" rel="stylesheet" href="/components/bootstrap/dist/css/bootstrap.min.css">
    </head>
    <body data-ng-app="blog" data-ng-controller="MainController as app">

        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/"><?php echo $this->e($title);?></a>
                </div>
            </div>
        </nav>

        <div class="container">

            <a href="/tasks/add">__NEW__</a>

            <?php echo $this->section('content');?>

            <div ng-show="true" ng-repeat="post in app.posts">
                <div ng-show="!post.edit">
                    <h1>{{post.title}}</h1>
                    <div>{{post.body}}</div>
                </div>
                <form ng-show="post.edit">
                    <div class="form-group">
                        <label>Title</label>
                        <input class="form-control" ng-model="post.title"/>
                    </div>
                    <div class="form-group">
                        <label>Body</label>
                        <textarea rows="7" class="form-control" ng-model="post.body"></textarea>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-6">
                        <button ng-show="!post.edit" ng-click="post.edit = true"
                                class="btn btn-sm btn-success">Edit</button>
                        <button ng-show="post.edit" ng-click="app.save(post)"
                                class="btn btn-sm btn-success">Save</button>
                        <button ng-show="post.edit" ng-click="post.edit = false"
                                class="btn btn-sm btn-danger">Cancel</button>
                    </div>
                    <div class="col-xs-6 text-right">
                        <button ng-show="!post.edit" ng-click="app.delete(post)"
                                class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </div>
            </div>

            <h3><em>debug</em></h3>
            <a href="/debug">__PHP_INFO__</a>
            <a href="/debug/cache">__CACHE__</a>

        </div>
    </body>
</html>
