<?php
/**
 * If application relies on router.php and is not a subclass of App base class
 * it must implement AppInterface
 *
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
namespace Stream\Interfaces;

interface AppInterface {
    public function dispatch($uri);
    static public function getInstance();
}
