#!/bin/sh

# WARNING: USE AT YOUR OWN RISK

# chance to make up your mind
if [ $# -eq 0 ] ; then
    cat << EOF
Usage: sh util/build.sh ok
EOF
    exit 1
fi


if [ "$(ls -A ./ 2> /dev/null)" != "" ] ; then
    echo "Directory not empty. ABORTING!"
    exit 1
fi

# cd output

# if [ $? -ne 0 ] ; then
#     echo "Must be run in project root directory. ABORTING!"
#     exit 1
# fi

#git clone http://github.com/ruslanas/stream

git clone ../stream

cd stream

composer install --no-dev

bower install

mkdir ../build

if cp -r webroot modules templates lib vendor router.php index.php data/.htaccess \
../build ; then
    echo "Files copied"
else
    echo "Could not copy files. ABORTING!"
    exit 1
fi

# if cp config.php.sample ../build/config.php ; then
#     echo "Edit config.php and manually import data from data/stream.sql"
# else
#     echo "Could not create config.php :-("
#     exit 1
# fi

cd ../build

rm -r modules/*/tests

tar -cvzf deploy.tar.gz *

echo "Congratulations! :-)"
