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

}