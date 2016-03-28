<?php

class AclTest extends PHPUnit_Framework_TestCase {
	public function testAllow() {
		$acl = new Acl();
		$this->assertTrue($acl->allow('GET', '/'));
		$this->assertTrue($acl->allow('GET', '/user/login'));
		$this->assertFalse($acl->allow('DELETE', '/user/1'));
		$this->assertTrue($acl->allow('POST', '/user/add'));
		$this->assertFalse($acl->allow('POST', '/clients.json'));
		
		// admin can create and delete
		$_SESSION['uid'] = 1;
		$this->assertTrue($acl->allow('GET', '/'));
		$this->assertTrue($acl->allow('POST', '/clients.json'));
		$this->assertTrue($acl->allow('DELETE', '/clients/1.json'));
		
		// can't create post, but can add client
		$_SESSION['uid'] = 2;
		$this->assertTrue($acl->allow('POST', '/clients.json')); // ???
		$this->assertFalse($acl->allow('DELETE', '/clients/1.json'));
		$this->assertFalse($acl->allow('POST', '/posts.json'));
		$this->assertFalse($acl->allow('PUT', '/clients.json'));
	}
}
