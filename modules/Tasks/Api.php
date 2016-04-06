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

        return $this->model->read(NULL, isset($_SESSION['uid']) ? $_SESSION['uid'] : NULL);

    }

    final public function post() {
        
        $uid = $_SESSION['uid'];

        $data = $this->request->getPostData();
        $data['user_id'] = $uid;
        
        if($this->param('id') !== NULL) {
            
            $data = array_merge($data, $this->request->getGet());

            return $this->model->update($this->param('id'), $data);
        
        } else {
            return $this->model->create($data);
        }
    }
}
