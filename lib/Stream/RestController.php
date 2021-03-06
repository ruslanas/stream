<?php

namespace Stream;

use \Exception;
use \ReflectionClass;
use \ReflectionMethod;

/** Provides handlers for HTTP requests by request method */
abstract class RestController extends Controller implements Interfaces\RestApi {

	abstract public function get();
	abstract public function post();
	abstract public function delete();

    public function __construct($params = NULL, $app = NULL, $deps = []) {

        parent::__construct($params, $app);

        foreach($deps as $key => $val) {
            $this->inject($key, $val);
        }

    }

    public function __call($method, $args) {

        $class = new ReflectionClass(get_class($this));
        $methods = $class->getMethods(ReflectionMethod::IS_FINAL);

        $allowed = [];

        array_walk($methods, function($meth) use (&$allowed) {
            $allowed[] = strtoupper($meth->name);
        });

        // expect sorted in alphabetical order
        sort($allowed);

        throw new \Stream\Exception\UnknownMethodException('Allow: '.join(', ', $allowed));

    }

}
