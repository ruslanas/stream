<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \PDO;
use \stdClass;

use \Stream\Util\Injectable;

/**
 * SCRUD
 */

class PersistentStorage extends Injectable {

    protected $_injectable = ['table', 'structure'];

    protected $storage;
    protected $db;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo = NULL) {
        $this->db = $pdo;
    }

    /**
     * Read from database
     * @param int|NULL $id
     * @param int|NULL $uid
     * @return \stdClass|array
     */
    public function read($id = NULL, $uid = NULL) {

        $tableName = $this->structure[0];

        $query = \Stream\Util\QueryBuilder::select($this->structure);

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

            return \Stream\Util\QueryBuilder::reshape($this->structure, $data);

        } else {

            $data = [];

            while ($row = $statement->fetch()) {

                $data[] = \Stream\Util\QueryBuilder::reshape($this->structure, $row);
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

        $statement = \Stream\Util\QueryBuilder::update($this->db, $this->structure, $data);

        $statement->execute();

        $id = $this->db->lastInsertId();

        if(empty($id)) { throw new \Exception('Failed to create record'); }

        return $this->read($id);
    }

    /**
     * Update record $id with associative $data
     * @param int $id
     * @param array $data
     * @return \stdClass
     */
    public function update($id, $data) {

        $statement = \Stream\Util\QueryBuilder::update($this->db, $this->structure, $data, $id);

        $statement->execute();

        return $this->read($id);
    }

    /** Get table name from structure DSL */
    private function _get_table_name() {

        if($this->structure !== NULL) { return $this->structure[0]; }

        throw new \Exception;

    }

    public function search($options, $_ = NULL) {

        if(empty($options)) {
            return [];
        }

        $sql = \Stream\Util\QueryBuilder::select($this->structure);

        $where = " WHERE NOT `{$this->structure[0]}`.deleted AND ";

        $sql .= $where;

        $filter = [];

        // there's a room for improvement
        foreach($options as $col => $value) {

            if($col === 'id') {
                $filter[] = "`{$this->structure[0]}`.id = :id";
            } else {
                $filter[] = "$col = :$col";
            }
        
        }

        $str = $_ ? ' OR ' : ' AND ';

        $sql .= join($str, $filter);

        $statement = $this->db->prepare($sql);

        foreach($options as $col => $value) {

            $type = \PDO::PARAM_STR;

            if(is_array($value)) {

                list($value, $type) = $value;

            }

            $statement->bindValue(":$col", $value, $type);

        }

        $statement->execute();
        $data = [];

        while($row = $statement->fetch()) {
            $data[] = \Stream\Util\QueryBuilder::reshape($this->structure, $row);
        }

        return $data;

    }

}
