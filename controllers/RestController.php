<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\Interfaces\RestApi;
use Stream\Exception\UnknownMethodException;

class RestController extends Stream\RestController implements RestApi {

    private $params = [];

    public function __construct($params, $request) {
        parent::__construct();
        $this->params = $params;
        $this->request = $request;
        $this->model = new Stream($this->app->pdo);
    }

    final public function get() {
        if(isset($this->params['id'])) {
            $id = $this->params['id'];
            $data = $this->model->getById($id);
        } else {
            $data = $this->model->getList();
        }
        return json_encode($data);
    }

    final public function delete() {

        if(!isset($this->params['id'])) {
            throw new Exception("Too few parameters");
        }

        $id = $this->params['id'];
        $item = $this->model->getById($id);
        $this->model->delete($id);
        return json_encode($item);
    }

    final public function post() {
        $data = $this->request->getPostData();
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $id = $this->model->save($id, $data);
        return json_encode($this->model->getById($id));
    }
}
