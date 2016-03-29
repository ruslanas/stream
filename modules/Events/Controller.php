<?php

namespace modules\Events;
use Stream\Interfaces\RestApi;

class Controller extends \Stream\RestController {
	
	protected $event;

	public function __construct() {
		parent::__construct();
		$this->event = new Model\Event;
	}

	final public function get() {
		return $this->event->read();
	}
	
	final public function post() {
		return $this->event->create();
	}
	
	final public function delete() {
		return $this->event->delete();
	}

	final public function put() {
		return $this->event->update();
	}
}
