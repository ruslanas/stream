<?php
/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

use Stream\Request;
use Stream\Exception\NotFoundException;
use Stream\Interfaces\DomainControllerInterface;

class UserController extends Stream\Controller implements DomainControllerInterface {

    public function __construct(Request $request = NULL) {
        parent::__construct();
        $this->request = $request !== NULL ? $request : new Request;

        $this->user = new User($this->request, $this->app->pdo);
        $this->templates->addFolder('user', 'templates/user');
    }

    public function dispatch($uri) {
        $components = explode('/', $uri);
        if(count($components) < 3) {
            return $this->login();
        }
        if(method_exists($this, $components[2])) {
            return $this->{$components[2]}();
        }
        throw new NotFoundException("Page not found");
        
    }

    public function add() {
        if($this->user->authenticate()) {
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

    public function logout() {
        unset($_SESSION['uid']);
        $this->redirect('/user/login');
    }

    public function login() {
        if($this->user->authenticate()) {
            $this->redirect('/');
        }
        return $this->templates->render('user::login', [
            'data' => $this->user->data
        ]);
    }
}
