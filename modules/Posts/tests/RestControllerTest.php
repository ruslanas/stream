<?php

use Stream\Exception\UnknownMethodException;

use modules\Posts\Controller;

class RestControllerTest extends PHPUnit_Extensions_Database_TestCase {
    public function getConnection() {
        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');
		$this->controller = new Controller([], new Fake\Request);
        return $this->createDefaultDBConnection($this->app->pdo);
    }
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data/stream.xml');
    }

	public function testApi() {

		$out = $this->controller->get();
		$data = json_decode($out);
		$this->assertTrue(is_array($data));
		$this->assertEquals(count($data), 1);
		$this->assertObjectHasAttribute('title', $data[0]);

		$controller = new Controller(['id' => 1], new Fake\Request);
		$out = $controller->get();
		$data = json_decode($out);
		$this->assertObjectHasAttribute('title', $data);

		$controller = new Controller([], new Fake\Request);
		$out = $controller->post();

		$data = json_decode($out);
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
		$controller = new Controller(['id' => 1], new Fake\Request);
		$controller->delete();

		$controller = new Controller([], new Fake\Request);
		$data = json_decode($controller->get());
		$this->assertTrue(is_array($data));
		$this->assertEquals(0, count($data));

		$this->expectException(Exception::class);
		$controller = new Controller([], new Fake\Request);
		$controller->delete();

	}
}
