<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Posts;

use \Stream\App;
use \Stream\Exception\UnknownMethodException;
use \Stream\Request;
use \Stream\Test\DatabaseTestCase;

use \modules\Posts\Controller;

class RestControllerTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->controller = new Controller;

        $this->req = $this->getMockBuilder(Request::class)->getMock();

        $this->model = $this->getMockBuilder(model\Post::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller->inject('request', $this->req);
        $this->controller->inject('model', $this->model);

    }

    public function testApi() {

        $this->controller->inject('params', []);

        $this->model->method('read')->willReturn([(object)['title' => 'Title']]);

        $data = $this->controller->get();

        $this->assertTrue(is_array($data));
        $this->assertEquals(count($data), 1);
        $this->assertObjectHasAttribute('title', $data[0]);
    }

    public function testApiGetById() {

        $this->controller->inject('params', ['id' => 1]);
        $this->model->method('getById')->with(1)->willReturn((object)['title' => 'Title']);

        $data = $this->controller->get();

        $this->assertObjectHasAttribute('title', $data);

    }

    public function testPostCreatesNewPost() {

        $this->controller->inject('params', []);

        $data = [
            'title' => 'Title',
            'body' => 'Post body'
        ];

        $this->req->method('getPostData')->willReturn($data);

        $this->model->method('save')->with(NULL, $data)->willReturn((object)$data);

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

        $this->model->method('delete')->with(1)->willReturn((object)['id'=>1]);
        $this->controller->inject('params', ['id' => 1]);

        $res = $this->controller->delete();

        $this->assertEquals(1, $res->id);

        $this->controller->inject('params', []);
        $this->expectException(\Exception::class);
        $this->controller->delete();

    }
}
