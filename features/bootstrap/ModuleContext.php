<?php

session_start();

use PHPUnit_Framework_Assert as UT;
use Stream\App, Stream\Request, Stream\Acl;

use modules\Tasks\model\Task;
use modules\Users\model\User;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class ModuleContext extends MinkContext implements Context, SnippetAcceptingContext {

    /** Give some context to scenario */
    public function __construct() {

        $this->app = new App;

        $this->app->loadConfig();

        $this->pdo = App::getConnection('test_stream');

        $this->app->rest(['/acl/:action'], \modules\Acl\Controller::class);

        $this->acl = \Mockery::mock(Acl::class);
        $this->req = \Mockery::mock(Request::class);
        $this->sess = \Mockery::mock(Session::class);

        $this->app->inject('acl', $this->acl);
        $this->app->inject('request', $this->req);
        $this->app->inject('session', $this->sess);

    }


    /**
     * @Given log in with email :arg1 and password :arg2
     */
    public function logInWithEmailAndPassword($arg1, $arg2) {

        $user = new User($this->pdo);

        $this->user = $user->login([
            'email' => $arg1,
            'password' => $arg2
        ]);

        UT::assertObjectHasAttribute('id', $this->user);

    }

    /**
     * @Given there is task with title :arg1 and id :arg2
     */
    public function thereIsTaskWithTitleAndId($arg1, $arg2) {

        $task = new \modules\Tasks\Decorators\Task($this->pdo);

        if(count($task->read($arg2))) {
            $task->update(NULL, ['title'=>$arg1, 'user_id' => $this->user->id]);
        } else {
            $task->create(['id'=>$arg2, 'title'=>$arg1, 'user_id' => $this->user->id]);
        }

        UT::assertEquals(10, $task->id);

    }

    /**
     * @When open task with id :arg1
     */
    public function openTaskWithId($arg1) {
        $task = new Task($this->pdo);
        $this->task = $task->read($arg1);
        UT::assertObjectHasAttribute('title', $this->task);
    }

    /**
     * @When can delegate
     */
    public function canDelegate() {
        UT::assertEquals($this->user->id, $this->task->user->id);
    }

    /**
     * @Then delegate to :arg1
     */
    public function delegateTo($arg1) {


        $this->task = (new \modules\Tasks\Decorators\Task($this->pdo))->read(1);
        $this->task->delegate($arg1);
        UT::assertEquals($arg1, $this->task->delegate->email);

    }

    /**
     * @Given there is user with email :arg1
     */
    public function thereIsUserWithEmail($arg1) {
        $user = new User($this->pdo);
        $found = $user->filter(['email', $arg1]);

        if(count($found) === 0) {
            $user->create(['email' => $arg1]);
        }
    }
}
