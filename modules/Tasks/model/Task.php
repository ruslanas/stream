<?php

namespace modules\Tasks\model;

use \PDO;

class Task extends \Stream\PersistentStorage {

    /** @var array Accessible by PersistentStorage */
    protected $structure = [

        'tasks',

        ['id', PDO::PARAM_INT],
        ['title', PDO::PARAM_STR],
        ['description', PDO::PARAM_STR],
        ['focus', PDO::PARAM_BOOL],
        ['created', PDO::PARAM_STR],
        ['modified', PDO::PARAM_STR],
        ['deleted', PDO::PARAM_BOOL],

        ['users as user', [
            ['id', PDO::PARAM_INT],
            ['email', PDO::PARAM_STR]
        ], 'user.id = user_id'],

        ['users as delegate', [
            ['id', PDO::PARAM_INT],
            ['email', PDO::PARAM_STR]
        ], 'delegate.id = delegate_id']

    ];

    protected $table = [

        'tasks' => [

            'id',
            'title',
            'description',
            'deleted',
            'user_id',
            'delegate_id',
            'focus' => ['type' => PDO::PARAM_BOOL],

            'users' => [
                'id',
                'username',
                'email'
            ]

        ]

    ];

}
