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

    public function testLoginDisplayForm() {
        
        $this->user->method('authenticate')->willReturn(FALSE);

        $out = $this->controller->login();
        $this->assertContains('action="/user/login"', $out);
        $this->assertStringStartsWith('<!DOCTYPE html>', $out);

    }

    public function testLoginRedirect() {
    
        $this->user->method('authenticate')->willReturn(TRUE);
    
        $this->controller->login();
        $this->assertEquals('/', $this->controller->redirect());
    
    }

    public function testDispatch() {

        $this->controller->inject('params', ['action' => 'login']);
        $out = $this->controller->dispatch();

        $this->assertStringStartsWith('<!DOCTYPE html>', $out);
        $this->assertContains('action="/user/login"', $out);
    }

    public function testDispatchDefaultAction() {
    
        $this->controller->inject('params', ['action' => '']);
        $out = $this->controller->dispatch();
        $this->assertContains('Sign In', $out);
    
    }

    public function testDispatchNotFound() {

        $this->controller->inject('params', ['action' => 'not_found']);
        $this->expectException(NotFoundException::class);
        $out = $this->controller->dispatch();
    
    }

    public function testLogout() {

        $this->controller->logout();
        $this->assertTrue($this->controller->redirect() !== FALSE);

    }

    public function testAddForm() {
        
        unset($_SESSION['uid']);

        $out = $this->controller->add();
        $this->assertContains('action="/user/add"', $out);

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

    public function testAddNotAuthenticatedNotExistingValid() {
        
        $this->user->method('authenticate')->willReturn(FALSE);
        $this->req->method('post')->willReturn(['email'=>'valid@example.ccom']);
        $this->user->method('valid')->willReturn(TRUE);

        $this->controller->add();
        $this->assertEquals('/user/login', $this->controller->redirect());
    
    }
}
