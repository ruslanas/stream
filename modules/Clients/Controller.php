<?php

namespace modules\Clients;

use \Stream\Exception\NotFoundException;
use \Stream\RestController;

class Controller extends RestController {

    public function __construct($params, $request) {
        parent::__construct();
        $this->params = $params;
        $this->request = $request;
        $this->model = new model\Client($this->app->pdo);
    }

    final public function get() {
        if(isset($this->params['id'])) {
            $out = $this->model->getById($this->params['id']);
            if($out === FALSE) {
                throw new NotFoundException("Not found");
            }
            return $out;
        } else {
            return $this->model->getList();
        }
    }

    final public function post() {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $data = $this->request->getPostData();
        return $this->model->save($id, $data);
    }

    final public function delete() {
        $this->model->delete($this->params['id']);
        return $this->model->getById($this->params['id'], TRUE);
    }
}
