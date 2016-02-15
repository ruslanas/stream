<?php

class RestController extends Controller implements RestApi {

    public $params = [];

    public function __construct() {
        parent::__construct();
        $this->model = new Stream();
    }

    public function get() {
        if(sizeof($this->params) == 2) {
            $id = ltrim($this->params[1], '/');
            $data = $this->model->getById($id);
            echo json_encode($data);
        } else {
            $data = $this->model->getList();
            echo json_encode($data);
        }
    }

    public function delete() {

        if(sizeof($this->params) < 2) {
            throw new Exception("Too few parameters");
        }

        $id = ltrim($this->params[1], '/');
        $item = $this->model->getById($id);
        $this->model->delete($id);
        echo json_encode($item);
    }

    public function post() {
        $raw_post_data = file_get_contents('php://input');
        if(sizeof($this->params) < 2) {
            $id = NULL;
        } else {
            $id = ltrim($this->params[1], '/');
        }
        $this->model->save($id, json_decode($raw_post_data, true));
        echo json_encode($this->model->getById($id));
    }
}
