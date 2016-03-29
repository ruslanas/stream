<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;

use modules\Posts\model\Post;
use modules\Users\model\User;

class PostTest extends PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var Stream
     */
    protected $stream;

    public function getConnection() {
        $this->app = new App();
        $this->app->loadConfig();
        $this->app->connect('test_stream');
        $this->stream = new Post($this->app->pdo);
        $this->user = new User(new Fake\Request, $this->app->pdo);
        return $this->createDefaultDBConnection($this->app->pdo);
    }
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data/stream.xml');
    }

    public function testStream() {
        $stream = new Post($this->app->pdo);
        $this->assertInstanceOf(Post::class, $stream);
    }

    public function testGetList() {
        $res = $this->stream->getList();
        $this->assertObjectHasAttribute('title', $res[0]);
        $this->assertEquals('Test', $res[0]->title);
    }

    public function testDelete() {
        $id = $this->stream->save(NULL, [
            'title' => 'Foo',
            'body' => 'Bar',
            'ignore_this_column' => 'Ignored' 
        ]);
        $res = $this->stream->getList();
        $this->assertEquals(sizeof($res), 2);
        $this->stream->delete($id);
        $res = $this->stream->getList();
        $this->assertEquals(sizeof($res), 1);
    }

    public function testSave() {
        $data = [
            'title' => 'Foo',
            'body' => 'Bar'
        ];
        $id = $this->stream->save(NULL, $data);
        $res = $this->stream->getById($id);
        $this->assertEquals($res->title, 'Foo');
        $this->assertEquals($res->body, 'Bar');
        
        $data['title'] = 'baz';
        $newId = $this->stream->save($id, $data);
        $this->assertEquals($id, $newId);

        $res = $this->stream->getById($id);
        $this->assertEquals($res->title,'baz');
        $this->stream->delete($id);
    }

    public function testGetById() {
        $message = $this->stream->getById(1);
        $this->assertEquals($message->title, 'Test');
    }
}
