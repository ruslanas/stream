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

use \modules\Users\model\User;

class Controller extends PageController implements DomainControllerInterface {

    protected $_injectable = ['params', 'request', 'user'];
    
    protected $user;

    public function __construct($params = NULL, Request $request = NULL, stdClass $user = NULL) {

        parent::__construct();

        $this->params = $params;
        
        $this->request = $request !== NULL ? $request : new Request;

        $this->user = new User($this->app->pdo);
        
        $this->templates->addFolder('user', 'modules/Users/templates');
    
    }

    final public function index() {
        return $this->login();
    }

    final public function add() {

        if($this->user->authenticate($this->request)) {
            
            return $this->redirect('/');
        
        }

        $data = $this->request->post();

        if($data && $this->user->exists($data)) {
        
            return $this->redirect('/user/add');
        
        }

        if($this->user->valid($data)) {
        
            $this->user->add($data);
        
            return $this->redirect('/user/login');
        
        }

        return $this->templates->render('user::add', []);
    
    }

    final public function logout() {
        
        unset($_SESSION['uid']);

        $this->redirect('/user/login');
    
    }

    final public function login() {
        
        if($this->user->authenticate($this->request)) {
            
            return $this->redirect('/');
        
        }

        // required by template
        $data = array_merge(['email' => ''], $this->user->data);

        return $this->templates->render('user::login', [
            'data' => $data
        ]);
    
    }

}
