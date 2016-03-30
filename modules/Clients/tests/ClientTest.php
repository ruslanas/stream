<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Test\DatabaseTestCase;

use modules\Clients\model\Client;

class ClientTest extends DatabaseTestCase {
	
	private $_data = [
		'name' => '___TEST_CLIENT___'
	];

	public function setUp() {
		parent::setUp();

		$this->client = new Client($this->app->pdo);
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

		$data = $this->client->save(NULL, $this->_data);
		$res = $this->client->filter($this->_data);
		$this->assertEquals(sizeof($res), 1);

		$newData = $this->client->save($data->id, $this->_data);
		$this->assertEquals($data->id, $newData->id);

	}

	public function tearDown() {

	}
}
