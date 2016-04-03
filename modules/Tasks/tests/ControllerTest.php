<?php

namespace modules\Tasks;

class ControllerTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        
        $this->req = $this->getMockBuilder(\Stream\Request::class)->getMock();
        $this->model = $this->getMockBuilder(model\Task::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tasks = new Controller([]);

        $this->tasks->inject('task', $this->model);
        $this->tasks->inject('request', $this->req);
    
    }

    public function testOpen() {
        $this->assertContains('<input name="title"', $this->tasks->open());
    }

    public function testSave() {
        $this->req->method('post')->willReturn(['title' => 'todo', 'description' => 'implement feature']);
        
        $this->model->method('create')
            ->with($this->arrayHasKey('title'))
            ->willReturn(['id'=>1, 'title' => 'todo', 'description' => 'implement feature']);

        $this->tasks->save();
        $this->assertEquals('/tasks/edit', $this->tasks->redirect());
    }

    public function testEdit() {
        $out = $this->tasks->edit();
        $this->assertContains('<textarea', $out);
    }    
}
