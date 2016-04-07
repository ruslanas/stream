<?php

namespace modules\Task\tests;

use modules\Tasks\model\Task;

class TaskTest extends \Stream\Test\DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $this->task = new Task($this->pdo);

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
