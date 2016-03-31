<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Posts;

use \Exception;

use \Stream\RestController;
use \Stream\Exception\UnknownMethodException;

use \modules\Posts\model\Post;

class Controller extends RestController {

    private $params = [];
    protected $_injectable = ['params', 'request', 'model'];

    public function __construct($selector, $request) {
        
        parent::__construct();

        $this->params = $selector;
        $this->request = $request;
        
        $this->model = new Post($this->app->pdo);
    }

    final public function get() {
        if(isset($this->params['id'])) {
            $id = $this->params['id'];
            $data = $this->model->getById($id);
        } else {
            $data = $this->model->getList();
        }
        return $data;
    }

    final public function delete() {

        if(!isset($this->params['id'])) {
            throw new Exception("Too few parameters");
        }

        $id = $this->params['id'];
        $item = $this->model->getById($id);
        $this->model->delete($id);
        return $item;
    }

    final public function post() {
        $data = $this->request->getPostData();
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        return $this->model->save($id, $data);
    }
}
