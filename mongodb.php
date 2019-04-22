<?php
class _MongoDB{
	function __construct(){
		$this->mcon = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		$this->database = "test";
		$this->table = "sites";
		$this->d_t = $this->database . '.' . $this->table;
	}

	function save($document = []){
		$bulk = new MongoDB\Driver\BulkWrite;
		//$document = ['_id' => new MongoDB\BSON\ObjectID, 'name' => '菜鸟教程'];

		$_id= $bulk->insert($document);

		var_dump($_id);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function find($filter = [], $options = []){
		// 插入数据
		// $bulk = new MongoDB\Driver\BulkWrite;
		// $bulk->insert(['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
		// $bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
		// $bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
		// $this->mcon->executeBulkWrite('test.sites', $bulk);

		$filter = ['x' => ['$eq' => 1]];
		$options = [
		    'projection' => ['_id' => 0],
		    'sort' => ['x' => -1],
		];
		// 查询数据
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->mcon->executeQuery($this->d_t, $query);

		foreach ($cursor as $document) {
		    print_r($document);
		}
	}

	function update(){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(
		    ['x' => 2],
		    ['$set' => ['name' => '菜鸟工具', 'url' => 'tool.runoob.com']],
		    ['multi' => false, 'upsert' => false]
		);

		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}

	function delete(){
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
		$bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $this->mcon->executeBulkWrite($this->d_t, $bulk, $writeConcern);
	}
}
?>
<?php
$md = new _MongoDB();
$md->save();
?>