<?php

namespace modules\Contributors;

class ControllerTest extends \Stream\Test\ControllerTestCase {
    
    public function setUp() {
    
        $this->controller = new Controller;
        parent::setUp();
    
    }

    public function testOpen() {
        $res = $this->controller->open();
        $this->assertStringStartsWith('<!DOCTYPE html>', $res);    
    }

}
