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

    protected $params = [];
    protected $_injectable = ['params', 'request', 'model'];

    public function __construct($params = NULL, \Stream\App $app = NULL) {

        parent::__construct($params, $app);

        $this->model = new Post(\Stream\App::getConnection());

    }

    final public function get() {

        if(isset($this->params['id'])) {

            $id = $this->params['id'];

            $data = $this->model->getById($id);

        } else {

            $data = $this->model->read();

        }

        return $data;

    }

    final public function delete() {

        if(!isset($this->params['id'])) {
            throw new Exception("Too few parameters");
        }

        $id = $this->params['id'];

        return $this->model->delete($id);

    }

    final public function post() {
        $data = $this->request->getPostData();
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        return $this->model->save($id, $data);
    }
}
