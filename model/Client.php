<?php
class Client {

	private $_default = [
		'type' => 0,
		'address' => '',
		'email' => '',
		'phone' => ''
	];

	public function __construct(PDO $pdo) {
		$this->db = $pdo;
	}
	public function getList() {

        $data = [];
        $sql = "SELECT clients.*, users.username FROM clients LEFT JOIN users ON clients.user_id = users.id"
        	." WHERE NOT clients.deleted"
        	." ORDER BY clients.created DESC LIMIT 100";
        foreach($this->db->query($sql) as $row) {
            $data[] = $row;
        }

        return $data;
	}

	public function save($id, $data) {
		$data = array_merge($this->_default, $data);
        if($id !== NULL) {
            $sql = "UPDATE clients"
            	." SET name = :name, email = :email, phone = :phone, type = :type, address = :address"
            	." WHERE id = :id";
        } else {
            $sql = "INSERT INTO clients (name, email, phone, type, address)"
            	." VALUES(:name, :email, :phone, :type, :address)";
        }

        $statement = $this->db->prepare($sql);
        $statement->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $statement->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $statement->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
        $statement->bindParam(':type', $data['type'], PDO::PARAM_INT);
        $statement->bindParam(':address', $data['address'], PDO::PARAM_STR);

        if($id !== NULL) {
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $statement->execute();
        return $id === NULL ? $this->db->lastInsertId() : $id;
	}

	public function getById($id, $showDeleted = FALSE) {
		
		$sql = "SELECT clients.*, users.username FROM clients"
			." LEFT JOIN users ON clients.user_id = users.id"
			." WHERE clients.id = :id AND clients.deleted = :deleted"
			." ORDER BY clients.created DESC LIMIT 100";

		$statement = $this->db->prepare($sql);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->bindParam(':deleted', $showDeleted);
		$statement->execute();
		return $statement->fetch();
	}

	public function delete($id) {
		$sql = "UPDATE clients SET deleted = 1 WHERE id = :id";
		$statement = $this->db->prepare($sql);
		$statement->bindParam(':id', $id);
		$statement->execute();
	}

	public function filter($options) {
		$sql = "SELECT * FROM clients WHERE ";
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
