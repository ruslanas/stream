<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Posts\model;

use \Stream\PersistentStorage;

class Post extends PersistentStorage {

    protected $table = [

        'posts' => [
            
            'id',
            'title',
            'body',
            'user_id',
            'deleted',

            'users' => [
                'username',
                'email'
            ],

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
}
