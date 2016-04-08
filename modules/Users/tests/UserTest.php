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

        $this->req = $this->getMockBuilder(Request::class)
            ->getMock();

        $this->user = new User($this->pdo);

    }

    // contributor suggested test
    public function testValidPasswordsIdentical() {

        $isValid = $this->user->valid([
            'email' => 'test@example.com',
            'password' => '123foo',
            'password2' => 123
        ]);

        $this->assertCount(1, $this->user->error());
        $this->assertFalse($isValid);

    }

    // something wrong here
    public function testAuthenticateFail() {

        $this->req->method('post')->willReturn([
            'email' => 'foo',
            'password' => 'bar',
            'password2' => 'baz'
        ]);

        $auth = $this->user->authenticate($this->req);

        $this->assertFalse($auth);
    }

    public function testAuthenticateSuccess() {

        $_SESSION['uid'] = 1;
        $this->assertTrue($this->user->authenticate($this->req));

    }

    public function testAuthenticateUserNotFound() {

        $_SESSION['uid'] = 1000;
        $this->assertFalse($this->user->authenticate($this->req));

    }

    public function testExists() {
        $x = $this->user->exists(['email' => 'info@example.com']);
        $this->assertTrue($x);
    }

    public function testExistsNot() {
        $y = $this->user->exists(['email' => 'does_not_exist@example.com']);
        $this->assertFalse($y);
    }

    public function testAddEmpty() {

        $this->assertFalse($this->user->add([]));

    }

    public function testAdd() {

        $data = ['email' => 'xxx@example.com', 'password' => '******'];

        $this->assertFalse($this->user->exists($data));

        $res = $this->user->add($data);

        $this->assertTrue(is_numeric($res->id), 'Id is numeric');

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
        $this->assertEquals('', $data->password);
    }

    public function testGetList() {

        $data = $this->user->getList();

        $this->assertGreaterThan(0, count($data));

        $this->assertObjectHasAttribute('email', $data[0]);
        $this->assertObjectHasAttribute('password', $data[0]);

    }
}
