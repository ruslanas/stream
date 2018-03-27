Stream
======

Prerequisites
-------------

PHP 7, composer, bower

Install
-------

```bash
git clone https://github.com/ruslanas/stream
cd stream
composer install
bower install
```

Import test database
--------------------

```bash
mysql < data/dump.sql
```

Create and edit config.php file
-------------------------------

```bash
cp config.php.sample config.php
```

Use PHP built-in server for development
---------------------------------------

```bash
php -S localhost:9001 -t webroot router-dev.php
```

Test and create code coverage report
------------------------------------

```bash
php vendor/bin/phpunit --testdox --coverage-html report
```

NginX configuration
-------------------

```Nginx
location / {
    try_files /webroot/$uri router.php;
}
```

Apache configuration
--------------------

```ApacheConf
RewriteEngine On

RewriteCond %{DOCUMENT_ROOT}/webroot/%{REQUEST_URI} -f
RewriteRule ^(.+) %{DOCUMENT_ROOT}/webroot/$1 [L]

RewriteCond %{REQUEST_URI} !^/webroot/*
RewriteRule ^ router.php [L]
```

Notes
-----

```bash
cp hooks/pre-commit .git/hooks
```
