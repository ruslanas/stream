<!DOCTYPE html>
<html>
    <head>
        
        <meta charset="utf-8">
        
        <title><?php echo $this->e($title);?></title>
        
        <?php foreach($stylesheets as $sheet): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $sheet; ?>">
        <?php endforeach; ?>

        <?php foreach($scripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>

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
