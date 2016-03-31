<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \League\Plates\Engine;

class PageController extends Controller {

    protected $templates;

    protected $_injectable = [];

    public function __construct() {
    
        parent::__construct();
    
        $this->setupTemplate();
    
    }

    public function setupTemplate() {
        
        $this->templates = new Engine($this->app->template_path);
        
        $this->templates->addData([
            
            'authorized' => !empty($_SESSION['uid']),
            
            'title' => $this->app->title,
            
            'scripts' => [],
            
            'stylesheets' => [
                "/components/bootstrap/dist/css/bootstrap.min.css"
            ]
        ]);
    
    }
}
