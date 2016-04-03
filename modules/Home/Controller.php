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

        $this->templates->addData([
            
            'scripts' => [

                "/components/angular/angular.min.js",
                "/components/angular-resource/angular-resource.min.js",
                "/components/angular-route/angular-route.min.js",
                "/components/angular-bootstrap/ui-bootstrap-tpls.min.js",

                "/js/app.js",
                
                "/js/stream.js",
                "/js/client.js",
                
                "/js/directives/data-grid.js"
            
            ]

        ]);
    }

    final public function index() {
        
        return $this->templates->render('stream::index');

    }

}
