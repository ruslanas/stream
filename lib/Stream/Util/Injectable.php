<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream\Util;

class Injectable {

    /** @var array Should contain injectable properties */
    protected $_injectable = [];

    static protected $_deps = [];
    static protected $_services = [];

    /**
     * Injects dependencies
     * @param string $property protected class memeber name
     * @param mixed $object
     */
    public function inject($property, $object) {

        if(in_array($property, $this->_injectable)) {
            $this->{$property} = $object;
        } else {
            throw new \Exception("Property `{$property}` not injectable");
        }

    }

    /**
     * Register or create service
     *
     * <pre>
     * <code>
     * $injectable->service('ServiceName', function($arg1, ...) { }); // factory
     * $injectable->service('ServiceName', $arg1, ...); // inits $this->ServiceName
     * </code>
     * </pre>
     */
    public function service() {
        
        $args = func_get_args();

        if(count($args) < 1 || !is_string($args[0])) {
            throw new \Exception;
        }

        if(!is_callable($args[1]) || count($args) < 2) {
            $func = self::$_services[$args[0]];
            $this->uses([$args[0], $func(array_slice($args, 1))]);
            return ;
        }

        self::$_services[$args[0]] = function($arguments) use ($args) {
            return call_user_func_array($args[1], $arguments);
        };
    
    }

    public function uses() {

        $deps = func_get_args();
    
        foreach($deps as $dep) {
        
            if(is_string($dep)) {
                $this->{$dep} = self::$_deps[$dep];
                continue;
            }

            if(is_array($dep)) {
        
                if(empty($dep[1] || !is_object($dep[1]))) {

                    throw new Exception;

                }

                $name = $dep[0];
                
                $dep = $dep[1];
        
            } else {
        
                $path = explode('\\', get_class($dep));
                $name = end($path);
        
            }
        
            $this->{$name} = $dep;
            self::$_deps[$name] = $dep;

        }

    }

}
