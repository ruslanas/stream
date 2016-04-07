<?php

namespace Stream;

class Session {

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    }

}