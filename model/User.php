<?php
class User {

    private $db;

    public $data = [
        'email' => '',
        'password' => ''
    ];

    public function __construct($request) {
        $this->request = $request;
        $this->db = new PDO("sqlite:data/user.sqlite");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function valid($data) {
        if($data !== null && isset($data['email'])
            && isset($data['password']) && isset($data['password2'])
            && $data['password'] === $data['password2']) {
            return true;
        }
        return false;
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
}
