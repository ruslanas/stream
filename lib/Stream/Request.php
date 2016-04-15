<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

class Request {
    
    public function post() {

        return filter_input_array(INPUT_POST);
        
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
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }

    public function getGet($key = NULL) {
        if($key === NULL) { return filter_input_array(INPUT_GET); }
        return filter_input(INPUT_GET, $key);
    }
    
    public function getUri() {
        return filter_input(INPUT_SERVER, 'REQUEST_URI');
    }
}
