<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use modules\Events\model\Event;

class EventTest extends PHPUnit_Extensions_Database_TestCase {
	
	public function getConnection() {
		
		$app = new App();
		
		$app->loadConfig();
		$app->connect('test_stream');

		$this->event = new Event($app->pdo);
		
		return $this->createDefaultDBConnection($app->pdo);
	
	}
	
	public function getDataSet() {
		return $this->createFlatXMLDataSet('data/stream.xml');
	}

	public function testRead() {
		$r = $this->event->read(1);
		$this->assertEquals('meeting', $r->type);
		$r = $this->event->read();
		$this->assertTrue(is_array($r));
	}

	public function testCreate() {
		
		$res = $this->event->create([
			'type' => 'call',
			'title' => 'talk',
			'description' => 'smalltalk'
		]);

		$this->assertEquals(2, $res->id);
		$this->assertEquals('call', $res->type);
		$this->assertEquals('talk', $res->title);
		$this->assertEquals('smalltalk', $res->description);

	}

	public function testDelete() {
		$res = $this->event->delete(1);
		$this->assertEquals('meeting', $res->type);
	}

	public function testUpdate() {
		$res = $this->event->update(1, [
			'type' => 'reception'
		]);
		$this->assertEquals('reception', $res->type);
	}
}
