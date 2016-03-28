<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @link http://github.com/ruslanas/stream/blob/master/App.php
 */

use Stream\Request;

class App implements AppInterface {

    private $_controllers = [];
    private $_domains = [];

    private $get_handlers = [];
    private $post_handlers = [];
    private $delete_handlers = [];
    private $put_handlers = [];

    private $cache;

    private static $instance = null; // shared instance

    private $_config = [
        'cache_ttl' => 60,
        'template_path' => 'templates',
        'title' => 'App'
    ];

    public static function getInstance() {
        if(static::$instance === null) {
            static::$instance = new App();
        }
        return static::$instance;
    }

    public function __construct($config = [], CacheInterface $cache = NULL) {
        $this->_config = array_merge($this->_config, $config);

        $this->acl = new Acl();
        if($cache !== NULL) {
            $this->cache = $cache;
        } else {
            $this->cache = new Cache();
        }
        static::$instance = $this;
    }

    public function __get($name) {
        if(isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        return null;
    }

    public function connect($conf = NULL) {
        if($conf === NULL) {
            $dsn = "mysql:host={$this->_config['host']};dbname={$this->_config['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $this->_config['user'], $this->_config['password']);
        } else {
            
            if(!isset($this->_config[$conf]) || !is_array($this->_config[$conf])) {
                throw new Exception("Configuration for `{$conf}` not found");
            }

            $data = $this->_config[$conf];
            $dsn = "mysql:host={$data['host']};dbname={$data['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['user'], $data['password']);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo = $pdo;
    }

    protected function authorize($method, $uri) {

        if(!$this->acl->allow($method, $uri)) {
            return false;
        }

        return true;
    }

    /**
     * Runs first found registered handler that matches request URI
     *
     * @param string $uri Request URI
     * @throws NotFoundException if handler for request method not registered
     * @throws UnknownMethodException if request method is not allowed
     * @throws ForbiddenException if user is not authorized
     */
    public function dispatch($uri) {

        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : NULL;

        if(!$this->authorize($method, $uri)) {
            throw new ForbiddenException("Not allowed");
        }

        // try to match ReST controller first
        $controller = $this->createController($method, $uri);

        if($controller instanceof RestApi) {
            $out = $controller->{$method}();
            if($controller->redirect()) {
                header('Location: '.$controller->redirect());
            }
            echo $out;
            return;
        }

        $cached = $this->cache->fetch($uri);
        $headers = getallheaders();

        $revalidate = (!empty($headers['Cache-Control'])
            && strpos($headers['Cache-Control'], 'max-age=0') !== FALSE) ? true : false;

        // cached and valid
        if(!$revalidate && $method === 'GET' && !empty($cached)) {
            echo $cached;
            return;
        }

        $controller = $this->createDomainController($uri);

        ob_start(function ($buffer) use ($uri, $method) {
            if($method == 'GET') {
                $this->cache->store($uri, $buffer, $this->cache_ttl);
            }
            return $buffer;
        });

        if($controller instanceof DomainControllerInterface) {
            $out = $controller->dispatch($uri);
            if($controller->redirect()) {
                header('Location: '.$controller->redirect());
                return;
            }
            echo $out;
        } else {

            switch($method) {
                case 'POST':
                    $handlers = &$this->post_handlers;
                    break;
                case 'GET':
                    $handlers = &$this->get_handlers;
                    break;
                case 'DELETE':
                    $handlers = &$this->delete_handlers;
                    break;
                case 'PUT':
                    $handlers = &$this->put_handlers;
                    break;
                default:
                    ob_end_clean();
                    throw new UnknownMethodException($method." not allowed");
            }

            $handler = null;
            foreach($handlers as $regexp => $func) {
                if(preg_match($regexp, $uri, $matches)) {
                    $handler = $func;
                    break;
                }
            }

            if($handler === null) {
                ob_end_clean();
                throw new NotFoundException("Could not ".$method.' '.$uri);
            }

            $handler($matches);

        }

        ob_end_flush();

    }

    protected function match($parameterized, $uri) {
        $re = preg_replace('/\:\w*/', '(\w*)', $parameterized);
        $re = '~'.$re.'~';
        $count = preg_match_all($re, $uri, $matches);
        if($count > 0) {
            preg_match_all('/\:(\w*)/', $parameterized, $parms);
            $out = [];
            for($i=1; $i<sizeof($matches); $i++) {
                $out[$parms[1][$i-1]] = $matches[$i][0];
            }
            return $out;
        }
        return false;
    }

    protected function createController($method, $uri) {
        foreach($this->_controllers as $regexp => $controller_class) {
            $matches = $this->match($regexp, $uri);
            if($matches !== false) {
                $controller = new $controller_class($matches, new Request);
                return $controller;
            }
        }
        return null;
    }

    protected function createDomainController($uri) {
        foreach($this->_domains as $name => $controller_class) {
            if(strpos($uri, $name) === 0) {
                return new $controller_class();
            }
        }
    }

    public function get($regexp, Closure $handler) {
        $this->get_handlers[$regexp] = $handler;
    }

    public function delete($regexp, Closure $handler) {
        $this->delete_handlers[$regexp] = $handler;
    }

    public function post($regexp, Closure $handler) {
        $this->post_handlers[$regexp] = $handler;
    }

    public function put($regexp, Closure $handler) {
        $this->put_handlers[$regexp] = $handler;
    }

    public function rest($endpoints, $controller) {
        $implements = class_implements($controller);
        if(!in_array('RestApi', $implements)) {
            throw new Exception("Controller must implement RestApi");
        }
        foreach($endpoints as $ep) {
            $this->_controllers[$ep] = $controller;
        }
    }

    public function domain($name, $controller) {
        $this->_domains[$name] = $controller;
    }

    public function cache_status() {
        return $this->cache->status();
    }

    public function loadConfig() {
        require 'config.php';
        $this->_config = array_merge($this->_config, $config);
        return $this->_config;
    }

}
