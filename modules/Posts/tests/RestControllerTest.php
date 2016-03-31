<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Exception\UnknownMethodException;
use Stream\Request;
use Stream\Test\DatabaseTestCase;

use modules\Posts\Controller;

class RestControllerTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        
        $this->controller = new Controller();

        $this->req = $this->getMockBuilder(Request::class)->getMock();
        $this->controller->inject('request', $this->req);
    }

    public function testApi() {

        $this->controller->inject('params', []);
        $data = $this->controller->get();

        $this->assertTrue(is_array($data));
        $this->assertEquals(count($data), 1);
        $this->assertObjectHasAttribute('title', $data[0]);

        $this->controller->inject('params', ['id' => 1]);
        $data = $this->controller->get();

        $this->assertObjectHasAttribute('title', $data);

    }

    public function testPostCreatesNewPost() {
        
        $this->controller->inject('params', []);

        $this->req->method('getPostData')->willReturn([
            'title' => 'Title',
            'body' => 'Post body'
        ]);

        $data = $this->controller->post();

        $this->assertObjectHasAttribute('title', $data);
        $this->assertEquals($data->title, 'Title');
        $this->assertEquals($data->body, 'Post body');

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

        $this->controller->inject('params', ['id' => 1]);
        $this->controller->delete();

        $this->controller->inject('params', []);
        $data = $this->controller->get();
        $this->assertTrue(is_array($data));
        $this->assertEquals(0, count($data));

        $this->expectException(Exception::class);
        $this->controller->delete();

    }
}
