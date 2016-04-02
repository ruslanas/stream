**Install**

```{sh}
git clone https://github.com/ruslanas/stream
cd stream
composer install
bower install
```

**Import test database**
```{sh}
mysql < data/dump.sql
```

**Create and edit config.php file**
```
cp config.php.sample config.php
```

**Use PHP built-in server for development**

```
php -S localhost:9001 router-dev.php
```

**Test and create code coverage report**

```
php vendor/bin/phpunit --testdox --coverage-html report
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

**Notes**

```
cp hooks/pre-commit .git
```