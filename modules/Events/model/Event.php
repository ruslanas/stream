<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Events\Model;

use Stream\PersistentStorage;

class Event extends PersistentStorage {
    
    protected $table = 'events';
    protected $join = [
        'users' => [
            'username',
            'email'
        ],
        'clients' => [
            'name',
            'deleted'
        ]
    ];
    protected $fields = [
        'id',
        'title',
        'description',
        'type',
        'when',
        'user_id'
    ];

}
