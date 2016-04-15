<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

namespace modules\Users;

use \stdClass;

use \Stream\PageController;
use \Stream\Request;
use \Stream\Exception\NotFoundException;
use \Stream\Interfaces\DomainControllerInterface;
use \Stream\Interfaces\RestApi;

use \modules\Users\model\User;

class Controller extends PageController implements DomainControllerInterface, RestApi {

    protected $_injectable = ['params', 'request', 'user'];

    protected $user;

    private function _error($message) {
        return ['error'=>$message];
    }

    public function __construct($params = NULL, \Stream\App $app = NULL) {

        parent::__construct($params, $app);

        $this->user = new User(\Stream\App::getConnection());

        $this->u = new Decorators\User(\Stream\App::getConnection());

    }

    final public function get() {
        
        $user = (new Decorators\User($this->app->pdo))
            ->filter(['like', 'email', $this->Request->getGet('email').'%']);
        
        $out = [];
        foreach($user as $u) {
            $out[] = $u->email;
        }

        return $out;
    }

    private function register($data) {

        $data = $this->Request->getPostData();

        if($data === NULL) {
            return $this->_error("No data");
        }

        if(!$this->user->valid($data)) {

            return $this->_error($this->user->error());

        }

        if($this->user->exists($data)) {
            return (object)['error'=> ['email' => 'User is already registered <a href="/login">Sign In</a>']];
        }

        $out = $this->user->add($data);

        unset($out->password);

        return $out;
    }

    private function grant($key) {
        $data = $this->u->filter(['access_key', $key])->current();
        
        if(!empty($data)) {
        
            unset($data->password);

            $this->Session->set('uid', $data->id);
        
        }
        
        return $data;
    }

    final public function post() {

        if($this->param('action') === 'login') {
            
            if($this->Request->getPostData('key')) {
                return $this->grant($this->Request->getPostData('key'));
            }

            return $this->login();
        }

        if($this->param('action') === 'register') {

            return $this->register($this->Request->getPostData());

        }

        if($this->param('action') === 'logout') {

            return $this->logout();

        }

        throw new \Stream\Exception\ForbiddenException("`{$this->param('action')}` failed");

    }

    final public function delete() {
        if($this->param('action') == 'login') {
            unset($_SESSION['uid']);
        }
        return false;
    }

    private function logout() {

        unset($_SESSION['uid']);
        return true;

    }

    private function login() {

        if($this->user->authenticate($this->Request)) {
            return $this->user->read($this->app->Session->get('uid'));
        }

        return (object)['error' => ['password' => 'Retype password', 'email' => 'Is email spelled correctly?']];

    }

}
