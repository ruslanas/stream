<?php

class NotFoundException extends Exception { }

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @link http://github.com/ruslanas/stream/blob/master/App.php
 */
class App {

    private $_controllers = [];

    private $get_handlers = [];
    private $post_handlers = [];
    private $delete_handlers = [];
    private $put_handlers = [];
    private $cache;

    private static $instance = null; // shared instance

    private $_config = [
        'cache_ttl' => 60,
        'template_path' => 'templates'
    ];

    public static function getInstance() {
        if(static::$instance === null) {
            static::$instance = new App();
        }
        return static::$instance;
    }

    public function __construct($config = []) {
        $this->_config = array_merge($this->_config, $config);
        $this->cache = new Cache();
        static::$instance = $this;
    }

    public function __get($name) {
        if(isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        //throw new Exception("Not configurable `".$name."`");
        return null;
    }

    /**
     * Runs first found registered handler that matches request URI
     *
     * @throws NotFoundException if handler for request method not registered
     */
    public function dispatch($uri) {

        $method = $_SERVER['REQUEST_METHOD'];

        // try to match ReST controller first
        $controller = $this->createController($method, $uri);

        if($controller instanceof RestApi) {
            $controller->$method();
            return;
        }

        $cached = $this->cache->fetch($uri);
        $headers = getallheaders();

        $expired = (!empty($headers['Cache-Control'])
            && strpos($headers['Cache-Control'], 'max-age=0') !== FALSE) ? true : false;

        if(!$expired && $method === 'GET' && !empty($cached)) {
            echo $cached;
            exit;
        }

        $this->cache->delete($uri);

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
                throw new Exception($method." not allowed");
        }

        foreach($handlers as $regexp => $func) {
            if(preg_match($regexp, $uri, $matches)) {

                if($method == 'GET') {
                    ob_start(function ($buffer) use ($uri) {
                        $this->cache->store($uri, $buffer, $this->cache_ttl);
                        return $buffer;
                    });
                }

                $func($matches);

                if($method == 'GET') {
                    ob_end_flush();
                }

                return;
            }
        }

        throw new NotFoundException("Could not ".$method.' '.$uri);

    }

    private function createController($method, $uri) {
        foreach($this->_controllers as $regexp => $controller_class) {
            if(preg_match($regexp, $uri, $matches)) {
                $controller = new $controller_class();
                $controller->params = $matches;
                return $controller;
            }
        }
        return null;
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

    public function rest($regexp, $controller) {
        $this->_controllers[$regexp] = $controller;
    }
}
