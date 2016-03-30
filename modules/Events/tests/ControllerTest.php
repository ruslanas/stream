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

		$this->app = new App();
		$this->app->loadConfig();
		$this->app->connect('test_stream');

		$model = $this->getMockBuilder(Event::class)
		->disableOriginalConstructor()
		->getMock();

		$model->method('create')->willReturn((object)['id'=>1]);
		$model->method('read')->willReturn((object)['id'=>1]);
		$model->method('update')->willReturn((object)['id'=>1]);
		$model->method('delete')->willReturn((object)['id'=>1]);

		$request = $this->getMockBuilder(Request::class)
		->disableOriginalConstructor()
		->getMock();

		$request->method('getPostData')->willReturn([
			'type' => 'call'
		]);

		$this->controller = new Controller([], $request);

		$this->controller->inject('event', $model);

	}
	
	public function testGet() {
		$this->assertObjectHasAttribute('id', $this->controller->get());
	}

	public function testDelete() {
		$this->controller->inject('params', ['id' => 1]);
		$this->assertObjectHasAttribute('id', $this->controller->delete());
	}

	public function testPost() {
		$this->assertObjectHasAttribute('id', $this->controller->post());
	}
	
	public function testPut() {
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
