<?php
/*
** Developed by Haroldo Gondim
** Ask me something! https://www.twitter.com/iSlent
*/
date_default_timezone_set('America/Sao_Paulo');

class db {
  
  public static $conn;
  public static $queries;
  
	public static function Connect ($host, $name, $user, $pass)
	{
		$conn = new mysqli($host, $user, $pass, $name);
     
		if (!$conn)
		{
			exit('Error_' . str_replace(' ', '_', mysqli_error()));
			exit;
		} else {
      self::$conn = $conn;
    }
	}
	
	public static function Query ($var)
	{
    self::$queries++;
		return self::$conn->query($var);
	}
  
  public static function Find ($type = 'first', $var) {
    if($type == 'first') {
      $query = self::Query($var);
      $first = self::Assoc($query);
      if(count($first)) {
        return $first;
      }
      
      return [];
    } elseif($type == 'all') {
      $data = array();
      $query = self::Query($var);
      while($rows = self::Assoc($query)) {
        $data[] = $rows;
      }
      
      if(count($data)) {
        return $data;
      }
      
      return [];
    } elseif($type == 'count') {
      /* desativada até o momento */
    }
  }
	
	public static function NumRows ($var)
	{
		return $var->num_rows;
	}
	
	public static function FetchArray ($var)
	{
		self::Assoc($var);
	}
	
	public static function Assoc ($var)
	{
		return $var->fetch_array(MYSQLI_ASSOC);
	}
	
	public static function InsertId()
	{
		return self::$conn->insert_id;
	}
  
  public static function Insert($tabela, $dados) {
    $colunas = array_keys($dados);
		$valores = array_values($dados);

		if(count($colunas) != count($valores)) { 
			exit('Parâmetros incorretos.');
		}

		$colunas_query = implode(",", $colunas);

		$valores_query = array();

		foreach ($valores as $atual) {
			$valores_query[] = "'".$atual."'";
		}

		$valores_query = implode(",", $valores_query);

		$query = "INSERT INTO $tabela ($colunas_query) VALUES ($valores_query)";

		$sql = self::Query($query);

		if($sql) {
			return true;
		} else {
			exit('Não foi possível inserir os valores na tabela ' . $tabela . '. Erro: ' . self::$conn->error);
			return false;
		}
  }
  
  public function update($tabela, $dados, $where = array()) {
		$colunas = array_keys($dados);
		$valores = array_values($dados);
		$condicoes_col = array_keys($where);
		$condicoes_val = array_values($where);

		$i = 0;
		$set_query = '';
		foreach ($colunas as $key => $atual) {
			$set_query .= $atual . '=\''.$valores[$i].'\',';

			$i++;
		}

		$set_query = substr($set_query, 0, -1);

		if(empty($where)) {
			$query = "UPDATE $tabela SET $set_query";
		} else {
			$where_query = '';
			foreach ($where as $coluna => $valor) {
				$where_query .= $coluna . '=\''.$where[$coluna].'\' AND ';
			}

			$where_query = substr($where_query, 0, -strlen(' AND '));

			$query = "UPDATE $tabela SET $set_query WHERE $where_query";
		}

		$sql = self::Query($query);

		if($sql) {
			return true;
		} else {
			exit('Não foi possível inserir os valores na tabela ' . $tabela . '. Erro: ' . self::$conn->error);
		}
	}
}
if($_SERVER['SERVER_NAME'] == 'minecraft.localhost') {
  db::Connect('localhost', 'forum_mine', 'root', '');
} else {
  db::Connect('localhost', 'forum_mine', 'root', '');
}