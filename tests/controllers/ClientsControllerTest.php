<?php
class ClientsControllerTest extends PHPUnit_Extensions_Database_TestCase {
    public function getConnection() {
        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');
		$this->controller = new ClientsController([], new Fake\Request([]));
        return $this->createDefaultDBConnection($this->app->pdo);
    }
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data/stream.xml');
    }

    public function testGet() {
        $res = json_decode($this->controller->get());
        $this->assertTrue(is_array($res));
        $this->assertEquals(1, count($res));

        $controller = new ClientsController(['id'=>1], new Fake\Request([]));
        $res = json_decode($controller->get());
        $this->assertEquals('Client Name', $res->name);
    }

    public function testDelete() {
        $controller = new ClientsController(['id' => 1], new Fake\Request([]));
        $res = json_decode($controller->delete());
        $this->assertEquals($res->deleted, '1');
        $this->expectException(NotFoundException::class);
        $controller->get();
    }

    public function testPost() {
        $controller = new ClientsController([], new Fake\Request(NULL, [
            'name' => 'New Client'
        ]));

        $out = json_decode($controller->post());
        $this->assertEquals($out->name, 'New Client');
    }
}
