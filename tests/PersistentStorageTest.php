<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\Test\DatabaseTestCase;

use Stream\PersistentStorage;

class PersistentStorageTest extends DatabaseTestCase {
    
    public function setUp() {
        
        parent::setUp();

        $this->storage = new PersistentStorage($this->pdo);

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

    public function testRemove() {

        $this->assertEquals(1, $this->storage->remove(1)->id);
    
    }
}
