<?php

require_once 'autoload.php';

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;



/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext {

    private $data = [];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct() {
    
        \Stream\App::getConnection('test_stream');
        
        $this->tasks = new modules\Tasks\Controller;
        
        $this->req = \Mockery::mock('request');

        $this->tasks->inject('request', $this->req);
    
    }

    /**
     * @Given :arg1 tab is open
     */
    public function tabIsOpen($arg1) {
        $this->tasks->open();
    }

    /**
     * @When click :arg1 button
     */
    public function clickButton($arg1) {
        $this->save();
    }

    /**
     * @Then new bigger textarea shows up for description
     */
    public function newBiggerTextareaShowsUpForDescription() {
        $this->tasks->edit();
    }

    /**
     * @Then I start typing new task into textbox
     */
    public function iStartTypingNewTaskIntoTextbox() {
        $this->data = ['title' => 'feature'];
    }

    /**
     * @Then save
     */
    public function save() {

        $this->req->shouldReceive('post')->andReturn($this->data);

        $this->tasks->save();
    }


    /**
     * @Then I enter description
     */
    public function iEnterDescription() {
        $this->data['description'] = 'implement tasks feature';
    }

}
