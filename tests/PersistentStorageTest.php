<?php

use Stream\Test\DatabaseTestCase;

use Stream\App;
use Stream\PersistentStorage;

class PersistentStorageTest extends DatabaseTestCase {
    
    public function setUp() {
        
        parent::__construct();

        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');

        $this->storage = new PersistentStorage($this->app->pdo);
    
    }

    public function testRead() {
        
        $this->storage->inject('table', [
            'posts' => [
                'title',
                'body',
                'deleted',
                'users' => [
                    'username',
                    'email',
                    'deleted'
                ]
            ]
        ]);

        $data = $this->storage->read(1);
        $this->assertObjectHasAttribute('title', $data);

    }
}
