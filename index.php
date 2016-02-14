<?php

$config = ["template_path" => 'templates'];
$app = new App($config);

$ctrl = new MainController();

$app->get('/^\/(\?.*)*$/', function($req) use ($ctrl) {
    $ctrl->home();
});

$app->rest('/^\/posts(\/[0-9]+)*\.json$/', 'RestController');

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

$app->get('/^\/debug$/', function($req) {
    $ctrl = new DebugController();
    $ctrl->info();
});

$app->get('/^\/debug\/cache/', function($req) {
    $ctrl = new DebugController();
    $ctrl->printHeaders();
});
