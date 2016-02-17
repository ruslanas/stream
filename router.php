<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

session_start();
require 'vendor/autoload.php';
spl_autoload_register(function($class_name) {
    $search = ["./", "controllers/", "model/", "interfaces/", "exceptions/"];

    foreach($search as $location) {
        $path = $location.$class_name.'.php';
        if(file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

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
    header("Allow: GET, POST, DELETE, PUT");
    die($e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    die('Fatal error: '.$e->getMessage());
}
