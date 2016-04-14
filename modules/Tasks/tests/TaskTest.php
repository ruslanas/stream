<?php

namespace modules\Task\tests;

use modules\Tasks;

class TaskTest extends \Stream\Test\DatabaseTestCase {

    public function setUp() {

        parent::setUp();

        $this->session = $this->getMockBuilder(\Stream\Session::class)->getMock();

        $this->task = new Tasks\model\Task($this->pdo);

        $this->task->use(['Session', $this->session]);

    }


    public function testSearchComplex() {
    
        $found = $this->task->filter(
            
            // inst ::= [op, [col|inst, val|[col, val]], [inst]]

            [' and ',
                [' like ', 'tasks.title', '%implement%'],
                [' or ', ['tasks.user_id', 1], ['delegate_id', 1]]
            ]
        
        );

        $this->assertEquals(1, count($found));

    }

    public function testSearchByTitle() {
        
        $found = $this->task->filter(['title', 'Implement new feature']);

        $this->assertGreaterThan(0, count($found));
        $this->assertObjectHasAttribute('id', $found[0]);
        $this->assertEquals('Implement new feature', $found[0]->title);

    }

    public function testSearchAnd() {

        $found = $this->task->filter(['and',
            ['tasks.id', 1],
            ['like', 'tasks.title', '%new%']
        ]);

        $this->assertEquals('Implement new feature', $found[0]->title);

    }

    public function testSearchEmpty() {
        $found = $this->task->filter([]);
        $this->assertEquals(0, count($found));
    }

    public function testSave() {

        $data = $this->task->create(['title' => 'todo', 'description' => 'implement feature']);
        $this->assertEquals('todo', $data->title);
        $this->assertEquals('implement feature', $data->description);

    }

    public function testGet() {

        $data = $this->task->read();

        $this->assertGreaterThan(0, count($data));
        $this->assertObjectHasAttribute('id', $data[0]);
        $this->assertObjectHasAttribute('title', $data[0]);
        $this->assertObjectHasAttribute('description', $data[0]);
        $this->assertObjectHasAttribute('focus', $data[0]);
        $this->assertObjectHasAttribute('created', $data[0]);
        $this->assertObjectHasAttribute('modified', $data[0]);

    }

    public function testUpdate() {

        $data = $this->task->update(1, ['delegate_id' => 2]);
        
        $this->assertEquals(2, $data->delegate->id);

    }

    public function testUpdateThrowsException() {
        $this->expectException(\Exception::class);
        $this->task->update(1, []);
    }

    public function testDelete() {

        $data = $this->task->read();
        $count = count($data);

        // user 2 has can only delete his own messages
        $this->task->delete(1, 2);

        $data = $this->task->read();

        $this->assertEquals($count, count($data));

    }

    public function testDelegate() {

        $task = (new Tasks\Decorators\Task($this->pdo))
            
            ->read(2)->delegate("behat@stream.wri.lt");
        
        $this->assertEquals(3, $task->delegate->id);

    }

    public function testDelegateNewUser() {

        // it should create new user
        $task = (new Tasks\Decorators\Task($this->pdo))
            
            ->read(2)
            ->delegate("admin@stream.wri.lt");

        $this->assertEquals(4, $task->delegate->id);
        $this->assertEquals('admin@stream.wri.lt', $task->delegate->email);

    }

    public function testReject() {
        
        $task = (new Tasks\Decorators\Task($this->pdo))
            ->read(1)
            ->reject();

        $this->assertEquals(0, $task->accepted);

    }

    public function testUpdateException() {
        
        $this->session->expects($this->once())
            ->method('get')
            ->with('uid')->willReturn(1);

        $this->expectException(\Exception::class);

        $this->task->update(1, [
            'user_id' => 2
        ]);
    
    }

}
