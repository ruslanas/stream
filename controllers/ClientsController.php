<?php

use Stream\Exception\NotFoundException;
use Stream\Interfaces\RestApi;

class ClientsController extends Stream\RestController {

	public function __construct($params, $request) {
		parent::__construct();
        $this->params = $params;
        $this->request = $request;
		$this->model = new Client($this->app->pdo);
	}

	final public function get() {
		if(isset($this->params['id'])) {
			$out = $this->model->getById($this->params['id']);
			if($out === FALSE) {
				throw new NotFoundException("Not found");
			}
			return json_encode($out);
		} else {
			return json_encode($this->model->getList());
		}
	}

	final public function post() {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $data = $this->request->getPostData();
        $id = $this->model->save($id, $data);
        return json_encode($this->model->getById($id));
	}

	final public function delete() {
		$this->model->delete($this->params['id']);
		return json_encode($this->model->getById($this->params['id'], TRUE));
	}
}
