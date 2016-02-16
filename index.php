<?php

/**
 * Main program
 * Configure application and setup request handlers
 * Application logic is implemented in controllers
 * Controllers use models to manipulate data and templates to present it to user
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

$app = new App([
    'template_path' => 'templates',
    'title' => 'Stream',
    'cache_ttl' => '600'
]);

$ctrl = new MainController();

$app->get('/^\/(\?.*)*$/', function($req) use ($ctrl) {
    $ctrl->home();
});

$app->rest('/^\/posts(\/[0-9]+)*\.json$/', 'RestController');
$app->domain('/debug', 'DebugController');

$app->get('/^\/posts\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->view($req[1]);
});

$app->get('/^\/edit\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->edit($req[1]);
});

$app->post('/^\/edit\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->save($req[1], $_POST);
});

$app->get('/^\/tasks\/add$/', function($req) use ($ctrl) {
    $ctrl->displayForm();
});

$app->post('/^\/tasks\/add$/', function($req) use ($ctrl) {
    $ctrl->save(NULL, $_POST);
});
