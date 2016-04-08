<?php

namespace modules\Task\tests;

use modules\Tasks\model\Task;

class TaskTest extends \Stream\Test\DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $this->task = new Task($this->pdo);

    }

    public function testSearchByTitle() {
        $found = $this->task->search(['title' => 'Implement new feature']);

        $this->assertGreaterThan(0, count($found));
        $this->assertObjectHasAttribute('id', $found[0]);
        $this->assertEquals('Implement new feature', $found[0]->title);

    }

    public function testSearchAnd() {

        $found = $this->task->search([
            'id' => [1, \PDO::PARAM_INT],
            'title' => ['implement new feature', \PDO::PARAM_STR]
        ]);

        $this->assertEquals('Implement new feature', $found[0]->title);

    }

    public function testSearchEmpty() {
        $found = $this->task->search([]);
        $this->assertEquals(0, count($found));
    }

    public function testSave() {

        $data = $this->task->create(['title' => 'todo', 'description' => 'implement feature']);
        $this->assertEquals('todo', $data->title);
        $this->assertEquals('implement feature', $data->description);

    }

    public function testGet() {

        $data = $this->task->read();

        $this->assertGreaterThan(0, count($data));
        $this->assertObjectHasAttribute('id', $data[0]);
        $this->assertObjectHasAttribute('title', $data[0]);
        $this->assertObjectHasAttribute('description', $data[0]);
        $this->assertObjectHasAttribute('focus', $data[0]);
        $this->assertObjectHasAttribute('created', $data[0]);
        $this->assertObjectHasAttribute('modified', $data[0]);

    }

    public function testDelete() {

        $data = $this->task->read();
        $count = count($data);

        // user 2 has can only delete his own messages
        $this->task->delete(1, 2);

        $data = $this->task->read();

        $this->assertEquals($count, count($data));

    }

}
