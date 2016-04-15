<?php

namespace modules\Tasks;

class Api extends \Stream\RestController {

    protected $_injectable = ['request', 'model', 'params', 'session'];

    public function __construct($params = NULL, $app = NULL) {

        parent::__construct($params, $app);

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

        $out = $this->model->delete($this->params['id'], $this->Session->get('uid'));
        if($out === NULL) {
            throw new \Exception('Not deleted');
        }

        return $out;

    }

    final public function get() {

        return $this->model->filter(

            ['and',
                ['or',
                    ['user_id', $this->Session->get('uid')],
                    ['and',
                        ['delegate_id', $this->Session->get('uid')],
                        ['accepted', "1"]
                    ]
                ],
                ['tasks.deleted', 0]
            ]);

    }

    private function delegate($id, $email) {
        
        $task = (new Decorators\Task($this->app->pdo))->read($id);
        $task->delegate($email)->update(NULL, ['focus' => 0]);

        return $task->current();
    
    }

    final public function post() {

        if($this->Request->getGet('action') === 'delegate') {
            $email = $this->Request->getGet('email');
            return $this->delegate($this->param('id'), $email);
        }

        $uid = $this->Session->get('uid');

        $data = $this->Request->getPostData();
        

        if($this->param('id') !== NULL) {

            unset($data['user_id']); // cannot be changed

            $get = $this->Request->getGet();
            
            if(is_array($get)) {
                $data = array_merge($data, $get);
            }

            $out = $this->model->update($this->param('id'), $data);

        } else {

            $data['user_id'] = $uid;
            
            $out = $this->model->create($data);
        
        }

        return $out;
    
    }
}
