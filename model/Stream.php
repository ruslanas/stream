<?php
class Stream {

    private $db;

    public function __construct() {
        $this->db = new PDO("sqlite:data/stream.sqlite");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
    }

    public function getById($id) {
        $statement = $this->db->prepare("SELECT * FROM posts WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        return $row;
    }
}
