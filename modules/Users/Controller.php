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

    public function get() {}

    final public function post() {

        if($this->param('action') === 'login') {
            if($this->user->authenticate($this->request)) {
                return $this->user->read($this->app->session->get('uid'));
            }
        }

        if($this->param('action') === 'register') {

            $data = $this->request->getPostData();

            if($data === NULL) {
                return $this->_error("No data");
            }

            if(!$this->user->valid($data)) {

                return $this->error($this->user->error());

            }

            if($this->user->exists($data)) {
                return $this->_error("Such is user already registered");
            }

            return $this->user->add($data);

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

    public function __construct($params = NULL, \Stream\App $app = NULL) {

        parent::__construct($params, $app);

        $this->user = new User(\Stream\App::getConnection());

        $this->templates->addFolder('user', __DIR__.DIRECTORY_SEPARATOR.'templates');

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
