<?php

/**
 * Main program
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

$app = new Stream\App();
$app->loadConfig();

$app->get('/', function($req) use ($app) {

    $controller = new modules\Home\Controller($req, $app);

    echo $controller->index();

});

$app->rest(['/posts.json', '/posts/:id.json'], modules\Posts\Controller::class);
$app->rest(['/clients.json', '/clients/:id.json'], modules\Clients\Controller::class);
$app->rest(['/events.json', '/events/:id.json'], modules\Events\Controller::class);

$app->domain('/user/:action', modules\Users\Controller::class);

$app->domain('/tasks/:action', modules\Tasks\Controller::class);
$app->rest(['/tasks.json', '/tasks/:id.json'], modules\Tasks\Api::class);

$app->domain('/tasks/:action/:id', modules\Tasks\Controller::class);

$app->domain('/dev/:action', modules\Homegrown\Dev\Controller::class);

$app->domain('/contributors/:action', modules\Contributors\Controller::class);
