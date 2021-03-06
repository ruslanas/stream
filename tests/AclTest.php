<?php

use Stream\App;
use Stream\Acl;
use Stream\Test\DatabaseTestCase;

class AclTest extends DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $this->acl = new Acl;
        $this->session = $this->getMockBuilder(\Stream\Session::class)->getMock();

        $this->acl->uses(['Session', $this->session]);
        $this->acl->uses(new \modules\Users\model\User($this->pdo));
    }

    public function testAllow() {

        $this->session->method('get')->with('uid')->willReturn(NULL);

        $this->assertTrue($this->acl->allow('GET', '/'));
        $this->assertTrue($this->acl->allow('GET', '/user/login'));

        $this->assertFalse($this->acl->allow('DELETE', '/user/1'));

        $this->assertTrue($this->acl->allow('POST', '/user/add'));
        $this->assertFalse($this->acl->allow('POST', '/clients.json'));
    }

    public function testAllowTasksAnonymosForbidden() {

        $this->session->method('get')->with('uid')->willReturn(NULL);

        $this->assertFalse($this->acl->allow('GET', '/tasks.json'));
        $this->assertFalse($this->acl->allow('POST', '/tasks.json'));
        $this->assertFalse($this->acl->allow('DELETE', '/tasks/10.json'));
    }

    public function testAllowQueryParams() {
        $this->session->method('get')->with('uid')->willReturn(NULL);
        $this->assertTrue($this->acl->allow('GET', '/?XDEBUG_SESSION_START=1'));
    }

    public function testAllowUserRegistration() {
        $this->session->method('get')->with('uid')->willReturn(NULL);
        $this->assertTrue($this->acl->allow('POST', '/users/register.json'));
    }

    public function testAllowManager() {

        // can't create post, but can add client

        $this->session->method('get')->with('uid')->willReturn(2);

        $this->assertTrue($this->acl->allow('POST', '/clients.json'));

        $this->assertFalse($this->acl->allow('DELETE', '/clients/1.json'));

        $this->assertFalse($this->acl->allow('POST', '/posts.json'));
        $this->assertFalse($this->acl->allow('PUT', '/clients.json'));
    }

    public function testAllowNotLoggedInForbiden() {

        $this->session->method('get')->with('uid')->willReturn(NULL);

        $this->assertFalse($this->acl->allow('GET', '/tasks.json'));
    }

    public function testAllowDeleteOwnTask() {

        $this->session->method('get')->with('uid')->willReturn(2);

        $this->assertTrue($this->acl->allow('DELETE', '/tasks/2.json'));
    }

    ////////////////////////////////////////
    // DataStore will validate and reject //
    ////////////////////////////////////////

    /**
     * Assert that user cannot delete other users tasks
     */
    // public function testAllowDenyDeleteTask() {
    //     $this->session->method('get')->with('uid')->willReturn(3);
    // 	$allow = $this->acl->allow('DELETE', '/tasks/2.json');
    //     $this->assertFalse($allow);
    // }
}
