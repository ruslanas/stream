<?php
/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
class UserController extends Controller implements DomainControllerInterface {

    public function __construct() {
        parent::__construct();
        $this->request = new Request();
        $this->user = new User($this->request);
        $this->templates->addFolder('user', 'templates/user');
    }

    public function dispatch($uri) {
        $components = explode('/', $uri);
        if(sizeof($components) < 3) {
            $this->login();
        }
        if(method_exists($this, $components[2])) {
            $this->{$components[2]}();
        }
    }

    public function add() {
        if($this->user->authenticate()) {
            $this->redirect('/');
        }

        $data = $this->request->post();

        if($data && $this->user->exists($data)) {
            $this->redirect('/user/add');
        }

        if($this->user->valid($data)) {
            $this->user->add($data);
            $this->redirect('/user/login');
        }
        echo $this->templates->render('user::add', []);
    }

    public function logout() {
        unset($_SESSION['uid']);
        $this->redirect('/user/login');
    }

    public function login() {
        if($this->user->authenticate()) {
            $this->redirect('/');
        }
        echo $this->templates->render('user::login', [
            'data' => $this->user->data
        ]);
    }
}
