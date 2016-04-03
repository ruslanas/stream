<?php

use Stream\App;
use modules\Home\Controller;

class HomeControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->controller = new Controller;
	}

	public function testHome() {
		$res = $this->controller->index();

		$this->assertContains('<!DOCTYPE html>', $res);
	}
}
