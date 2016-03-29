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
		return json_encode($this->event->read());
	}
	
	final public function post() {
		return json_encode($this->event->create());
	}
	
	final public function delete() {
		return json_encode($this->event->delete());
	}

	final public function put() {
		return json_encode($this->event->update());
	}
}
