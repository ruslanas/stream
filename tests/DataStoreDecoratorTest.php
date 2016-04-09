<?php

class DataStoreDecoratorTest extends \Stream\Test\DatabaseTestCase {
    
    public function setUp() {

        parent::setUp();

        $this->task = new \modules\Tasks\Decorators\Task(\Stream\App::getConnection('test_stream'));
    
    }

    public function testRead() {
        $title = $this->task->read(1)->title;
        $this->assertEquals('Implement new feature', $title);
    }

    public function testReadAll() {
        $title = $this->task->read()[0]->title;
        $this->assertEquals('Implement new feature', $title);
    }

    public function testUpdate() {

        //$task = $this->taskDecorator->read(1)->delegate("admin@stream.wri.lt");
    
        $this->task->id = 1;

        $this->assertEquals('info@example.com', $this->task->delegate->email);

        $this->task->update(NULL, [
        
            'title' => "Delegated: ".$this->task->title,
            'delegate_id' => 1
        
        ]);

        $this->assertEquals('Delegated: Implement new feature', $this->task->title);
        $this->assertEquals('admin@example.com', $this->task->delegate->email);
    }

}
