<?php

namespace modules\Tasks;

class ControllerTest extends \PHPUnit_Framework_TestCase {

    public $_data;

    public function setUp() {
        
        $this->req = $this->getMockBuilder(\Stream\Request::class)->getMock();
        $this->model = $this->getMockBuilder(model\Task::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->api = new Api([]);
        $this->api->inject('model', $this->model);
        $this->api->inject('request', $this->req);
        
        $this->tasks = new Controller([]);
        $this->tasks->inject('task', $this->model);
        $this->tasks->inject('request', $this->req);
    
    }

    public function testOpen() {

        $this->model->method('read')
            ->willReturn([(object)[
                'id'=>1,
                'title'=>'Title',
                'description' => 'Description'
            ]
        ]);
        $this->assertContains('<input name="title"', $this->tasks->open());

    }

    public function testSave() {

        $this->req->method('post')->willReturn(['title' => 'todo', 'description' => 'implement feature']);
        
        $this->model->method('create')
            ->with($this->arrayHasKey('title'))
            ->willReturn((object)['id'=>1, 'title' => 'todo', 'description' => 'implement feature']);

        $this->tasks->save();
        $this->assertEquals('/tasks/edit/1', $this->tasks->redirect());

    }

    public function testEdit() {
        
        $this->tasks->inject('params', ['id' => 1]);

        $m = \Mockery::mock(model\Task::class);

        $m->shouldReceive('read')
            ->with()
            ->andReturn([(object)[
            'id' => 1,
            'title' => 'foo',
            'description' => 'bar'
        ]]);

        $m->shouldReceive('read')->with(1)->andReturn((object)[
            'id' => 1,
            'title' => 'foo',
            'description' => 'bar'
        ]);

        $this->tasks->inject('task', $m);

        $out = $this->tasks->edit();

        $this->assertContains('<textarea', $out);
    
    }

    public function testGetRest() {
        
        $this->model->method('read')->willReturn([(object)['id'=>1]]);
        $out = $this->api->get();
        $this->assertEquals(1, count($out));
    
    }

    public function testPostRest() {

        $this->model->method('create')->willReturn((object)['id'=>1]);
        $this->req->method('getPostData')->willReturn((object)['id'=>1]);
        $out = $this->api->post();
        $this->assertObjectHasAttribute('id', $out);

    }

}
