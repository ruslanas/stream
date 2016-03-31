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
        
        return $this->read($id);

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
