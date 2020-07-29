<?php 
	class DB
		{
			private static $_instance = null;
			private $_pdo,
			$_query,
			$_error = false,
			$_result,
			$_count = 0;

		private function __construct(){
			try
			{
				$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'), Config::get('mysql/username'),Config::get('mysql/password'));
			}
			catch(PDOException $e)
			{
				die($e->getMessage());
			}
		}

		public static function getInstance(){
		if(!isset(self::$_instance))
		{
			self::$_instance = new db();
		}
		return self::$_instance;
	}


		public function query($sql, $params= array())
		{
			$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql))
		{
			$i = 1;
			foreach ($params as $value) {
				$this->_query->bindValue($i,$value);
				$i++;
			}
		}
		if($this->_query->execute())
		{
			$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
			$this->_count = $this->_query->rowCount();
		}
		else
		{
			$this->_error = true;
		}
		return $this;
		}

		private function action($action, $table, $where = array())
	{
		if(count($where) === 3)
		{
			$operators = array('=','>','<','>=','<=');

			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if(in_array($operator, $operators))
			{
				$sql_query = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

				
				if(!$this->query($sql_query,array($value))->error())
				{
					return $this;
				}
			}
		}
			return false;
	}
	public function get($table, $where)
	{
		return $this->action('SELECT *',$table, $where);
	}
	public function select($table)
	{
		return $this->query("SELECT * FROM {$table}");
	}
	public function delete($table, $where)
	{
		return $this->action('DELETE',$table, $where);
	}
	public function insert($table, $fields = array())
	{
		if(count($fields))
		{

			$keys = array_keys($fields);
			$values = str_repeat("?,", count($fields)-1).'?';
			
			$sql = "INSERT INTO {$table} (". implode(',',$keys) .") VALUES ({$values})";
			
			if(!$this->query($sql, $fields)->error())
			{
				return true;
			}
		}
		return false;
	}
	public function update_todo($table, $id, $fields = array())
	{
		$set = '';
		$i= 1;
		foreach ($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($i < count($fields))
			{
				$set .= ', ';
			}
			$i++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE todo_id = {$id}";
		

		if(!$this->query($sql, $fields)->error())
		{
			return true;
		}
		return false;
	}

	public function count()
	{
		return $this->_count;
	}
	public function error()
	{
		return $this->_error;
	}
	public function result()
	{
		return $this->_result;
	}
	public function GetFirst()
	{
		return $this->result()[0];
	}


}
?>