<?php
class UserControllerTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->app = new App();
		$this->app->loadConfig();
		$this->app->connect('test_stream');
	}

	public function testLogin() {
		$controller = new UserController(new Fake\Request);
		$out = $controller->login();
		$this->assertContains('<form', $out);

		$controller = new UserController(new Fake\Request(['email'=>'test@example.com', 'password' => 'foo']));
		$controller->login();
		$this->assertTrue($controller->redirect() !== FALSE);
	}
}
