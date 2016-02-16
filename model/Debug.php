<?php
class Debug {
    public function catch_info() {
        return App::getInstance()->cache_status();
    }
}
