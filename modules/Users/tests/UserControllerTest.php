<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Exception\NotFoundException;
use Stream\Test\DatabaseTestCase;

use modules\Users\Controller;

class UserControllerTest extends DatabaseTestCase {
    
    public function setUp() {
        parent::setUp();
    }

    public function testLogin() {
        
        $controller = new Controller($this->getRequestMock());
        
        $out = $controller->login();
        $this->assertContains('<form', $out);

        $controller = new Controller($this->getRequestMock(['email'=>'test@example.com', 'password' => 'foo']));
        
        $controller->login();
        $this->assertTrue($controller->redirect() !== FALSE);
    }

    public function testDispatch() {
        $controller = new Controller($this->getRequestMock());

        $out = $controller->dispatch('/user/login');
        $this->assertContains('Sign In', $out);

        $out = $controller->dispatch('/user');
        $this->assertContains('Sign In', $out);

        $this->expectException(NotFoundException::class);
        $out = $controller->dispatch('/user/not_found');
        $this->assertContains('not found', $out);
    }

    public function testLogout() {
        $controller = new Controller();
        $controller->logout();
        $this->assertTrue($controller->redirect() !== FALSE);
    }

    public function testAdd() {
        unset($_SESSION['uid']);

        $controller = new Controller();
        $out = $controller->add();
        $this->assertContains('<form', $out);

        $controller = new Controller($this->getRequestMock([
            'email' => 'new@example.com',
            'password' => 'bar',
            'password2' => 'bar'
        ]));

        $controller->add();
        $this->assertEquals('/user/login', $controller->redirect());

        $controller->add();
        // user already exists
        $this->assertEquals('/user/add', $controller->redirect());
        
        $_SESSION['uid'] = 1;
        $controller->add();
        // user can add only himself
        $this->assertEquals('/', $controller->redirect());
    }
}
