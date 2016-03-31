<?php

use Stream\App;
use modules\Home\Controller;

class HomeControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$app = new App();
		$app->loadConfig();
		$app->connect('test_stream');

		$this->controller = new Controller();
	}

	public function testHome() {
		$res = $this->controller->default();

		$this->assertContains('<!DOCTYPE html>', $res);
	}
}
