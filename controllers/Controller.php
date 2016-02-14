<?php

class Controller {

    protected $templates;
    private $app;

    public function __construct() {
        $this->app = App::getInstance();
        $this->templates = new League\Plates\Engine($this->app->template_path);
    }

    public function redirect($uri) {
        header('Location: '.$uri);
        exit;
    }

}
