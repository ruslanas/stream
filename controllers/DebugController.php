<?php

class DebugController extends Controller implements DomainControllerInterface {
    public function __construct() {
        parent::__construct();
        $this->templates->addFolder('debug', 'templates/debug');
    }

    public function dispatch($uri) {
        $action = explode('/', $uri);
        if(sizeof($action) < 3) {
            $this->info();
            return;
        }
        $this->$action[2]();
    }

    public function cache() {
        echo $this->templates->render('debug::apc', [
            'title' => $this->app->title.'__APC',
            'data' => apc_cache_info()
        ]);
    }

    public function opcache() {
        echo $this->templates->render('debug::opcache', [
            'title' => $this->app->title.'__OpCache',
            'data' => opcache_get_status()
        ]);
    }

    public function info() {
        echo $this->templates->render('debug::phpinfo', [
            'title' => $this->app->title.'__PHP_INFO'
        ]);
    }
}
