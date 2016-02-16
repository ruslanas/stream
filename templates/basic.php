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
                <div>
                    <ul class="nav navbar-nav">
                        <li><a href="/debug">PHP info</a></li>
                        <li><a href="/debug/cache">APC</a></li>
                        <li><a href="/debug/opcache">OpCache</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">

            <?php echo $this->section('content');?>

        </div>
    </body>
</html>
