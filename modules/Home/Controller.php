<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace modules\Home;

use modules\Posts\model\Post;
use Stream\PageController;

class Controller extends PageController {

    private $model;

    public function __construct($params = NULL, $app = NULL) {

        parent::__construct($params, $app);

        $this->templates->addFolder('stream', __DIR__.DIRECTORY_SEPARATOR.'templates');

        $this->model = new Post(\Stream\App::getConnection());

        $this->_scripts = array_merge($this->_scripts, [

            "/js/app.js",
            
            "/js/stream.js",
            "/js/client.js",
            
            "/js/directives/data-grid.js"
        
        ]);

        $this->templates->addData([
            
            'scripts' => $this->_scripts

        ]);
    }

    final public function index() {
        
        return $this->templates->render('stream::index');

    }

}
