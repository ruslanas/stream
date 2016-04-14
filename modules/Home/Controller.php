<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace modules\Home;

use modules\Posts\model\Post;
use Stream\PageController;

class Controller extends PageController {

    public function __construct($params = NULL, $app = NULL) {

        $this->_stylesheets[] = 'https://fonts.googleapis.com/css?family=Open+Sans';
        $this->_stylesheets[] = '/css/styles.css';
        
        $this->_scripts = array_merge($this->_scripts, [

            '/components/angular-sanitize/angular-sanitize.min.js',
            '/components/wiz-markdown/wizMarkdown/wizMarkdown.js',

            "/js/app.js",

            '/js/tasks.js',
            '/js/login.js',

        ]);

        parent::__construct($params, $app);

        $this->templates->addFolder('stream', __DIR__.DIRECTORY_SEPARATOR.'templates');

    }

    final public function index() {

        return $this->templates->render('stream::index');

    }

}
