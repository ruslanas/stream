<?php

use Stream\App;
use modules\Clients\model\Client;

class ClientTest extends PHPUnit_Extensions_Database_TestCase {
	
	private $_data = [
		'name' => '___TEST_CLIENT___'
	];

	public function getConnection() {
		$app = new App();
		$app->loadConfig();
		$app->connect('test_stream');
		$this->client = new Client($app->pdo);
		return $this->createDefaultDBConnection($app->pdo);
	}
	public function getDataSet() {
		return $this->createFlatXMLDataSet('data/stream.xml');
	}

	public function testClient() {
		$this->assertTrue($this->client instanceof Client);
		$item = $this->client->getById(1);
		$this->assertTrue(!empty($item->name));
	}
	
	public function testGetList() {
		$res = $this->client->getList();
		$this->assertEquals(1, sizeof($res));
	}
	
	public function testFilter() {
		$res = $this->client->filter($this->_data);
		$this->assertEquals(sizeof($res), 0);
		$id = $this->client->save(NULL, $this->_data);
		$res = $this->client->filter($this->_data);
		$this->assertEquals(sizeof($res), 1);
		$newId = $this->client->save($id, $this->_data);
		$this->assertEquals($id, $newId);
		$this->client->delete($id);
	}

	public function tearDown() {

	}
}
