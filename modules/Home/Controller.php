<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace modules\Home;

use modules\Posts\model\Post;

class Controller extends \Stream\Controller {

    private $model;

    public function __construct() {
        parent::__construct();
        $this->templates->addFolder('stream', 'modules/Home/templates');
        $this->model = new Post($this->app->pdo);
    }

    public function default() {
        $data = $this->model->getList();
        echo $this->templates->render('stream::index', [
            'data' => $data
        ]);
    }

}
