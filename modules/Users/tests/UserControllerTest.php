<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Exception\NotFoundException;
use Stream\Test\DatabaseTestCase;
use Stream\Request;

use modules\Users;
use modules\Users\model;

class UserControllerTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {

        // model will use be initialized with last successful connection
        App::getConnection('test_stream');
        
        $this->req = $this->getMockBuilder(Request::class)->getMock();
        $this->user = $this->getMockBuilder(model\User::class)->getMock();

        $this->controller = new Users\Controller;
        
        $this->controller->inject('params', []);
        $this->controller->inject('request', $this->req);
        $this->controller->inject('user', $this->user);
    
    }

    public function testLoginError() {
        $this->controller->inject('params', ['action' => 'login']);
        $this->user->method('authenticate')->willReturn(false);
        $out = $this->controller->post();
        $this->assertObjectHasAttribute('error', $out);
    }

}
