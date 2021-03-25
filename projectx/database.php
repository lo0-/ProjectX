<?php
class Database{
	public $_pdo;
	
	// General functions
	public function connect($host, $database, $username, $password){
		$charset = 'utf8mb4';

		$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			 $this->_pdo = new PDO($dsn, $username, $password, $options);
		} catch (\PDOException $e) {
			 throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}
	public function fetchAll($sql, $params){
		$sth = $this->_pdo->prepare($sql);
		$sth->execute();
		$result = $sth->fetchAll();
		return $result;
	}
	public function fetch($sql, $params){
		$sth = $this->_pdo->prepare($sql);
		$sth->execute();
		$result = $sth->fetch();
		return $result;
	}
	
	// ProjectX functions
	public function fetchEntityData($entity_type, $id){
		$entity_type = $this->cleanDbObjectName($entity_type);
		$result = null;
		
		// Fetch by id
		if(is_numeric($id)){
			$sql = "SELECT t.* FROM (SELECT 1 a) e LEFT JOIN $entity_type t ON 1 = 1 AND t.id = :id";
			$sth = $this->_pdo->prepare($sql);
			$sth->execute(array(':id' => $id));
			$result = $sth->fetch();
		}
		
		// Fetch by name
		else{
			$sql = "SELECT t.* FROM (SELECT 1 a) e LEFT JOIN $entity_type t ON 1 = 1 AND t.name = :name";
			$sth = $this->_pdo->prepare($sql);
			$sth->execute(array(':name' => $id));
			$result = $sth->fetch();
		}
		
		return $result;
	}
	public function deleteEntity($entity_type, $id){
		$entity_type = $this->cleanDbObjectName($entity_type);
		$result = null;
		
		// Delete by id
		if(is_numeric($id)){
			$sql = "DELETE FROM $entity_type WHERE id = :id";
			$sth = $this->_pdo->prepare($sql);
			$sth->execute(array(':id' => $id));
		}
		
		// Delete by name
		else{
			$sql = "DELETE FROM $entity_type WHERE name = :name";
			$sth = $this->_pdo->prepare($sql);
			$sth->execute(array(':name' => $id));
		}
	}
	public function updateEntity($entity){
		$entity_type = $this->cleanDbObjectName($entity->type);
		
		// Generate sql
		$sql = "UPDATE $entity_type SET ";
		foreach($entity->data as $key => $value){
			$sql .= "`$key` = :$key,";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE id = $entity->id";
		
		// Execute sql
		$sth = $this->_pdo->prepare($sql);
		$sth->execute($entity->data);
	}
	
	// Protected functions
	protected function cleanDbObjectName($object_name){
		$object_name = str_replace(' ', '-', $object_name);
		$object_name = preg_replace('/[^A-Za-z0-9_\-]/', '', $object_name);
		$object_name = strtolower($object_name);
		return $object_name;
	}
}