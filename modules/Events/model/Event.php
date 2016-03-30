<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Events\Model;

use Stream\PersistentStorage;

class Event extends PersistentStorage {
    
    protected $table = 'events';
    protected $fields = [
        'id',
        'title',
        'description',
        'type',
        'when',
        'user_id'
    ];

}
