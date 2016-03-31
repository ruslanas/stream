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
        
        $this->req = $this->getMockBuilder(Request::class)->getMock();
        $this->user = $this->getMockBuilder(model\User::class)->getMock();

        $this->controller = new Users\Controller();
        
        $this->controller->inject('request', $this->req);
        $this->controller->inject('user', $this->user);
    
    }

    public function testLoginDisplayForm() {
        
        $this->user->method('authenticate')->willReturn(FALSE);

        $out = $this->controller->login();
        $this->assertContains('<form', $out);

    }

    public function testLoginRedirect() {
    
        $this->user->method('authenticate')->willReturn(TRUE);
    
        $this->controller->login();
        $this->assertTrue($this->controller->redirect() !== FALSE);
    
    }

    public function testDispatch() {

        $out = $this->controller->dispatch('/user/login');
        $this->assertContains('Sign In', $out);

        $out = $this->controller->dispatch('/user');
        $this->assertContains('Sign In', $out);

        $this->expectException(NotFoundException::class);
        $out = $this->controller->dispatch('/user/not_found');
        $this->assertContains('not found', $out);
    
    }

    public function testLogout() {
        $this->controller->logout();
        $this->assertTrue($this->controller->redirect() !== FALSE);
    }

    public function testAdd() {
        
        unset($_SESSION['uid']);

        $out = $this->controller->add();
        $this->assertContains('<form', $out);

    }

    public function tesAddSuccess() {
        $this->req->method('post')->willReturn([
            'email' => 'new@example.com',
            'password' => 'bar',
            'password2' => 'bar'
        ]);

        $this->controller->add();
        $this->assertEquals('/user/login', $this->controller->redirect());

    }

    public function testAddExitingUserRedirect() {
        
        $this->user->method('exists')->willReturn(TRUE);

        $this->req->method('post')->willReturn([
            'email'=>'email@example.com'
        ]);
        
        $this->controller->add();
        $this->assertEquals('/user/add', $this->controller->redirect());

    }

    public function testAddLoggedInRedirect() {        

        $this->user->method('authenticate')->willReturn(TRUE);

        $this->controller->add();
        $this->assertEquals('/', $this->controller->redirect());

    }
}
