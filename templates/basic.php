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
    <body>

        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/#/"><?php echo $this->e($title);?></a>
                </div>
                <div>
                    <ul class="nav navbar-nav">
                        <li><a href="/#/clients">Clients</a></li>
                        <li><a href="/#/tasks">Tasks</a></li>
                        <li><a href="/user/logout">Sign Out</a></li>
                        <li><a href="/user/add">Register</a></li>
                        <li><a href="/#/login">Sign In</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="container"><?php echo $this->section('content');?></section>

    </body>
</html>
