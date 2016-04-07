<?php
if(php_sapi_name() == 'cli-server') {

    if(preg_match('/(?:map|png|js|css|html|woff2|ico)$/i', $_SERVER['REQUEST_URI'], $matches)) {

        $path = 'webroot'.$_SERVER['REQUEST_URI'];

        if(file_exists($path)) {
            switch($matches[0]) {
                case 'html':
                    header('Content-Type: text/html');
                    break;
                case 'js':
                    header('Content-Type: text/js');
                    break;
                case 'css':
                    header('Content-Type: text/css');
                    break;
                case 'png':
                    header('Content-Type: image/png');
                    break;
                case 'woff2':
                    header('Content-Type: application/font-woff2');
                    break;
                case 'ico';
                    header('Content-Type: image/x-icon');
                    break;
                default:
                    header('Content-Type: application/octet-stream');
            }
            echo file_get_contents($path);
            exit;
        }
    }
}

require 'router.php';
