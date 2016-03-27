<?php

use Stream\Request;

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */
class User {

    private $db;
    private $_errors = [];

    public $data = [
        'email' => '',
        'password' => ''
    ];

    public function __construct(Request $request, PDO $pdo) {
        $this->request = $request;
        $this->db = $pdo;
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
            if($data['password'] != $data['password2']) {
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
        $statement = $this->db->prepare("SELECT * FROM user WHERE email = :email");
        $statement->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch();
        if($row === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function add($data) {

        if(!isset($data['email']) || !isset($data['password'])) {
            return false;
        }

        $encrypted = password_hash($data['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO user (email, password) VALUES (:email, :password)";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $statement->bindParam(':password', $encrypted, PDO::PARAM_STR);
        $statement->execute();
    }

    public function loggedIn() {
        return !empty($_SESSION['uid']);
    }

    /**
     * Check if session started
     */
    public function authenticate() {
        if($this->loggedIn()) {
            return true;
        }
        $this->data = $this->request->post();

        if(!isset($this->data['email']) || !isset($this->data['password'])) {
            return false;
        }

        $sql = "SELECT * FROM user WHERE email = :email";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(":email", $this->data['email']);
        $statement->execute();
        $row = $statement->fetch();
        if($row && password_verify($this->data['password'], $row['password'])) {
            $_SESSION['uid'] = $row['id'];
            return true;
        } else {
            return false;
        }
    }
    public function error() {
        return $this->_errors;
    }
}
