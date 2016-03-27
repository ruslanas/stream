<?php

class FakeRequest {
	public function getPostData() {
		return ['title' => 'test', 'body' => 'test_body'];
	}
}

class RestControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
	}
	public function testApi() {
		$this->app = new App();
		$this->app->loadConfig();
		$this->app->connect('test_stream');
		$this->controller = new RestController([], new FakeRequest);

		ob_start(function($buffer) {
			$data = json_decode($buffer);
			$this->assertTrue(is_array($data));
			$this->assertEquals(count($data), 1);
			$this->assertObjectHasAttribute('title', $data[0]);
		});
		$this->controller->get();
		ob_end_flush();

		$controller = new RestController(['id' => 1], new FakeRequest);
		ob_start(function($buffer) {
			$data = json_decode($buffer);
			$this->assertObjectHasAttribute('title', $data);
		});
		$controller->get();
		ob_end_flush();

		$controller = new RestController([], new FakeRequest);
		ob_start(function($buffer) use($controller) {
			$data = json_decode($buffer);
			$this->assertObjectHasAttribute('title', $data);
			$this->assertEquals($data->title, 'test');
			$this->assertEquals($data->body, 'test_body');
			$controller->params['id'] = $data->id;
			$controller->delete();
		});

		$id = $controller->post();
		ob_end_flush();
	}
}
