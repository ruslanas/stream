<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Test\DatabaseTestCase;

use modules\Users\model\User;

class UserTest extends DatabaseTestCase {
    
    public function setUp() {
        parent::setUp();
        $this->user = new User($this->getRequestMock(), App::getInstance()->pdo);
    }

    public function testAuthenticate() {
        $auth = $this->user->authenticate();
        $this->assertFalse($auth);
        $_SESSION['uid'] = 1;
        $this->assertTrue($this->user->authenticate());
        // user does not exist
        $_SESSION['uid'] = 1000;
        $this->assertFalse($this->user->authenticate());
    }

    public function testExists() {
        $x = $this->user->exists(['email' => 'info@example.com']);
        $this->assertTrue($x);
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
        $this->assertEquals('test@example.com', $data->email);
    }

    public function testGetList() {
        $data = $this->user->getList();
        $this->assertEquals(2, count($data));
    }
}
