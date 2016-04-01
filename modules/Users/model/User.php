<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Users\model;

use \PDO;

use \Stream\Util\Injectable;

class User extends Injectable {

    private $db;
    private $_errors = [];
    private $_table = 'users';

    protected $_injectable = ['request'];

    public $data = [];

    public function __construct(PDO $pdo = NULL) {
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
        $statement = $this->db->prepare("SELECT * FROM `{$this->_table}` WHERE email = :email");
        $statement->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch();
        if($row === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function getById($id) {
        
        $statement = $this->db->prepare("SELECT * FROM `{$this->_table}` WHERE id = :id");

        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    }

    public function getList() {
        $statement = $this->db->prepare("SELECT * FROM `{$this->_table}` LIMIT 100");
        $statement->execute();
        $out = [];
        while($row = $statement->fetch()) {
            $out[] = $row;
        }
        return $out;
    }

    public function add($data) {

        if(!isset($data['email']) || !isset($data['password'])) {

            return false;

        }

        $encrypted = password_hash($data['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO `{$this->_table}` (email, password) VALUES (:email, :password)";

        $statement = $this->db->prepare($sql);
        $statement->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $statement->bindParam(':password', $encrypted, PDO::PARAM_STR);
        $statement->execute();
        
        return $this->db->lastInsertId();
    
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
     */
    public function authenticate($req) {
        
        if($this->loggedIn()) {
        
            return true;
        
        }

        $this->data = $req->post();

        $this->data = is_array($this->data) ? $this->data : [];

        if(!isset($this->data['email']) || !isset($this->data['password']) || isset($this->data['password2'])) {
            
            return false;
        
        }

        $sql = "SELECT * FROM `{$this->_table}` WHERE email = :email";
        
        $statement = $this->db->prepare($sql);
        $statement->bindParam(":email", $this->data['email']);
        $statement->execute();
        
        $row = $statement->fetch();

        if($row && password_verify($this->data['password'], $row->password)) {
            $_SESSION['uid'] = $row->id;
            return true;
        } else {
            return false;
        }
    }
    public function error() {
        return $this->_errors;
    }
}
