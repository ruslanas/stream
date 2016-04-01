<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \ReflectionMethod;

use \League\Plates\Engine;
use \Stream\Interfaces\DomainControllerInterface;
use \Stream\Exception\NotFoundException;

class PageController extends Controller implements DomainControllerInterface {

    protected $templates;

    protected $_injectable = [];

    public function __construct() {
    
        parent::__construct();
    
        $this->setupTemplate();
    
    }

    /**
     * @param string $uri
     * @throws \Stream\Exception\NotFoundException
     */
    public function dispatch($uri = NULL) {

        $action = !empty($this->params['action']) ? $this->params['action'] : 'index';

        //////////////////////////////////////////////////////////////////////////////
        // [Fatal error] Maximum function nesting level of '256' reached, aborting! //
        //////////////////////////////////////////////////////////////////////////////

        if(method_exists($this, $action) && $action != 'dispatch') {

            $reflection = new ReflectionMethod($this, $action);
            if($reflection->isPublic() && !$reflection->isConstructor()) {
                return $this->{$action}();
            }
        
        }

        throw new NotFoundException("Page `$uri` not found");
        
    }

    protected function setupTemplate() {
        
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
