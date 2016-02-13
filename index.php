<?php

$config = ["template_path" => 'templates'];
$app = new App($config);

$ctrl = new MainController();

$app->get('/^\/(\?.*)*$/', function($req) use ($ctrl) {
    $ctrl->home();
});

$app->get('/^\/posts\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->view($req[1]);
});

$app->get('/^\/edit\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->edit($req[1]);
});

$app->post('/^\/edit\/([0-9]+)$/', function($req) use ($ctrl) {
    $ctrl->save($req[1], $_POST);
});

$app->get('/^\/posts.json$/', function($req) use ($ctrl) {
    $ctrl->showAll();
});

$app->delete('/^\/posts\/([0-9]+)\.json$/', function($req) use ($ctrl) {
    $ctrl->delete($req[1]);
});
$app->post('/^\/posts\/([0-9]+)\.json$/', function($req) use ($ctrl) {
    $raw_post_data = file_get_contents('php://input');
    $ctrl->save($req[1], json_decode($raw_post_data, true), true);
});

$app->get('/^\/debug$/', function($req) {
    $ctrl = new DebugController();
    $ctrl->info();
});

$app->get('/^\/debug\/cache/', function($req) {
    $ctrl = new DebugController();
    $ctrl->printHeaders();
});

$app->get('/^\/tasks\/add$/', function($req) use ($ctrl) {
    $ctrl->displayForm();
});

$app->post('/^\/tasks\/add$/', function($req) use ($ctrl) {
    $ctrl->save(NULL, $_POST);
});
