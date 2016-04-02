<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream\Test;

class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    public function getConnection() {
        
        $this->pdo = \Stream\App::getConnection('test_stream');

        return $this->createDefaultDBConnection($this->pdo);

    }
    
    public function getDataSet() {

        return $this->createFlatXMLDataSet('data/stream.xml');
    
    }

}
