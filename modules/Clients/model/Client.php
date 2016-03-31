<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Clients\model;

use \PDO;
use Stream\PersistentStorage;

/**
 * Class represents clients database
 */
class Client extends PersistentStorage {

    protected $table = [
        'clients' => ['id',
            'name',
            'email',
            'phone',
            'type',
            'address',
            'user_id',
            'deleted',
            
            'users' => [
                'username',
                'email'
            ]
        ]
    ];

    public function getList() {

        return $this->read();

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
        $statement->bindParam(':deleted', $showDeleted, PDO::PARAM_BOOL);
        $statement->execute();

        return $statement->fetch();

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
