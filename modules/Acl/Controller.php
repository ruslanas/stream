<?php

namespace modules\Acl;

use \Stream\Interfaces\RestApi;

class Controller extends \Stream\PageController implements RestApi {
    
    /** Declare final to make accessible */
    final public function get() {}
    public function post() {}
    public function delete() {}

}
