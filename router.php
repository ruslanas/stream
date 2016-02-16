<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */

if(php_sapi_name() == 'cli-server') {
    if(preg_match('/(?:png|js|css)$/i', $_SERVER['REQUEST_URI'], $matches)) {

        $path = 'webroot'.$_SERVER['REQUEST_URI'];

        if(file_exists($path)) {
            switch($matches[0]) {
                case 'js':
                    header('Content-Type: text/js');
                    break;
                case 'css':
                    header('Content-Type: text/css');
                    break;
                case 'png':
                    header('Content-Type: image/png');
                    break;
                default:
                    header('Content-Type: application/octet-stream');
            }
            echo file_get_contents($path);
            exit;
        }
    }
}

require 'vendor/autoload.php';
spl_autoload_register(function($class_name) {
    $search = ["./", "controllers/", "model/", "interfaces/", "exceptions/"];
    $found = false;

    foreach($search as $location) {
        $path = $location.$class_name.'.php';
        if(file_exists($path)) {
            require_once $path;
            $found = true;
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
