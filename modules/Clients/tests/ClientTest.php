<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Test\DatabaseTestCase;

use modules\Clients\model\Client;

class ClientTest extends DatabaseTestCase {
	
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

		$this->assertEquals('Client Name', $res[0]->name);
		$this->assertEquals('admin@example.com', $res[0]->user->email);

	}
	
	public function testFilter() {

		$filter = ['name' => 'test'];
		
		$res = $this->client->filter($filter);
		$this->assertEquals(count($res), 0);

		$data = $this->client->save(NULL, ['name' => 'test', 'email' => 'test@example.com']);

		$res = $this->client->filter($filter);
		$this->assertEquals(count($res), 1);
		$this->assertEquals('test@example.com', $res[0]->email);

	}

	public function tearDown() {

	}
}
