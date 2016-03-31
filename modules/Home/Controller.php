<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace modules\Home;

use modules\Posts\model\Post;
use Stream\PageController;

class Controller extends PageController {

    private $model;

    public function __construct() {

        parent::__construct();

        $this->templates->addFolder('stream', 'modules/Home/templates');
        $this->model = new Post($this->app->pdo);

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

    public function default() {
        
        // $data = $this->model->getList();
        
        return $this->templates->render('stream::index');
    }

}
