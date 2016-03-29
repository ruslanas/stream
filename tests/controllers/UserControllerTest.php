<?php

use Stream\Exception\NotFoundException;

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

	public function testDispatch() {
		$controller = new UserController(new Fake\Request);

		$out = $controller->dispatch('/user/login');
		$this->assertContains('Sign In', $out);

		$out = $controller->dispatch('/user');
		$this->assertContains('Sign In', $out);

		$this->expectException(NotFoundException::class);
		$out = $controller->dispatch('/user/not_found');
		$this->assertContains('not found', $out);
	}

	public function testLogout() {
		$controller = new UserController();
		$controller->logout();
		$this->assertTrue($controller->redirect() !== FALSE);
	}

	public function testAdd() {
		unset($_SESSION['uid']);

		$controller = new UserController();
		$out = $controller->add();
		$this->assertContains('<form', $out);

		$controller = new UserController(new Fake\Request([
			'email' => 'new@example.com',
			'password' => 'bar',
			'password2' => 'bar'
		]));

		$controller->add();
		$this->assertEquals('/user/login', $controller->redirect());

		$controller->add();
		// user already exists
		$this->assertEquals('/user/add', $controller->redirect());
		
		$_SESSION['uid'] = 1;
		$controller->add();
		// user can add only himself
		$this->assertEquals('/', $controller->redirect());
	}
}
