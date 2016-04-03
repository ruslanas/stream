<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Exception\UnknownMethodException;
use Stream\Request;

use modules\Events\Controller;
use modules\Events\model\Event;

class ControllerTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {

        $this->model = $this->getMockBuilder(Event::class)

            ->disableOriginalConstructor()
            ->getMock();

        $this->req = $this->getMockBuilder(Request::class)

            ->getMock();

        $this->req->method('getPostData')->willReturn([
            'type' => 'call'
        ]);

        $this->controller = new Controller;

        $this->controller->inject('event', $this->model);
        $this->controller->inject('request', $this->req);

    }
    
    public function testGetList() {
        
        $this->model->method('read')->with(NULL)->willReturn([(object)['id'=>1]]);
        
        $this->assertTrue(is_array($this->controller->get()), 'Array expected');
    }

    public function testGetOne() {

        $this->model->method('read')->with(1)->willReturn((object)['id'=>1]);

        $this->controller->inject('params', ['id' => 1]);
        $this->assertObjectHasAttribute('id', $this->controller->get());
    }

    public function testDelete() {
        
        $this->model->method('delete')->willReturn((object)['id'=>1]);

        $this->controller->inject('params', ['id' => 1]);
        $this->assertObjectHasAttribute('id', $this->controller->delete());
    }

    public function testPost() {
        
        $this->model->method('create')->willReturn((object)['id'=>1]);
        
        $this->assertObjectHasAttribute('id', $this->controller->post());
    }
    
    public function testPut() {
        
        $this->model->method('update')->willReturn((object)['id'=>1]);
        
        $this->controller->inject('params', ['id' => 1]);
        $this->assertObjectHasAttribute('id', $this->controller->put());

    }

    public function testAllowHeaderField() {
        
        $allow = '';
        
        try {
            $this->controller->head();
        } catch (UnknownMethodException $e) {
            $allow = $e->getAllow();
        }

        $this->assertEquals('Allow: DELETE, GET, POST, PUT', $allow);
    }

}
