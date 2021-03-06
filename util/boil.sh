#!/bin/sh

if [ $# -eq 0 ] ; then
    cat << EOF
Usage: sh util/boil.sh ModuleName
EOF
    exit 1
fi

cd modules

if [ -d "$1" ] ; then
    cat << EOF
Module \`$1\` exists.
Aborting!
EOF
    exit 1
fi

mkdir $1
cd $1

DIR=${PWD##*/}

echo Generating module \`$DIR\` boilerplate...

cat << EOF > Controller.php
<?php

namespace modules\\$DIR;

use \\Stream\\Interfaces\\RestApi;

class Controller extends \\Stream\\PageController implements RestApi {
    
    /** Declare final to make accessible */
    public function get() {}
    public function post() {}
    public function delete() {}

}
EOF

mkdir tests
mkdir templates
mkdir model

cat << EOF > tests/ControllerTest.php
<?php

namespace modules\\$DIR;

class ControllerTest extends \\PHPUnit_Framework_TestCase {
    
    public function setUp() {
    
        parent::setUp();
    
        \$this->controller = new Controller;
    
    }

    public function testFail() {
    
        \$this->assertTrue(FALSE, 'No tests created');
    
    }

}
EOF

cat << EOF > templates/index.php
<?php \$this->layout('basic'); ?>
EOF

cat << EOF
\`$DIR\` module created. 
EOF

