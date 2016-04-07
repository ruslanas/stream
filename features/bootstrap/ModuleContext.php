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

}
