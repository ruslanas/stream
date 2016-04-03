<?php

namespace modules\Tasks;

class Controller extends \Stream\PageController {

    protected $_injectable = ['task', 'request'];

    public function __construct($params = NULL, $app = NULL) {

        parent::__construct($params, $app);

        $this->task = new model\Task(\Stream\App::getConnection());

        $this->templates->addFolder('task', __DIR__.DIRECTORY_SEPARATOR.'templates');
    
    }

    final public function open() {
        return $this->templates->render('task::index');
    }

    final public function save() {
        
        $data = $this->request->post();
        $res = $this->task->create($data);

        return $this->redirect('/tasks/edit/'.$res->id);
    
    }

    final public function edit() {
        return $this->templates->render('task::edit');
    }

}
