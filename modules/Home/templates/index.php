<?php $this->layout('basic');?>

<div data-ng-app="stream" data-ng-controller="MainController as app">

    <ng-view></ng-view>

    <button ng-show="!app.showForm" ng-click="app.showForm = true" class="btn btn-sm btn-default">New message</button>
    <form ng-show="app.showForm">
        <div class="form-group">
            <label>Title</label>
            <input class="form-control" ng-model="post.title"/>
        </div>
        <div class="form-group">
            <label>Body</label>
            <textarea rows="7" class="form-control" ng-model="post.body"></textarea>
        </div>
        <button class="btn btn-success" ng-click="app.create(post)">Save</button>
    </form>

    <div ng-show="true" ng-repeat="post in app.posts">
        <div ng-show="!post.edit">
            <h1>[{{post.id}}] {{post.title}}</h1>
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
        <?php if($authorized): ?>
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
        <?php endif;?>
    </div>

</div>

