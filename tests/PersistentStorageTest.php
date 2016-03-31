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
        $this->storage->inject('table', [
            'posts' => [
                'id',
                'title',
                'body',
                'deleted',
                'users' => [
                    'id',
                    'username',
                    'email',
                    'deleted'
                ]
            ]
        ]);
    }

    public function testRead() {
        
        $data = $this->storage->read(1);
        $this->assertObjectHasAttribute('title', $data);

    }

    public function testDeleteReturnsDeletedRecord() {
        $deleted = $this->storage->delete(1);
        $this->assertEquals(1, $deleted->id);
    }

    public function testDeleteReturnsFalse(){
        $deleted = $this->storage->delete(1000);
        $this->assertFalse($deleted, 'Expected FALSE if record does not exist');
    }
}
