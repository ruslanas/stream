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
    protected $request;
    protected $params;
    
    protected $_redirect = FALSE;
    protected $_injectable = ['request', 'params'];

    public function __construct($params = NULL, \Stream\App $app = NULL) {

        $this->params = $params;
        $this->app = $app;

        // can be injected later
        $this->request = isset($app->request) ? $app->request : NULL;
    
    }

    public function redirect($uri = NULL) {
        
        if($uri !== NULL) {
        
            $this->_redirect = $uri;
    
            return FALSE;
        
        }
        
        return $this->_redirect;
    
    }

    /** @param string $param*/
    protected function param($param) {
        return isset($this->params[$param]) ? $this->params[$param] : NULL;
    }

}
