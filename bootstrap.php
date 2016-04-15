<?php

/**
 * PHPUnit tests bootstrapper
 *
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

session_start();
require_once 'vendor/autoload.php';

(new \Stream\Util\Injectable)->service('QueryBuilder', function($pdo, $structure) {
    return new \Stream\Util\QueryBuilder($pdo, $structure);
});
