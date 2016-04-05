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

                        <li ng-class="{active: (mi.path === sys.currentMenuItem)}"
                            ng-repeat="mi in sys.menuItems"
                            ng-show="mi.authorize ? authorized : !authorized"><a href="{{mi.path}}">{{mi.title}}</a></li>
                        
                    </ul>
                </div>
            </div>
        </nav>
        <section class="container"><?php echo $this->section('content');?></section>

    </body>
</html>
