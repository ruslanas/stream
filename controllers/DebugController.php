<?php

class DebugController extends Controller {
    public function printHeaders() {
        // header('Content-Type: text/plain');
        var_dump(getallheaders());
        var_dump(apc_cache_info());
    }

    public function info() {
        phpinfo();
    }
}
