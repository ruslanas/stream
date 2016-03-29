<?php

use Stream\Exception\ForbiddenException;

class AppTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
	}
	
	public function testApp() {
		App::deleteInstance();
		$app = App::getInstance();
		$this->assertInstanceOf(App::class, $app);
		$app = new App([], new Cache);
		$this->assertEquals(NULL, $app->non_existing_property);
	}

	public function testConnect() {
		$this->expectException(Exception::class);
		$this->app = App::getInstance();
		$this->app->loadConfig();
		$this->assertInstanceOf('App', $this->app);
		$this->app->connect();
		$this->assertInstanceOf('PDO', $this->app->pdo);
		$this->app->connect('test_database');
		$this->assertInstanceOf('PDO', $this->app->pdo);
		$this->app->connect('not_existing_database_configuration_key');
	}
	public function testLoadConfig() {
		// should be merged with default config options
		$app = new App(['test_conf' => true]);
		$conf = $app->loadConfig();
		$this->assertArrayHasKey('test_conf', $conf);
	}
	public function testRest() {
		$this->expectException(Exception::class);
		$app = new App();
		$app->rest('/endpoint', (object)[]);
	}

	public function testDispatch() {
		$this->expectException(ForbiddenException::class);
		$app = new App();
		$app->dispatch('/');
	}

	public function testMatch() {
		$cls = new ReflectionClass('App');
		$meth = $cls->getMethod('match');
		$meth->setAccessible('true');

		$app = new App();
		$params = $meth->invokeArgs($app, ['/foo/:foo/bar/:bar/baz/:baz', '/foo/10/bar/20/baz/baz?boo=boo']);
		$this->assertEquals($params['foo'], 10);
		$this->assertEquals($params['bar'], 20);
		$this->assertEquals($params['baz'], 'baz');
	}
}
