<?php
namespace Fake;

class Request extends \Stream\Request {
	private $_post = [
		'post' => ['email' => 'foo', 'password' => 'bar'],
		'raw_post_data' => ['title' => 'test', 'body' => 'test_body']
	];
	public function __construct($data = NULL) {
		if($data !== NULL) {
			$this->_post['post'] = $data;
		}
	}
	public function post() {
		return $this->_post['post'];
	}
	public function getPostData() {
		return $this->_post['raw_post_data'];
	}
}
