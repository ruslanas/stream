<?php

namespace modules\Tasks;

class Api extends \Stream\RestController {

    protected $_injectable = ['request', 'model', 'params', 'session'];

    public function __construct($params = NULL, $app = NULL) {

        $deps = [];

        if($app !== NULL) {
            $deps = ['session' => $app->session];
        }

        parent::__construct($params, $app, $deps);

        $this->model = new model\Task(\Stream\App::getConnection());

    }

    /**
     * Delete Task
     * @return \stdClass Deleted task object
     * @throws \Exception
     */
    final public function delete() {

        if(empty($this->params['id'])) {
            throw new \Exception('Insufficient argument');
        }

        $out = $this->model->delete($this->params['id'], $this->session->get('uid'));
        if($out === NULL) {
            throw new \Exception('Not deleted');
        }

        return $out;

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
