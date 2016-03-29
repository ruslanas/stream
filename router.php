<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

session_start();
require 'vendor/autoload.php';
require 'autoload.php';

try {

    require_once 'index.php'; // <-- application setup

    App::getInstance()->dispatch($_SERVER['REQUEST_URI']);

} catch (NotFoundException $e) {
    http_response_code(404);
    die($e->getMessage());
} catch (ForbiddenException $e) {
    http_response_code(401);
    die("Illegal access: ".$e->getMessage());
} catch (UnknownMethodException $e) {
    http_response_code(405);
    header($e->getAllow());
    die($e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    syslog(LOG_CRIT, $e->getMessage()."\n".$e->getTraceAsString());
    die('Fatal error: '.$e->getMessage());
}
