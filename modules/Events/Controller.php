<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Events;

use \Stream\RestController;

class Controller extends RestController {
    
    protected $event;
    protected $_injectable = ['params', 'request', 'event'];

    public function __construct($params, $request) {
        
        parent::__construct();

        $this->params = $params;
        $this->request = $request;

        $this->event = new model\Event($this->app->pdo);
    
    }

    final public function get() {
        return $this->event->read();
    }
    
    final public function post() {
        return $this->event->create($this->request->getPostData());
    }
    
    final public function delete() {
        return $this->event->delete($this->params['id']);
    }

    final public function put() {
        return $this->event->update($this->params['id'], $this->request->getPostData());
    }
}
