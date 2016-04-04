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
    protected $params = [];

    protected $_scripts = [
        "/components/angular/angular.min.js",
        "/components/angular-resource/angular-resource.min.js",
        "/components/angular-route/angular-route.min.js",
        "/components/angular-bootstrap/ui-bootstrap-tpls.min.js",
    ];

    public function __construct($params = [], $app = NULL) {
    
        parent::__construct($params, $app);
    
        $this->setupTemplate();
    
    }

    /**
     * @param string $uri
     * @throws \Stream\Exception\NotFoundException
     */
    public function dispatch($uri = NULL) {

        $action = !empty($this->params['action']) ? $this->params['action'] : 'index';

        if(method_exists($this, $action)) {

            $reflection = new ReflectionMethod($this, $action);
            if($reflection->isFinal()) {
                return $this->{$action}();
            }
        
        }

        throw new NotFoundException("Page `$uri` not found [$action]");
        
    }

    protected function setupTemplate() {
        
        $this->templates = new Engine(isset($this->app->template_path) ? $this->app->template_path : 'templates');
        
        $this->templates->addData([
            
            'authorized' => !empty($_SESSION['uid']),
            
            'title' => isset($this->app->title) ? $this->app->title : 'Stream',
            
            'scripts' => $this->_scripts,
            
            'stylesheets' => [
                "/components/bootstrap/dist/css/bootstrap.min.css",
                "/css/styles.css"
            ]
        ]);
    
    }
}
