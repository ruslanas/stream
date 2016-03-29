<?php

namespace Stream;

abstract class RestController extends Controller implements Interfaces\RestApi {

	abstract public function get();
	abstract public function post();
	abstract public function delete();

    public function __call($method, $args) {
        $class = new \ReflectionClass(get_class($this));
        $methods = $class->getMethods(\ReflectionMethod::IS_FINAL);
        $allowed = [];
        array_walk($methods, function($meth) use (&$allowed) {
            $allowed[] = strtoupper($meth->name);
        });
        // expect sorted in alphabetical order
        sort($allowed);
        throw new Exception\UnknownMethodException('Allow: '.join(', ', $allowed));
    }

}
