<?php

namespace Stream\Test;

class ControllerTestCase extends \PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->req = $this->getMockBuilder(\Stream\Request::class)->getMock();
        $this->controller->inject('request', $this->req);

    }

}
