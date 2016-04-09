<?php

namespace modules\Users\Decorators;

use \PDO;

class User extends \Stream\DataStoreDecorator {

    protected $structure = [

        'users',

        ['id', PDO::PARAM_INT],
        ['email', PDO::PARAM_STR],
        ['password', PDO::PARAM_STR],
        ['deleted', PDO::PARAM_BOOL],
        ['created', PDO::PARAM_STR],
        ['modified', PDO::PARAM_STR],
        ['group', PDO::PARAM_STR]

    ];

}
