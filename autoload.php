<?php
spl_autoload_register(function($class_name) {
    $search = ["system/", "controllers/", "model/", "interfaces/", "lib/", "exceptions/"];

    foreach($search as $location) {
        $path = $location.$class_name.'.php';
        if(file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

