<?php

namespace modules\Tasks;

class Api extends \Stream\RestController {

    final public function delete() {
        
        $m = new model\Task($this->app->pdo);
        
        if(empty($this->params['id'])) {
            throw new Exception;
        }

        return $m->delete($this->params['id']);
    }

    final public function get() {
        $m = new model\Task($this->app->pdo);

        return $m->read();
    }

    public function post() {}
}