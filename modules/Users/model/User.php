<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Users\model;

use \PDO;

class User extends \Stream\PersistentStorage {

    public $data = [];

    protected $_errors = [];

    protected $structure = [

        'users',

        ['id', PDO::PARAM_INT],
        ['email', PDO::PARAM_STR],
        ['password', PDO::PARAM_STR],
        ['deleted', PDO::PARAM_BOOL],
        ['created', PDO::PARAM_STR],
        ['modified', PDO::PARAM_STR],
        ['group', PDO::PARAM_STR]

    ];

    public function __construct($pdo = NULL) {
        parent::__construct($pdo);
    }

    public function valid(Array $data = NULL) {

        $this->_errors = [];

        if(!empty($data['email'])) {
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->_errors['email'] = 'Email invalid';
            }
        } else {
            $this->_errors['email'] = 'Can\'t be empty';
        }

        if(!empty($data['password']) && !empty($data['password2'])) {
            if($data['password'] !== $data['password2']) {
                $this->_errors['password2'] = 'Passwords do not match';
            }
        } else {
            $this->_errors['password'] = 'Both fields must be filled';
        }

        if(count($this->_errors)) {
            return false;
        }

        return true;

    }

    public function exists($data) {

        return !!$this->search(['email' => $data['email']]);

    }

    public function getById($id) {

        $data = $this->read($id);

        /////////////////////////////////////////////////
        // Do not send even hashed passwords to client //
        /////////////////////////////////////////////////

        if(isset($data->password)) {
            $data->password = '';
        }

        return $data;
    }

    public function getList() {
        return $this->read();
    }

    public function add($data) {

        if(!isset($data['email']) || !isset($data['password'])) {

            return false;

        }

        $encrypted = password_hash($data['password'], PASSWORD_BCRYPT);

        $data['password'] = $encrypted;

        return $this->create($data);

    }

    /**
     * Check if user exists
     */
    public function loggedIn() {

        if(!empty($_SESSION['uid'])) {

            $data = $this->getById($_SESSION['uid']);

            if(!empty($data)) {

                return true;

            }
        }

        return false;
    }

    /**
     * Check if session started
     * @param \Stream\Request $req
     * @return bool
     */
    public function authenticate($req) {

        if($this->loggedIn()) {

            return true;

        }

        $data = $req->post();

        if(empty($data)) {
            $data = $req->getPostData();
        }

        $data = is_array($data) ? $data : [];

        if(!isset($data['email']) || !isset($data['password']) || isset($data['password2'])) {

            return false;

        }

        return $this->login($data);
    }

    /**
     * Find user in database and set session (uid)
     * @param array $credentials
     * @return \stdClass|false
     */
    public function login($data = []) {

        $sql = "SELECT * FROM `{$this->structure[0]}` WHERE email = :email";

        $statement = $this->db->prepare($sql);
        $statement->bindParam(":email", $data['email']);
        $statement->execute();

        $row = $statement->fetch();

        if($row && password_verify($data['password'], $row->password)) {

            $_SESSION['uid'] = $row->id;

            return $row;

        } else {

            return false;

        }
    }

    public function error() {
        return $this->_errors;
    }
}
