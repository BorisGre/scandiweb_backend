<?php

	class DB{
	
		private $link = false;
		private $instance = false;
		private $mysql = null;
	
		private $config = array();
		
		private $where;
		private $limit;
		private $order;
		
		public function __construct($config){

			$this->config = $config;
			$this->mysql = new mysqli;
			$this->link = $this->connect();
		}

		public function __destruct(){

			if($this->link){
				$this->mysql->close();
			} 
		}

		/*
		 * Create or return a connection to the MySQL server.
		 */
		
		private function connect(){

			if(!is_resource($this->link) || empty($this->link)){

				try {
					$link = $this->mysql->real_connect($this->config['host'], $this->config['user'], $this->config['password']);
					$this->mysql->select_db($this->config['database']);
					$this->mysql->character_set_name('utf8');

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

		public function count($table){

			$query = sprintf("SELECT COUNT(*) FROM (%s)", $table);
			$this->mysql->real_query($query);
			$result = $this->mysql->use_result();
			return $result->fetch_row();
		}

		public function add($table, $data){
			
			$fields = '';
			$values = '';
			
			foreach($data as $column => $value){

				$fields .= sprintf("`%s`,", $column);
				$values .= sprintf("'%s',", $this->mysql->real_escape_string($value));
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, $values);
		
			if(!$this->mysql->query($sql)){

				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->mysql->errno.': '.$this->mysql->error);
			}else{

				return true;
			}
		}
		
		public function get($table, $page = 0, $itemsPerPage){
			
		    $data = [];
			$sql = sprintf("SELECT * FROM %s limit %s,%s", $table, $page*$itemsPerPage, $itemsPerPage);

			 try {

					$this->mysql->real_query($sql);
				
					if ($result = $this->mysql->use_result()){
					//	echo "CCC";
						$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
						$data = $rows;
						foreach ($rows as $row) {
							//var_dump($row);
						}
						$result->close();
          }

			} catch(Exception $e){

			 throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->mysql->errno.': '.$this->mysql->error);
			}
			
			return $data;
		}
	
		
		public function update($table, $data){

			$fields = '';
			$values = '';
			
			foreach($data as $column => $value){

				$fields .= sprintf("`%s`,", $column);
				$values .= sprintf("'%s',", $this->mysql->real_escape_string($value));
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$sql = sprintf("UPDATE INTO %s (%s) VALUES (%s)", $table, $fields, $values);
		
			if(!$this->mysql->query($sql)){

				throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->mysql->errno.': '.$this->mysql->error);
			}else{

				return true;
			}		
		}
		
		public function delete($table, $data){
			
			foreach($data as $field => $value){

				$this->where = $field .'="'.$value.'"';
			}

			if(empty($this->where)){

				throw new Exception("Where is not set. Can't delete whole table.");
			} else {
			
				$sql = sprintf("DELETE FROM %s%s", $table, ' WHERE '.$this->where); //$this->extra());
			
				if(!$this->mysql->real_query($sql)){
					
					throw new Exception('Error executing MySQL query: '.$sql.'. MySQL error '.$this->mysql->errno.': '.$this->mysql->error);
				}else{
					return true;
				}
			}
		}
	}
?>
