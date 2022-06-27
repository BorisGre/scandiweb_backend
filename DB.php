<?php
//namespace Scandiweb;
	class DB{
	
		private $__link = null;
		//private $__instance = null;
		private $__mysql = null;
	
		private array $__config = array();
		private string $currenTable; 

		private string $where;
		private string $limit;
		private string $order;

		private $__DBtables = array();
	
		
		public function __construct($config, $itemsPerPage = 0){

			$this->__config = $config;
			$this->itemsPerPage = $itemsPerPage;
			$this->__mysql = new mysqli;
			$this->__link = $this->connect();
			$this->buildTablesLinks = $this->buildLinks();
		}

		public function __destruct(){

			if($this->__link){
				$this->__mysql->close();
			} 
		}

		private function buildLinks(){

			$res = $this->__mysql->query("show tables");
		 	$rows = $res->fetch_all(MYSQLI_NUM);
			
			foreach($rows as $row => $value){ 

				$table = $value[0];
				array_push($this->__DBtables, $value[0]);
				$this->$table = fn() => $this->bl($table);
			}
		}

		public function bl($table){

			$this->currentTable = $table;
			return $this;
		}

		/*
		 * Create or return a connection to the MySQL server.
		 */
		
		private function connect(){

			if(!is_resource($this->__link) || empty($this->__link)){

				try {
					$link = $this->__mysql->real_connect($this->__config['host'], $this->__config['user'], $this->__config['password']);
					$this->__mysql->select_db($this->__config['database']);
					//$this->__mysql->character_set_name('utf8');

				} catch (Exception $e){
					throw new Exception('Could not connect to MySQL database.');
				}
			}
			return $link;
		}
		
		/**
		 * MySQL Where methods
		 */
		
		private function __where($info, $type = 'AND'){

			$where = $this->where;

			foreach($info as $row => $value){

				if(empty($where)){

					$where = sprintf("WHERE `%s`='%s'", $row, mysql_real_escape_string($value));
				} else {

					$where .= sprintf(" %s `%s`='%s'", $type, $row, mysql_real_escape_string($value));
				}
			}
			$this->where = $where;
		}
		
		private function where($field, $equal = null){

			if(is_array($field)){
				$this->__where($field);
			}else{
				$this->__where(array($field => $equal));
			}
			return $this;
		}
		
		private function and_where($field, $equal = null){

			return $this->where($field, $equal);
		}
		
		private function or_where($field, $equal = null){

			if(is_array($field)){
				$this->__where($field, 'OR');
			}else{
				$this->__where(array($field => $equal), 'OR');
			}
			return $this;
		}
		
		/**
		 * MySQL limit method
		 */
		
		private function limit($limit){

			$this->limit = 'LIMIT '.$limit;
			return $this;
		}
		
		/**
		 * MySQL query helper
		 */
		
		private function extra(){

			$extra = '';
			if(!empty($this->where)) $extra .= ' '.$this->where;
			if(!empty($this->order)) $extra .= ' '.$this->order;
			if(!empty($this->limit)) $extra .= ' '.$this->limit;
			// cleanup
			$this->where = null;
			$this->order = null;
			$this->limit = null;
			return $extra;
		}
		
		/**
		 * MySQL Query methods
		 */

		public function count($table = ''){

			$query = sprintf("SELECT COUNT(*) FROM (%s)", $this->currentTable);
			$this->__mysql->real_query($query);
			$result = $this->__mysql->use_result();
			return $result->fetch_row()[0];
		}

		public function getTypes(){

			$this->__mysql->real_query('SELECT name FROM TYPE');
			
			return $types;
		}

		public function add($data){
			
			$fields = '';
			$values = '';
			
			foreach($data as $column => $value){

				$fields .= sprintf("`%s`,", $this->__mysql->real_escape_string($column));
				$values .= sprintf("'%s',", $this->__mysql->real_escape_string($value));
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);

			$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->currentTable, $fields, $values);
		
			if(!$this->__mysql->query($sql)){

				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->__mysql->errno.': '.$this->__mysql->error);
			}else{

				return true;
			}
		}	
		
		public function get($page = 0){
			
		    $data = [];
			$offset = $this->__mysql->real_escape_string($page*$this->itemsPerPage);
			$limit = $this->itemsPerPage;

			$sql = sprintf("SELECT * FROM %s limit %s,%s", $this->currentTable, $offset, $this->itemsPerPage);

			 try {

					$this->__mysql->real_query($sql);
				
					if ($result = $this->__mysql->use_result()){
					
						$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
						$data = $rows;
						foreach ($rows as $row) {
							//var_dump($row);
						}
						$result->close();
         			}

			} catch(Exception $e){

			 throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->__mysql->errno.': '.$this->__mysql->error);
			}
			
			return $data;
		}

		
		public function update($data){

			$fields = '';
			$values = '';
			
			foreach($data as $column => $value){

				$fields .= sprintf("`%s`,", $this->__mysql->real_escape_string($column));
				$values .= sprintf("'%s',", $this->__mysql->real_escape_string($value));
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);

			$sql = sprintf("UPDATE INTO %s (%s) VALUES (%s)", $this->currentTable, $fields, $values);
		
			if(!$this->__mysql->real_query($sql)){

				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->__mysql->errno.': '.$this->__mysql->error);
			} else { 

				return true;
			}		
		}
		
		public function delete($data){

			foreach($data as $field => $value){

				$escapedField = $this->__mysql->real_escape_string($field);
				$escapedValue = $this->__mysql->real_escape_string($value);

				$this->where = $escapedField.'="'.$escapedValue.'"';
			}

			if(empty($this->where)){

				throw new Exception("Where is not set. Can't delete whole table.");
			} else {
			
				$sql = sprintf("DELETE FROM %s%s", $this->currentTable, ' WHERE '.$this->where); //$this->extra());
			
				if(!$this->__mysql->real_query($sql)){
					
					throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->__mysql->errno.': '.$this->__mysql->error);
				}else{
					return true;
				}
			}
		}
	}
?>
