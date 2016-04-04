<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \modules\Users\model\User;

/** Manages Acess Control List. Application checks if HTTP method
 * is allowed on particular URL for in session.
 */
class Acl {

    public function allow($method, $uri) {

        if(!empty($_SESSION['uid']) && $method === 'DELETE' && $uri === '/users/login.json') {
            return true;
        }

        if(!empty($_SESSION['uid'])) {

            $uid = $_SESSION['uid'];

            $user = new User(App::getConnection());
            
            $data = $user->getById($uid);

            if($method === 'DELETE') {

                if($data->group == 'admin') {
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

        if(in_array($action, ["login", "add", "login.json"]) || $method === 'GET') {
            return true;
        }
        
        return false;
    }
}
