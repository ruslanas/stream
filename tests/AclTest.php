<?php

use Stream\App;
use Stream\Acl;
use Stream\Test\DatabaseTestCase;

class AclTest extends DatabaseTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->acl = new Acl();
	}

	public function testAllow() {

		$this->assertTrue($this->acl->allow('GET', '/'));
		$this->assertTrue($this->acl->allow('GET', '/user/login'));
		$this->assertFalse($this->acl->allow('DELETE', '/user/1'));
		$this->assertTrue($this->acl->allow('POST', '/user/add'));
		$this->assertFalse($this->acl->allow('POST', '/clients.json'));
	}

	public function testAllowAdmin() {		
		// admin can create and delete
		$_SESSION['uid'] = 1;
		$this->assertTrue($this->acl->allow('GET', '/'));
		$this->assertTrue($this->acl->allow('POST', '/clients.json'));
		$this->assertTrue($this->acl->allow('DELETE', '/clients/1.json'));
	}

	public function testAllowManager() {

		// can't create post, but can add client
		$_SESSION['uid'] = 2;
		$this->assertTrue($this->acl->allow('POST', '/clients.json')); // ???
		$this->assertFalse($this->acl->allow('DELETE', '/clients/1.json'));
		$this->assertFalse($this->acl->allow('POST', '/posts.json'));
		$this->assertFalse($this->acl->allow('PUT', '/clients.json'));

	}
}
