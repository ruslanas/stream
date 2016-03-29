<?php
spl_autoload_register(function($class_name) {
    $search = ["system/", "controllers/", "model/"];

    foreach($search as $location) {
        $path = $location.$class_name.'.php';
        if(file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

spl_autoload_register(function($class_name) {
	$path = 'lib'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
	if(file_exists($path)) {
		require_once $path;
	}
});

spl_autoload_register(function($class_name) {
	$path = str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
	if(file_exists($path)) {
		require_once $path;
	}
});
