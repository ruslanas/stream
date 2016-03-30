<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Exception\UnknownMethodException;
use Stream\Request;
use Stream\Test\DatabaseTestCase;

use modules\Posts\Controller;

class RestControllerTest extends DatabaseTestCase {

    public function setUp() {
        
        parent::setUp();

        $this->controller = new Controller([], $this->getRequestMock());
    }

    public function testApi() {

        $data = $this->controller->get();

        $this->assertTrue(is_array($data));
        $this->assertEquals(count($data), 1);
        $this->assertObjectHasAttribute('title', $data[0]);

        $controller = new Controller(['id' => 1], $this->getRequestMock());
        $data = $controller->get();

        $this->assertObjectHasAttribute('title', $data);

        $controller = new Controller([], $this->getRequestMock());
        
        $data = $controller->post();

        $this->assertObjectHasAttribute('title', $data);
        $this->assertEquals($data->title, 'test');
        $this->assertEquals($data->body, 'test_body');

    }

    public function testPut() {
        $allow = '';
        try {
            $this->controller->put();
        } catch (UnknownMethodException $e) {
            $allow = $e->getAllow();
        }
        $this->assertEquals('Allow: DELETE, GET, POST', $allow);

        $this->expectException(UnknownMethodException::class);
        $this->controller->put();
    }

    public function testDelete() {

        $controller = new Controller(['id' => 1], $this->getRequestMock());
        
        $controller->delete();

        $controller = new Controller([], $this->getRequestMock());
        
        $data = $controller->get();
        $this->assertTrue(is_array($data));
        $this->assertEquals(0, count($data));

        $this->expectException(Exception::class);
        $controller = new Controller([], $this->getRequestMock());
        $controller->delete();

    }
}
