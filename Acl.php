<?php
/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
class Acl {

    // this method is a stub
    public function allow($method, $uri) {
        if(!empty($_SESSION['uid']) && !in_array($method, ["DELETE"])) {
            return true;
        }

        $components = explode('/', ltrim($uri, '/'));
        $action = isset($components[1]) ? $components[1] : '__get_only__';
        if(in_array($action, ["login", "add"]) || $method === 'GET') {
            return true;
        }
        return false;
    }
}
