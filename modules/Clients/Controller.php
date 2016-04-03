<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Clients;

use \Stream\Exception\NotFoundException;
use \Stream\RestController;

class Controller extends RestController {

    protected $_injectable = ['params', 'request','model'];

    public function __construct($params = NULL, \Stream\App $app = NULL) {
        
        parent::__construct($params, $app);
        
        $this->model = new model\Client(\Stream\App::getConnection());

    }

    final public function get() {
        if(isset($this->params['id'])) {
            $out = $this->model->getById($this->params['id']);
            if($out === FALSE) {
                throw new NotFoundException("Client record not found");
            }
            return $out;
        } else {
            return $this->model->getList();
        }
    }

    final public function post() {

        $id = isset($this->params['id']) ? $this->params['id'] : NULL;
        return $this->model->save($id, $this->request->getPostData());

    }

    final public function delete() {
        return $this->model->delete($this->params['id']);
    }
}
