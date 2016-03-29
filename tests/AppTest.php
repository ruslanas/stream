<?php

use Stream\Exception\ForbiddenException;
use Stream\App;
use Stream\Cache;

class AppTest extends PHPUnit_Framework_TestCase {
	
	public function testApp() {
		App::deleteInstance();
		$app = App::getInstance();
		$this->assertInstanceOf(App::class, $app);
		$this->app = new App([], new Cache);
		$this->assertEquals(NULL, $this->app->non_existing_property);
	}

	public function testSerialize() {
		$app = new App();
		$res = $app->serialize((object)['id'=>1]);
		$this->assertJsonStringEqualsJsonString('{"id":1}', $res);
	}

	public function testConnect() {
		$this->expectException(Exception::class);
		$this->app = App::getInstance();
		$this->app->loadConfig();
		$this->assertInstanceOf(App::class, $this->app);
		$this->app->connect();
		$this->assertInstanceOf(PDO::class, $this->app->pdo);
		$this->app->connect('test_database');
		$this->assertInstanceOf(PDO::class, $this->app->pdo);
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

	public function testDispatchException() {
		$this->expectException(ForbiddenException::class);
		$app = new App();
		$app->dispatch('/');
	}

	public function testMatch() {
		$cls = new ReflectionClass(App::class);
		$meth = $cls->getMethod('match');
		$meth->setAccessible('true');

		$app = new App();
		$params = $meth->invokeArgs($app, ['/foo/:foo/bar/:bar/baz/:baz', '/foo/10/bar/20/baz/baz?boo=boo']);
		$this->assertEquals($params['foo'], 10);
		$this->assertEquals($params['bar'], 20);
		$this->assertEquals($params['baz'], 'baz');
	}
}
