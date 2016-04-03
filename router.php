<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

session_start();

require 'vendor/autoload.php';

try {

    require_once 'index.php'; // <-- application setup

    echo Stream\App::getInstance()->dispatch($_SERVER['REQUEST_URI']);

} catch (Stream\Exception\NotFoundException $e) {

    http_response_code(404);
    die($e->getMessage());

} catch (Stream\Exception\ForbiddenException $e) {

    http_response_code(401);
    die("Illegal access: ".$e->getMessage());

} catch (Stream\Exception\UnknownMethodException $e) {

    http_response_code(405);
    header($e->getAllow());
    die($e->getMessage());

} catch (Exception $e) {

    http_response_code(500);
    syslog(LOG_CRIT, $e->getMessage()."\n".$e->getTraceAsString());
    die('Fatal error: '.$e->getMessage());

}
