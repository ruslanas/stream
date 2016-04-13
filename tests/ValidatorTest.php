<?php

/**
 * Experimental validator test
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use \Stream\Validator;
use \Stream as F;

class ValidatorTest extends \PHPUnit_Framework_TestCase {
    
    public function setUp() {

        Validator::init(); // autoload class before using constants

        $dsl = [
            
            'tasks' => \modules\Tasks\Decorators\Task::class, [

                ['id', F\FIXED | F\REQUIRED],
                ['title', F\REQUIRED],

                ['completed', F\BOOL],
                
                ['url', F\URL],
                ['user_id', F\FIXED | F\REQUIRED],
                
                ['deleted', F\USER],

                ['users as user' => \modules\Users\Decorators\User::class, [
                
                    ['id', F\FIXED],
                    ['email', F\EMAIL | F\REQUIRED],
                    ['password', F\REQUIRED]
                
                ], 'user.id = tasks.user_id']

            ]

        ];
    
        $this->validator = new Validator($dsl);

        $pdo = \Stream\App::getConnection('test_stream');

        $this->sess = $this->getMockBuilder(\Stream\Session::class)->getMock();

        $this->validator->use(['Session', $this->sess]);
        $this->validator->use($pdo);
        $this->validator->use(new \modules\Users\Decorators\User($pdo));
                
    }

    public function testSkipFixed() {

        $this->assertEquals(2, F\REQUIRED);

        $this->sess->method('get')->willReturn(2);

        // in order to check data integrity we have to examine existing data
        $errors = $this->validator->validate([
            'id' => 1,
            'email' => 'info@example.com',
            'url' => 'http://dfg',
            'user_id' => "1",
            'deleted' => "1",
            'completed' => true
        ]);

        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayNotHasKey('email', $errors);
        $this->assertArrayNotHasKey('completed', $errors);
        $this->assertArrayNotHasKey('url', $errors);
        $this->assertArrayHasKey('deleted', $errors);
        $this->assertArrayNotHasKey('user_id', $errors);
    }

}
