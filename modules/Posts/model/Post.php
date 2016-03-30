<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Posts\model;

use \PDO;

class Post {

    private $db;
    private $table = 'posts';

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getList() {

        $data = [];
        $sql = "SELECT * FROM `{$this->table}` ORDER BY created DESC";
        foreach($this->db->query($sql) as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function delete($id) {
        
        $sql = "DELETE FROM `{$this->table}` WHERE id = :id";

        $statement = $this->db->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function save($id, $data) {
        
        if($id !== NULL) {
            $sql = "UPDATE `{$this->table}` SET title = :title, body = :body WHERE id = :id";
        } else {
            $sql = "INSERT INTO `{$this->table}` (title, body) VALUES(:title, :body)";
        }

        $statement = $this->db->prepare($sql);
        $statement->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $statement->bindParam(':body', $data['body'], PDO::PARAM_STR);

        if($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $statement->execute();

        return $id === NULL ? $this->db->lastInsertId() : $id;
    }

    public function getById($id) {
        
        $query = "SELECT * FROM `{$this->table}` WHERE id = :id";

        $statement = $this->db->prepare($query);
        
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetch();
    }
}
