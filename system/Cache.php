<?php
/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */
class Cache implements CacheInterface {

    public function delete($uri) {
        return false;
    }

    public function store($key, $data, $ttl) {
        return false;
    }

    public function fetch($uri) {
        return false;
    }

    public function status() {
        return false;
    }
}
