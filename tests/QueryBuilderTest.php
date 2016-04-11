<?php

use \Stream\Util\QueryBuilder;

class QueryBuilderTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->dsl = [

            'tasks',

            ['id', PDO::PARAM_INT],
            ['title', PDO::PARAM_STR],
            ['description', PDO::PARAM_STR],

            ['users as user', [
                ['id', PDO::PARAM_INT],
                ['email', PDO::PARAM_STR]
            ], 'user.id = tasks.user_id'],

            ['users as delegate', [
                ['id', PDO::PARAM_INT],
                ['email', PDO::PARAM_STR]
            ], 'delegate.id = tasks.delegate_id']

        ];
    }

    public function testFilter() {
    
        $statement = QueryBuilder::filter($this->dsl,
            
            [' ANd ',
                ['liKe', 'title', '%impl%'],
                [' oR',
                    ['= ', 'tasks.user_id', 1],
                    ['delegate_id', 2],
                    ['lIke', 'user.email', 'admin%']]
            
            ], \Stream\App::getConnection('test_stream')

        );

        $statement->execute();

        $expected = "WHERE ((title LIKE ?) AND ((tasks.user_id = ?) OR (delegate_id = ?) OR (user.email LIKE ?)))";
        
        $this->assertContains($expected, $statement->queryString);
        $this->assertEquals(1, $statement->rowCount());    
    
    }

    public function testSelect() {
        $query = \Stream\Util\QueryBuilder::select($this->dsl);

        $expected = "SELECT tasks.id, tasks.title, tasks.description, user.id as user_id, user.email as user_email, delegate.id as delegate_id, delegate.email as delegate_email"
            . " FROM tasks"
            . " LEFT JOIN users as user ON user.id = tasks.user_id"
            . " LEFT JOIN users as delegate ON delegate.id = tasks.delegate_id";

        $this->assertEquals($expected, $query);

    }

    public function testReshape() {
        $dsl = [

            'table',

            ['id', PDO::PARAM_INT],
            ['table_table', PDO::PARAM_INT], // <-

            ['user as owner',[
                ['id', PDO::PARAM_INT],
                ['email', PDO::PARAM_STR]
            ], 'owner.id = table.user_id']

        ];

        $data = (object)[
            'id' => 1,
            'table_table' => 'table',
            'owner_id' => 10,
            'owner_email' => '@',
        ];

        $obj = \Stream\Util\QueryBuilder::reshape($dsl, $data);

        $this->assertEquals('@', $obj->owner->email);

    }
}
