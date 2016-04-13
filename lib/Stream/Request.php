<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

class Request {
    
    public function post() {

        if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        } else {
            return $_POST;
        }

    }

    public function getPostData($key = NULL) {
        $raw_post_data = file_get_contents('php://input');
        $data = json_decode($raw_post_data, true);

        if(isset($key)) { return isset($data[$key]) ? $data[$key] : NULL; }
    
        return $data;
    }

    public function getHeaders() {
        return getallheaders();
    }

    public function getMethod() {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : NULL;
    }

    public function getGet($key = NULL) {
        if($key === NULL) { return $_GET; }
        if(isset($_GET[$key])) { return $_GET[$key]; }
        return NULL;
    }
}
