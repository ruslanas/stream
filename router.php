<?php

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
    $search = ["./", "controllers/", "model/"];
    $found = false;

    foreach($search as $location) {
        $path = $location.$class_name.'.php';
        if(file_exists($path)) {
            require_once $path;
            $found = true;
            break;
        }
    }

    if(!$found) {
        throw new Exception("Class not found `{$class_name}` in ".__FILE__.':'.__LINE__);
    }
});

require_once 'index.php';
try {
    $app->dispatch($_SERVER['REQUEST_URI']);
} catch (NotFoundException $e) {
    die($e->getMessage());
} catch (Exception $e) {
    die('Fatal error: '.$e->getTraceAsString());
}
