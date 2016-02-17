<?php
class Request {
    public function post() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        } else {
            return $_POST;
        }
    }
}
