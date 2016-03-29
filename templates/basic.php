<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->e($title);?></title>
        <link rel="stylesheet" type="text/css" href="/css/basic.css"/>
        <script src="/components/angular/angular.min.js"></script>
        <script src="/components/angular-resource/angular-resource.min.js"></script>
        <script src="/components/angular-route/angular-route.min.js"></script>
        <script src="/components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>

        <script src="/js/app.js"></script>
        <!-- modules -->
        <script src="/js/stream.js"></script>
        <script src="/js/client.js"></script>
        <script src="/js/directives/data-grid.js"></script>

        <link type="text/css" rel="stylesheet" href="/components/bootstrap/dist/css/bootstrap.min.css">
    </head>
    <body data-ng-app="stream" data-ng-controller="MainController as app">

        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/"><?php echo $this->e($title);?></a>
                </div>
                <div>
                    <ul class="nav navbar-nav">
                        <?php if($authorized): ?>
                            <li><a href="/#clients">Clients</a></li>
                            <li><a href="/user/logout">Sign Out</a></li>
                        <?php else: ?>
                            <li><a href="/user/add">Register</a></li>
                            <li><a href="/user/login">Sign In</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">

            <ng-view></ng-view>

            <?php echo $this->section('content');?>

        </div>
    </body>
</html>
