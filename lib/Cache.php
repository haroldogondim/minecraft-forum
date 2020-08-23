<?php
class Cache
{
	static public $caminho = 'lib/temp/%s.txt';
	static public $duracao = 300;
  
	static function novoCaminho($caminho) {
		$this->caminho = $caminho;
	}
  
	static function arquivo($nome) {
		return sprintf(self::$caminho, $nome);
	}
  
	static function ler($nome) {
		$arquivo_nome = self::arquivo($nome);
		if (file_exists($arquivo_nome)) {
			$conteudo = unserialize(file_get_contents($arquivo_nome));
			if ($conteudo['expira'] > time() || $conteudo['expira'] == 0)
			{
				return $conteudo['conteudo'];
			}
			else
			{
				@unlink($arquivo_nome);
				return false;
			}
		}
		return false;
	}
  
	static function salva($conteudo, $nome, $duracao = null) {
		$expira = (is_null($duracao) ? self::$duracao : $duracao) + ($duracao == 0 ? 0 : time());
		$arquivo_nome = self::arquivo($nome);
		file_put_contents($arquivo_nome, serialize(array('conteudo' => $conteudo, 'expira' => $expira)));
		return $conteudo;
	}
  
	static function exclui($nome) {
		foreach(glob(sprintf(self::$caminho, $nome)) as $cache) {
			@unlink($cache);
		}
	}
}