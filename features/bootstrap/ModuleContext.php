<?php

use PHPUnit_Framework_Assert as UT;
use Stream\App, Stream\Request, Stream\Acl;

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

        UT::assertContains('<input name="title"', $out);
    
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

        
        if($this->result === NULL) {
            $this->result = $this->task->create(['title'=>$this->title]);
        } else {
            $this->result = $this->task->update($this->result->id, [
                'title' => $this->title,
                'description' => $this->description
            ]);
        }

        UT::assertEquals($this->title, $this->result->title);
    
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
    public function iFillInIntoTextarea($arg1)
    {
        $this->description = $arg1;
    }
}
