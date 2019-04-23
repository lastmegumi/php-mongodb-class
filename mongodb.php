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
		//$document = ['_id' => new MongoDB\BSON\ObjectID, 'name' => '菜鸟教程'];
		$_id= $bulk->insert($document);
		var_dump($_id);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}


	function saveAll($documents = array()){
		if(!$documents){ return;	}
		// 插入数据
		$bulk = new MongoDB\Driver\BulkWrite;
		// $bulk->insert(['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
		// $bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
		// $bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
		foreach($documents as $doc):
			$bulk->insert($doc);
		endforeach;
		$this->mcon->executeBulkWrite($this->d_t, $bulk);
	}

	function findAll($filter = [], $options = []){
		// $filter = ['x' => ['$eq' => 3]];
		// $options = [
		//     'projection' => ['_id' => 0],
		//     'sort' => ['x' => -1],
		// ];
		// 查询数据
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);
		foreach ($cursor as $document) {
		    $arr[] = $document;
		}
		return $arr;
	}

	function find(){
		// $filter = ['x' => ['$eq' => 3]];
		// $options = [
		//     'projection' => ['_id' => 0],
		//     'sort' => ['x' => -1],
		// ];
		// 查询数据
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);
		foreach ($cursor as $document) {
			return $document;
		}
		//return $arr;
	}

	function update($filter = [], $field = [], $options = []){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(
			$filter,
			$field,
			$options
		    // ['x' => 2],
		    // ['$set' => ['name' => '菜鸟工具', 'url' => 'tool.runoob.com']],
		    // ['multi' => false, 'upsert' => false]
		);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function delete($filter = [], $options = ['limit' => 1]){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete($filter, $options);
		// $bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
		// $bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function deleteAll($filter = []){
		$this->delete($filter, ['limit' => 0])
	}
}
?>