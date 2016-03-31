<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

use \PDO;
use \stdClass;

/**
 * CRUD
 */
abstract class PersistentStorage {

    protected $table = NULL;
    protected $fields = [];
    protected $join = [];

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Construct select query with a very hacky join just to pass a testcase
     * @param int $id
     * @return mixed
     */
    public function read($id = NULL) {


        $fieldList = "`{$this->table}`.*";
        $joins = '';

        foreach($this->join as $tbl => $cols) {

            $rel = rtrim($tbl, 's')."_id";
            $joins .= " LEFT JOIN `{$tbl}` ON `{$tbl}`.id = `{$this->table}`.`$rel`";
            foreach($cols as $col) {
                $fieldList .= ", `$tbl`.`$col` AS `{$tbl}_{$col}`";
            }

        }

        $query = "SELECT $fieldList FROM `{$this->table}`".$joins;
        
        if ($id !== NULL) {
            $query .= " WHERE `{$this->table}`.id = :id";
            if(in_array('deleted', $this->fields)) {
                $query .= " AND NOT `{$this->table}`.deleted";
            }
        } else {
            if(in_array('deleted', $this->fields)) {
                $query .= " WHERE NOT `{$this->table}`.deleted";
            }
        }

        $statement = $this->db->prepare($query);

        if ($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $statement->execute();

        if ($id !== NULL) {
            return $this->reshape($statement->fetch());
        } else {
            
            $data = [];
            
            while ($row = $statement->fetch()) {
                
                $data[] = $this->reshape($row);
            }
            
            return $data;
        }
    }

    protected function reshape($row) {

        foreach($this->join as $tbl => $cols) {
            
            $rel = new stdClass();

            foreach($cols as $col) {
                $property = $tbl."_".$col;
                $rel->{$col} = $row->{$property};
            }
            
            $rec = rtrim($tbl, 's');
            $row->{$rec} = $rel;

        }

        return $row;

    }

    public function delete($id) {

        $ret = $this->read($id);

        if(in_array('deleted', $this->fields)) {
            $query = "UPDATE `{$this->table}` SET deleted = 1 WHERE id = :id";
        } else {
            $query = "DELETE FROM `{$this->table}` WHERE id = :id";
        }

        $statement = $this->db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // if(in_array('deleted', $this->fields)) {
        //     $ret = $this->read($id);
        // }

        return $ret;
    }

    public function create($data) {

        $statement = $this->prepareStatement($data);

        $statement->execute();

        return $this->read($this->db->lastInsertId());
    }

    public function update($id, $data) {

        $statement = $this->prepareStatement($data, $id);

        $statement->execute();

        return $this->read($id);
    }

    private function prepareStatement($data, $id = NULL) {

        if ($id === NULL) {
            $query = "INSERT INTO `{$this->table}` SET ";
        } else {
            $query = "UPDATE `{$this->table}` SET ";
        }

        $q = [];

        foreach ($this->fields as $field) {

            if (empty($data[$field])) {
                continue;
            }

            $q[] = $field . "=:" . $field;
        }

        $query .= join(',', $q);

        if ($id !== NULL) {
            $query .= " WHERE id = :id";
        }

        $statement = $this->db->prepare($query);

        foreach ($this->fields as $field) {

            if (empty($data[$field])) {
                continue;
            }

            $statement->bindParam(":" . $field, $data[$field]);
        }

        if ($id !== NULL) {
            $statement->bindParam(":id", $id);
        }

        return $statement;
    }
}