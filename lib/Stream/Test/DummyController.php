<?php

namespace Stream\Test;

use \ReflectionMethod;

use \Stream\RestController;
use \Stream\Interfaces\DomainControllerInterface;
use \Stream\Exception\NotFoundException;

class DummyController extends RestController implements DomainControllerInterface {
    
    private $data;
    protected $params;

    public function __construct($params = NULL, \Stream\App $app = NULL) {

        parent::__construct($params, $app);

        $this->params = $params;
    
    }

    public function dispatch($uri = NULL) {

        $action = !empty($this->params['action']) ? $this->params['action'] : 'index';

        $meth = 'action'.ucfirst($action);

        if(method_exists($this, $meth)) {

            return $this->{$meth}();
        
        }

        throw new NotFoundException("Page `$uri` not found");
        
    }

    public function notStandardRequestMethod() {
        throw new \Exception('Do not call me');
    }

    protected function actionXxx() {
        return 'I am protected';
    }

    public function actionIndex() {
        return '<!DOCTYPE html>';
    }

    final public function get() {
        return 'GET response';
    }

    final public function post() {
        
        $this->data = $this->Request->getPostData();

        $this->data['params'] = $this->params;
        
        return $this->data;
    }

    final public function delete() {
        return "DELETE response";
    }

}
