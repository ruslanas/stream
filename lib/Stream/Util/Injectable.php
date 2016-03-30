<?php

namespace Stream\Util;

class Injectable {

    public function inject($property, $object) {

        if(in_array($property, $this->_injectable)) {
            $this->{$property} = $object;
        }

    }

}
