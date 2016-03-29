**Install**

```
git clone https://github.com/ruslanas/stream
composer install
bower install
```

**Use PHP built-in server for development**

```
php -S localhost:9001 router-dev.php
```

**NginX configuration**

```
location / {
    try_files /webroot/$uri router.php;
}
```

**Apache configuration**

```
RewriteEngine On

RewriteCond %{DOCUMENT_ROOT}/webroot/%{REQUEST_URI} -f
RewriteRule ^(.+) %{DOCUMENT_ROOT}/webroot/$1 [L]

RewriteCond %{REQUEST_URI} !^/webroot*
RewriteRule ^ router.php [L]
```

**Test**

```
cd stream
php vendor/phpunit/phpunit/phpunit --testdox --coverage-html report
```
