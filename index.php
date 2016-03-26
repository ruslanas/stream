<?php

/**
 * Main program
 * Configure application and setup request handlers
 * Application logic is implemented in controllers
 * Controllers use models to manipulate data and templates to present it to user
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

$app = new App();
$app->loadConfig();
$app->connect();

$ctrl = new MainController();

$app->get('/^\/(\?.*)*$/', function($req) use ($ctrl) {
    $ctrl->home();
});

$app->rest(['/posts.json', '/posts/:id.json'], 'RestController');

$app->rest(['/clients.json', '/clients/:id.json'], 'ClientsController');

$app->domain('/user', 'UserController');
$app->domain('/debug', 'DebugController');
