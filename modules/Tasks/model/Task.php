<?php

namespace modules\Tasks\model;

class Task extends \Stream\PersistentStorage {

    protected $table = [
        
        'tasks' => [
            'id',
            'title',
            'description',
            'deleted',
            'user_id'
        ]
    
    ];

}
