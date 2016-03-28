<?php

class UserTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$app = new App();
		$app->loadConfig();
		$app->connect('test_stream');
		$this->user = new User(new Fake\Request, $app->pdo);
	}
	public function tearDown() {
		// nothing
	}
	public function testAuthenticate() {
		$auth = $this->user->authenticate();
		$this->assertFalse($auth);
	}
	public function testExists() {
		$x = $this->user->exists(['email' => 'info@example.com']);
		$this->assertTrue($x);
		$y = $this->user->exists(['email' => 'does_not_exist@example.com']);
		$this->assertFalse($y);
	}
	public function testValid() {
		$isValid = $this->user->valid([]);
		$this->assertFalse($isValid);

		$isValid = $this->user->valid([
			'email' => 'test',
			'password' => 'foo',
			'password2' => 'bar'
		]);
		$this->assertFalse($isValid);
		$err = $this->user->error();
		$this->assertEquals(2, count($err));
		$this->assertArrayHasKey('email', $err);
		$this->assertArrayHasKey('password2', $err);

		$isValid = $this->user->valid([
			'email' => 'test@example.com',
			'password' => 'foo',
			'password2' => 'foo'
		]);
		$this->assertEquals(0, count($this->user->error()));
		$this->assertTrue($isValid);
	}
}
