<?php

use modules\Clients\Controller;

class ClientsControllerTest extends PHPUnit_Extensions_Database_TestCase {
    public function getConnection() {
        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');
		$this->controller = new Controller([], new Fake\Request([]));
        return $this->createDefaultDBConnection($this->app->pdo);
    }
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data/stream.xml');
    }

    public function testGet() {
        $res = json_decode($this->controller->get());
        $this->assertTrue(is_array($res));
        $this->assertEquals(1, count($res));

        $controller = new Controller(['id'=>1], new Fake\Request([]));
        $res = json_decode($controller->get());
        $this->assertEquals('Client Name', $res->name);
    }

    public function testDelete() {
        $controller = new Controller(['id' => 1], new Fake\Request([]));
        $res = json_decode($controller->delete());
        $this->assertEquals($res->deleted, '1');
        $this->expectException(Stream\Exception\NotFoundException::class);
        $controller->get();
    }

    public function testPost() {
        $controller = new Controller([], new Fake\Request(NULL, [
            'name' => 'New Client'
        ]));

        $out = json_decode($controller->post());
        $this->assertEquals($out->name, 'New Client');
    }
}
