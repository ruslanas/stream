<?php

namespace modules\Contributors;

use \Stream\Interfaces\RestApi;

class Controller extends \Stream\PageController implements RestApi {
    
    /** Declare final to make accessible */
    public function get() {}
    public function post() {}
    public function delete() {}

    public function __construct($params = NULL, $app = NULL) {
        parent::__construct($params, $app);
        $this->templates->addFolder('contributors', __DIR__.DIRECTORY_SEPARATOR.'templates');
    }

    final public function open() {
        return $this->templates->render('contributors::index');
    }

}
