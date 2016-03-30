<?php
/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;


use \PDO;

class PersistentStorage
{

    protected $table = NULL;
    protected $fields = [];

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function read($id = NULL)
    {

        $query = "SELECT * FROM `{$this->table}`";

        if ($id !== NULL) {
            $query .= " WHERE id = :id";
        }

        $statement = $this->db->prepare($query);

        if ($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $statement->execute();

        if ($id !== NULL) {
            return $statement->fetch();
        } else {
            $data = [];
            while ($row = $statement->fetch()) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function delete($id)
    {

        $ret = $this->read($id);

        $statement = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

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

    protected function prepareStatement($data, $id = NULL)
    {

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