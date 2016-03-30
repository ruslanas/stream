<?php

/**
 * Controller base class
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace Stream;

use Stream\Util\Injectable;

class Controller extends Injectable {

    protected $app;
    protected $_redirect = FALSE;

    public function __construct() {

        $this->app = App::getInstance();

    }

    public function redirect($uri = NULL) {
        if($uri !== NULL) {
            $this->_redirect = $uri;
            return FALSE;
        }
        return $this->_redirect;
    }

}
