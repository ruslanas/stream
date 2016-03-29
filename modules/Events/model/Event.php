<?php

namespace modules\Events\Model;

class Event {
    public function read() {
        return (object)['id' => 1];
    }
    public function create() {
        return (object)['id' => 2];
    }
    public function update() {
        return (object)['id' => 1, 'message' => 'updated'];
    }
    public function delete() {
        return (object)['id' => 1, 'message' => 'updated'];
    }
}
