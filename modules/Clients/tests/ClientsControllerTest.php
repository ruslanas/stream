<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com>
 */

use Stream\App;
use Stream\Request;
use Stream\Test\DatabaseTestCase;
use Stream\Exception\NotFoundException;

use modules\Clients\Controller;
use modules\Clients\model\Client;

class ClientsControllerTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->controller = new Controller();

        $this->req = $this->getMockBuilder(Request::class)
            ->getMock();

        $this->model = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller->inject('request', $this->req);
        $this->controller->inject('model', $this->model);
        $this->controller->inject('params', []);
    }

    public function testGet() {

        $this->model->method('getList')->willReturn([
            (object)['name' => 'foo']
        ]);

        $res = $this->controller->get();
        
        $this->assertTrue(is_array($res), 'Array of objects expected');
        $this->assertEquals(1, count($res));
        $this->assertObjectHasAttribute('name', $res[0]);

        $this->controller->inject('params', ['id' => 1]);

        $this->model->method('getById')->willReturn((object)['name' => 'Client Name']);
        
        $res = $this->controller->get();
        $this->assertEquals('Client Name', $res->name);

    }

    public function testGetThrowsExeption() {
        $this->expectException(NotFoundException::class);
        $this->controller->inject('params', ['id' => 1000]);
        $this->model->method('getById')->willReturn(FALSE);
        $this->controller->get();
    }

    public function testDelete() {

        $this->controller->inject('params', ['id' => 1]);
        $this->model->method('delete')->willReturn((object)['id'=>1, 'name' => 'test']);
        $res = $this->controller->delete();
        $this->assertEquals($res->id, '1');
        
    }

    public function testPostNew() {

        $this->req->method('getPostData')->willReturn([
            'name' => 'New Client'
        ]);

        $this->model->method('save')->willReturn((object)['name' => 'New Client']);

        $this->controller->inject('params', []);
        $record = $this->controller->post();
        $this->assertEquals($record->name, 'New Client');

    }

    public function testPostExisting() {

        $this->controller->inject('params', ['id' => 1]);
        
        $this->model->method('save')->willReturn((object)['name' => 'Updated Client']);
        
        $record = $this->controller->post();
        $this->assertEquals($record->name, 'Updated Client');

    }
}
