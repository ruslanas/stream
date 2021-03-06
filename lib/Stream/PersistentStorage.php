<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \PDO;

use Stream\Util\Injectable;
use Stream\Util\QueryBuilder;

/**
 * SCRUD
 */

class PersistentStorage extends Injectable {

    protected $_injectable = ['table', 'structure'];

    protected $storage;
    protected $db;
    protected $QueryBuilder;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo = NULL, $dsl = NULL) {
        
        if($dsl !== NULL) {
            $this->structure = $dsl;
        }
        
        $this->db = $pdo;
        
        $this->service('QueryBuilder', $pdo, $this->structure);

    }

    /**
     * Read from database
     * @param int|NULL $id
     * @param int|NULL $uid
     * @return \stdClass|array
     */
    public function read($id = NULL, $uid = NULL) {

        $tableName = $this->structure[0];

        $query = $this->QueryBuilder->select();

        $where = '';

        if ($id !== NULL) {

            $where = " WHERE `{$tableName}`.id = :id";
            $where .= " AND NOT `{$tableName}`.deleted";

        } else {

            $where = " WHERE NOT `{$tableName}`.deleted";

        }

        if(!empty($uid)) {
            if(!empty($where)) {
                $where .= " AND `{$tableName}`.user_id = :user_id";
            } else {
                $where = " WHERE `{tableName}`.user_id = :user_id";
            }
        }

        $statement = $this->db->prepare($query.$where);

        if ($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        if(!empty($uid)) {
            $statement->bindParam(':user_id', $uid, PDO::PARAM_INT);
        }

        $statement->execute();

        if ($id !== NULL) {

            $data = $statement->fetch();

            return $this->QueryBuilder->reshape($data);

        } else {

            $data = [];

            while ($row = $statement->fetch()) {

                $data[] = $this->QueryBuilder->reshape($row);
            }

            return $data;
        }
    }

    /**
     * Returns deleted record or FALSE
     * @param int $id
     * @return \stdClass|FALSE
     */
    public function delete($id, $uid = NULL) {

        $ret = $this->read($id);

        if(!$ret) {
            return FALSE;
        }

        $tableName = $this->_get_table_name();

        $deletable = true;
        array_walk($this->structure, function($it) use (&$deletable) {
            if($it[0] === 'deleted') { $deletable = false; }
        });

        if(!$deletable) {
            $query = "UPDATE `{$tableName}` SET deleted = 1 WHERE id = :id";
        } else {
            $query = "DELETE FROM `{$tableName}` WHERE id = :id";
        }

        if($uid !== NULL) {
            $query .= " AND `{$tableName}`.user_id = :user_id";
        }

        $statement = $this->db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        if($uid !== NULL) {
            $statement->bindParam(':user_id', $uid, PDO::PARAM_INT);
        }

        if($statement->execute()) {
            $ret->deleted = 1;
        }

        if($statement->rowCount() === 0) {
            return NULL;
        }

        return $ret;

    }

    /**
     * Alias for `delete`. Play nice with Angular.
     * @param int $id
     * @return \stdClass
     */
    public function remove($id) {

        return $this->delete($id);

    }

    /**
     * Insert record with associative $data
     * @param array $data
     * @return \stdClass
     */
    public function create($data) {

        $statement = $this->QueryBuilder->update($data);

        $statement->execute();

        $id = $this->db->lastInsertId();

        if(empty($id)) { throw new \Exception('Failed to create record'); }

        return $this->read($id);
    }

    protected function isValid($data) {
        return true;
    }

    /**
     * Update record $id with associative $data
     * @param int $id
     * @param array $data
     * @return \stdClass
     */
    public function update($id, $data) {

        if(!$this->isValid($data)) {
            throw new \Exception;
        }

        $statement = $this->QueryBuilder->update($data, $id);

        $statement->execute();

        return $this->read($id);
    }

    /** Get table name from structure DSL */
    private function _get_table_name() {

        if($this->structure !== NULL) { return $this->structure[0]; }

        throw new \Exception;

    }

    public function filter($f) {
        
        if(empty($f)) { return []; }

        $st = $this->QueryBuilder->filter($f);
        
        $st->execute();
        $arr = [];
        while($r = $st->fetch()) {
            $arr[] = $this->QueryBuilder->reshape($r);
        }
        return $arr;
    }

    /**
     * @param array $options
     * @param bool $_
     */
    public function search($options, $_ = NULL) {

        if(empty($options)) {
            return [];
        }

        $sql = $this->QueryBuilder->select();

        $where = " WHERE NOT `{$this->structure[0]}`.deleted AND ";

        $sql .= $where;

        $filter = [];

        // there's a room for improvement
        foreach($options as $col => $value) {

            //[$col, $type]
            //[$op => [$col, $type]]

            if(is_array($value)) {

                reset($value);
                $col = key($value);
                $filter[] = "$col LIKE :$col";
                continue;
            
            }

            if($col === 'id') {
                $filter[] = "`{$this->structure[0]}`.id = :id";
            } else {
                $filter[] = "$col = :$col";
            }
        
        }

        $str = $_ ? ' OR ' : ' AND ';

        $sql .= '('.join($str, $filter).')';

        $statement = $this->db->prepare($sql);

        foreach($options as $col => $value) {

            if(is_array($value)) {

                reset($value);
                $col = key($value);

                list($value, $type) = $value[$col];
                $value = $value === NULL ? '' : $value;

            }

            $statement->bindValue(":$col", $value, $type);

        }

        $statement->execute();
        $data = [];

        while($row = $statement->fetch()) {
            $data[] = $this->QueryBuilder->reshape($row);
        }

        return $data;

    }

}
