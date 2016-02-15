**Use PHP built-in server for development**

```
php -S localhost:9001 router.php
```

**NginX configuration**

```
location / {
    try_files $uri $uri/ /webroot/$uri router.php;
}
```
