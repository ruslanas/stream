**Use PHP built-in server for development**

```
php -S localhost:9001 router.php
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
