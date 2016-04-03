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

    /** @var array Holds data structure definition. Subject to change. */
    protected $table = NULL;

    protected $_injectable = ['table'];

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

        reset($this->table);
        $tableName = key($this->table);

        $fieldList = "`{$tableName}`.*";

        $joins = '';

        foreach($this->table[$tableName] as $tbl => $cols) {

            if(!is_array($cols)) {
                continue;
            }

            $rel = rtrim($tbl, 's')."_id";

            $joins .= " LEFT JOIN `{$tbl}` ON `{$tbl}`.id = `{$tableName}`.`$rel`";
            
            foreach($cols as $col) {
            
                $fieldList .= ", `$tbl`.`$col` AS `{$tbl}_{$col}`";
            
            }

        }

        $query = "SELECT $fieldList FROM `{$tableName}`".$joins;
        
        if ($id !== NULL) {
            
            $query .= " WHERE `{$tableName}`.id = :id";
            if(in_array('deleted', $this->table[$tableName])
                && !is_array($this->table[$tableName])) {

                $query .= " AND NOT `{$tableName}`.deleted";
            
            }

        } else {
            
            if(in_array('deleted', $this->table[$tableName])) {
                $query .= " WHERE NOT `{$tableName}`.deleted";
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

        if($row === FALSE) {
            return FALSE;
        }

        $tableName = $this->_get_table_name();

        foreach($this->table[$tableName] as $tbl => $cols) {
            
            if(!is_array($cols)) {
                continue;
            }

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

    /**
     * Returns deleted record or FALSE
     * @param int $id
     * @return stdClass|FALSE
     */
    public function delete($id) {

        $ret = $this->read($id);

        $tableName = $this->_get_table_name();

        if(in_array('deleted', $this->table[$tableName])) {
            $query = "UPDATE `{$tableName}` SET deleted = 1 WHERE id = :id";
        } else {
            $query = "DELETE FROM `{$tableName}` WHERE id = :id";
        }

        $statement = $this->db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

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

    private function _get_table_name() {
    
        reset($this->table);
    
        return key($this->table);
    
    }

    private function prepareStatement($data, $id = NULL) {

        $tableName = $this->_get_table_name();

        if ($id === NULL) {
            $query = "INSERT INTO `{$tableName}` SET ";
        } else {
            $query = "UPDATE `{$tableName}` SET ";
        }

        $q = [];

        foreach ($this->table[$tableName] as $idx => $field) {
            
            if(is_array($field)) {
                continue;
            }
            
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

        foreach ($this->table[$tableName] as $idx => $field) {

            if(is_array($field)) {
                continue;
            }

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

    public function search($options) {

        $tableName = $this->_get_table_name();

        $sql = "SELECT * FROM `{$tableName}` WHERE ";
        
        $filter = [];
        
        foreach($options as $col => $value) {
            $filter[] = "$col = :$col";
        }
        
        $sql .= join(',', $filter);
        $statement = $this->db->prepare($sql);
        
        foreach($options as $col => $value) {
            $statement->bindParam(":$col", $value, PDO::PARAM_STR);
        }

        $statement->execute();
        $data = [];
        
        while($row = $statement->fetch()) {
            $data[] = $row;
        }
        
        return $data;

    }

}
