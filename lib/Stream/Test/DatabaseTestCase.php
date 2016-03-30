<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream\Test;

use \Stream\Request;
use \Stream\App;

class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    public function getConnection() {
        
        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');

        return $this->createDefaultDBConnection($this->app->pdo);
    }
    
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data/stream.xml');
    }

    protected function getRequestMock($post = NULL, $raw = NULL) {
        
        $postData = ($post === NULL) ? ['email' => 'foo', 'password' => 'bar'] : $post;
        $rawPostData = ($raw === NULL) ? ['title' => 'test', 'body' => 'test_body'] : $raw;

        $req = $this->getMockBuilder(Request::class)
        ->getMock();

        $req->method('post')->willReturn($postData);
        $req->method('getPostData')->willReturn($rawPostData);

        return $req;
    }

}