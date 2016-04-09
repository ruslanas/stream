<?php
/**
 * @author Ruslanas Balčiūnas
 */

/* DELETE AFTER USAGE */
$url = 'http://stream.wri.lt/deploy.tar.gz';
$fname = 'latest.tar.gz';
file_put_contents($fname, fopen($url, 'r'));

// remove old version
if(file_exists('latest.tar')) {
    unlink('latest.tar');
}

$p = new PharData($fname);
try {
    $p->decompress();
} catch (Exception $e) {
    die($e->getMessage());
}

try {
    $phar = new PharData('latest.tar');
    $phar->extractTo('./', null, true);
} catch (Exception $e) {
    $e->getMessage();
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'webroot'));

foreach($iterator as $item) {
    chmod($item, 0755);
}

unlink('latest.tar');
unlink($fname);

echo 'Done!';
?>
