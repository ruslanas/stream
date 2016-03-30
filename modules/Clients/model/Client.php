<?php

namespace modules\Clients\model;

use \PDO;
use Stream\PersistentStorage;

class Client extends PersistentStorage {

    private $_default = [
        'type' => 0,
        'address' => '',
        'email' => '',
        'phone' => ''
    ];

    protected $table = 'clients';
    protected $fields = [
        'id',
        'name',
        'email',
        'phone',
        'type',
        'address'
    ];

    public function getList() {

        $data = [];

        $sql = "SELECT clients.*, users.username FROM clients LEFT JOIN users ON clients.user_id = users.id"
            ." WHERE NOT clients.deleted"
            ." ORDER BY clients.created DESC LIMIT 100";

        foreach($this->db->query($sql) as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function save($id, $data) {

        if($id !== NULL) {
            return $this->update($id, $data);
        } else {
            return $this->create($data);
        }

    }

    public function getById($id, $showDeleted = FALSE) {
        
        $sql = "SELECT clients.*, users.username FROM clients"
            ." LEFT JOIN users ON clients.user_id = users.id"
            ." WHERE clients.id = :id AND clients.deleted = :deleted"
            ." ORDER BY clients.created DESC LIMIT 100";

        $statement = $this->db->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':deleted', $showDeleted);
        $statement->execute();
        return $statement->fetch();
    }

    public function delete($id) {
        $sql = "UPDATE clients SET deleted = 1 WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function filter($options) {
        $sql = "SELECT * FROM clients WHERE ";
        $filter = [];
        foreach($options as $col => $value) {
            $filter[] = "$col = :$col";
        }
        $sql .= join(',', $filter);
        $statement = $this->db->prepare($sql);
        
        foreach($options as $col => $value) {
            $statement->bindParam(":$col", $value, PDO::PARAM_STR);
        }

        $statement->execute();
        $data = [];
        while($row = $statement->fetch()) {
            $data[] = $row;
        }
        return $data;
    }
}
