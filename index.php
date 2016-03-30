<?php

/**
 * Main program
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

$app = new Stream\App();
$app->loadConfig();
$app->connect();

$app->get('/', function($req) {
    $controller = new modules\Home\Controller();
    $controller->default();
});

$app->rest(['/posts.json', '/posts/:id.json'], modules\Posts\Controller::class);
$app->rest(['/clients.json', '/clients/:id.json'], modules\Clients\Controller::class);
$app->rest(['/events.json', '/events/:id.json'], modules\Events\Controller::class);

$app->domain('/user', modules\Users\Controller::class);
