<?php
class RedisCache implements CacheInterface {
    public function __construct() {
        $this->redis = new Predis\Client();
    }

    public function delete($uri) {
        $this->redis->del($uri);
    }

    public function store($key, $data, $ttl) {
        $this->redis->set($key, $data);
        $this->redis->expire($key, $ttl);
    }

    public function fetch($uri) {
        return $this->redis->get($uri);
    }

    public function status() {
        return $this->redis->keys('*');
    }
}
