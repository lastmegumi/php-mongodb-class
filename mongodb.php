<?php
class _MongoDB{
	private static $instance = null;

	private function __construct(){
		$password = "pwd";
		$connect_string = "mongodb+srv://lastzzz:{$password}@cluster0-wnwag.gcp.mongodb.net/test?retryWrites=true";
		//$connect_string = "mongodb://localhost:27017";
		$this->mcon = new MongoDB\Driver\Manager($connect_string);
		$this->database = "test";
		$this->table = "sites";
		$this->d_t = $this->database . '.' . $this->table;
	}

	public function __clone(){
        //throw error
        trigger_error("Can't clone object",E_USER_ERROR);
    }    

	//public static function getInstance()
	public static function init()
	{
		if (self::$instance == null)
		{
		  self::$instance = new _MongoDB();
		}
		return self::$instance;
	}

	static function _id($id = null){
		return new MongoDB\BSON\ObjectId($id);
	}

	function setTable($table){		
		$this->table = $table; 
		$this->d_t = $this->database . '.' . $this->table;
	}

	function setDatabase($database){
		$this->database = $database; 
		$this->d_t = $this->database . '.' . $this->table;
	}

	function currentTable(){return $this->table;}

	function currentDatabase(){return $this->database;}

	function save($document = []){
		if(!$document){
			foreach (get_object_vars($this) as $key => $value) {
				$document[$key]	= $value;
			}
		}
		$bulk = new MongoDB\Driver\BulkWrite;
		$_id= $bulk->insert($document);
		//var_dump($_id);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
		return $_id;
	}

	function saveAll($documents = array()){
		if(!$documents){ return;}
		$bulk = new MongoDB\Driver\BulkWrite;
		foreach($documents as $doc):
			$bulk->insert($doc);
		endforeach;
		return $this->mcon->executeBulkWrite($this->d_t, $bulk);
	}

	function findAll($filter = [], $options = []){
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);
		foreach ($cursor as $document) {
		    $arr[] = $document;
		}
		return @$arr? $arr : null;
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
		return $result;
	}

	function delete($filter = [], $options = ['limit' => 1]){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete($filter, $options);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
		return $result;
	}

	function deleteAll($filter = []){
		return $this->delete($filter, ['limit' => 0]);
	}
}
?>