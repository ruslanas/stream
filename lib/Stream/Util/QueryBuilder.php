<?php

namespace Stream\Util;

use \PDO;

/*
protected $structure = [

    'tasks',

    ['id', PDO::PARAM_INT],
    ['title', PDO::PARAM_STR],
    ['description', PDO::PARAM_STR],
    ['deleted', PDO::PARAM_BOOL]

    ['users as user', [
        ['id', PDO::PARAM_INT],
        ['email', PDO::PARAM_STR]
    ], 'user.id = tasks.user_id'],

    ['users as delegate', [
        ['id', PDO::PARAM_INT],
        ['email', PDO::PARAM_STR]
    ], 'delegete.id = tasks.delegate_id']

];

 */
class QueryBuilder {

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

/**
    [' AND ',
        ['liKe', 'title', '%test%'],
        [' oR', ['= ', 'task.user_id', 1], ['delegate_id', 1]]
    ]
*/
    static private function parse($val, &$values, $pos = 0) {
    
        if(!is_array($val)) { 
            if($pos === 1) return $val ; else { $values[] = $val; return '?'; }
        }

        if(count($val) == 2) { array_unshift($val, ' = '); }

        $tk = [];

        for($i=1;$i<count($val);$i++) { $tk[] = self::parse($val[$i], $values, $i) ; }

        $str = join(' '.strtoupper(trim($val[0])).' ', $tk);

        return '('.$str.')';
    
    }

    static public function filter(array $dsl, array $filter, \PDO $pdo) {
        
        $values = [];
        
        $q = self::select($dsl).' WHERE '.self::parse($filter, $values);
        
        $statement = $pdo->prepare($q);
        
        for($i=0;$i<count($values);$i++) {
            $statement->bindParam($i+1, $values[$i]);
        }
        
        return $statement;

    }

    static public function select($dsl) {

        $table = $dsl[0];
        $fields = [];
        $joins = [];
        $tables = [];

        for($i=1;$i<count($dsl);$i++) {

            if(count($dsl[$i]) === 2) {
                $fields[] = $table.".".$dsl[$i][0];
            }

            if(count($dsl[$i]) > 2) {

                $exp = explode(' ', $dsl[$i][0]);
                $t = end($exp);
                $cols = $dsl[$i][1];
                $num_cols = count($cols);

                $joins[] = 'LEFT JOIN '.$dsl[$i][0]." ON ".$dsl[$i][2];

                for($j=0;$j<$num_cols;$j++) {
                    $fields[] = $t.".".$cols[$j][0]." as ".$t."_".$cols[$j][0];
                }

            }

        }

        return "SELECT ".join(', ', $fields)." FROM {$table} ".join(' ', $joins);

    }

    static public function reshape($dsl, $data) {
        $out = new \stdClass;

        if(empty($data)) { return $data; }

        for($i=1;$i<count($dsl);$i++) {
            if(count($dsl[$i]) === 2) {
                $prop = $dsl[$i][0];
                $out->{$prop} = $data->{$prop};
            } else {
                $ex = explode(' as ', $dsl[$i][0]);
                $t = end($ex);

                $out->{$t} = new \stdClass;

                $cols = $dsl[$i][1];
                for($j=0;$j<count($cols);$j++) {

                    $col = $cols[$j][0];
                    $prop = $t.'_'.$col;

                    if(property_exists($data, $prop)) {
                        $out->{$t}->{$col} = $data->{$prop};
                    }
                }
            }

        }

        return $out;
    }

    static public function update($db, $dsl, $data, $id = NULL) {

        if(empty($data)) { throw new \Exception; }

        $tableName = $dsl[0];

        if ($id === NULL) {
            $query = "INSERT INTO `{$tableName}` SET ";
        } else {
            $query = "UPDATE `{$tableName}` SET ";
        }

        $q = [];
        $t = [];

        $num = count($dsl);

        for($i=1;$i<$num;$i++) {

            $col = $dsl[$i];
            if(count($col) > 2 || !isset($data[$col[0]])) {
                continue;
            }

            $q[] = $col[0] . "=:" . $col[0];
            $t[$col[0]] = $col[1];

        }

        $query .= join(',', $q);

        if ($id !== NULL) {
            $query .= " WHERE `{$tableName}`.id = :id";
        }

        $statement = $db->prepare($query);

        foreach($t as $col => $type) {

            $statement->bindValue(":" . $col, $data[$col], $type);

        }

        if ($id !== NULL) {
            $statement->bindValue(":id", $id, PDO::PARAM_INT);
        }


        return $statement;

    }


}
