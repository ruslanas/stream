<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\Test\DatabaseTestCase;

use Stream\PersistentStorage;

class PersistentStorageTest extends DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $dsl = [
            'posts',

            ['id', PDO::PARAM_INT],
            ['title', PDO::PARAM_STR],
            ['body', PDO::PARAM_STR],
            ['deleted', PDO::PARAM_BOOL],

            ['users as user', [
                ['id', PDO::PARAM_INT],
                ['email', PDO::PARAM_STR],
                ['deleted', PDO::PARAM_BOOL],
            ], 'user.id = posts.user_id'],
        ];

        $this->storage = new PersistentStorage($this->pdo, $dsl);
        
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
