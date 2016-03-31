<?php

namespace Stream\Test;

use \Stream\RestController;

class DummyController extends RestController {
    
    private $data;
    private $params;

    public function __construct($params, $req) {
        $this->params = $params;
        $this->data = $req->getPostData();
    }

    final public function get() {
        return 'GET response';
    }

    final public function post() {
        $this->data['params'] = $this->params;
        return $this->data;
    }

    final public function delete() {
        return "DELETE response";
    }

}
