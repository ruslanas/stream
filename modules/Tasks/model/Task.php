<?php

namespace modules\Tasks\model;

use \PDO;

class Task extends \Stream\PersistentStorage {

    protected $table = [
        
        'tasks' => [
            'id',
            'title',
            'description',
            'deleted',
            'user_id',
            'focus' => [
                'type' => PDO::PARAM_BOOL
            ]
        ]
    
    ];

}
