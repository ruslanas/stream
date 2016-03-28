<?php

class RestControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->app = new App();
		$this->app->loadConfig();
		$this->app->connect('test_stream');
		$this->controller = new RestController([], new Fake\Request);
	}
	public function testApi() {

		$out = $this->controller->get();
		$data = json_decode($out);
		$this->assertTrue(is_array($data));
		$this->assertEquals(count($data), 1);
		$this->assertObjectHasAttribute('title', $data[0]);

		$controller = new RestController(['id' => 1], new Fake\Request);
		$out = $controller->get();
		$data = json_decode($out);
		$this->assertObjectHasAttribute('title', $data);

		$controller = new RestController([], new Fake\Request);
		$out = $controller->post();

		$data = json_decode($out);
		$this->assertObjectHasAttribute('title', $data);
		$this->assertEquals($data->title, 'test');
		$this->assertEquals($data->body, 'test_body');
		$controller->params['id'] = $data->id;
		$controller->delete();

	}
	public function testPut() {
		$this->expectException(UnknownMethodException::class);
		$this->controller->put();
	}
}
