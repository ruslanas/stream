<?php

/**
 * Task Decorator
 * @author Ruslanas Balčiūnas
 */

namespace modules\Tasks\Decorators;

use \PDO;

class Task extends \Stream\DataStoreDecorator {

    /**
     * @var array Data structure
     */
    protected $structure = [

        'tasks',

        ['id', PDO::PARAM_INT],
        ['title', PDO::PARAM_STR],
        ['description', PDO::PARAM_STR],
        ['focus', PDO::PARAM_BOOL],
        ['created', PDO::PARAM_STR],
        ['modified', PDO::PARAM_STR],
        ['delegate_id', PDO::PARAM_INT],
        ['user_id', PDO::PARAM_INT],

        ['accepted', PDO::PARAM_BOOL],
        ['completed', PDO::PARAM_BOOL],

        ['deleted', PDO::PARAM_BOOL], // record will be marked as deleted

        ['users as user', [
            ['id', PDO::PARAM_INT],
            ['email', PDO::PARAM_STR]
        ], 'user.id = user_id'],

        ['users as delegate', [
            ['id', PDO::PARAM_INT],
            ['email', PDO::PARAM_STR]
        ], 'delegate.id = delegate_id']

    ];

    /**
     * @param string $email
     */
    public function delegate($email) {
    
        $user = (new \modules\Users\Decorators\User($this->db))->filter(['email', $email]);

        if($user->current() === NULL) {
            $user->create(['email'=>$email]);
        }

        $this->update(NULL, [
            'delegate_id' => $user->current()->id,
            'accepted' => "1"
        ]);
    
        return $this;

    }

    public function reject() {
        $this->update(NULL, ['accepted' => 0]);
        return $this;
    }

}
