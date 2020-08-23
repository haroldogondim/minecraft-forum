<?php

/** 
** WEB PANEL
** CREATED BY HAROLDO GONDIM (haroldogondim.com)
** UNAUTORIZATHED COPYING
*/

class SQL {
  
  public $conn;
  
  
  public function __construct($conn) {
    $this->conn = $conn;
	}
  
  public function query($query) {
    return $this->conn->query($query);
  }
  
  public function first($query, $params = array()) {
    $sql = $this->conn->prepare($query);
    
    if(is_array($params) && count($params)) {
      for($i = 0; $i < count($params); $i++) {
        $i_value = $i + 1;
        $sql->bindValue($i_value, $params[$i]);
      }
    }
    
    $sql->execute();
    return $sql->fetch(PDO::FETCH_OBJ);
  }
  
  public function data($query, $params = array()) {
    $sql = $this->conn->prepare($query);
    
    if(is_array($params) && count($params)) {
      for($i = 0; $i < count($params); $i++) {
        $i_value = $i + 1;
        $sql->bindValue($i_value, $params[$i]);
      }
    }
    
    if(!$sql->execute()) {
      print_r($sql->errorInfo());
      exit;
    }
    
    return $sql->fetchAll(PDO::FETCH_OBJ);
  }
  
  
  public function insert($tabela, $dados) {
		$colunas = array();
		$valores = array();

		foreach ($dados as $coluna => $valor) {
			$colunas[] = $coluna;
			$valores[] = $valor;
		}

		if(count($colunas) != count($valores)) { 
			$this->error = 'DB01';
			return false;
		}

		$colunas_query = implode(",", $colunas);

		$valores_query = array();

		foreach ($colunas as $atual) {
			$valores_query[] = '?';
		}

		$valores_query = implode(",", $valores_query);

		$query = "INSERT INTO $tabela ($colunas_query) VALUES ($valores_query)";

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($colunas); $i++) {
			$i_real = $i + 1;
			
			$sql->bindValue($i_real, $valores[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}

	public function update($tabela, $dados, $where = array()) {
		$colunas = array();
		$valores = array();
		$condicoes_col = array();
		$condicoes_val = array();

		foreach ($dados as $coluna => $valor) {
			$colunas[] = $coluna;
			$valores[] = $valor;
		}

		foreach ($where as $coluna => $valor) {
			$condicoes_col[] = $coluna;
			$condicoes_val[] = $valor;
		}

		if(count($colunas) != count($valores)) { 
			$this->error = 'DB01';
			return false;
		}

		$i = 0;
		$set_query = '';
		foreach ($colunas as $atual) {
			$set_query .= $atual . '=?,';

			$i++;
		}

		$set_query = substr($set_query, 0, -1);

		if(empty($where)) {
			$query = "UPDATE $tabela SET $set_query";
		} else {
			$where_query = '';
			foreach ($where as $coluna => $valor) {
				$where_query .= $coluna . '=? AND ';
			}

			$where_query = substr($where_query, 0, -strlen(' AND '));

			$query = "UPDATE $tabela SET $set_query WHERE $where_query";
		}

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($colunas); $i++) {
			$i_real = $i + 1;
			
			$sql->bindValue($i_real, $valores[$i]);
		}

		for ($i=0; $i < count($condicoes_col); $i++) { 
			$i_real = $i_real + 1;

			$sql->bindValue($i_real, $condicoes_val[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}

	public function delete($tabela, $where) {
		$condicoes_col = array();
		$condicoes_val = array();

		foreach ($where as $coluna => $valor) {
			$condicoes_col[] = $coluna;
			$condicoes_val[] = $valor;
		}

		$where_query = '';
		foreach ($where as $coluna => $valor) {
			$where_query .= $coluna . '=? AND ';
		}

		$where_query = substr($where_query, 0, -strlen(' AND '));

		$query = "DELETE FROM $tabela WHERE $where_query";

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($condicoes_col); $i++) { 
			$i_real = $i + 1;

			$sql->bindValue($i_real, $condicoes_val[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}
  
  public function lastId() {
    return $this->conn->lastInsertId();
  }
  
}
  // Check if this is localhost server or client server
  if($_SERVER['SERVER_NAME'] == 'minecraft.localhost') {
    $conn_db = 'forum_mine';
    $conn_host = 'localhost';
    $conn_user = 'root';
    $conn_pass = '';
    $conn_pure = new PDO("mysql:host=$conn_host; dbname=$conn_db", $conn_user, $conn_pass);
    if(!$conn_pure) {
      exit('Failure to connect to mySQL. Please, check the parameters and try again.');
    }
  } else {
    $conn_db = 'forum_mine';
    $conn_host = 'localhost';
    $conn_user = 'root';
    $conn_pass = '';
    $conn_pure = new PDO("mysql:host=$conn_host; dbname=$conn_db", $conn_user, $conn_pass);
    if(!$conn_pure) {
      exit('Failure to connect to mySQL. Please, check the parameters and try again.');
    }
  }

$conn = new SQL($conn_pure);