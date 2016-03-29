<?php

class ControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->controller = new modules\Events\Controller;
	}
	public function testGet() {
		$this->assertObjectHasAttribute('id', json_decode($this->controller->get()));
	}
	public function testDelete() {
		$this->controller->delete();
		$this->assertObjectHasAttribute('id', json_decode($this->controller->get()));
	}
	public function testPost() {
		$this->controller->post();
		$this->assertObjectHasAttribute('id', json_decode($this->controller->get()));
	}
	
	public function testPut() {
		$this->controller->put();
		$this->assertObjectHasAttribute('id', json_decode($this->controller->get()));
	}

	public function testAllowHeaderField() {
		$allow = '';
		try {
			$this->controller->head();
		} catch (Stream\Exception\UnknownMethodException $e) {
			$allow = $e->getAllow();
		}
		$this->assertEquals('Allow: DELETE, GET, POST, PUT', $allow);
	}
}
