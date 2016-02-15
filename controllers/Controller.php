<?php
/**
 * Controller base class
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
class Controller {

    protected $templates;
    protected $app;

    public function __construct() {
        $this->app = App::getInstance();
        $this->templates = new League\Plates\Engine($this->app->template_path);
    }

    protected function redirect($uri) {
        header('Location: '.$uri);
        exit;
    }

}
