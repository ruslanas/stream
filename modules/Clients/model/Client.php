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

    public function getById($id) {
        
        return $this->read($id);

    }

    public function filter($options) {

        return $this->search($options);

    }
}
