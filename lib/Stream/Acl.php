<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \modules\Users\model\User;

/** Manages Acess Control List. Application checks if HTTP method
 * is allowed on particular URL for in session.
 */
class Acl extends \Stream\Util\Injectable {

    protected $_injectable = ['session'];

    public function allow($method, $uri) {

        $uid = $this->Session->get('uid');

        // ingnore query parameters
        $components = explode('?', $uri);
        $uri = $components[0];

        if(strpos($uri, '/tk/') === 0) { return true; }

        if($method === 'GET' && in_array($uri, [
            "/tasks",
            "/register",
            "/login",
            "/logout",
        ])) { return true; }

        if($uid === NULL && $method === 'POST' && in_array($uri, [
            '/users/login.json',
            '/users/register.json',
            '/users/logout.json'
        ])) { return true; }

        if($uid !== NULL) {

            $data = $this->User->getById($uid);

            if($method === 'DELETE') {

                // we promise that user will delete only his own tasks

                if(preg_match('/^\/tasks\/[0-9]+\.json$/', $uri)) {
                    return true;
                }

                return false;

            }

            if($method === 'POST') {

                if(preg_match('/posts(|\/[0-9]+).json/', $uri) !== 0 && $data->group != 'admin') {

                    return false;

                }

                return true;
            }

            if($method == 'GET') {

                return true;

            }

            return false;
        }

        $components = explode('/', ltrim($uri, '/'));

        $action = isset($components[1]) ? $components[1] : '__get_only__';

        if(in_array($action, [
            "login",
            "add",
            "login.json"
        ]) || $uri === '/') {
            return true;
        }

        return false;
    }
}
