<?php

namespace modules\Task\tests;

use modules\Tasks\model\Task;

class TaskTest extends \Stream\Test\DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $this->task = new Task(\Stream\App::getConnection('test_stream'));
    
    }

    public function testSave() {
    
        $data = $this->task->create(['title' => 'todo', 'description' => 'implement feature']);
        $this->assertEquals('todo', $data->title);
        $this->assertEquals('implement feature', $data->description);
    
    }

}