<?php
namespace Stream;

class Request {
    public function post() {
        if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        } else {
            return $_POST;
        }
    }
    public function getPostData() {
        $raw_post_data = file_get_contents('php://input');
        return json_decode($raw_post_data, true);
    }
}