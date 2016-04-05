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
        $this->assertEquals(1, count($data));
    }

}