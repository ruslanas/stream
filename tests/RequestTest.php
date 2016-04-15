<?php

use Stream\Request;

class RequestTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->request = new Request;
	}

	public function testGetPostData() {
		$data = $this->request->getPostData();
		$this->assertEquals(NULL, $data);

	}

	public function testPost() {

		$_SERVER['REQUEST_METHOD'] = 'HEAD';
		$this->assertEquals(NULL, $this->request->post());

	}

}
