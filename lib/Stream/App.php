<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @link http://github.com/ruslanas/stream
 */

namespace Stream;

use \PDO;
use \Exception;

use \Closure;

use \Stream\Request;

use \Stream\Exception\ForbiddenException;
use \Stream\Exception\NotFoundException;
use \Stream\Exception\UnknownMethodException;

use \Stream\Interfaces\AppInterface;
use \Stream\Interfaces\CacheInterface;
use \Stream\Interfaces\RestApi;
use \Stream\Interfaces\DomainControllerInterface;

use \Stream\Util\Injectable;

/** Application dispatches requests to controllers and manages database connection */
class App extends Injectable implements AppInterface {

    protected $_injectable = ['acl', 'request', 'cache', '_config'];

    private $_controllers = [];
    private $_domains = [];
    private $_connections = [];

    private $_hooks = [];

    private $get_handlers = [];
    private $post_handlers = [];
    private $delete_handlers = [];
    private $put_handlers = [];

    protected $cache;

    protected static $instance = null; // shared instance

    protected $_config = [
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

    /**
     * Load database configuration from /config.php and create PDO
     * @param string $conf
     * @return \PDO
     */
    static public function getConnection($conf = NULL) {

        $app = self::getInstance();

        $app->loadConfig();

        // use last connection
        if(($app->pdo instanceof \PDO) && $conf === NULL) {
            return $app->pdo;
        }

        return $app->connect($conf);

    }

    public static function deleteInstance() {
        static::$instance = NULL;
    }

    public function __construct(CacheInterface $cache = NULL) {

        $this->session = new \Stream\Session;
        $this->acl = new Acl($this->session);
        $this->request = new Request;

        $this->cache = ($cache !== NULL) ? $cache : new Cache;

        static::$instance = $this;
    }

    public function __get($name) {
        if(isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        return null;
    }

    public function connect($conf = NULL) {

        $conf = ($conf === NULL) ? 'default' : $conf;

        if(!empty($this->_connections[$conf])) {
            return $this->_connections[$conf];
        }

        if($conf === 'default') {
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
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        $this->_connections[$conf] = $pdo;

        $this->pdo = $pdo;

        return $pdo;

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

        $method = $this->request->getMethod();

        if(!$this->authorize($method, $uri)) {
            throw new ForbiddenException("Not allowed");
        }

        // try to match ReST controller first
        $controller = $this->createController($method, $uri);

        if($controller !== NULL) {

            if(method_exists($controller, $method)) {

                $reflection = new \ReflectionMethod($controller, $method);

                if($reflection->isFinal()) {
                    $out = $controller->{$method}();
                } else {

                    // unsupported HTTP request method or corresponding controller
                    // method is not declared final
                    throw new \Stream\Exception\UnknownMethodException("Hacker?");

                }

            } else {
                throw new NotFoundException('Page `$uri` not found');
            }

            if($controller->redirect()) {

                header('Location: '.$controller->redirect());

            }

            return $this->serialize($out);

        }

        $headers = $this->request->getHeaders();

        $revalidate = (!empty($headers['Cache-Control'])
            && strpos($headers['Cache-Control'], 'max-age=0') !== FALSE) ? true : false;

        $cached = $this->cache->fetch($uri);

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

            try {
                $out = $controller->dispatch($uri);
            } catch(NotFoundException $e) {
                ob_end_clean();
                throw $e;
            }

            if($controller->redirect()) {

                header('Location: '.$controller->redirect());

                ob_end_clean();

                return;

            }

            ob_end_flush();

            return $out;

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

                $params = $this->match($regexp, $uri);

                if(is_array($params)) {
                    $handler = $func;
                    break;
                }
            }

            if($handler === null) {

                ob_end_clean();

                if(is_callable($this->hook('hook.notFound'))) {
                    // php < 7 complains
                    $_f = $this->hook('hook.notFound');
                    return $_f($uri);
                } else {

                    throw new NotFoundException("Could not ".$method.' '.$uri);

                }
            }

            if(is_callable($handler)) {
                $handler($params);
            }

        }

        ob_end_flush();

    }

    protected function match($parameterized, $uri) {

        $components = explode('?', $uri);
        $path = rtrim($components[0], '/');

        if(empty($path) && $parameterized == '/') {
            return [];
        }

        $re = preg_replace('/\:\w*/', '(\w*)', $parameterized);

        $re = '[^'.$re.'$]';

        $count = preg_match_all($re, $path, $matches);

        if($count > 0) {

            preg_match_all('/\:(\w*)/', $parameterized, $parms);

            $out = [];

            for($i=1; $i<count($matches); $i++) {
                $out[$parms[1][$i-1]] = $matches[$i][0];
            }

            return $out;
        }

        return FALSE;
    }

    protected function createController($method, $uri) {

        foreach($this->_controllers as $regexp => $controller_class) {

            $matches = $this->match($regexp, $uri);

            if($matches !== FALSE) {
                if(class_exists($controller_class)) {

                    $implements = class_implements($controller_class);

                    if(!in_array(RestApi::class, $implements)) {

                        throw new Exception("Controller `{$controller_class}` must implement RestApi");

                    }

                    return new $controller_class($matches, $this);

                } else {
                    throw new NotFoundException;
                }
            }
        }

        return NULL;
    }

    protected function createDomainController($uri) {

        foreach($this->_domains as $name => $controller_class) {

            $matches = $this->match($name, $uri);

            if($matches !== false) {

                if(class_exists($controller_class)) {
                    return new $controller_class($matches, $this);
                } else {
                    throw new \Stream\Exception\NotFoundException("Page controller not found");
                }

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

    /**
     * Map parameterized URI(s) to controller
     * @param string|array $endpoints
     * @param string $controller
     * @throws \Exception if $controller is not a string
     */
    public function rest($endpoints, $controller) {

        if(!is_string($controller)) {
            throw new \Exception('App configuration error');
        }

        if(!is_array($endpoints)) {
            $endpoints = [$endpoints];
        }

        foreach($endpoints as $ep) {
            $this->_controllers[$ep] = $controller;
        }

    }

    public function domain($name, $controller) {
        $this->_domains[$name] = $controller;
    }

    public function loadConfig() {

        require 'config.php';

        $this->_config = array_merge($this->_config, $config);
        return $this->_config;
    }

    public function serialize($data) {
        return json_encode($data);
    }

    public function hook($hook, callable $func = NULL) {
        if($func == NULL) {
            return isset($this->_hooks[$hook]) ? $this->_hooks[$hook] : NULL;
        }
        $this->_hooks[$hook] = $func;
    }

}
