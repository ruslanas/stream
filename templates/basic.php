<!DOCTYPE html>
<html>
    <head>
        
        <meta charset="utf-8">
        <base href="/">
        <title><?php echo $this->e($title);?></title>
        
        <?php foreach($stylesheets as $sheet): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $sheet; ?>">
        <?php endforeach; ?>

        <?php foreach($scripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>

    </head>
    <body data-ng-app="stream" ng-controller="AppController as sys">

        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/#/"><?php echo $this->e($title);?></a>
                </div>
                <div>
                    <ul class="nav navbar-nav">

                        <li><a href="clients" ng-show="authorized" ng-class="">Clients</a></li>
                        <li><a href="tasks" ng-show="authorized">Tasks</a></li>
                        
                        <li><a href="logout" ng-show="authorized">Sign Out</a></li>
                        
                        <li><a href="register" ng-show="!authorized">Register</a></li>
                        <li><a href="login" ng-show="!authorized">Sign In</a></li>
                    
                    </ul>
                </div>
            </div>
        </nav>
        <section class="container"><?php echo $this->section('content');?></section>

    </body>
</html>
