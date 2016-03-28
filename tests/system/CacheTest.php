<?php
class CacheTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->cache = new Cache();
	}

	public function testDelete() {
		$this->cache->store('test', '<html>');
		$data = $this->cache->fetch('test');
		$this->assertEquals('<html>', $data);
		$this->cache->delete('test');
		$data = $this->cache->fetch('test');
		$this->assertFalse($data);
	}
}
