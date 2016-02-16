<?php
interface CacheInterface {
    public function fetch($key);
    public function delete($key);
    public function store($key, $val, $ttl);
    public function status();
}
