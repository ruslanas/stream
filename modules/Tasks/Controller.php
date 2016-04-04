<?php

namespace modules\Tasks;

class Controller extends \Stream\PageController {

    protected $_injectable = ['task', 'request', 'params'];

    public function __construct($params = NULL, $app = NULL) {

        parent::__construct($params, $app);

        $this->task = new model\Task(\Stream\App::getConnection());

        $this->templates->addFolder('task', __DIR__.DIRECTORY_SEPARATOR.'templates');
        $this->templates->addData([
            'scripts' => array_merge($this->_scripts, ['/js/tasks.js'])
        ]);
    
    }

    final public function open() {
        return $this->templates->render('task::index', [
            'data' => $this->task->read()
        ]);
    }

    final public function list() {
        return $this->templates->render('task::list', [
            'data' => $this->task->read()
        ]);
    }

    final public function save() {
        
        $data = $this->request->post();
        
        if(empty($data['id'])) {
            $res = $this->task->create($data);
            return $this->redirect('/tasks/edit/'.$res->id);
        } else {
            $res = $this->task->update($data['id'], $data);
            return $this->redirect('/tasks/open');
        }

    
    }

    final public function edit() {
        
        if(empty($this->params['id'])) {
            throw new \Exception;
        }
        
        $id = $this->params['id'];
        $list = $this->task->read();
        $data = $this->task->read($id);
        
        return $this->templates->render('task::edit', [
            'data' => $data,
            'list' => $list
        ]);
    }

}
