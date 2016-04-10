<?php

namespace Stream;

class Session {

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    }

    public function set($key, $val) {
        $_SESSION[$key] = $val;
    }

}