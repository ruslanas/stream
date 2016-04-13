<?php

/**
 * Main program
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

$app = new Stream\App();

$app->use(new \Stream\Cache);
$app->use(new \Stream\Request);
$app->use(new \Stream\Session);
$app->use(new \Stream\Acl);

$app->loadConfig();

$app->get('/', function($req) use ($app) {

    $controller = new modules\Home\Controller($req, $app);

    echo $controller->index();

});

// html5mode rewrite
$app->hook('hook.notFound', function($req) use ($app) {
    echo $app->dispatch('/');
});

$app->domain('/user/:action', modules\Users\Controller::class);

$app->rest('/users/:action.json', modules\Users\Controller::class);

$app->domain('/tasks/:action', modules\Tasks\Controller::class);
$app->rest(['/tasks.json', '/tasks/:id.json'], modules\Tasks\Api::class);

$app->domain('/tasks/:action/:id', modules\Tasks\Controller::class);

$app->domain('/dev/:action', modules\Homegrown\Dev\Controller::class);

$app->domain('/contributors/:action', modules\Contributors\Controller::class);
