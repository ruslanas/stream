<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\App;
use Stream\Request;
use Stream\Test\DatabaseTestCase;

use modules\Posts\model\Post;
use modules\Users\model\User;

class PostTest extends DatabaseTestCase {

    protected $stream;

    public function setUp() {

        parent::setUp();

        $this->stream = new Post($this->app->pdo);
        $this->user = new User($this->getRequestMock(), $this->app->pdo);

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
      
        $this->stream->delete(1);
        $res = $this->stream->getList();
        $this->assertEquals(0, count($res));
    
    }

    public function testSave() {
        
        $data = [
            'title' => 'Foo',
            'body' => 'Bar'
        ];

        $record = $this->stream->save(NULL, $data);
        
        $this->assertEquals($record->title, 'Foo');
        $this->assertEquals($record->body, 'Bar');
        
        $data['title'] = 'baz';
        $updatedRec = $this->stream->save($record->id, $data);
        $this->assertEquals($record->id, $updatedRec->id);
        $this->assertEquals($updatedRec->title,'baz');

    }

    public function testGetById() {
        $message = $this->stream->getById(1);
        $this->assertEquals($message->title, 'Test');
    }
}
