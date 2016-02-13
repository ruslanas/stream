<?php
class Cache {
    public function delete($uri) {
        apc_delete($uri);
    }
    public function store($key, $data, $ttl) {
        apc_store($key, $data, $ttl);
    }
    public function fetch($uri) {
        return apc_fetch($uri);
    }
}
