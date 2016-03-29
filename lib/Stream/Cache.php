<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

class Cache implements Interfaces\CacheInterface {

    private $_data = [];

    public function delete($key) {
        unset($this->_data[$key]);
    }

    public function store($key, $data, $ttl = 600) {
        $this->_data[$key] = $data;
    }

    public function fetch($key) {
        return isset($this->_data[$key]) ? $this->_data[$key] : FALSE;
    }

}
