<?php

class Controller {

    protected $templates;

    public function __construct($templates = 'templates') {
        $this->templates = new League\Plates\Engine($templates);
    }

    public function redirect($uri) {
        header('Location: '.$uri);
        exit;
    }

}
