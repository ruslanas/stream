<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace modules\Events\Model;

use \PDO;

class Event {
    
    protected $table = 'events';
    protected $fields = [
        'id',
        'title',
        'description',
        'type',
        'when',
        'user_id'
    ];

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }
    
    public function read($id = NULL) {
        
        $query = "SELECT * FROM `{$this->table}`";

        if($id !== NULL) {
            $query .= " WHERE id = :id";
        }

        $statement = $this->db->prepare($query);
        
        if($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $statement->execute();
        
        if($id !== NULL) {
            return $statement->fetch();
        } else {
            $data = [];
            while($row = $statement->fetch()) {
                $data[] = $row;
            }
            return $data;
        }
    }

    private function _generate_statement($data, $id = NULL) {

        if($id === NULL) {
            $query = "INSERT INTO `{$this->table}` SET ";
        } else {
            $query = "UPDATE `{$this->table}` SET ";
        }

        $q = [];

        foreach($this->fields as $field) {

            if(empty($data[$field])) {
                continue;
            }

            $q[] = $field."=:".$field;
        }

        $query .= join(',', $q);

        if($id !== NULL) {
            $query .= " WHERE id = :id";
        }

        $statement = $this->db->prepare($query);

        foreach($this->fields as $field) {
            
            if(empty($data[$field])) {
                continue;
            }

            $statement->bindParam(":".$field, $data[$field]);
        }

        if($id !== NULL) {
            $statement->bindParam(":id", $id);
        }

        return $statement;
    }

    public function create($data) {

        $statement = $this->_generate_statement($data);

        $statement->execute();

        return $this->read($this->db->lastInsertId());
    }
    
    public function update($id, $data) {
        
        $statement = $this->_generate_statement($data, $id);
        
        $statement->execute();

        return $this->read($id);
    }
    
    public function delete($id) {
        
        $ret = $this->read($id);

        $statement = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        return $ret;
    }

}
