<?php

/**
 * Dev router.
 * 
 * PHP version 5
 * 
 * @category Dev
 * @package  Stream
 * @author   Ruslanas Balciunas <ruslanas.com@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/ruslanas/stream
 */

$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');

if (file_exists(
    __DIR__.DIRECTORY_SEPARATOR.'webroot'.DIRECTORY_SEPARATOR.$requestUri
)
    && $requestUri != '/'
) {
    return false;
}

require 'router.php';
