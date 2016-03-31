<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Test\DatabaseTestCase;
use Stream\Request;

use modules\Users\model\User;

class UserTest extends DatabaseTestCase {
    
    public function setUp() {
        
        parent::setUp();

        $this->req = $this->getMockBuilder(Request::class)->getMock();

        $this->user = new User(NULL, App::getInstance()->pdo);

        $this->user->inject('request', $this->req);

    }

    // something wrong here
    public function testAuthenticateFail() {

        $this->req->method('post')->willReturn([
            'email' => 'foo',
            'password' => 'bar',
            'password2' => 'baz'
        ]);

        $auth = $this->user->authenticate();
        $this->assertFalse($auth);
    }

    public function testAuthenticateSuccess() {
        $_SESSION['uid'] = 1;
        $this->assertTrue($this->user->authenticate());
    }

    public function testAuthenticateUserNotFound() {
        $_SESSION['uid'] = 1000;
        $this->assertFalse($this->user->authenticate());
    }

    public function testExists() {
        $x = $this->user->exists(['email' => 'info@example.com']);
        $this->assertTrue($x);
    }

    public function testExistsNot() {
        $y = $this->user->exists(['email' => 'does_not_exist@example.com']);
        $this->assertFalse($y);
    }
    
    public function testAdd() {
        $this->assertFalse($this->user->add([]));
        $data = ['email' => 'xxx@example.com', 'password' => 'password'];
        $this->assertFalse($this->user->exists($data));
        $id = $this->user->add($data);
        $this->assertTrue(is_numeric($id), 'Must return user Id');
        $this->assertTrue($this->user->exists($data));
    }

    public function testValid() {
        $isValid = $this->user->valid([]);
        $this->assertFalse($isValid);

        $isValid = $this->user->valid([
            'email' => 'test',
            'password' => 'foo',
            'password2' => 'bar'
        ]);
        
        $this->assertFalse($isValid);
        
        $err = $this->user->error();
        $this->assertEquals(2, count($err));
        $this->assertArrayHasKey('email', $err);
        $this->assertArrayHasKey('password2', $err);

        $isValid = $this->user->valid([
            'email' => 'test@example.com',
            'password' => 'foo',
            'password2' => 'foo'
        ]);
        $this->assertEquals(0, count($this->user->error()));
        $this->assertTrue($isValid);
    }

    public function testGetById() {
        $data = $this->user->getById(1);
        $this->assertEquals('admin@example.com', $data->email);
    }

    public function testGetList() {
        $data = $this->user->getList();
        $this->assertEquals(2, count($data));
    }
}
