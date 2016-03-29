<?php

use modules\Home\Controller;

class HomeControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$app = new App();
		$app->loadConfig();
		$app->connect();
		$this->controller = new Controller();
	}

	public function testHome() {
		ob_start();
		$this->controller->default();
		$res = ob_get_contents();
		ob_end_clean();

		$this->assertContains('<!DOCTYPE html>', $res);
	}
}
