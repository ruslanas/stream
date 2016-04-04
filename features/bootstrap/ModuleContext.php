<?php

session_start();

use PHPUnit_Framework_Assert as UT;
use Stream\App, Stream\Request, Stream\Acl;
use modules\Tasks\model\Task;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class ModuleContext implements Context, SnippetAcceptingContext {

    private $title;
    private $description;
    private $result;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct() {

        
        $this->app = new App;
        
        $this->app->loadConfig();

        $this->pdo = App::getConnection('test_stream');

        // There's a room for improvement
        $this->app->domain('/tasks/:action', \modules\Tasks\Controller::class);
        $this->app->domain('/tasks/:action/:id', \modules\Tasks\Controller::class);

        $this->app->rest(['/acl/:action'], \modules\Acl\Controller::class);

        $this->app->rest(['/tasks.json', '/tasks/:id.json'], \modules\Tasks\Api::class);

        $this->app->rest(['/user/:action'], \modules\Users\Controller::class);

        $this->app->domain('/contributors/:action', \modules\Contributors\Controller::class);

        $this->acl = \Mockery::mock(Acl::class);
        $this->req = \Mockery::mock(Request::class);

        $this->app->inject('acl', $this->acl);
        $this->app->inject('request', $this->req);

    }

    /**
     * @Given tab :arg1 is open
     */
    public function tabIsOpen($arg1) {

        $this->acl->shouldReceive('allow')->andReturn(TRUE);
        $this->req->shouldReceive('getMethod')->andReturn('GET');
        $this->req->shouldReceive('getHeaders')->andReturn([]);

        $out = $this->app->dispatch('/'.strtolower($arg1).'/open');

        UT::assertStringStartsWith('<!DOCTYPE html>', $out);
    
    }

    /**
     * @When I create new task memo
     */
    public function iCreateNewTaskMemo() {
        $this->task = new \modules\Tasks\model\Task($this->pdo);
    }

    /**
     * @Then I fill in :arg1 into textbox
     */
    public function iFillInIntoTextbox($arg1) {
        $this->title = $arg1;
    }

    /**
     * @Then hit :arg1
     */
    public function hit($arg1) {

        switch(strtolower($arg1)) {
            
            case 'save':
                $this->result = $this->task->create(['title'=>$this->title]);
                UT::assertEquals($this->title, $this->result->title);
                break;

            case 'done':
                
                $this->result = $this->task->update($this->result->id, [
                    'title' => $this->title,
                    'description' => $this->description
                ]);

                UT::assertEquals($this->description, $this->result->description);
                break;

        }

    
    }

    /**
     * @When edit form opens
     */
    public function editFormOpens() {
    
        $out = $this->app->dispatch('/tasks/edit/'.$this->result->id);

        UT::assertContains('<textarea', $out);
    
    }

    /**
     * @Then I fill in :arg1 into textarea
     */
    public function iFillInIntoTextarea($arg1) {
        $this->description = $arg1;
    }


    /**
     * @Then task appears in the list
     */
    public function taskAppearsInTheList() {

        $out = $this->app->dispatch('/tasks.json');

        UT::assertTrue(is_array(json_decode($out)), 'Should return array of objects');
    }

    /**
     * @Then I find a task with id :arg1
     */
    public function iFindATaskWithId($arg1) {
        $this->id = $arg1;
    }


    /**
     * @Then close it
     */
    public function closeIt() {
        $out = $this->app->dispatch('/tasks/close/'.$this->id);
    }


    /**
     * @Given there is a task with id :arg1
     */
    public function thereIsATaskWithId($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I find a task with id :arg1 and title :arg2
     */
    public function iFindATaskWithIdAndTitle($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given there is a task with a title :arg1
     */
    public function thereIsATaskWithATitle($arg1) {
        
        $tsk = new Task($this->pdo);
        $ret = $tsk->create([
            'title' => $arg1
        ]);

        UT::assertObjectHasAttribute('id', $ret);
        UT::assertEquals($arg1, $ret->title);
    
    }

    /**
     * @Then I find a task with a title :arg1
     */
    public function iFindATaskWithATitle($arg1) {
        $tsk = new Task($this->pdo);
        $ret = $tsk->search(['title' => $arg1]);
        UT::assertGreaterThan(0, count($ret));
        UT::assertTrue(is_numeric($ret[0]->id));
        $this->id = $ret[0]->id;
    }

    /**
     * @Then call RESTful :arg1
     */
    public function callRestful($arg1) {
        
        $this->req->shouldReceive('getMethod')->andReturn($arg1);
        $this->req->shouldReceive('getHeaders')->andReturn([]);
        $this->acl->shouldReceive('allow')->andReturn(TRUE);

        $json = $this->app->dispatch('/tasks/'.$this->id.".json");
        $data = json_decode($json);
        UT::assertEquals('1', $data->deleted);
    }

    /**
     * @Given logged in as group :arg1 member
     */
    public function loggedInAsGroupMember($arg1)
    {
        $this->req->shouldReceive('getMethod')->times(1)->andReturn('POST');
        
        $this->req->shouldReceive('post')->andReturn([
            'email' => 'admin@example.com',
            'password' => 'foo'
        ]);

        $this->req->shouldReceive('getHeaders')->andReturn([]);
        $this->acl->shouldReceive('allow')->andReturn(TRUE);

        $out = $this->app->dispatch('/user/login');

        UT::assertTrue(is_object(json_decode($out)), 'Should contain JSON encoded object');
        
    }

    /**
     * @Then call RESTfull :arg1 endpoint :arg2
     */
    public function callRestfullEndpoint($arg1, $arg2) {
       
        $this->req->shouldReceive('getMethod')->andReturn($arg1);
        $this->acl->shouldReceive('allow')->andReturn(TRUE);

        $this->app->dispatch($arg2);

    }

}
