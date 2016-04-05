<?php

namespace modules\Tasks;

class Api extends \Stream\RestController {

    protected $_injectable = ['request', 'model', 'params'];

    public function __construct($params = NULL, $app = NULL) {
        parent::__construct($params, $app);
        $this->model = new model\Task(\Stream\App::getConnection());
    }

    final public function delete() {
        
        if(empty($this->params['id'])) {
            throw new Exception;
        }

        return $this->model->delete($this->params['id']);
    }

    final public function get() {

        return $this->model->read();

    }

    final public function post() {
        
        if($this->param('id') !== NULL) {
            
            $data = $this->request->getPostData();
            $data = array_merge($data, $this->request->getGet());

            return $this->model->update($this->param('id'), $data);
        
        } else {
            return $this->model->create($this->request->getPostData());
        }
    }
}
