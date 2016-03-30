<?php

use Stream\App;
use Stream\Request;
use Stream\Test\DatabaseTestCase;

use modules\Clients\Controller;

class ClientsControllerTest extends DatabaseTestCase {

    public function setUp() {
        parent::setUp();
        $this->controller = new Controller([], $this->getRequestMock());
    }

    public function testGet() {

        $res = $this->controller->get();
        
        $this->assertTrue(is_array($res));
        $this->assertEquals(1, count($res));
        $this->assertObjectHasAttribute('name', $res[0]);

        $controller = new Controller(['id'=>1], $this->getRequestMock());
        $res = $controller->get();
        $this->assertEquals('Client Name', $res->name);
    }

    public function testDelete() {
        $controller = new Controller(['id' => 1], $this->getRequestMock());
        
        $res = $controller->delete();
        
        $this->assertEquals($res->deleted, '1');
        
        $this->expectException(Stream\Exception\NotFoundException::class);
        
        $controller->get();
    }

    public function testPost() {
        $controller = new Controller([], $this->getRequestMock(NULL, [
            'name' => 'New Client'
        ]));

        $out = $controller->post();
        $this->assertEquals($out->name, 'New Client');
    }
}
