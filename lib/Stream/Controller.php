<?php
/**
 * Controller base class
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace Stream;

class Controller {

    protected $templates;
    protected $app;
    protected $_redirect = FALSE;

    public function __construct() {
        $this->app = \App::getInstance();
        $this->templates = new \League\Plates\Engine($this->app->template_path);
        $this->templates->addData([
            'authorized' => !empty($_SESSION['uid']),
            'title' => $this->app->title
        ]);
    }

    public function redirect($uri = NULL) {
        if($uri !== NULL) {
            $this->_redirect = $uri;
            return FALSE;
        }
        return $this->_redirect;
    }

}
