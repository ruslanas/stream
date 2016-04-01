<?php

namespace Stream\Test;

use \Stream\RestController;
use \Stream\Interfaces\DomainControllerInterface;

class DummyController extends RestController implements DomainControllerInterface {
    
    private $data;
    protected $params;

    public function __construct($params, $req) {
        $this->params = $params;
        $this->data = $req->getPostData();
    }

    public function dispatch($uri) {
        return $this->{$this->params['action']}();
    }

    final public function index() {
        return '<!DOCTYPE html>';
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
