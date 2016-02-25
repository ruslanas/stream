<?php

class RestController extends Controller implements RestApi {

    public $params = [];

    public function __construct() {
        parent::__construct();
        $this->model = new Stream();
    }

    public function get() {
        if(isset($this->params['id'])) {
            $id = $this->params['id'];
            $data = $this->model->getById($id);
            echo json_encode($data);
        } else {
            $data = $this->model->getList();
            echo json_encode($data);
        }
    }

    public function delete() {

        if(!isset($this->params['id'])) {
            throw new Exception("Too few parameters");
        }

        $id = $this->params['id'];
        $item = $this->model->getById($id);
        $this->model->delete($id);
        echo json_encode($item);
    }

    public function post() {
        $raw_post_data = file_get_contents('php://input');
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $this->model->save($id, json_decode($raw_post_data, true));
        echo json_encode($this->model->getById($id));
    }
}
