<?php
class _MongoDB{
	function __construct(){
		$this->mcon = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		$this->database = "test";
		$this->table = "sites";
		$this->d_t = $this->database . '.' . $this->table;
	}

	function setTable($table){		
		$this->table = $table;
		$this->d_t = $this->database . '.' . $this->table;
	}

	function setDatabase($database){
		$this->database = "test"; 
		$this->d_t = $this->database . '.' . $this->table;
	}

	function currentTable(){return $this->table;}

	function currentDatabase(){return $this->database;}

	function save($document = []){
		$bulk = new MongoDB\Driver\BulkWrite;
		$_id= $bulk->insert($document);
		var_dump($_id);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function saveAll($documents = array()){
		if(!$documents){ return;	}
		$bulk = new MongoDB\Driver\BulkWrite;
		foreach($documents as $doc):
			$bulk->insert($doc);
		endforeach;
		$this->mcon->executeBulkWrite($this->d_t, $bulk);
	}

	function findAll($filter = [], $options = []){

		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);
		foreach ($cursor as $document) {
		    $arr[] = $document;
		}
		return $arr;
	}

	function find($filter = [], $options = [], $join = []){
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);
		foreach ($cursor as $document) {
			if($join){
				$table = $join['name']? $join['name']:$join['table'];
				$key = $join['key']? $join['key']: $this->table . "_id";
				$this->setTable($join['table']);
				$document->$table = $this->find([$key => $document->_id], @$join['join']);
			}
			return $document;
		}
	}

	function update($filter = [], $field = [], $options = []){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(
			$filter,
			$field,
			$options
		);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function delete($filter = [], $options = ['limit' => 1]){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete($filter, $options);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function deleteAll($filter = []){
		$this->delete($filter, ['limit' => 0]);
	}
}

$md = new _MongoDB();
try {
	
$res = $md->find([],[],["table" =>'runoob']);
print_r($res);
//$md->setTable("runoob");
//$md->save(['sites_id'	=>	$res->_id, "name"	=>	"ceshi"]);
} catch (Exception $e) {
	print_r($e->getMessage());
}
?>