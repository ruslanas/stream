<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */
class Stream {

    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getList() {

        $data = [];
        $sql = "SELECT * FROM posts ORDER BY created DESC";
        foreach($this->db->query($sql) as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function delete($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function save($id, $data) {
        if($id !== NULL) {
            $sql = "UPDATE posts SET title = :title, body = :body WHERE id = :id";
        } else {
            $sql = "INSERT INTO posts (title, body) VALUES(:title, :body)";
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
        $statement = $this->db->prepare("SELECT * FROM posts WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        return $row;
    }
}
