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

$app->rest(['/posts.json', '/posts/:id.json'], modules\Posts\Controller::class);
$app->rest(['/clients.json', '/clients/:id.json'], modules\Clients\Controller::class);
$app->rest(['/events.json', '/events/:id.json'], modules\Events\Controller::class);

$app->domain('/user', UserController::class);
