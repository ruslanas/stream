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
        }

    }

}
