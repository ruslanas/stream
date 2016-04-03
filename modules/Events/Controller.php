<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Events;

use \Stream\RestController;

class Controller extends RestController {
    
    protected $event;
    protected $_injectable = ['params', 'request', 'event'];

    public function __construct($params = NULL, \Stream\App $app = NULL) {
        
        parent::__construct($params, $app);

        $this->event = new model\Event(\Stream\App::getConnection());
    
    }

    final public function get() {
        return $this->event->read(isset($this->params['id']) ? $this->params['id'] : NULL);
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
