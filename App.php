<?php

class NotFoundException extends Exception { }

class App {

    private $get_handlers = [];
    private $post_handlers = [];
    private $delete_handlers = [];
    private $put_handlers = [];
    private $cache;

    private $config = [
        'cache_ttl' => 60
    ];

    public function __construct($config = []) {
        $this->config = array_merge($this->config, $config);
        $this->cache = new Cache();
    }

    public function dispatch($uri) {

        $method = $_SERVER['REQUEST_METHOD'];

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
                        $this->cache->store($uri, $buffer, $this->config['cache_ttl']);
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

    // public function __call($method, $arguments) {
    //     if($method == 'get') {
    //         $this->get_handlers[$arguments[0]] = $arguments[1];
    //     }
    // }

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

}
