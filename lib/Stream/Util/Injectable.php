<?php

namespace Stream\Util;

class Injectable {

    /** @var array Should contain injectable properties */
    protected $_injectable = [];

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

    public function use() {

        $deps = func_get_args();
    
        foreach($deps as $dep) {
        
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
        
        }

        $this->{$name} = $dep;
    
    }

}
